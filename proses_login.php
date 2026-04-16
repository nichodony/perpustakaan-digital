<?php
session_start();
include 'db_config.php'; // Menyertakan file koneksi database

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
            // Set session
            $_SESSION['UserID'] = $user['UserID'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Menyimpan role

            // Mengarahkan berdasarkan role
            if ($user['role'] == 'administrator') {
                header("Location: admin/dashboard.php"); // Halaman khusus admin
            } elseif ($user['role'] == 'petugas') {
                header("Location: petugas/dashboard.php"); // Halaman khusus petugas
            } elseif ($user['role'] == 'peminjam') {
                header("Location: peminjam/dashboard.php"); // Halaman khusus peminjam
            } else {
                $_SESSION['error_message'] = "Role tidak terdefinisi.";
                header("Location: login.php");
            }
            exit();
        } else {
            // Jika password salah
            $_SESSION['error_message'] = "Password salah.";
        }
    } else {
        // Jika username tidak ditemukan
        $_SESSION['error_message'] = "Username tidak ditemukan.";
    }

    // Redirect kembali ke halaman login
    header("Location: login.php");
    exit();
}
?>
