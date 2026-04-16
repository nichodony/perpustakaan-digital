<?php
session_start();
include 'koneksi.php'; // Menyertakan file koneksi database

$error_message = ''; // Pesan error untuk login

// Cek jika form dikirimkan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Cek apakah username ada dalam database
    $sql = "SELECT * FROM user WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password yang dimasukkan dengan password yang ada di database
        if (password_verify($password, $user['password'])) {

            // Cek status akun
            if ($user['status'] == 'diblokir') {
                // Jika status 'diblokir', tampilkan pesan error
                $error_message = "AKUN ANDA TERBLOKIR.";
            } else {
                // Set session jika status akun aktif
                $_SESSION['UserID'] = $user['UserID'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role']; // Menyimpan role

                // Arahkan berdasarkan role
                if ($user['role'] == 'administrator') {
                    header("Location: admin/dashboard.php"); // Halaman khusus admin
                } elseif ($user['role'] == 'petugas') {
                    header("Location: petugas/dashboard.php"); // Halaman khusus petugas
                } elseif ($user['role'] == 'peminjam') {
                    header("Location: peminjam/dashboard.php"); // Halaman khusus peminjam
                } else {
                    $error_message = "Role tidak terdefinisi.";
                }
            }
        } else {
            // Jika password salah
            $error_message = "Password salah.";
        }
    } else {
        // Jika username tidak ditemukan
        $error_message = "Username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan Digital</title>

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

        .login-container {
            width: 350px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            padding: 30px 20px;
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #fff;
        }

        .login-container form {
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

        .login-container input {
            width: 75%;
            padding: 12px 40px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            background: #f5f5f5;
            color: #333;
        }

        .login-container input:focus {
            border: 2px solid #6a11cb;
            outline: none;
        }

        .login-container input[type="submit"] {
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
            margin-top: 10px;
        }

        .login-container input[type="submit"]:hover {
            background: #2575fc;
        }

        .error-message {
            margin-top: 10px;
            font-size: 14px;
            color: white;
            background: red;
            border-radius: 6px;
        }

        .icon-container {
            font-size: 50px;
            color: #fff;
            margin-bottom: 20px;
        }

        .register-link {
            margin-top: 15px;
            font-size: 14px;
            color: #fff;
        }

        .register-link a {
            color: #6a11cb;
            font-weight: bold;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        @media (max-width: 500px) {
            .login-container {
                width: 90%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="icon-container">
            <i class="fas fa-book-reader"></i>
        </div>
        <h2>Login Perpustakaan</h2>
        <form method="POST" action="">
            <div class="input-container">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-container">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <input type="submit" value="Login">
        </form>

        <?php if (!empty($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <div class="register-link">
            <p>Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
        </div>
    </div>

</body>
</html>
