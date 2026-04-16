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

// Mengambil statistik buku, peminjaman, dan pengguna
$sql_buku = "SELECT COUNT(*) AS total_buku FROM buku";
$result_buku = $conn->query($sql_buku);
$total_buku = $result_buku->fetch_assoc()['total_buku'];

$sql_peminjaman = "SELECT COUNT(*) AS total_peminjaman FROM peminjaman";
$result_peminjaman = $conn->query($sql_peminjaman);
$total_peminjaman = $result_peminjaman->fetch_assoc()['total_peminjaman'];

$sql_user = "SELECT COUNT(*) AS total_user FROM user";
$result_user = $conn->query($sql_user);
$total_user = $result_user->fetch_assoc()['total_user'];

// Untuk mengonfirmasi peminjaman
if (isset($_POST['konfirmasi_peminjaman'])) {
    $peminjaman_id = $_POST['peminjaman_id'];
    $sql_konfirmasi = "UPDATE peminjaman SET Status = 'Dikonfirmasi' WHERE PeminjamanID = ?";
    $stmt_konfirmasi = $conn->prepare($sql_konfirmasi);
    $stmt_konfirmasi->bind_param("i", $peminjaman_id);
    $stmt_konfirmasi->execute();
    header("Location: petugas_dashboard.php");
    exit();
}

// Menambahkan buku
if (isset($_POST['tambah_buku'])) {
    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $tahun_terbit = $_POST['tahun_terbit'];

    $sql_tambah_buku = "INSERT INTO buku (Judul, Penulis, TahunTerbit) VALUES (?, ?, ?)";
    $stmt_tambah_buku = $conn->prepare($sql_tambah_buku);
    $stmt_tambah_buku->bind_param("sss", $judul, $penulis, $tahun_terbit);
    $stmt_tambah_buku->execute();
    header("Location: petugas_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
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

        .content {
            flex-grow: 1;
            margin-left: 250px;
            padding: 20px;
            color: black;
            overflow-y: auto;
            text-align: left;
        }

        h1 {
            margin-bottom: 20px;
            color: white;
        }

        .stat-card {
            background-color: #fff;
            color: #2c3e50;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
        }

        .stat-card h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 1.2rem;
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

        .form-container {
            background-color: white;
            color: #333;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .form-container input, .form-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-container button {
            background-color: #3498db;
            color: white;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: #2980b9;
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
        <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="buku.php"><i class="fas fa-book"></i> Buku</a>
        <a href="peminjaman.php"><i class="fas fa-file-alt"></i> Peminjaman</a>
    </div>
    <div class="content">
        <h1>Dashboard Petugas</h1>

        <div class="stat-card">
            <h3>Total Buku</h3>
            <p><?php echo $total_buku; ?> Buku</p>
        </div>

        <div class="stat-card">
            <h3>Total Riwayat Peminjaman</h3>
            <p><?php echo $total_peminjaman; ?> Peminjaman</p>
        </div>

        <div class="stat-card">
            <h3>Total Pengguna</h3>
            <p><?php echo $total_user; ?> Pengguna</p>
        </div>
    </div>
    <?php $conn->close(); ?>
    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
    </Script>
</body>
</html>
