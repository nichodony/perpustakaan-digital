<?php
session_start();
include('../koneksi.php');

// Cek apakah pengguna sudah login dan memiliki role 'administrator'
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'administrator') {
    header("Location: ../login.php");
    exit();
}

// Ambil data petugas dari database
$sql = "SELECT * FROM user WHERE role = 'petugas'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Petugas - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* CSS Anda di sini */
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
            color: white;
            flex-direction: column;
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

        .btn {
            padding: 8px 15px;
            color: white;
            background-color: #e74c3c;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .btn:hover {
            background-color: #c0392b;
        }

        .btn-edit {
            background-color: #3498db;
        }

        .btn-edit:hover {
            background-color: #2980b9;
        }

        .actions {
            display: flex;
            justify-content: space-evenly;
        }

        .add-btn {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            display: block;
            width: 200px;
            text-align: center;
        }

        .add-btn:hover {
            background-color: #2980b9;
        }
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
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="buku.php"><i class="fas fa-book"></i> Buku</a>
        <a href="peminjaman.php"><i class="fas fa-file-alt"></i> Laporan</a>
        <a href="user.php"><i class="fas fa-users"></i> Pengguna</a>
        <a href="petugas.php" class="active"><i class="fas fa-user-tie"></i> Petugas</a>
        <a href="kategori.php"><i class="fas fa-tags"></i> Kategori</a>
    </div>


    <!-- Content -->
    <div class="content">
    <h1>Daftar Petugas</h1>
    <a href="tambah_petugas.php" class="add-btn">+ Tambah Petugas</a> <!-- Tambah tombol di sini -->
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Menampilkan data petugas dari database
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['username']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['NamaLengkap']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Email']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['Alamat']) . "</td>";
                        echo "<td class='actions'>
                                <a href='edit_petugas.php?id=" . $row['UserID'] . "' class='btn btn-edit'>Edit</a>
                                <a href='hapus_petugas.php?id=" . $row['UserID'] . "' class='btn'>Hapus</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Tidak ada petugas yang ditemukan</td></tr>";
                }
                ?>
            </tbody>
        </table>
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

<?php
$conn->close();
?>
