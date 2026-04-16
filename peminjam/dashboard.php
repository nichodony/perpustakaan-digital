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

$search = isset($_GET['search']) ? $_GET['search'] : '';
$selected_kategori = isset($_GET['kategori']) ? $_GET['kategori'] : [];

// Query daftar buku dengan filter pencarian
$sql_buku_list = "SELECT DISTINCT buku.* FROM buku 
    LEFT JOIN kategoribuku_relasi ON buku.BukuID = kategoribuku_relasi.BukuID
    LEFT JOIN kategoribuku ON kategoribuku_relasi.KategoriID = kategoribuku.KategoriID
    WHERE buku.Judul LIKE ?";

$params = ["%" . $search . "%"];
$types = "s";

if (!empty($selected_kategori)) {
    $placeholders = implode(",", array_fill(0, count($selected_kategori), "?"));
    $sql_buku_list .= " AND kategoribuku.KategoriID IN ($placeholders)";
    
    foreach ($selected_kategori as $kat) {
        $params[] = $kat;
        $types .= "i";
    }
}

$stmt_buku = $conn->prepare($sql_buku_list);
$stmt_buku->bind_param($types, ...$params);
$stmt_buku->execute();
$result_buku_list = $stmt_buku->get_result();

$userID = $_SESSION['UserID'];
$sql_user = "SELECT * FROM user WHERE UserID = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $userID);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

$sql_kategori = "SELECT * FROM kategoribuku";
$result_kategori = $conn->query($sql_kategori);

// Tutup koneksi sementara untuk efisiensi
$stmt_user->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital - Peminjaman Buku</title>
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
            color: white;
        }

        h3 {
            margin-bottom: 30px;
            font-size: 100%;
            font-weight: 600;
        }

        .book-cards-container {
            display: grid;
            grid-template-columns: repeat(5, 1fr); /* Menampilkan 5 buku dalam satu baris */
            gap: 20px;
            padding: 20px;
            justify-content: center;
        }

        .book-card {
            background-color: white;
            padding: 15px;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s ease, background-color 0.3s ease;
        }

        .book-card:hover {
            transform: translateY(-15px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            background-color: #f5f5f5;
        }

        .book-card img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
            transition: transform 0.3s ease, filter 0.3s ease;
        }

        .book-card:hover img {
            transform: scale(1.1);
            filter: brightness(90%);
        }

        .fav-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: none;
            border: none;
            font-size: 30px;
            cursor: pointer;
            color: #ccc;
            transition: transform 0.3s, color 0.3s ease-in-out;
        }

        .fav-btn.active {
            color: #e74c3c;
        }

        .fav-btn:hover {
            transform: scale(1.2);
        }

        .book-actions {
            margin-top: 15px;
        }

        .book-actions a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #8e44ad;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        .book-actions a:hover {
            background-color: #9b59b6;
        }
        .search-bar {
            text-align: center;
            margin-bottom: 20px;
        }
        .search-bar input {
            width: 50%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }
        .search-bar button {
            padding: 10px 15px;
            border: none;
            background-color: #8e44ad;
            color: white;
            cursor: pointer;
            border-radius: 5px;
            font-size: 1rem;
        }
        .search-bar button:hover {
            background-color: #9b59b6;
        }
        .category-filter {
        text-align: center;
        margin-top: 20px;
    }

    .category-filter h3 {
        margin-bottom: 10px;
        font-size: 1.5rem;
        font-weight: 600;
        color: white;
    }

    .category-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }

    .category-button {
        padding: 10px 15px;
        border: 2px solid #8e44ad;
        border-radius: 20px;
        background: white;
        color: #8e44ad;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease-in-out;
        display: inline-block;
    }

    .category-button:hover {
        background: #8e44ad;
        color: white;
    }

    .category-button.active {
        background: #8e44ad;
        color: white;
        border-color: #fff;
    }

    .category-button input {
        display: none;
    }

    .filter-btn {
        margin-top: 15px;
        padding: 12px 20px;
        border: none;
        background: #9b59b6;
        color: white;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 25px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .filter-btn:hover {
        background: #8e44ad;
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
        <div class="dropdown-menu" id="profileDropdown">
            <a href="profil.php"><i class="fas fa-user-edit"></i> Ubah Profil</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <a href="dashboard.php" class="active"><i class="fas fa-book"></i> Dashboard</a>
        <a href="history.php"><i class="fas fa-history"></i> Riwayat</a>
        <a href="favorit.php"><i class="fas fa-bookmark"></i> Koleksi Pribadi</a>
    </div>
    <div class="content">
        <h1>Daftar Buku</h1>
        <<!-- Form Pencarian -->
        <div class="search-bar">
            <form method="GET" action="">
                <input type="text" name="search" placeholder="Cari judul buku..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit"><i class="fas fa-search"></i> Cari</button>
            </form>
        </div>

        <div class="category-filter">
            <form method="GET" action="">
                <div class="category-buttons">
                    <?php while ($kategori = $result_kategori->fetch_assoc()): ?>
                        <label class="category-button">
                            <input type="checkbox" name="kategori[]" value="<?php echo $kategori['KategoriID']; ?>"
                                <?php echo in_array($kategori['KategoriID'], $selected_kategori) ? 'checked' : ''; ?>>
                            <?php echo htmlspecialchars($kategori['NamaKategori']); ?>
                        </label>
                    <?php endwhile; ?>
                </div>
            </form>
        </div>

        <div class="book-cards-container" id="bookList">
            <?php while ($buku = $result_buku_list->fetch_assoc()): ?>
                <div class="book-card">
                    <img src="../assets/img/<?php echo htmlspecialchars($buku['Gambar']); ?>" alt="Gambar Buku">
                    <button class="fav-btn" onclick="tambahFavorit(<?php echo $buku['BukuID']; ?>, this)">
                        <i class="fas fa-heart"></i>
                    </button>
                    <h3><?php echo htmlspecialchars($buku['Judul']); ?></h3>
                    <div class="book-actions">
                        <a href="buku_detail.php?BukuID=<?php echo $buku['BukuID']; ?>">Lihat Detail</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
        function tambahFavorit(bukuID, btn) {
            const userID = <?php echo $_SESSION['UserID']; ?>;
            fetch('tambah_favorit.php?BukuID=' + bukuID + '&UserID=' + userID)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        btn.classList.add('active');
                        alert('Buku berhasil ditambahkan ke koleksi pribadi!');
                    } else if (data.status === 'exists') {
                        alert('Buku ini sudah ada di koleksi pribadi!');
                    } else {
                        alert(data.message);
                    }
                });
        }
        document.addEventListener("DOMContentLoaded", function () {
        const kategoriButtons = document.querySelectorAll('.category-button input');
        const bookContainer = document.querySelector('.book-cards-container');

        // Tambahkan event listener ke setiap tombol kategori
        kategoriButtons.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                fetchFilteredBooks();
            });

            // Set warna kategori saat halaman pertama dimuat
            if (checkbox.checked) {
                checkbox.parentElement.classList.add("active");
            }

            checkbox.parentElement.addEventListener("click", function () {
                checkbox.checked = !checkbox.checked;
                this.classList.toggle('active', checkbox.checked);
                fetchFilteredBooks();
            });
        });

        function fetchFilteredBooks() {
            let selectedCategories = [];
            kategoriButtons.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedCategories.push(checkbox.value);
                }
            });

            let queryString = selectedCategories.length > 0 ? '?kategori=' + selectedCategories.join(',') : '';

            fetch('filter_buku.php' + queryString)
                .then(response => response.text())
                .then(data => {
                    bookContainer.innerHTML = data;
                })
                .catch(error => console.error('Error:', error));
        }
    });

    </script>
</body>
</html>
