<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan Digital</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: #f4f4f4;
            color: #333;
        }
        header {
            background: #2c3e50;
            padding: 20px 30px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        header nav a {
            color: white;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1.1rem;
            transition: color 0.3s ease;
            font-weight: 500;
        }
        header nav a:hover {
            color: #f39c12;
        }
        .hero {
            background: url('https://stfsp.ac.id/wp-content/uploads/2021/06/photo-1507842217343-583bb7270b66.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 0 20px;
        }
        .hero h1 {
            font-size: 4rem;
            font-family: 'Merriweather', serif;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.6);
        }
        .hero p {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto 30px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }
        .cta-btn {
            background: #f39c12;
            color: white;
            font-size: 1.1rem;
            padding: 15px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .cta-btn:hover {
            background-color: #e67e22;
        }
        .description {
            max-width: 1200px;
            margin: 50px auto;
            padding: 40px 20px;
            text-align: center;
            background: white;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .description h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }
        .description p {
            font-size: 1.1rem;
            line-height: 1.8;
            margin-bottom: 20px;
        }
        .gallery {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 20px;
            padding: 40px 20px;
        }
        .gallery img {
            width: 100%;
            max-width: 350px;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .gallery img:hover {
            transform: scale(1.05);
        }
        footer {
            background: #2c3e50;
            color: white;
            padding: 30px 20px;
            text-align: center;
            margin-top: 50px;
        }
        footer a {
            color: #f39c12;
            text-decoration: none;
            font-weight: bold;
        }
        footer a:hover {
            text-decoration: underline;
        }
        /* Responsif */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            .hero p {
                font-size: 1rem;
                margin: 0 auto 20px;
            }
            .cta-btn {
                font-size: 1rem;
                padding: 12px 25px;
            }
            .gallery {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="#">Beranda</a>
            <a href="#about">Tentang</a>
            <a href="login.php" id="login-link">Login</a>
            <a href="register.php">Daftar</a>
        </nav>
    </header>

    <section class="hero">
        <h1>Perpustakaan Digital</h1>
        <p>Tempat terbaik untuk menemukan koleksi buku digital berkualitas. Akses kapan saja, di mana saja, untuk semua kalangan.</p>
        <a href="#about" class="cta-btn">Pelajari Lebih Lanjut</a>
    </section>

    <section id="about" class="description">
        <h2>Tentang Perpustakaan Kami</h2>
        <p>Perpustakaan Digital adalah platform inovatif yang menyediakan koleksi buku digital dari berbagai genre. Dengan antarmuka yang mudah digunakan, Anda dapat mengakses ribuan buku dari berbagai kategori seperti fiksi, non-fiksi, akademik, hingga referensi. Dengan layanan 24/7, Anda dapat mengakses buku kapan saja, di mana saja, hanya dengan satu klik.</p>
        <p>Fasilitas modern kami dirancang untuk memberikan pengalaman membaca yang optimal, mulai dari pencarian buku yang mudah hingga pembacaan buku secara langsung di perangkat Anda.</p>
    </section>

    <section class="gallery">
        <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f" alt="Rak Buku Digital 1">
        <img src="https://images.unsplash.com/photo-1491841550275-ad7854e35ca6" alt="Ruangan Perpustakaan 1">
        <img src="https://images.unsplash.com/photo-1568667256549-094345857637" alt="Perpustakaan Modern">
    </section>

    <footer>
        <p>&copy; 2025 Perpustakaan Digital | <a href="#">Syarat & Ketentuan</a> | <a href="#">Kebijakan Privasi</a></p>
    </footer>
</body>
</html>
