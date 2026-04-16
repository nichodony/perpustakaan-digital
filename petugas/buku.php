<?php
session_start();

// Cek apakah petugas sudah login dan memiliki role 'petugas'
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../login.php");
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

$userID = $_SESSION['UserID'];
$sql = "SELECT * FROM user WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Mengambil data buku dari database
$sql_buku = "SELECT * FROM buku";
$result_buku = $conn->query($sql_buku);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            display: flex;
            min-height: 100vh;
            background: linear-gradient(45deg, #3498db, #8e44ad);
            color: #fff;
        }

        .sidebar {
            width: 250px;
            background: linear-gradient(135deg, #8e44ad, #3498db);
            padding: 20px;
            position: fixed;
            height: 100%;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .sidebar h2 {
            text-align: center;
            font-family: 'Lora', serif;
            color: white;
            margin-bottom: 20px;
            font-size: 1.5rem;
            letter-spacing: 2px;
        }

        .sidebar a {
            display: flex;
            align-items: center;
            color: white;
            text-decoration: none;
            margin: 15px 0;
            font-size: 1.1rem;
            padding: 12px;
            border-radius: 5px;
            transition: all 0.3s;
            cursor: pointer;
        }

        .sidebar a i {
            margin-right: 15px;
        }

        .sidebar a:hover {
            background: #fff;
            color: #8e44ad;
            transform: translateX(10px);
        }

        .sidebar a.active {
            background: #fff;
            color: #8e44ad;
        }

        .content {
            margin-left: 270px;
            padding: 30px;
            width: calc(100% - 270px);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2rem;
        }

        .book-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr); /* 4 kolom per baris */
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .book-card {
            background: #fff;
            color: #2c3e50;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            position: relative;
            height: 450px; /* Panjang buku seperti halaman vertikal */
            width: 100%;
        }

        .book-card img {
            width: 100%;
            height: 100%; /* Membuat gambar mengambil seluruh tinggi card */
            object-fit: cover;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.);
        }

        .book-card:hover img {
            transform: scale(1.1); /* Efek pembesaran gambar saat hover */
        }

        /* Tombol tambah buku */
        .add-book-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            background: linear-gradient(45deg, #8e44ad, #9b59b6);
            color: white;
            font-size: 2rem;
            width: 65px;
            height: 65px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s, background 0.3s;
            cursor: pointer;
            z-index: 100;
        }

        .add-book-btn i {
            font-size: 1.5rem;
        }

        .add-book-btn:hover {
            transform: scale(1.1);
            background: #9b59b6;
        }

        footer {
            text-align: center;
            padding: 10px;
            background: rgba(0, 0, 0, 0.8);
            position: fixed;
            bottom: 0;
            width: 100%;
            color: white;
        }

        /* Profile Header */
        .profile-header {
            position: sticky;
            top: 10px;
            right: 20px;
            display: flex;
            align-items: center;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 15px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
        }

        .profile-info {
            display: flex;
            align-items: center;
        }

        .profile-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .profile-text {
            text-align: left;
        }

        .profile-text h3 {
            font-size: 1rem;
            color: white;
            margin: 0;
        }

        .profile-text p {
            font-size: 0.8rem;
            color: #ccc;
            margin: 0;
        }

        /* Dropdown Menu */
        .dropdown-menu {
            display: none;
            position: absolute;
            top: 60px;
            right: 10px;
            background-color: #fff;
            color: #8e44ad;
            border-radius: 8px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .dropdown-menu a {
            padding: 10px;
            text-decoration: none;
            display: block;
            color: #8e44ad;
            transition: background-color 0.3s ease;
        }

        .dropdown-menu a:hover {
            background-color: #f2f2f2;
        }

    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Perpustakaan Digital</h2>
        <div class="profile-header" onclick="toggleDropdown()">
            <div class="profile-info">
                <!-- Tampilkan foto profil jika ada, jika tidak tampilkan gambar default -->
                <img src="../assets/img/<?php echo !empty($user['Foto']) ? htmlspecialchars($user['Foto']) : 'default.jpg'; ?>" alt="Profil Pengguna">
                <div class="profile-text">
                    <!-- Tampilkan username yang sesuai dengan kolom di database -->
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3> <!-- Gantilah 'username' jika kolom Anda berbeda -->
                    <p>Petugas</p>
                </div>
            </div>
        </div>

        <!-- Dropdown Menu -->
        <div class="dropdown-menu" id="profileDropdown">
            <a href="profil.php"><i class="fas fa-user-edit"></i> Ubah Profil</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a> <!-- Ikon logout -->
        </div>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="buku.php" class="active"><i class="fas fa-book"></i> Buku</a>
        <a href="peminjaman.php"><i class="fas fa-file-alt"></i> Peminjaman</a>
    </div>

    <div class="content">
        <h1>Daftar Buku</h1>
        <div class="book-container">
            <?php if ($result_buku->num_rows > 0): ?>
                <?php while ($book = $result_buku->fetch_assoc()): ?>
                    <div class="book-card">
                        <a href="detail_buku.php?BukuID=<?php echo $book['BukuID']; ?>">
                            <img src="../assets/img/<?php echo !empty($book['Gambar']) ? $book['Gambar'] : 'default.jpg'; ?>" alt="Gambar Buku">
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center;">Tidak ada buku yang tersedia.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tombol Tambah Buku -->
    <a href="tambah_buku.php" class="add-book-btn">
        <i class="fas fa-plus"></i>
    </a>

    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>
