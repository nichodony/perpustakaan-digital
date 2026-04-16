<?php
session_start();
include('../koneksi.php');

// Cek apakah pengguna sudah login dan memiliki role 'administrator'
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'administrator') {
    header("Location: login.php");
    exit();
}

// Cek apakah ada parameter 'id' yang diterima
if (isset($_GET['id'])) {
    $userID = $_GET['id'];

    // Update status menjadi 'Aktif'
    $sql = "UPDATE user SET status = 'Aktif' WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userID);

    if ($stmt->execute()) {
        // Redirect ke halaman daftar peminjam setelah sukses
        header("Location: user.php");
        exit();
    } else {
        echo "Gagal mengubah status pengguna.";
    }
}
?>
