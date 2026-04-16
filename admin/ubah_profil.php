<?php
session_start();

// Cek apakah peminjam sudah login
if (!isset($_SESSION['UserID']) || $_SESSION['role'] != 'peminjam') {
    header("Location: login.php");
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
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data pengguna
$userID = $_SESSION['UserID'];
$sql = "SELECT * FROM user WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userID);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Proses form submit untuk ubah profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_baru = $_POST['username'];
    $foto_baru = $_FILES['foto']['name'];
    $foto_tmp = $_FILES['foto']['tmp_name'];

    // Jika ada file foto yang diunggah
    if (!empty($foto_baru)) {
        $foto_path = 'assets/img/' . basename($foto_baru);
        move_uploaded_file($foto_tmp, $foto_path);

        // Update nama dan foto di database
        $update_sql = "UPDATE user SET username = ?, Foto = ? WHERE UserID = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $nama_baru, $foto_baru, $userID);
    } else {
        // Jika tidak ada foto baru, hanya update nama
        $update_sql = "UPDATE user SET username = ? WHERE UserID = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $nama_baru, $userID);
    }

    // Eksekusi query update
    if ($stmt->execute()) {
        $_SESSION['username'] = $nama_baru; // Update nama di session
        header("Location: peminjam_dashboard.php");
        exit();
    } else {
        $error_message = "Gagal mengubah profil.";
    }
}

// Tutup koneksi
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Profil - Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Lora:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(45deg, #8e44ad, #3498db);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .form-container:hover {
            transform: translateY(-8px);
        }

        .form-container h2 {
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: 600;
            color: #3498db;
        }

        .form-container input[type="text"],
        .form-container input[type="file"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1.1rem;
            transition: border-color 0.3s ease;
        }

        .form-container input[type="text"]:focus,
        .form-container input[type="file"]:focus {
            border-color: #3498db;
            outline: none;
        }

        .form-container button {
            width: 100%;
            padding: 12px;
            background-color: #8e44ad;
            color: white;
            font-size: 1.1rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .form-container button:hover {
            background-color: #9b59b6;
        }

        .form-container .error {
            color: red;
            font-size: 0.9rem;
            margin-bottom: 20px;
        }

        .form-container img {
            width: 130px;
            height: 130px;
            border-radius: 50%;
            margin-bottom: 20px;
            object-fit: cover;
            border: 4px solid #3498db;
            transition: border 0.3s ease;
        }

        .form-container img:hover {
            border-color: #9b59b6;
        }

        .upload-icon {
            font-size: 2rem;
            color: #8e44ad;
            margin-bottom: 20px;
            cursor: pointer;
        }

        .upload-icon:hover {
            color: #3498db;
        }

        @media (max-width: 600px) {
            .form-container {
                padding: 20px;
                max-width: 90%;
            }

            .form-container h2 {
                font-size: 1.6rem;
            }
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2>Ubah Profil</h2>

        <?php if (isset($error_message)): ?>
            <p class="error"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <img src="assets/img/<?php echo htmlspecialchars($user['Foto']); ?>" alt="Foto Profil">
        
        <form action="ubah_profil.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="username" placeholder="Nama" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            
            <label for="foto" class="upload-icon"><i class="fas fa-camera"></i> Ganti Foto</label>
            <input type="file" name="foto" id="foto" accept="image/*">

            <button type="submit">Simpan Perubahan</button>
        </form>
    </div>

</body>
</html>
