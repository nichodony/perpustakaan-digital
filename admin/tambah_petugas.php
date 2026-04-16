<?php
session_start();

// Cek apakah pengguna sudah login dan memiliki role 'administrator'
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'administrator') {
    header("Location: login.php");
    exit();
}

$host = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";

// Membuat koneksi ke database
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Menangani form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $namaLengkap = $_POST['namaLengkap'];
    $alamat = $_POST['alamat'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Enkripsi password

    // Menambahkan petugas ke database dengan role 'petugas'
    $sql = "INSERT INTO user (username, password, Email, NamaLengkap, Alamat, role) 
            VALUES (?, ?, ?, ?, ?, 'petugas')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $username, $password, $email, $namaLengkap, $alamat);

    if ($stmt->execute()) {
        // Redirect ke halaman petugas setelah berhasil menambah petugas
        header("Location: petugas.php");
        exit();
    } else {
        $error_message = "Gagal menambah petugas, coba lagi.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Petugas - Perpustakaan Digital</title>
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
            font-size: 1rem;
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
            flex-grow: 1;
            padding: 20px;
            color: white;
            overflow-y: auto;
        }

        .form-container {
            background-color: #fff;
            color: #2c3e50;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            width: 70%;
            margin: 30px auto;
        }

        .form-container h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 1.8rem;
            color: #8e44ad;
        }

        .form-container label {
            font-size: 1.1rem;
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
        }

        .form-container input, .form-container select, .form-container textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        .form-container button {
            background-color: #8e44ad;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            width: 100%;
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

        .error-message {
            background-color: #e74c3c;
            color: white;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Content -->
    <div class="content">
        <h1>Tambah Petugas</h1>

        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form action="tambah_petugas.php" method="POST">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>

                <label for="namaLengkap">Nama Lengkap:</label>
                <input type="text" name="namaLengkap" id="namaLengkap" required>

                <label for="alamat">Alamat:</label>
                <textarea name="alamat" id="alamat" rows="4" required></textarea>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>

                <button type="submit">Tambah Petugas</button>
            </form>
        </div>
    </div>

</body>
</html>
