<?php
session_start();

// Cek apakah peminjam sudah login dan memiliki role 'peminjam'
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'peminjam') {
    header("Location: ../login.php");
    exit();
}

// Koneksi database
$host = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";

$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil parameter BukuID dari URL
if (isset($_GET['BukuID'])) {
    $BukuID = $_GET['BukuID'];
    $UserID = $_SESSION['UserID'];

    // Hapus buku dari koleksi pribadi
    $sql_hapus = "DELETE FROM koleksipribadi WHERE BukuID = ? AND UserID = ?";
    $stmt_hapus = $conn->prepare($sql_hapus);
    $stmt_hapus->bind_param("ii", $BukuID, $UserID);

    if ($stmt_hapus->execute()) {
        // Setelah berhasil, redirect ke halaman favorit
        header("Location: favorit.php?message=Buku+berhasil+di+hapaus");
    } else {
        echo "Error: " . $stmt_hapus->error;
    }

    $stmt_hapus->close();
}

// Tutup koneksi
$conn->close();
?>
