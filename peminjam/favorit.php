<?php
session_start();

// Cek apakah peminjam sudah login dan memiliki role 'peminjam'
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'peminjam') {
    header("Location: ../login.php");
    exit();
}

// Koneksi database
$host = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";

$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data pengguna berdasarkan UserID
$userID = $_SESSION['UserID'];
$sql_user = "SELECT * FROM user WHERE UserID = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $userID);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// Fetch favorite books from the database
$user_id = $_SESSION['UserID'];
$sql_favorit = "SELECT k.*, b.Judul, b.Penulis, b.Deskripsi, b.Gambar 
                FROM koleksipribadi k 
                JOIN buku b ON k.BukuID = b.BukuID 
                WHERE k.UserID = ?";
$stmt_favorit = $conn->prepare($sql_favorit);
$stmt_favorit->bind_param("i", $user_id);
$stmt_favorit->execute();
$result_favorit = $stmt_favorit->get_result();

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku Favorit - Perpustakaan Digital</title>
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
            text-align: center;
        }

        h1 {
            margin-bottom: 30px;
            font-size: 2rem;
            font-weight: 600;
            color: #fff;
        }

        .book-cards-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 columns */
            gap: 30px;
            padding: 30px;
            margin-top: 30px;
        }

        .book-card {
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
        }

        .book-card img {
            width: 100%;
            height: 280px;
            object-fit: cover;
            border-radius: 12px;
            transition: transform 0.3s ease;
        }

        .book-card img:hover {
            transform: scale(1.05);
        }

        .book-card h3 {
            font-size: 1.6rem;
            margin-top: 15px;
            color: #333;
            font-weight: 600;
        }

        .book-card p {
            font-size: 1rem;
            margin: 10px 0;
            color: #555;
        }

        .book-card .book-details {
            font-size: 0.9rem;
            color: #777;
            margin-top: 10px;
        }

        .book-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .book-actions a {
            padding: 8px 22px; /* Ukuran lebih kecil */
            background: linear-gradient(45deg, #3498db, #8e44ad);
            color: white;
            text-decoration: none;
            border-radius: 20px; /* Lebih membulat */
            font-size: 0.9rem; /* Ukuran teks lebih kecil */
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
            box-shadow: 2px 3px 8px rgba(0, 0, 0, 0.2);
        }

        .book-actions a:hover {
            background: linear-gradient(45deg, #8e44ad, #3498db);
            box-shadow: 3px 5px 12px rgba(0, 0, 0, 0.25);
            transform: translateY(-3px);
        }

        .header {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1.1rem;
            color: #fff;
            font-weight: 600;
        }

        @media (max-width: 900px) {
            .book-cards-container {
                grid-template-columns: repeat(2, 1fr); /* 2 columns for smaller screens */
            }
        }

        @media (max-width: 600px) {
            .book-cards-container {
                grid-template-columns: 1fr; /* 1 column for very small screens */
            }
        }
    </style>
</head>
<body>
<div class="sidebar">
        <h2>Perpustakaan Digital</h2>
        <div class="profile-header" onclick="toggleDropdown()">
            <div class="profile-info">
                <img src="../assets/img/<?php echo !empty($user['Foto']) ? htmlspecialchars($user['Foto']) : 'default.jpg'; ?>" alt="Profil Pengguna">
                <div class="profile-text">
                    <h3><?php echo htmlspecialchars($user['username']); ?></h3>
                    <p>Peminjam</p>
                </div>
            </div>
        </div>

        <!-- Dropdown Menu -->
        <div class="dropdown-menu" id="profileDropdown">
            <a href="profil.php"><i class="fas fa-user-edit"></i> Ubah Profil</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a> <!-- Ikon logout -->
        </div>


        <a href="dashboard.php"><i class="fas fa-book"></i> Dashboard</a>
        <a href="history.php"><i class="fas fa-history"></i> Riwayat</a>
        <a href="favorit.php" class="active"><i class="fas fa-bookmark"></i> Koleksi Pribadi</a>
    </div>

    <div class="content">
        <h1>Daftar Buku Favorit Anda</h1>

        <div class="book-cards-container">
            <?php while ($buku_favorit = $result_favorit->fetch_assoc()): ?>
            <div class="book-card">
                <img src="../assets/img/<?php echo $buku_favorit['Gambar']; ?>" alt="Gambar Buku">
                <h3><?php echo $buku_favorit['Judul']; ?></h3>
                <div class="book-details">
                    <p><strong>Penulis:</strong> <?php echo $buku_favorit['Penulis']; ?></p>
                </div>

                <div class="book-actions">
                    <a href="buku_detail.php?BukuID=<?php echo $buku_favorit['BukuID']; ?>">Lihat Detail</a>
                    <a href="hapus_favorit.php?BukuID=<?php echo $buku_favorit['BukuID']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus buku ini dari favorit?');">Hapus Favorit</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
    <script>
        // JavaScript untuk toggle dropdown
        function toggleDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>
