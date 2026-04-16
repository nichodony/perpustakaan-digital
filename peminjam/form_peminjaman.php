<?php
session_start();

if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'peminjam') {
    header("Location: ../login.php");
    exit();
}

$host = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['UserID'];
$buku_id = $_GET['BukuID'];

// Cek apakah buku sudah dipinjam oleh user dan belum dikembalikan
$sql_check = "SELECT * FROM peminjaman WHERE UserID = ? AND BukuID = ? AND Status = 'Dipinjam'";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ii", $user_id, $buku_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo "<script>alert('Anda sudah meminjam buku ini dan belum mengembalikannya!'); window.location.href='peminjam_dashboard.php';</script>";
} else {
    $tanggal_peminjaman = date('Y-m-d');
    $tanggal_pengembalian = date('Y-m-d', strtotime('+7 days')); // 7 hari masa pinjam

    $sql_pinjam = "INSERT INTO peminjaman (UserID, BukuID, TanggalPeminjaman, TanggalPengembalian, Status) 
                   VALUES (?, ?, ?, ?, 'Dipinjam')";
    $stmt_pinjam = $conn->prepare($sql_pinjam);
    $stmt_pinjam->bind_param("iiss", $user_id, $buku_id, $tanggal_peminjaman, $tanggal_pengembalian);

    if ($stmt_pinjam->execute()) {
        echo "<script>alert('Buku berhasil dipinjam!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Gagal meminjam buku!'); window.location.href='dashboard.php';</script>";
    }
}

$conn->close();
?>
