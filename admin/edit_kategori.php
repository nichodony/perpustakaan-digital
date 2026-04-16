<?php
include '../koneksi.php'; // Pastikan file ini berisi koneksi ke database
if (isset($_GET['KategoriID'])) {
    $kategoriID = intval($_GET['KategoriID']);
    $query = "SELECT * FROM kategoribuku WHERE KategoriID = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $kategoriID);
    $stmt->execute();
    $result = $stmt->get_result();
    $kategori = $result->fetch_assoc();
    
    if (!$kategori) {
        echo "<div class='alert alert-danger'>Kategori tidak ditemukan.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-warning'>ID Kategori tidak ditemukan.</div>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $namaKategori = $_POST['NamaKategori'];
    
    $updateQuery = "UPDATE kategoribuku SET NamaKategori = ? WHERE KategoriID = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("si", $namaKategori, $kategoriID);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Kategori berhasil diperbarui.</div>";
        echo "<meta http-equiv='refresh' content='2;url=kategori_list.php'>"; // Redirect ke daftar kategori setelah 2 detik
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui kategori.</div>";
    }
}
?>

<div class="container mt-4">
    <h2>Edit Kategori</h2>
    <form method="POST">
        <div class="mb-3">
            <label for="NamaKategori" class="form-label">Nama Kategori</label>
            <input type="text" class="form-control" id="NamaKategori" name="NamaKategori" value="<?php echo htmlspecialchars($kategori['NamaKategori']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="kategori_list.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<?php include 'footer.php'; ?>
