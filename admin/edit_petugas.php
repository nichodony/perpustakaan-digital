<?php
session_start();
include('../koneksi.php');

// Cek apakah pengguna sudah login dan memiliki role 'administrator'
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'administrator') {
    header("Location: ../login.php");
    exit();
}

// Pastikan ada parameter ID di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('ID petugas tidak valid!'); window.location.href = 'petugas.php';</script>";
    exit();
}

$id = intval($_GET['id']);

// Ambil data petugas berdasarkan ID
$sql = "SELECT * FROM user WHERE UserID = ? AND role = 'petugas'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('Petugas tidak ditemukan!'); window.location.href = 'petugas.php';</script>";
    exit();
}

$row = $result->fetch_assoc();

// Proses update data saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $namaLengkap = $_POST['NamaLengkap'];
    $email = $_POST['Email'];
    $alamat = $_POST['Alamat'];

    $updateSql = "UPDATE user SET username = ?, NamaLengkap = ?, Email = ?, Alamat = ? WHERE UserID = ?";
    $stmtUpdate = $conn->prepare($updateSql);
    $stmtUpdate->bind_param("ssssi", $username, $namaLengkap, $email, $alamat, $id);

    if ($stmtUpdate->execute()) {
        echo "<script>alert('Data petugas berhasil diperbarui!'); window.location.href = 'petugas.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data!');</script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Petugas - Perpustakaan Digital</title>
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
            align-items: center;
            justify-content: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.2);
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            font-size: 1.8rem;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input {
            padding: 10px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            background: white;
            color: #333;
        }

        button {
            padding: 10px;
            font-size: 1rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #2980b9;
        }

        .back-link {
            display: block;
            margin-top: 15px;
            color: white;
            text-decoration: none;
            font-size: 1rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Petugas</h1>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" value="<?php echo htmlspecialchars($row['username']); ?>" required>
            <input type="text" name="NamaLengkap" placeholder="Nama Lengkap" value="<?php echo htmlspecialchars($row['NamaLengkap']); ?>" required>
            <input type="email" name="Email" placeholder="Email" value="<?php echo htmlspecialchars($row['Email']); ?>" required>
            <input type="text" name="Alamat" placeholder="Alamat" value="<?php echo htmlspecialchars($row['Alamat']); ?>" required>
            <button type="submit">Simpan Perubahan</button>
        </form>
        <a href="petugas.php" class="back-link">Kembali</a>
    </div>
</body>
</html>
