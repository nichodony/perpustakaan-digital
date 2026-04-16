<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['UserID'])) {
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

if (isset($_GET['BukuID'])) {
    $id = $_GET['BukuID'];

    // Hapus data buku
    $sql = mysqli_query($conn, "DELETE FROM buku WHERE BukuID = '$id'");
    if($sql == TRUE){
        // Redirect setelah penghapusan
    header("Location: buku.php");
    }else{
        echo 'gagal';
    }
    
} else {
    echo "Buku tidak ditemukan.";
}
?>
