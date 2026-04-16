<?php
include('../koneksi.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama_kategori = trim($_POST['nama_kategori']);

    if (!empty($nama_kategori)) {
        $stmt = $conn->prepare("UPDATE kategoribuku SET NamaKategori = ? WHERE KategoriID = ?");
        $stmt->bind_param("si", $nama_kategori, $id);
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "error";
        }
        $stmt->close();
    } else {
        echo "error";
    }
}

$conn->close();
?>
