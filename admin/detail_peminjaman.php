<?php
session_start();
include('../koneksi.php');

// Cek apakah pengguna sudah login
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'administrator') {
    header("Location: ../login.php");
    exit();
}

// Ambil ID peminjaman dari URL
if (!isset($_GET['PeminjamanID'])) {
    echo "ID peminjaman tidak ditemukan.";
    exit();
}

$PeminjamanID = $_GET['PeminjamanID'];

// Query untuk mengambil detail peminjaman
$sql = "SELECT p.PeminjamanID, u.NamaLengkap, b.Judul, p.TanggalPeminjaman, p.TanggalPengembalian, p.Status 
        FROM peminjaman p
        JOIN user u ON p.UserID = u.UserID
        LEFT JOIN buku b ON p.BukuID = b.BukuID
        WHERE p.PeminjamanID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $PeminjamanID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Data peminjaman tidak ditemukan.";
    exit();
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Peminjaman</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #3498db, #8e44ad);
            margin: 0;
        }
        .container {
            background: white;
            padding: 30px;
            width: 400px;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            text-align: center;
        }
        h2 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .detail {
            text-align: left;
            margin: 15px 0;
            font-size: 16px;
        }
        .detail p {
            margin: 10px 0;
            color: #34495e;
            font-weight: 500;
            font-size: 16px;
            padding: 8px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #ecf0f1;
        }

        .detail p strong {
            flex: 1;
            color: #2c3e50;
        }

        .detail p i {
            margin-right: 10px;
            color: #8e44ad;
            font-size: 18px;
        }

        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
        }
        .badge.dipinjam {
            background: #f39c12;
            color: white;
        }
        .badge.dikembalikan {
            background: #2ecc71;
            color: white;
        }
        .print-btn {
            margin-top: 20px;
            padding: 12px 18px;
            font-size: 16px;
            border: none;
            background: #3498db;
            color: white;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
        }
        .print-btn:hover {
            background: #2980b9;
            transform: scale(1.05);
        }
        .back-btn {
            margin-top: 15px;
            text-decoration: none;
            display: inline-block;
            color: #8e44ad;
            font-weight: bold;
            transition: 0.3s;
        }
        .back-btn:hover {
            color: #3498db;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Detail Peminjaman</h2>
    <p><i class="fas fa-id-badge"></i> <strong>ID Peminjaman:</strong> <?php echo $row["PeminjamanID"]; ?></p>
    <p><i class="fas fa-user"></i> <strong>Nama Peminjam:</strong> <?php echo $row["NamaLengkap"]; ?></p>
    <p><i class="fas fa-book"></i> <strong>Judul Buku:</strong> <?php echo $row["Judul"] ? $row["Judul"] : "Buku Tidak Ditemukan"; ?></p>
    <p><i class="fas fa-calendar-alt"></i> <strong>Tanggal Peminjaman:</strong> <?php echo $row["TanggalPeminjaman"]; ?></p>
    <p><i class="fas fa-calendar-check"></i> <strong>Tanggal Pengembalian:</strong> <?php echo $row["TanggalPengembalian"]; ?></p>
    <p><i class="fas fa-info-circle"></i> <strong>Status:</strong> 
        <span class="badge <?php echo ($row["Status"] == 'Dikembalikan') ? 'dikembalikan' : 'dipinjam'; ?>">
            <?php echo $row["Status"]; ?>
        </span>
    </p>

    
    <button class="print-btn" onclick="window.print()"><i class="fas fa-print"></i> Cetak</button>
    <br>
    <a href="peminjaman.php" class="back-btn"><i class="fas fa-arrow-left"></i> Kembali</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
