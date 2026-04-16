<?php
session_start();

// Cek apakah pengguna login dan memiliki role 'peminjam'
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'peminjam') {
    header("Location: .../login.php");
    exit();
}

// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Mengambil ID buku dari URL
if (!isset($_GET['BukuID'])) {
    echo "<script>alert('ID Buku tidak ditemukan!'); window.location.href='history.php';</script>";
    exit();
}

$buku_id = $_GET['BukuID'];

// Query untuk mengambil detail buku
$sql_buku = "SELECT * FROM buku WHERE BukuID = ?";
$stmt_buku = $conn->prepare($sql_buku);
$stmt_buku->bind_param("i", $buku_id);
$stmt_buku->execute();
$result_buku = $stmt_buku->get_result();

if ($result_buku->num_rows == 0) {
    echo "<script>alert('Buku tidak ditemukan!'); window.location.href='history.php';</script>";
    exit();
}

$buku = $result_buku->fetch_assoc();

// Query untuk mengambil ulasan buku
$sql_ulasan = "SELECT u.Ulasan, u.Rating, us.NamaLengkap 
               FROM ulasanbuku u 
               JOIN user us ON u.UserID = us.UserID 
               WHERE u.BukuID = ?";
$stmt_ulasan = $conn->prepare($sql_ulasan);
$stmt_ulasan->bind_param("i", $buku_id);
$stmt_ulasan->execute();
$result_ulasan = $stmt_ulasan->get_result();

// Query untuk mengambil kategori buku dari tabel relasi
$sql_kategori = "SELECT kb.NamaKategori 
                 FROM kategoribuku_relasi kbr 
                 JOIN kategoribuku kb ON kbr.KategoriID = kb.KategoriID 
                 WHERE kbr.BukuID = ?";
$stmt_kategori = $conn->prepare($sql_kategori);
$stmt_kategori->bind_param("i", $buku_id);
$stmt_kategori->execute();
$result_kategori = $stmt_kategori->get_result();

// Simpan kategori dalam array
$kategori_list = [];
while ($row = $result_kategori->fetch_assoc()) {
    $kategori_list[] = $row['NamaKategori'];
}

// Gabungkan kategori menjadi teks yang dipisahkan koma
$kategori_text = implode(", ", $kategori_list);


$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Buku - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- FontAwesome for arrow icons -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(120deg, #8e44ad, #3498db);
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 50px;
        }

        .container {
            max-width: 1100px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: row;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s;
        }

        .container:hover {
            transform: scale(1.02);
        }

        .book-image {
            width: 300px;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .book-info {
            flex: 1;
            padding-left: 30px;
        }

        .book-info h1 {
            font-size: 2rem;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .book-info p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 10px;
        }

        .book-actions {
            margin-top: 20px;  /* Space between category and button */
            text-align: left; /* Align the button in the center */
        }

        .book-actions a {
            padding: 12px 24px; /* Larger padding for button */
            background: linear-gradient(45deg, #2ecc71, #27ae60);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .book-actions a:hover {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            cursor: pointer;
        }

        .reviews-container {
            margin-top: 40px;
            width: 100%;
            max-width: 1100px;
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            color: #333;
        }

        .review {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .review .review-rating {
            color: #f39c12;
        }

        /* Back Button Style */
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
            margin-right: 8px; /* Spacing between icon and text */
        }

        .back-button:hover {
            background: #34495e;
            transform: scale(1.05); /* Slight zoom effect */
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.4);
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: center;
            }
            .book-image {
                margin-bottom: 20px;
            }
            .book-info {
                text-align: center;
            }
        }

        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7); /* Semi-transparent background */
            align-items: center;
            justify-content: center;
            z-index: 1000; /* Ensure it's on top */
        }

        .popup .close {
            position: absolute;
            top: 10px;
            right: 10px;
            background: red;
            color: white;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
        }

        .popup h2 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .popup-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 600px;
            width: 90%;
            text-align: center;
            position: relative;
            max-height: 80vh; /* Maximum height of the popup */
            overflow-y: auto; /* Enable vertical scrolling */
        }

        .popup p {
            font-size: 1rem;
            color: #333;
            max-height: 70vh; /* Adjust this value if you want to control text area height */
            overflow-y: auto;
        }

    </style>
</head>
<body>
    <!-- Back Button -->
    <button class="back-button" onclick="window.history.back();">
        <i class="fas fa-arrow-left"></i>
    </button>

    <div class="container">
        <img src="../assets/img/<?php echo $buku['Gambar']; ?>" alt="Gambar Buku" class="book-image">
        <div class="book-info">
            <h1><?php echo $buku['Judul']; ?></h1>
            <p><strong>Penulis:</strong> <?php echo $buku['Penulis']; ?></p>
            <p><strong>Penerbit:</strong> <?php echo $buku['Penerbit']; ?></p>
            <p><strong>Tahun Terbit:</strong> <?php echo $buku['TahunTerbit']; ?></p>
            <p><strong>Kategori:</strong> <?php echo $kategori_text; ?></p>
            <p><?php echo substr($buku['Deskripsi'], 0, 150); ?>...</p>
            <a href="javascript:void(0);" onclick="showPopup()">Baca Selengkapnya</a>
            
            <div class="book-actions">
                <a href="form_peminjaman.php?BukuID=<?php echo $buku['BukuID']; ?>">Pinjam Buku</a>
            </div>
        </div>
    </div>

    <!-- Popup Modal -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closePopup()">×</span>
            <h2>Deskripsi Buku</h2>
            <p id="full-description"><?php echo nl2br($buku['Deskripsi']); ?></p>
        </div>
    </div>

    <div class="reviews-container">
        <h2>Ulasan Buku</h2>
        <?php while ($ulasan = $result_ulasan->fetch_assoc()): ?>
            <div class="review">
                <div class="review-author"><?php echo $ulasan['NamaLengkap']; ?></div>
                <div class="review-rating"><?php echo str_repeat("⭐", $ulasan['Rating']); ?></div>
                <p><?php echo nl2br($ulasan['Ulasan']); ?></p>
            </div>
        <?php endwhile; ?>
    </div>

    <script>
        function showPopup() {
            document.getElementById('popup').style.display = 'flex';
        }

        function closePopup() {
            document.getElementById('popup').style.display = 'none';
        }
    </script>

</body>
</html>
