<?php
include 'koneksi.php'; // Pastikan file ini menghubungkan ke database

if (isset($_GET['query'])) {
    $search = "%" . $_GET['query'] . "%";
    $sql = "SELECT BukuID, Judul FROM buku WHERE Judul LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    $books = [];
    while ($row = $result->fetch_assoc()) {
        $books[] = $row;
    }

    echo json_encode($books); // Kembalikan data dalam format JSON
}
?>
