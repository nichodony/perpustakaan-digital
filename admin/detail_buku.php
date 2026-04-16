<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['UserID'])) {
    header("Location: ../login.php");
    exit();
}

// Koneksi database
$host = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Proses penghapusan buku jika tombol hapus diklik
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['hapus_buku'])) {
    // Hapus gambar dari folder jika ada
    if (!empty($buku['Gambar']) && file_exists("../assets/img/" . $buku['Gambar'])) {
        unlink("assets/img/" . $buku['Gambar']); // Hapus gambar
    }

    // Hapus data buku dari database
    $sql_hapus = "DELETE FROM buku WHERE BukuID = ?";
    $stmt_hapus = $conn->prepare($sql_hapus);
    $stmt_hapus->bind_param("i", $BukuID);
    $stmt_hapus->execute();

    // Redirect ke halaman utama setelah penghapusan
    header("Location: buku.php");
    exit();
}

// Mendapatkan BukuID dari URL
if (isset($_GET['BukuID'])) {
    $BukuID = intval($_GET['BukuID']);
    $sql_buku = "SELECT * FROM buku WHERE BukuID = ?";
    
    // Gunakan Prepared Statement
    $stmt = $conn->prepare($sql_buku);
    $stmt->bind_param("i", $BukuID);
    $stmt->execute();
    $result_buku = $stmt->get_result();

    if ($result_buku->num_rows > 0) {
        $buku = $result_buku->fetch_assoc();
    } else {
        // Jika buku tidak ditemukan, set default agar tidak error
        $buku = [
            'Judul' => 'Tidak Ditemukan',
            'Deskripsi' => 'Deskripsi tidak tersedia',
            'Gambar' => 'default.jpg',
            'Penulis' => 'Tidak diketahui',
            'Penerbit' => 'Tidak diketahui',
            'TahunTerbit' => '-',
            'Kategori' => 'Tidak diketahui'
        ];
    }

    // Ambil kategori buku terkait
    $sql_kategori = "
    SELECT k.NamaKategori 
    FROM kategoribuku k
    JOIN kategoribuku_relasi kr ON k.KategoriID = kr.KategoriID
    WHERE kr.BukuID = ?
    ";
    $stmt_kategori = $conn->prepare($sql_kategori);
    $stmt_kategori->bind_param("i", $BukuID);
    $stmt_kategori->execute();
    $result_kategori = $stmt_kategori->get_result();

    $kategori_buku = [];
    while ($kategori = $result_kategori->fetch_assoc()) {
    $kategori_buku[] = $kategori['NamaKategori'];
    }
    $stmt_kategori->close();

    $stmt->close();
} else {
    echo "<div style='color: red; text-align: center; font-size: 1.5rem;'>ID Buku tidak ditemukan.</div>";
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Buku - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; margin: 0; padding: 0; }
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

        .container { 
            background: #fff; 
            color: #333; 
            border-radius: 15px; 
            padding: 20px; 
            max-width: 600px; 
            width: 100%; 
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15); 
            text-align: center;
        }

        .container img { 
            width: 80%; 
            max-height: 400px; 
            object-fit: cover; 
            border-radius: 10px; 
            margin-bottom: 15px;
        }

        .container h2 { 
            margin-top: 10px; 
            color: #2c3e50; 
            font-size: 1.8rem; 
            margin-bottom: 10px;
        }

        .description { 
            font-size: 1rem; 
            color: #555; 
            margin-top: 10px;
        }

        .read-more { 
            color: #3498db; 
            text-decoration: underline; 
            cursor: pointer; 
            border: none; 
            background: none; 
            font-size: 1rem;
        }

        .book-info { 
            text-align: left; 
            font-size: 1rem; 
            color: #555; 
            margin-top: 20px;
        }

        .book-info p { 
            margin: 5px 0;
        }

        .actions { 
            margin-top: 20px;
        }

        .actions a, .actions button { 
            padding: 12px 18px; 
            font-size: 1.1rem; 
            border-radius: 5px; 
            cursor: pointer; 
            text-decoration: none; 
            margin-top: 10px; 
            display: inline-block; 
            transition: all 0.3s ease;
        }

        .edit-btn { 
            background: #3498db; 
            color: white; 
            margin-right: 10px;
        }

        .edit-btn:hover { 
            background: #2980b9;
        }

        .delete-btn { 
            background: #e74c3c; 
            color: white; 
            border: none; 
        }

        .delete-btn:hover { 
            background: #c0392b;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            background: #2c3e50;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 50px;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .back-button i {
            margin-right: 8px; 
        }

        .back-button:hover {
            background: #34495e;
            transform: scale(1.05);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.4);
        }

    </style>
</head>
<body>

<button class="back-button" onclick="window.history.back();">
    <i class="fas fa-arrow-left"></i> Kembali
</button>

<div class="container">
    <img src="../assets/img/<?php echo $buku['Gambar'] ?? 'default.jpg'; ?>" alt="Gambar Buku">
    <h2><?php echo $buku['Judul']; ?></h2>

    <!-- Deskripsi singkat & tombol toggle -->
    <p class="description" id="short-description">
        <?php echo nl2br(substr($buku['Deskripsi'], 0, 150)); ?>...
        <button class="read-more" onclick="toggleDescription()">Baca Selengkapnya</button>
    </p>
    
    <!-- Deskripsi lengkap -->
    <p class="description" id="full-description" style="display:none;">
        <?php echo nl2br($buku['Deskripsi']); ?>
        <button class="read-more" onclick="toggleDescription()">Baca Sedikit Lagi</button>
    </p>

    <div class="book-info">
        <p><strong>Penulis:</strong> <?php echo $buku['Penulis'] ?? 'Tidak diketahui'; ?></p>
        <p><strong>Penerbit:</strong> <?php echo $buku['Penerbit'] ?? 'Tidak diketahui'; ?></p>
        <p><strong>Tahun Terbit:</strong> <?php echo $buku['TahunTerbit'] ?? '-'; ?></p>
        <p><strong>Kategori:</strong> <?php echo implode(', ', $kategori_buku); ?></p>
        </div>

    <div class="actions">
        <a href="edit_buku.php?BukuID=<?php echo $buku['BukuID']; ?>" class="edit-btn">Edit</a>
        <a href="hapus_buku.php?BukuID=<?= $buku['BukuID']; ?>" type="submit" name="hapus_buku" class="delete-btn" onclick="return confirm('Hapus buku ini?')">Hapus</a>
    </div>
</div>

<script>
function toggleDescription() {
    var shortDesc = document.getElementById('short-description');
    var fullDesc = document.getElementById('full-description');
    var readMoreBtn = document.querySelector('.read-more');

    if (shortDesc.style.display === "none") {
        shortDesc.style.display = "block";
        fullDesc.style.display = "none";
        readMoreBtn.innerText = "Baca Selengkapnya";
    } else {
        shortDesc.style.display = "none";
        fullDesc.style.display = "block";
        readMoreBtn.innerText = "Baca Sedikit Lagi";
    }
}
</script>

</body>
</html>
