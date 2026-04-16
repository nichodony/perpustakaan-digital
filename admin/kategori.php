<?php
session_start();
include('../koneksi.php');

// Cek apakah pengguna sudah login dan memiliki role 'administrator'
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'administrator') {
    header("Location: ../login.php");
    exit();
}

// Ambil daftar kategori buku
$sql_kategori = "SELECT * FROM kategoribuku";
$result_kategori = $conn->query($sql_kategori);

// Proses tambah kategori
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah'])) {
    $nama_kategori = isset($_POST['nama_kategori']) ? trim($_POST['nama_kategori']) : '';
    if (!empty($nama_kategori)) {
        $stmt = $conn->prepare("INSERT INTO kategoribuku (NamaKategori) VALUES (?)");
        $stmt->bind_param("s", $nama_kategori);
        $stmt->execute();
        $stmt->close();
        header("Location: kategori.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Buku - Perpustakaan Digital</title>
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

        .table-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            color: black;
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
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            font-size: 0.9rem;
            transition: background-color 0.3s ease;
        }

        .btn-edit {
            background-color: #f39c12;
        }

        .btn-edit:hover {
            background-color: #e67e22;
        }

        .btn-delete {
            background-color: #e74c3c;
        }

        .btn-delete:hover {
            background-color: #c0392b;
        }

        .btn-add {
            background-color: #2ecc71;
            margin-bottom: 10px;
        }

        .btn-add:hover {
            background-color: #27ae60;
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
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
        }

        .modal-content input {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .modal-content button {
            padding: 8px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn-save {
            background-color: #2ecc71;
            color: white;
        }

        .btn-cancel {
            background-color: #e74c3c;
            color: white;
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
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="buku.php"><i class="fas fa-book"></i> Buku</a>
        <a href="peminjaman.php"><i class="fas fa-file-alt"></i> Laporan</a>
        <a href="user.php"><i class="fas fa-users"></i> Pengguna</a>
        <a href="petugas.php"><i class="fas fa-user-tie"></i> Petugas</a>
        <a href="kategori.php" class="active"><i class="fas fa-tags"></i> Kategori</a>
    </div>

    <div class="content">
        <h1>Daftar Kategori Buku</h1>
        <div class="table-container">
            <form method="POST">
                <input type="text" name="nama_kategori" placeholder="Tambah Kategori Baru" required>
                <button type="submit" name="tambah" class="btn btn-add">Tambah</button>
            </form>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($kategori = $result_kategori->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($kategori['KategoriID']); ?></td>
                            <td><?php echo htmlspecialchars($kategori['NamaKategori']); ?></td>
                            <td>
                                <button class="btn btn-edit" onclick="openEditModal(<?php echo $kategori['KategoriID']; ?>, '<?php echo htmlspecialchars($kategori['NamaKategori']); ?>')">Edit</button>
                                <a href="hapus_kategori.php?id=<?php echo $kategori['KategoriID']; ?>" class="btn btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal untuk Edit -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <h3>Edit Kategori</h3>
            <input type="hidden" id="edit_id_kategori">
            <input type="text" id="edit_nama_kategori" required>
            <button class="btn-save" onclick="simpanEditKategori()">Simpan</button>
            <button class="btn-cancel" onclick="closeEditModal()">Batal</button>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function openEditModal(id, nama) {
        document.getElementById('edit_id_kategori').value = id;
        document.getElementById('edit_nama_kategori').value = nama;
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function simpanEditKategori() {
        let id = document.getElementById('edit_id_kategori').value;
        let nama_kategori = document.getElementById('edit_nama_kategori').value;

        if (nama_kategori.trim() === '') {
            alert('Nama kategori tidak boleh kosong!');
            return;
        }

        $.post("edit_kategori_ajax.php", { id: id, nama_kategori: nama_kategori }, function(response) {
            if (response === "success") {
                alert("Kategori berhasil diperbarui!");
                location.reload();
            } else {
                alert("Gagal mengupdate kategori.");
            }
        });

        closeEditModal();
    }
        function toggleDropdown() {
            var dropdown = document.getElementById('profileDropdown');
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>
