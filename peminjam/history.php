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


// Mendapatkan daftar buku yang dipinjam oleh pengguna
$user_id = $_SESSION['UserID'];
$sql_buku_pinjam = "SELECT p.*, b.Judul, b.Penulis, b.Deskripsi, p.Status 
                    FROM peminjaman p 
                    JOIN buku b ON p.BukuID = b.BukuID 
                    WHERE p.UserID = ?";
$stmt_buku_pinjam = $conn->prepare($sql_buku_pinjam);
$stmt_buku_pinjam->bind_param("i", $user_id);
$stmt_buku_pinjam->execute();
$result_buku_pinjam = $stmt_buku_pinjam->get_result();

// Menutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Buku yang Dipinjam - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        /* Styling yang telah ada */
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
            text-align: center;
            margin-bottom: 20px;
            font-size: 2rem;
            color: white;
        }

        .book-cards-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            margin-top: 30px;
        }

        .book-card {
            background-color: white;
            padding: 50px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .book-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .book-card h3 {
            font-size: 1.5rem;
            margin-bottom: 10px;
            color: #333;
        }

        .book-card p {
            font-size: 1rem;
            margin-bottom: 10px;
            color: #555;
        }

        .book-card .book-details {
            font-size: 0.9rem;
            color: #777;
        }

        .book-card .book-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .book-actions a {
            padding: 8px 12px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .book-actions a:hover {
            background-color: #2980b9;
        }

        /* Styling untuk tombol kembalikan buku */
        .book-actions .return-btn {
            background-color: #e74c3c; /* Warna merah untuk tombol kembalikan */
            color: white;
        }

        .book-actions .return-btn:hover {
            background-color: #c0392b; /* Warna merah lebih gelap saat hover */
        }

        /* Styling Modal */
        .modal {
            display: none; /* Modal tersembunyi secara default */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
        }

        .modal-content textarea {
            width: 100%;
            height: 100px;
            margin-bottom: 10px;
            padding: 10px;
            font-size: 1rem;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .modal-content input[type="number"] {
            width: 50%;
            padding: 10px;
            font-size: 1rem;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .modal-content button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .modal-content button:hover {
            background-color: #2980b9;
        }

        /* Button Close */
        .close {
            position: absolute;
            top: 10px;
            right: 10px;
            color: #aaa;
            font-size: 24px;
            cursor: pointer;
        }

        .header {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 1rem;
            color: #fff;
            font-weight: 600;
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
                    <p>Peminjam</p>
                </div>
            </div>
        </div>

        <!-- Dropdown Menu -->
        <div class="dropdown-menu" id="profileDropdown">
            <a href="profil.php"><i class="fas fa-user-edit"></i> Ubah Profil</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a> <!-- Ikon logout -->
        </div>

        <!-- Menu Sidebar -->
        <a href="dashboard.php"><i class="fas fa-book"></i> Dashboard</a>
        <a href="history.php" class="active"><i class="fas fa-history"></i> Riwayat</a>
        <a href="favorit.php"><i class="fas fa-bookmark"></i> Koleksi Pribadi</a>
    </div>

    <div class="content">
        <h1>Daftar Buku yang Dipinjam</h1>

        <div class="book-cards-container">
            <?php while ($buku_pinjam = $result_buku_pinjam->fetch_assoc()): ?>
            <div class="book-card">
                <h3><?php echo $buku_pinjam['Judul']; ?></h3>
                <div class="book-details">
                    <p><strong>Penulis:</strong> <?php echo $buku_pinjam['Penulis']; ?></p>
                    <p><strong>Status:</strong> <?php echo ucfirst($buku_pinjam['Status']); ?></p>
                    <p><strong>Tanggal Peminjaman:</strong> <?php echo $buku_pinjam['TanggalPeminjaman']; ?></p>
                    <p><strong>Tanggal Pengembalian:</strong> <?php echo $buku_pinjam['TanggalPengembalian']; ?></p>
                </div>
                <?php if ($buku_pinjam['Status'] == 'Dipinjam'): ?>
                    <a href="#" class="book-actions return-btn" data-peminjaman-id="<?php echo $buku_pinjam['PeminjamanID']; ?>" data-buku-id="<?php echo $buku_pinjam['BukuID']; ?>">Kembalikan Buku</a>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        </div>
    </div>

    <!-- Modal Pengembalian Buku dan Ulasan -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Berikan Ulasan Anda</h2>
            <form id="ulasanForm" method="POST" action="proses_pengembalian.php">
                <input type="hidden" name="peminjaman_id" id="peminjaman_id">
                <input type="hidden" name="buku_id" id="buku_id">
                <label for="rating">Rating (1-5):</label>
                <input type="number" name="rating" id="rating" min="1" max="5" required>
                <br><br>
                <label for="ulasan">Ulasan:</label>
                <textarea name="ulasan" id="ulasan" required></textarea>
                <br><br>
                <button type="submit">Kirim Ulasan</button>
            </form>
        </div>
    </div>

    <script>
        // JavaScript untuk toggle dropdown
        function toggleDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }

        // Ambil tombol dan modal
        const modal = document.getElementById("myModal");
        const closeButton = document.getElementsByClassName("close")[0];

        // Ambil semua tombol Kembalikan Buku
        const returnButtons = document.querySelectorAll(".return-btn");

        // Ketika tombol "Kembalikan Buku" diklik
        returnButtons.forEach(button => {
            button.addEventListener("click", function(e) {
                e.preventDefault();
                const peminjamanId = this.getAttribute("data-peminjaman-id");
                const bukuId = this.getAttribute("data-buku-id");

                // Isi modal dengan ID peminjaman dan BukuID
                document.getElementById("peminjaman_id").value = peminjamanId;
                document.getElementById("buku_id").value = bukuId;

                // Tampilkan modal
                modal.style.display = "flex";
            });
        });

        // Menutup modal
        closeButton.onclick = function() {
            modal.style.display = "none";
        }

        // Menutup modal jika klik di luar modal
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>

</body>
</html>
