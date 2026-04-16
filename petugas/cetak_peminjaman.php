<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("ID peminjaman tidak valid.");
}

$id = intval($_GET['id']);

$sql = "SELECT p.PeminjamanID, u.NamaLengkap, b.Judul, p.TanggalPeminjaman, p.TanggalPengembalian, p.Status
        FROM peminjaman p
        JOIN user u ON p.UserID = u.UserID
        LEFT JOIN buku b ON p.BukuID = b.BukuID
        WHERE p.PeminjamanID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data peminjaman tidak ditemukan.");
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Peminjaman</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            text-align: center;
        }
        .container {
            max-width: 600px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            text-align: left;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            width: 80px;
            margin-bottom: 10px;
        }
        .header h2 {
            margin: 0;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #3498db;
            color: white;
        }
        .signature {
            margin-top: 30px;
            text-align: center;
        }
        .signature p {
            margin-bottom: 60px;
            border-bottom: 1px solid black;
            display: inline-block;
            width: 200px;
        }
        .print-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background: #2ecc71;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
        }
        .print-btn:hover {
            background: #27ae60;
        }
        @media print {
            .print-btn {
                display: none;
            }
            .container {
                box-shadow: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        <div class="header">
            <img src="../assets/img/lb.png" alt="Logo Perpustakaan">
            <h2>Perpustakaan Digital</h2>
            <p>Bukti Peminjaman Buku</p>
        </div>

        <table>
            <tr><th>ID Peminjaman</th><td><?php echo $data["PeminjamanID"]; ?></td></tr>
            <tr><th>Nama Peminjam</th><td><?php echo htmlspecialchars($data["NamaLengkap"]); ?></td></tr>
            <tr><th>Judul Buku</th><td><?php echo htmlspecialchars($data["Judul"] ?: 'Buku Tidak Ditemukan'); ?></td></tr>
            <tr><th>Tanggal Peminjaman</th><td><?php echo $data["TanggalPeminjaman"]; ?></td></tr>
            <tr><th>Tanggal Pengembalian</th><td><?php echo $data["TanggalPengembalian"]; ?></td></tr>
            <tr><th>Status</th><td><?php echo $data["Status"]; ?></td></tr>
        </table>

        <div class="signature">
            <p>Peminjam</p>
        </div>

        <button class="print-btn" onclick="window.print()">🖨 Cetak</button>
    </div>
</body>
</html>

<?php
$conn->close();
?>
