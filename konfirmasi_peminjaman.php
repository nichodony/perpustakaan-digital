<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pastikan hanya petugas yang bisa mengkonfirmasi
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'petugas') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["konfirmasi"])) {
    $peminjamanID = $_POST["PeminjamanID"];

    // Update status peminjaman menjadi 'Dipinjam'
    $sql = "UPDATE peminjaman SET Status = 'Dipinjam' WHERE PeminjamanID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $peminjamanID);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Peminjaman berhasil dikonfirmasi!";
    } else {
        $_SESSION['error_message'] = "Gagal mengkonfirmasi peminjaman.";
    }

    $stmt->close();
    header("Location: petugas_peminjaman.php");
    exit();
}

$conn->close();
?>
