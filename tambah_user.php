<?php
session_start();
include('koneksi.php');

// Cek apakah pengguna sudah login dan memiliki role 'administrator'
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'administrator') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash password
    $email = $_POST['email'];
    $namaLengkap = $_POST['namaLengkap'];
    $alamat = $_POST['alamat'];
    $status = 'Aktif';  // Status di-set sebagai 'Aktif' secara default
    $role = 'peminjam'; // Secara otomatis set role menjadi 'peminjam'

    // Query untuk memasukkan data ke dalam database
    $sql = "INSERT INTO user (username, password, Email, NamaLengkap, Alamat, status, role) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    // Gunakan prepared statement
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $username, $password, $email, $namaLengkap, $alamat, $status, $role);

    if ($stmt->execute()) {
        header("Location: user.php"); // Redirect ke halaman daftar peminjam setelah berhasil
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Peminjam - Perpustakaan Digital</title>
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
            height: 100vh;
            background: linear-gradient(45deg, #3498db, #8e44ad);
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            color: white;
            overflow-y: auto;
        }

        .form-container {
            background-color: #fff;
            color: #2c3e50;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.8rem;
        }

        .form-container label {
            font-size: 1.2rem;
            margin-bottom: 10px;
            display: block;
        }

        .form-container input, .form-container select, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1.1rem;
        }

        .form-container button {
            padding: 12px 20px;
            background-color: #8e44ad;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #9b59b6;
        }

        .form-container .message {
            margin-top: 20px;
            text-align: center;
            font-size: 1.2rem;
            color: green;
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

        .button-container {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        button, .back-button {
            padding: 12px 20px;
            font-size: 1.2rem;
            border-radius: 8px;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            transition: 0.3s ease;
        }

        .back-button {
            background-color: #e74c3c;
            color: white;
        }

        .back-button.detail {
            background-color: #2ecc71;
        }

        .back-button:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="content">
        <h1>Tambah Peminjam</h1>

        <div class="form-container">
            <form method="POST">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="namaLengkap">Nama Lengkap</label>
                <input type="text" id="namaLengkap" name="namaLengkap" required>

                <label for="alamat">Alamat</label>
                <input type="text" id="alamat" name="alamat" required>

                <!-- Status otomatis di-set sebagai "Aktif" -->
                <input type="hidden" name="status" value="Aktif" />

                <button type="submit">Tambah Peminjam</button>
            </form>
        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>
