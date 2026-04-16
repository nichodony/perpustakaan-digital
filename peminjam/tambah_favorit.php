<?php
session_start();

// Cek apakah peminjam sudah login dan memiliki role 'peminjam'
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'peminjam') {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
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
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

// Ambil data BukuID dan UserID dari parameter GET
if (isset($_GET['BukuID']) && isset($_GET['UserID'])) {
    $BukuID = $_GET['BukuID'];
    $UserID = $_GET['UserID'];

    // Periksa apakah buku sudah ada di koleksi pribadi
    $sql_check = "SELECT * FROM koleksipribadi WHERE BukuID = ? AND UserID = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $BukuID, $UserID);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        // Buku sudah ada, tidak perlu ditambahkan lagi
        echo json_encode(['status' => 'error', 'message' => 'Buku sudah ada di koleksi pribadi']);
    } else {
        // Menambahkan buku ke koleksi pribadi
        $sql_insert = "INSERT INTO koleksipribadi (UserID, BukuID) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $UserID, $BukuID);
        if ($stmt_insert->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Buku berhasil ditambahkan ke koleksi pribadi']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat menambahkan buku']);
        }
    }
    $stmt_check->close();
    $stmt_insert->close();
}

// Tutup koneksi
$conn->close();
?>
