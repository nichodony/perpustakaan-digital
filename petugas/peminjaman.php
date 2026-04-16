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

if (isset($_SESSION['success_message'])) {
    echo "<p style='color: green; text-align: center;'>" . $_SESSION['success_message'] . "</p>";
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    echo "<p style='color: red; text-align: center;'>" . $_SESSION['error_message'] . "</p>";
    unset($_SESSION['error_message']);
}

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

// Query untuk mengambil data peminjaman
$sql = "SELECT p.PeminjamanID, u.NamaLengkap, b.Judul, p.TanggalPeminjaman, p.TanggalPengembalian, p.Status
        FROM peminjaman p
        JOIN user u ON p.UserID = u.UserID
        LEFT JOIN buku b ON p.BukuID = b.BukuID";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Peminjaman Buku - Perpustakaan Digital</title>
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            color: #333;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table th {
            background: linear-gradient(135deg, #8e44ad, #3498db);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: bold;
        }

        table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        table tr:hover {
            background-color: #ecf0f1;
            transition: 0.3s ease-in-out;
        }

        table th:first-child, table td:first-child {
            border-radius: 10px 0 0 10px;
        }

        table th:last-child, table td:last-child {
            border-radius: 0 10px 10px 0;
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
        <a href="buku.php"><i class="fas fa-book"></i> Buku</a>
        <a href="peminjaman.php" class="active"><i class="fas fa-file-alt"></i> Peminjaman</a>
    </div>

    <div class="content">
        <h1>Daftar Peminjaman Buku</h1>

        <table>
            <thead>
                <tr>
                    <th>ID Peminjaman</th>
                    <th>Nama Peminjam</th>
                    <th>Judul Buku</th>
                    <th>Tanggal Peminjaman</th>
                    <th>Tanggal Pengembalian</th>
                    <th>Status</th>
                    <th>Status Confirm</th>

                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["PeminjamanID"] . "</td>";
                        echo "<td>" . $row["NamaLengkap"] . "</td>";
                        echo "<td>" . ($row["Judul"] ? $row["Judul"] : "Buku Tidak Ditemukan") . "</td>";
                        echo "<td>" . $row["TanggalPeminjaman"] . "</td>";
                        echo "<td>" . $row["TanggalPengembalian"] . "</td>";
                        echo "<td>" . $row["Status"] . "</td>";
                        echo "<td>";
                        echo "<a href='cetak_peminjaman.php?id=" . $row["PeminjamanID"] . "' target='_blank' style='background-color: blue; color: white; padding: 5px 10px; text-decoration: none; border-radius: 5px;'>Cetak</a>";
                        echo "</td>";

                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>Tidak ada peminjaman saat ini.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>

<?php
// Menutup koneksi
$conn->close();
?>
