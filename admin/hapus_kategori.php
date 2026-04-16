<?php
include 'koneksi.php'; // Pastikan koneksi ke database sudah benar

// Periksa apakah KategoriID dikirim melalui URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Cek apakah kategori ada di database
    $query = "SELECT * FROM kategoribuku WHERE KategoriID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Jika kategori ditemukan, lakukan penghapusan
        $deleteQuery = "DELETE FROM kategoribuku WHERE KategoriID = ?";
        $deleteStmt = $conn->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $id);

        if ($deleteStmt->execute()) {
            echo "<script>
                alert('Kategori berhasil dihapus!');
                window.location.href='kategori.php'; // Redirect ke halaman kategori
            </script>";
        } else {
            echo "<script>alert('Gagal menghapus kategori!');</script>";
        }
    } else {
        echo "<script>alert('Kategori tidak ditemukan!');</script>";
    }
} else {
    echo "<script>alert('ID kategori tidak ditemukan!');</script>";
}
?>
