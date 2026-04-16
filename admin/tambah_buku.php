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

// Proses tambah buku
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = $_POST['judul'];
    $deskripsi = $_POST['deskripsi'];
    $penulis = $_POST['penulis'];
    $penerbit = $_POST['penerbit'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $kategori = $_POST['kategori']; // kategori yang dipilih (bisa array)
    $image = $_FILES['image'];

    // Proses upload gambar
    $imageName = $image['name'];
    $imageTmpName = $image['tmp_name'];
    $imageSize = $image['size'];
    $imageError = $image['error'];
    
    $imageExt = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageExt, $allowedExts)) {
        if ($imageError === 0) {
            if ($imageSize < 5000000) { // Max size 5MB
                $imageNewName = uniqid('', true) . '.' . $imageExt;
                $imageDestination = '../assets/img/' . $imageNewName;

                if (move_uploaded_file($imageTmpName, $imageDestination)) {
                    // Masukkan buku ke dalam tabel buku
                    $sql = "INSERT INTO buku (Judul, Deskripsi, Penulis, Penerbit, TahunTerbit, Gambar) VALUES (?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssssss", $judul, $deskripsi, $penulis, $penerbit, $tahun_terbit, $imageNewName);
                    $stmt->execute();
                    $last_id = $conn->insert_id;

                    // Masukkan relasi kategori buku ke dalam tabel kategoribuku_relasi
                    foreach ($kategori as $kategori_id) {
                        $sql_relasi = "INSERT INTO kategoribuku_relasi (BukuID, KategoriID) VALUES (?, ?)";
                        $stmt_relasi = $conn->prepare($sql_relasi);
                        $stmt_relasi->bind_param("ii", $last_id, $kategori_id);
                        $stmt_relasi->execute();
                    }

                    // Redirect ke halaman daftar buku
                    header("Location: buku.php");
                    exit();
                } else {
                    $message = "Gagal mengupload gambar.";
                }
            } else {
                $message = "Ukuran gambar terlalu besar.";
            }
        } else {
            $message = "Terjadi kesalahan dalam upload gambar.";
        }
    } else {
        $message = "Format gambar tidak valid. Gunakan JPG, JPEG, PNG, atau GIF.";
    }
}

// Ambil daftar kategori dari database
$sql_kategori = "SELECT * FROM kategoribuku";
$result_kategori = $conn->query($sql_kategori);
if ($result_kategori === false) {
    die("Error: " . $conn->error);
}

// Tutup koneksi database hanya jika koneksi berhasil dibuat
if (isset($conn)) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Buku - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            height: 100vh;
            background: linear-gradient(45deg, #3498db, #8e44ad);
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            color: white;
            overflow-y: auto;
        }

        .form-container {
            background-color: #fff;
            color: #2c3e50;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            width: 70%;
            margin: 30px auto;
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.8rem;
            color: #8e44ad;
        }

        .form-container label {
            font-size: 1.1rem;
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
        }

        .form-container input, .form-container select, .form-container textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        .form-container button {
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

        .form-container button:hover {
              background-color: #9b59b6;
        }

        .form-container .message {
            margin-top: 20px;
            text-align: center;
            font-size: 1.2rem;
            color: green;
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

        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        button, .back-button {
            padding: 12px 20px;
            font-size: 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            transition: 0.3s ease;
        }

        .back-button {
            background-color: #e74c3c;
            color: white;
        }

        .back-button.detail {
            background-color: #2ecc71;
        }

        .back-button:hover {
            opacity: 0.8;
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;  /* Jika ruang tidak cukup, elemen akan pindah ke baris berikutnya */
            gap: 15px;        /* Jarak antar elemen */
            justify-content: flex-start; /* Posisi elemen di kiri */
        }

        .checkbox-group label {
            display: inline-block;
            width: auto;
            font-size: 1rem;
        }

        .checkbox-group input {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <!-- Content -->
    <div class="content">
        <div class="form-container">
            <h2>Tambah Buku Baru</h2>

            <?php if (isset($message)): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>

            <form action="tambah_buku.php" method="POST" enctype="multipart/form-data">
                <label for="judul">Judul Buku</label>
                <input type="text" id="judul" name="judul" required>

                <label for="deskripsi">Deskripsi Buku</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" required></textarea>

                <label for="penulis">Penulis</label>
                <input type="text" id="penulis" name="penulis" required>

                <label for="penerbit">Penerbit</label>
                <input type="text" id="penerbit" name="penerbit" required>

                <label for="tahun_terbit">Tahun Terbit</label>
                <input type="number" id="tahun_terbit" name="tahun_terbit" required>

                <label for="kategori">Pilih Kategori</label>
                <div class="checkbox-group">
                    <?php
                    // Tampilkan setiap kategori sebagai checkbox
                    while ($kategori = $result_kategori->fetch_assoc()) {
                        echo '<label><input type="checkbox" name="kategori[]" value="' . $kategori['KategoriID'] . '"> ' . $kategori['NamaKategori'] . '</label>';
                    }
                    ?>
                </div>

                <label for="image">Gambar Sampul Buku</label>
                <input type="file" id="image" name="image" accept="image/*" required>

                <div class="button-container">
                    <button type="submit">Tambah Buku</button>
                    <a href="buku.php" class="back-button">Kembali ke Daftar Buku</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>