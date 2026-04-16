<?php
session_start();
include('../koneksi.php');

// Pastikan hanya admin yang bisa menghapus
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'administrator') {
    header("Location: ../login.php");
    exit();
}

// Pastikan parameter ID tersedia
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Cek apakah petugas dengan ID ini ada
    $checkSql = "SELECT * FROM user WHERE UserID = ? AND role = 'petugas'";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Hapus petugas dari database
        $deleteSql = "DELETE FROM user WHERE UserID = ?";
        $stmtDelete = $conn->prepare($deleteSql);
        $stmtDelete->bind_param("i", $id);

        if ($stmtDelete->execute()) {
            echo "<script>alert('Petugas berhasil dihapus!'); window.location.href = 'petugas.php';</script>";
        } else {
            echo "<script>alert('Gagal menghapus petugas!'); window.location.href = 'petugas.php';</script>";
        }
    } else {
        echo "<script>alert('Petugas tidak ditemukan!'); window.location.href = 'petugas.php';</script>";
    }
} else {
    echo "<script>alert('ID petugas tidak valid!'); window.location.href = 'petugas.php';</script>";
}

$conn->close();
?>
