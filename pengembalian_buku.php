<?php
session_start();

if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'peminjam') {
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
    $buku_id = $_GET['BukuID'];
    $user_id = $_SESSION['UserID'];

    // Cek apakah buku ini dipinjam oleh pengguna
    $sql_cek = "SELECT * FROM peminjaman WHERE BukuID = ? AND UserID = ? AND StatusPeminjaman = 'Dipinjam'";
    $stmt_cek = $conn->prepare($sql_cek);
    $stmt_cek->bind_param("ii", $buku_id, $user_id);
    $stmt_cek->execute();
    $result_cek = $stmt_cek->get_result();

    if ($result_cek->num_rows > 0) {
        // Buku dipinjam, maka bisa mengembalikan
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Proses pengembalian dan ulasan
            $rating = $_POST['rating'];
            $ulasan = $_POST['ulasan'];

            // Update status peminjaman menjadi 'Dikembalikan'
            $sql_update_status = "UPDATE peminjaman SET StatusPeminjaman = 'Dikembalikan' WHERE BukuID = ? AND UserID = ?";
            $stmt_update_status = $conn->prepare($sql_update_status);
            $stmt_update_status->bind_param("ii", $buku_id, $user_id);
            $stmt_update_status->execute();

            // Menyimpan ulasan dan rating
            $sql_ulasan = "INSERT INTO ulasanbuku (UserID, BukuID, Ulasan, Rating) VALUES (?, ?, ?, ?)";
            $stmt_ulasan = $conn->prepare($sql_ulasan);
            $stmt_ulasan->bind_param("iisi", $user_id, $buku_id, $ulasan, $rating);
            $stmt_ulasan->execute();

            echo "<script>alert('Buku berhasil dikembalikan dan ulasan telah disimpan!'); window.location.href='peminjam_buku.php';</script>";
        }
    } else {
        echo "<script>alert('Buku ini tidak dipinjam oleh Anda!'); window.location.href='peminjam_buku.php';</script>";
    }
}

// Menutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Buku</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #fff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #3498db;
            font-family: 'Lora', serif;
        }

        label {
            font-size: 1.1rem;
            margin-bottom: 10px;
            color: #555;
            display: block;
        }

        input[type="number"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            color: #333;
            box-sizing: border-box;
        }

        textarea {
            height: 150px;
            resize: vertical;
        }

        .form-group {
            margin-bottom: 20px;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #2980b9;
        }

        .back-button {
            text-align: center;
            margin-top: 20px;
        }

        .back-button a {
            text-decoration: none;
            color: #3498db;
            font-size: 1rem;
            transition: color 0.3s ease;
        }

        .back-button a:hover {
            color: #2980b9;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Pengembalian Buku dan Ulasan</h1>

        <form method="POST">
            <div class="form-group">
                <label for="rating">Rating (1-5):</label>
                <input type="number" id="rating" name="rating" min="1" max="5" required>
            </div>

            <div class="form-group">
                <label for="ulasan">Ulasan:</label>
                <textarea id="ulasan" name="ulasan" required placeholder="Berikan ulasan untuk buku ini..."></textarea>
            </div>

            <button type="submit">Kembalikan dan Berikan Ulasan</button>
        </form>

        <div class="back-button">
            <a href="peminjam_buku.php">Kembali ke Daftar Buku</a>
        </div>
    </div>

</body>
</html>
