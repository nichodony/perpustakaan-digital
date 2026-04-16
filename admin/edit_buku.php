<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['BukuID'])) {
    $BukuID = $_GET['BukuID'];

    // Ambil data buku yang akan diedit
    $sql = "SELECT * FROM buku WHERE BukuID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $BukuID);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    // Proses update data buku jika formulir disubmit
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $Judul = $_POST['Judul'];
        $Deskripsi = $_POST['Deskripsi'];  // Ambil deskripsi
        $Penulis = $_POST['Penulis'];
        $Penerbit = $_POST['Penerbit'];
        $TahunTerbit = $_POST['TahunTerbit'];
        $kategori = $_POST['kategori']; // Ambil kategori terpilih

        // Cek apakah gambar baru diunggah
        if (isset($_FILES['Gambar']) && $_FILES['Gambar']['error'] === 0) {
            // Menghapus gambar lama jika ada
            if (!empty($book['Gambar'])) {
                unlink("assets/img/" . $book['Gambar']);
            }

            // Menyimpan gambar baru
            $gambar = $_FILES['Gambar'];
            $gambar_name = basename($gambar['name']);
            $target_dir = "assets/img/";
            $target_file = $target_dir . $gambar_name;
            
            // Memindahkan gambar yang diunggah
            move_uploaded_file($gambar['tmp_name'], $target_file);
        } else {
            $gambar_name = $book['Gambar']; // Gunakan gambar lama jika tidak diubah
        }

        // Update query dengan gambar baru
        $update_sql = "UPDATE buku SET Judul = ?, Deskripsi = ?, Penulis = ?, Penerbit = ?, TahunTerbit = ?, Gambar = ? WHERE BukuID = ?";        
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssssssi", $Judul, $Deskripsi, $Penulis, $Penerbit, $TahunTerbit, $gambar_name, $BukuID);        
        $update_stmt->execute();

        // Update kategori buku di tabel relasi
        // Hapus kategori lama
        $delete_kategori_sql = "DELETE FROM kategoribuku_relasi WHERE BukuID = ?";
        $delete_stmt = $conn->prepare($delete_kategori_sql);
        $delete_stmt->bind_param("i", $BukuID);
        $delete_stmt->execute();

        // Tambahkan kategori baru
        if (!empty($kategori)) {
            foreach ($kategori as $KategoriID) {
                $insert_kategori_sql = "INSERT INTO kategoribuku_relasi (BukuID, KategoriID) VALUES (?, ?)";
                $insert_stmt = $conn->prepare($insert_kategori_sql);
                $insert_stmt->bind_param("ii", $BukuID, $KategoriID);
                $insert_stmt->execute();
            }
        }

        // Redirect setelah update
        header("Location: buku.php");
        exit();
    }
} else {
    echo "Buku tidak ditemukan.";
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body { 
            background: linear-gradient(45deg, #3498db, #8e44ad); 
            color: #fff; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh; 
            padding: 20px; 
            position: relative;
        }

        .content {
            margin-left: center;
            padding: 20px;
            color: white;
            overflow-y: auto;
            width: 100%;
        }

        .edit-form {
            background-color: #fff;
            color: #2c3e50;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            width: 70%;
            margin: 30px auto;
        }

        .edit-form h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.8rem;
            color: #8e44ad;
        }

        .edit-form label {
            font-size: 1.1rem;
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
        }

        .edit-form input,
        .edit-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        .edit-form button {
            background-color: #8e44ad;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.3s ease;
        }

        .edit-form button:hover {
            background-color: #9b59b6;
        }

        .gambar-preview {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
        }

        .gambar-preview img {
            width: 150px;
            height: auto;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        footer {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 10px 0;
            text-align: center;
            color: #fff;
            position: absolute;
            width: 100%;
            bottom: 0;
        }

        footer p {
            font-size: 1rem;
        }

        @media (max-width: 768px) {
            .content {
                margin-left: 0;
            }

            .edit-form {
                width: 90%;
            }
        }
        .back-button {
            display: block;
            width: max-content;
            padding: 12px 30px;
            margin: 20px auto;
            background: #8e44ad;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
            cursor: pointer;
        }
        .back-button:hover {
            background: #9b59b6;
        }

        /* Menggunakan Flexbox untuk container */
        .kategori-container {
            display: flex;
            flex-wrap: wrap;  /* Jika ruang tidak cukup, elemen akan pindah ke baris berikutnya */
            gap: 15px;        /* Jarak antar elemen */
            justify-content: flex-start; /* Posisi elemen di kiri */
        }

        /* Masing-masing checkbox dan label dalam div */
        .kategori-checkbox {
            display: flex;
            align-items: center;  /* Agar checkbox dan label sejajar secara vertikal */
        }

        .kategori-checkbox input {
            margin-right: 5px;  /* Jarak antara checkbox dan label */
        }

        .kategori-checkbox label {
            font-size: 1rem; /* Ukuran font label bisa disesuaikan */
            margin-left: 5px; /* Jika diperlukan, beri sedikit jarak antara checkbox dan teks */
        }

    </style>
</head>
<body>
    <!-- Content -->
    <div class="content">
        <div class="edit-form">
            <h2>Edit Buku</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="Judul">Judul:</label>
                <input type="text" name="Judul" value="<?php echo $book['Judul']; ?>" required>

                <label for="Deskripsi">Deskripsi:</label>
                <textarea name="Deskripsi" rows="4" required><?php echo $book['Deskripsi']; ?></textarea>

                <label for="Penulis">Penulis:</label>
                <input type="text" name="Penulis" value="<?php echo $book['Penulis']; ?>" required>

                <label for="Penerbit">Penerbit:</label>
                <input type="text" name="Penerbit" value="<?php echo $book['Penerbit']; ?>" required>

                <label for="TahunTerbit">Tahun Terbit:</label>
                <input type="number" name="TahunTerbit" value="<?php echo $book['TahunTerbit']; ?>" required>

                <label for="kategori">Pilih Kategori:</label><br>
                <div class="kategori-container">
                    <?php 
                    // Ambil semua kategori dari tabel kategoribuku
                    $sql_kategori_all = "SELECT * FROM kategoribuku";
                    $result_kategori_all = $conn->query($sql_kategori_all);

                    // Ambil kategori buku yang sudah dipilih
                    $sql_kategori_selected = "
                        SELECT k.KategoriID 
                        FROM kategoribuku k
                        JOIN kategoribuku_relasi kr ON k.KategoriID = kr.KategoriID
                        WHERE kr.BukuID = ?
                    ";
                    $stmt_kategori_selected = $conn->prepare($sql_kategori_selected);
                    $stmt_kategori_selected->bind_param("i", $BukuID);
                    $stmt_kategori_selected->execute();
                    $result_kategori_selected = $stmt_kategori_selected->get_result();
                    $selected_kategori = [];
                    while ($kategori = $result_kategori_selected->fetch_assoc()) {
                        $selected_kategori[] = $kategori['KategoriID'];
                    }
                    $stmt_kategori_selected->close();

                    // Tampilkan checkbox untuk setiap kategori
                    while ($kategori = $result_kategori_all->fetch_assoc()):
                    ?>
                        <div class="kategori-checkbox">
                            <input type="checkbox" name="kategori[]" value="<?php echo $kategori['KategoriID']; ?>"
                                <?php echo in_array($kategori['KategoriID'], $selected_kategori) ? 'checked' : ''; ?>> 
                            <label><?php echo $kategori['NamaKategori']; ?></label>
                        </div>
                    <?php endwhile; ?>
                </div>



                <label for="Gambar">Gambar Buku (Opsional):</label>
                <input type="file" name="Gambar" accept="image/*">

                <?php if (!empty($book['Gambar'])): ?>
                    <div class="gambar-preview">
                        <label>Gambar Saat Ini:</label>
                        <img src="../assets/img/<?php echo $book['Gambar']; ?>" alt="Gambar Buku">
                    </div>
                <?php endif; ?>

                <button type="submit">Update Buku</button>
            </form>
            <a href="buku.php" class="back-button">Kembali</a>
        </div>
    </div>
    <?php $conn->close(); ?>
</body>
</html>
