<?php
session_start();
include('../koneksi.php');

// Cek apakah pengguna sudah login dan memiliki role 'administrator'
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'administrator') {
    header("Location: ../login.php");
    exit();
}

// Pastikan ID pengguna ada di URL
if (isset($_GET['id'])) {
    $userID = $_GET['id'];

    // Ambil data peminjam berdasarkan UserID
    $sql = "SELECT * FROM user WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
    } else {
        echo "Peminjam tidak ditemukan.";
        exit();
    }
} else {
    echo "ID peminjam tidak ada.";
    exit();
}

// Proses ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $namaLengkap = $_POST['NamaLengkap'];
    $email = $_POST['Email'];
    $alamat = $_POST['Alamat'];
    $password = $_POST['Password'];  // Password Baru (tanpa konfirmasi)

    // Validasi password jika diubah
    if (!empty($password)) {
        // Hash password baru
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Update data peminjam dengan password yang baru
        $updateSql = "UPDATE user SET username = ?, NamaLengkap = ?, Email = ?, Alamat = ?, password = ? WHERE UserID = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("sssssi", $username, $namaLengkap, $email, $alamat, $hashedPassword, $userID);
    } else {
        // Jika tidak ada password baru, hanya update data selain password
        $updateSql = "UPDATE user SET username = ?, NamaLengkap = ?, Email = ?, Alamat = ? WHERE UserID = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("ssssi", $username, $namaLengkap, $email, $alamat, $userID);
    }
    
    if ($stmt->execute()) {
        header("Location: user.php");
        exit();
    } else {
        echo "Gagal mengupdate data.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Peminjam - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Lora:wght@400;700&display=swap" rel="stylesheet">
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
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 30px 20px;
            height: 100%;
            position: fixed;
        }

        .sidebar h2 {
            font-family: 'Lora', serif;
            margin-bottom: 30px;
            text-align: center;
        }

        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            margin: 15px 0;
            font-size: 1.2rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: color 0.3s ease;
        }

        .sidebar a:hover {
            color: #f39c12;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        h1 {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-container {
            background-color: rgba(0, 0, 0, 0.7);
            padding: 30px;
            border-radius: 10px;
            max-width: 600px;
            margin: auto;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            background-color: #34495e;
            color: white;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .form-container button:hover {
            background-color: #2980b9;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2c3e50;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 20px;
        }

        .back-btn:hover {
            background-color: #34495e;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Perpustakaan Digital</h2>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="buku.php">Buku</a>
        <a href="peminjaman.php">Peminjaman</a>
        <a href="user.php">Pengguna</a>
        <a href="petugas.php">Petugas</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <!-- Content -->
    <div class="content">
        <h1>Edit Akun Peminjam</h1>

        <div class="form-container">
            <form action="" method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($row['username']); ?>" required>

                <label for="NamaLengkap">Nama Lengkap</label>
                <input type="text" id="NamaLengkap" name="NamaLengkap" value="<?php echo htmlspecialchars($row['NamaLengkap']); ?>" required>

                <label for="Email">Email</label>
                <input type="email" id="Email" name="Email" value="<?php echo htmlspecialchars($row['Email']); ?>" required>

                <label for="Alamat">Alamat</label>
                <input type="text" id="Alamat" name="Alamat" value="<?php echo htmlspecialchars($row['Alamat']); ?>" required>

                <label for="Password">Password Baru</label>
                <input type="password" id="Password" name="Password" placeholder="Masukkan password baru (kosongkan jika tidak ingin mengubah password)">

                <button type="submit">Simpan Perubahan</button>
            </form>

            <a href="user.php" class="back-btn">Kembali ke Daftar Peminjam</a>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>
