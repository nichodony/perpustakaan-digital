<?php
session_start();

// Cek apakah pengguna sudah login dan apakah yang login adalah petugas atau administrator
if (!isset($_SESSION['UserID']) || ($_SESSION['role'] != 'administrator' && $_SESSION['role'] != 'petugas')) {
    header("Location: login.php");
    exit();
}

// Mendapatkan PeminjamanID dari URL
if (isset($_GET['PeminjamanID'])) {
    $peminjamanID = $_GET['PeminjamanID'];

    // Koneksi ke database
    $host = "localhost";
    $username = "root";
    $password = "";
    $dbname = "perpustakaan";
    $conn = new mysqli($host, $username, $password, $dbname);

    // Cek koneksi
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query untuk mengambil status peminjaman
    $sql = "SELECT StatusPeminjaman FROM peminjaman WHERE PeminjamanID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $peminjamanID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Ambil status peminjaman
        $row = $result->fetch_assoc();
        $currentStatus = $row['StatusPeminjaman'];

        // Tentukan status baru
        $newStatus = '';
        if ($currentStatus == 'pending') {
            $newStatus = 'returned';
        } elseif ($currentStatus == 'returned') {
            $newStatus = 'overdue';
        } else {
            $newStatus = 'pending';
        }

        // Query untuk memperbarui status
        $updateSql = "UPDATE peminjaman SET StatusPeminjaman = ? WHERE PeminjamanID = ?";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bind_param("si", $newStatus, $peminjamanID);

        if ($updateStmt->execute()) {
            // Redirect kembali ke halaman peminjaman setelah status berhasil diperbarui
            header("Location: peminjaman.php?status=success");
        } else {
            echo "Terjadi kesalahan dalam memperbarui status.";
        }
    } else {
        echo "Peminjaman tidak ditemukan.";
    }

    // Menutup koneksi
    $conn->close();
} else {
    echo "ID Peminjaman tidak ditemukan.";
}
?>
