<?php
$host = 'localhost';
$username = 'root';  // ganti dengan username DB Anda
$password = '';      // ganti dengan password DB Anda
$dbname = 'perpustakaan';

$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
