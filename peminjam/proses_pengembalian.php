<?php
session_start();
include('../koneksi.php');

// Cek apakah data sudah dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $peminjaman_id = $_POST['peminjaman_id'];
    $buku_id = $_POST['buku_id']; 
    $rating = $_POST['rating'];
    $ulasan = mysqli_real_escape_string($conn, $_POST['ulasan']);
    $user_id = $_SESSION['UserID']; // ID pengguna yang login

    // Menyimpan ulasan buku ke dalam tabel ulasanbuku
    $sql_ulasan = "INSERT INTO ulasanbuku (UserID, BukuID, Ulasan, Rating) 
                   VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql_ulasan);
    $stmt->bind_param("iisi", $user_id, $buku_id, $ulasan, $rating);
    $stmt->execute();

    // Mengupdate status peminjaman menjadi "Dikembalikan"
    $sql_update_peminjaman = "UPDATE peminjaman SET Status = 'Dikembalikan' WHERE PeminjamanID = ?";
    $stmt_update = $conn->prepare($sql_update_peminjaman);
    $stmt_update->bind_param("i", $peminjaman_id);
    $stmt_update->execute();

    // Redirect ke halaman detail buku atau halaman lain yang diinginkan
    header("Location: history.php");
    exit();
}
?>
