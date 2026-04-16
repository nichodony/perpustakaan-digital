<?php
// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $alamat = $_POST['alamat'];

    // Validasi input
    if (empty($username) || empty($password) || empty($confirm_password) || empty($email) || empty($nama_lengkap) || empty($alamat)) {
        $error_message = "Harap isi semua kolom.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Konfirmasi password tidak cocok.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $role = 'peminjam';  // Role otomatis menjadi peminjam
        $status = 'aktif';    // Status otomatis menjadi aktif

        // Query untuk menyimpan data pengguna ke dalam database
        $sql = "INSERT INTO user (username, password, Email, NamaLengkap, Alamat, role) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $username, $hashed_password, $email, $nama_lengkap, $alamat, $role);

        if ($stmt->execute()) {
            $success_message = "Pendaftaran berhasil! Akun Anda sudah terdaftar. <br> Silakan <a href='login.php'>Login sekarang</a>.";
            // Setelah 3 detik, arahkan pengguna ke halaman login
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 3000); // 3 detik
                  </script>";
        } else {
            $error_message = "Terjadi kesalahan: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Perpustakaan Digital</title>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <!-- CSS -->
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #fff;
        }

        .register-container {
            width: 400px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            padding: 15px 5px;
            text-align: center;
        }

        .register-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #fff;
        }

        .register-container form {
            display: flex;
            flex-direction: column;
        }

        .input-container {
            position: relative;
            margin-bottom: 20px;
        }

        .input-container i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6a11cb;
        }

        .register-container input, .register-container textarea {
            width: 75%;
            padding: 12px 40px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            background: #f5f5f5;
            color: #333;
        }

        .register-container input:focus, .register-container textarea:focus {
            border: 2px solid #6a11cb;
            outline: none;
        }

        .register-container input[type="submit"] {
            width: 100%;
            padding: 12px;
            background: #6a11cb;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
            font-weight: bold;
            border: none;
            border-radius: 8px;
        }

        .register-container input[type="submit"]:hover {
            background: #2575fc;
        }

        .error-message, .success-message {
            margin-top: 10px;
            font-size: 14px;
        }

        .error-message {
            color: #ff4d4d;
        }

        .success-message {
            color: #4caf50;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.2);
            padding: 10px;
            border-radius: 8px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .login-link {
            margin-top: 15px;
            font-size: 14px;
            color: #fff;
        }

        .login-link a {
            color: #6a11cb;
            font-weight: bold;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .register-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="register-container">
        <h2>Register Perpustakaan</h2>
        <form method="POST" action="">
        <div class="input-container">
                <i class="fas fa-user"></i>
                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
            </div>
            <div class="input-container">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-container">
                <i class="fas fa-home"></i>
                <textarea name="alamat" placeholder="Alamat" rows="4" required></textarea>
            </div>
            <div class="input-container">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-container">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-container">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
            </div>
            <input type="submit" value="Daftar">
        </form>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <p class="success-message"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <div class="login-link">
            <p>Sudah punya akun? <a href="login.php">Login sekarang</a></p>
        </div>
    </div>

</body>
</html>
