<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "perpustakaan";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$selected_kategori = isset($_GET['kategori']) ? explode(',', $_GET['kategori']) : [];

$sql_buku_list = "SELECT DISTINCT buku.* FROM buku 
    LEFT JOIN kategoribuku_relasi ON buku.BukuID = kategoribuku_relasi.BukuID
    LEFT JOIN kategoribuku ON kategoribuku_relasi.KategoriID = kategoribuku.KategoriID
    WHERE 1";

$params = [];
$types = "";

if (!empty($selected_kategori)) {
    $placeholders = implode(",", array_fill(0, count($selected_kategori), "?"));
    $sql_buku_list .= " AND kategoribuku.KategoriID IN ($placeholders)";
    
    foreach ($selected_kategori as $kat) {
        $params[] = $kat;
        $types .= "i";
    }
}

$stmt = $conn->prepare($sql_buku_list);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

while ($buku = $result->fetch_assoc()) {
    echo '<div class="book-card">
            <img src="../assets/img/' . htmlspecialchars($buku['Gambar']) . '" alt="Gambar Buku">
            <h3>' . htmlspecialchars($buku['Judul']) . '</h3>
            <div class="book-actions">
                <a href="buku_detail.php?BukuID=' . $buku['BukuID'] . '">Lihat Detail</a>
            </div>
        </div>';
}

$stmt->close();
$conn->close();
?>
