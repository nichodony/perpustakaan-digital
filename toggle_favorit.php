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

if (!isset($_SESSION['UserID'])) {
    echo "error";
    exit();
}

$userID = $_SESSION['UserID'];
$bukuID = $_POST['BukuID'];

// Cek apakah buku sudah ada di favorit
$sql_check = "SELECT * FROM favorit WHERE UserID = ? AND BukuID = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $userID, $bukuID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Hapus dari favorit
    $sql_delete = "DELETE FROM favorit WHERE UserID = ? AND BukuID = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("ii", $userID, $bukuID);
    $stmt->execute();
    echo "removed";
} else {
    // Tambah ke favorit
    $sql_insert = "INSERT INTO favorit (UserID, BukuID) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("ii", $userID, $bukuID);
    $stmt->execute();
    echo "added";
}

$conn->close();
?>
