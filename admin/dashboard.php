<?php
session_start();
include('../koneksi.php');

// Cek apakah pengguna sudah login dan memiliki role 'administrator'
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'administrator') {
    header("Location: ../login.php");
    exit();
}

// Ambil data admin dari database
$userID = $_SESSION['UserID'];
$sql = "SELECT * FROM user WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Ambil statistik buku, peminjaman, pengguna, dan petugas
$sql_buku = "SELECT COUNT(*) AS total_buku FROM buku";
$result_buku = $conn->query($sql_buku);
$total_buku = $result_buku->fetch_assoc()['total_buku'];

$sql_peminjaman = "SELECT COUNT(*) AS total_peminjaman FROM peminjaman WHERE Status = 'Dipinjam'";
$result_peminjaman = $conn->query($sql_peminjaman);
$total_peminjaman = $result_peminjaman->fetch_assoc()['total_peminjaman'];

$sql_user = "SELECT COUNT(*) AS total_user FROM user WHERE role = 'peminjam'";
$result_user = $conn->query($sql_user);
$total_user = $result_user->fetch_assoc()['total_user'];

$sql_petugas = "SELECT COUNT(*) AS total_petugas FROM user WHERE role = 'petugas'";
$result_petugas = $conn->query($sql_petugas);
$total_petugas = $result_petugas->fetch_assoc()['total_petugas'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Untuk ikon -->
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

        /* Sidebar Style */
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
            margin-left: 270px;
            padding: 30px;
            width: calc(100% - 270px);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 2rem;
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
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Perpustakaan Digital</h2>
        <div class="profile-header" onclick="toggleDropdown()">
            <div class="profile-info">
                <img src="../assets/img/admin.png" alt="Profil Pengguna">
                <div class="profile-text">
                    <h3><?php echo $_SESSION['username']; ?></h3>
                    <p>Administrator</p>
                </div>
            </div>
        </div>
        <!-- Dropdown Menu -->
        <div class="dropdown-menu" id="profileDropdown">
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a> <!-- Ikon logout -->
        </div>
        <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="buku.php"><i class="fas fa-book"></i> Buku</a>
        <a href="peminjaman.php"><i class="fas fa-file-alt"></i> Laporan</a>
        <a href="user.php"><i class="fas fa-users"></i> Pengguna</a>
        <a href="petugas.php"><i class="fas fa-user-tie"></i> Petugas</a>
        <a href="kategori.php"><i class="fas fa-tags"></i> Kategori</a>
    </div>

    <div class="content">
        <h1>Dashboard Admin</h1>

        <div class="stat-card">
            <h3>Total Buku</h3>
            <p><?php echo $total_buku; ?> Buku</p>
        </div>

        <div class="stat-card">
            <h3>Total Peminjaman Sedang</h3>
            <p><?php echo $total_peminjaman; ?> Peminjaman</p>
        </div>

        <div class="stat-card">
            <h3>Total Pengguna</h3>
            <p><?php echo $total_user; ?> Pengguna</p>
        </div>

        <div class="stat-card">
            <h3>Total Petugas</h3>
            <p><?php echo $total_petugas; ?> Petugas</p>
        </div>
    </div>
    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
