-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 20 Feb 2025 pada 23.17
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `perpustakaan`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `buku`
--

CREATE TABLE `buku` (
  `BukuID` int(11) NOT NULL,
  `Judul` varchar(255) NOT NULL,
  `Deskripsi` text NOT NULL,
  `Penulis` varchar(255) NOT NULL,
  `Penerbit` varchar(255) NOT NULL,
  `TahunTerbit` int(11) NOT NULL,
  `Gambar` varchar(255) NOT NULL,
  `Kategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `buku`
--

INSERT INTO `buku` (`BukuID`, `Judul`, `Deskripsi`, `Penulis`, `Penerbit`, `TahunTerbit`, `Gambar`, `Kategori`) VALUES
(3, '10 PM Vol 2', 'Si penyiar radio misterius bernama Arvino kembali membagikan kisah-kisah seram kepada para pendengar siaran 66,6 FM Dabest Radio. Setiap malam Jumat ketika kisah-kisah tersebut dibacakan, pendengar seakan merasakan sendiri kisah tersebut. Dan tanpa mereka sadari, siaran tersebut mengundang kehadiran makhluk halus di sekitar mereka\r\n\r\nKeunggulan:\r\nBuku ini merupakan lanjutan dari cerita yang sama yaitu 10 PM Vol 1\r\nDi komik cetak 10PM seri 2 ini tidak ada di versi webtoonnya.\r\nMengusung tema yang unik dan relatable\r\nKomik horor menjadi salah satu bacaan yang banyak dicari oleh masyarakat luas, khususnya pecinta horor.\r\nBerisi tentang kumpulan kisah-kisah horror yang diceritakan secara unik melalui siaran radio\r\nTerdapat ghostpedia di akhir cerita yang dapat memberikan pengetahuan lebih bagi pembaca mengenai hantu-hantu lokal\r\nDikemas dengan layout yang menari\r\n\r\nTarget Pasar:\r\nAnak muda hingga dewasa. (17+)\r\n\r\n******\r\n10 PM Vol. 2 adalah buku kumpulan cerita horor karya Andam, seorang penyiar radio yang dikenal dengan programnya yang menyeramkan di 66,6 FM Dabest Radio. Buku ini merupakan sekuel dari buku sebelumnya, 10 PM Vol. 1, yang juga berisi kisah-kisah horor yang menegangkan.\r\n\r\nKisah-kisah dalam buku ini dibacakan oleh Arvino, sang penyiar radio misterius, setiap malam Jumat. Para pendengar seolah-olah merasakan sendiri kengerian dalam setiap cerita yang dibacakan. Buku ini menawarkan pengalaman membaca yang mencekam dan membuat bulu kuduk merinding.', 'Arvidan', 'Andam', 2024, '10.png', 'Novel'),
(4, '', '', '', '', 0, 'th.jpg', ''),
(5, '3726 MDPL', 'Selain disibukkan dengan skripsi, Rangga, Ketua Panitia OSPEK Fakultas Kehutanan 2023 itu juga menyibukkan dirinya dengan mengagumi Andini. Seorang mahasiswi yang bercita-cita bisa mendaki Gunung Rinjani, sekaligus adik tingkat yang ia sebut sebagai manusia favorit.\r\n\r\nAndini dikelilingi oleh banyak cinta, banyak manusia yang ingin dengannya, terutama Rangga dengan seluruh kesan istimewanya. Namun, dalam dirinya, ada manusia dulu yang entah masih jadi pemenang atau definisi lain dari itu.\r\n\r\nTahun Terbit : Cetakan Pertama, 2024\r\n\r\nPernahkah Anda terpikir betapa menariknya dunia yang terbuka lebar lewat lembaran buku? Membaca bukan hanya kegiatan rutin, tetapi juga petualangan tak terbatas ke dalam imajinasi dan pengetahuan.\r\n\r\nMembaca mengasah pikiran, membuka wawasan, dan memperkaya kosakata. Ini adalah pintu menuju dunia di luar kita yang tak terbatas.\r\n\r\nTetapkan waktu khusus untuk membaca setiap hari. Dari membaca sebelum tidur hingga menyempatkan waktu di pagi hari, kebiasaan membaca dapat dibentuk dengan konsistensi.\r\n\r\nPilih buku sesuai minat dan level literasi. Mulailah dengan buku yang sesuai dengan keinginan dan kemampuan membaca.\r\n\r\nTemukan tempat yang tenang dan nyaman untuk membaca. Lampu yang cukup, kursi yang nyaman, dan sedikit musik pelataran bisa menciptakan pengalaman membaca yang lebih baik.\r\n\r\nBuat catatan atau jurnal tentang buku yang telah Anda baca. Tuliskan pemikiran, kesan, dan pelajaran yang Anda dapatkan.', 'Nurwina Sari', 'Romancious', 2024, '3726.jpg', 'Fiksi'),
(6, 'Laiqa: Kresek Hitam', 'Maera pikir, masuk asrama rehabilitasi merupakan hukuman terbaik atas penebusan dosa-dosa masa lalunya. Ternyata, hukuman yang sesungguhnya didapat setelah dia keluar dari sana. Dia kehilangan saudara dan teman, di-DO dari kampus, dan yang jauh lebih buruk, tak lagi dipercaya kedua orangtuanya. Ketika Maera berusaha menata ulang kehidupannya, orang-orang yang dia harap bisa menolong malah berbalik menghancurkannya. Apakah beban yang terlampau berat ini mampu dihadapi Maera di usianya yang baru sembilan belas? Haruskah hidupnya berakhir bagaikan kresek hitam yang akan disingkirkan oleh keluarganya?\r\n\r\nProlog:\r\nAKU BERDIRI SAJA di tengah halaman luas itu, melihat teman-temanku pulang satu per satu sampai hanya tinggal aku sendiri. Sudah satu jam sejak orang terakhir pergi dan kakiku sudah keram, tapi aku tetap bertahan. Kugoyangkan tubuh untuk mengusir sedikit pegal, ke depan bertumpu pada ujung jari, ke belakang bertumpu pada tumit. Lumayan, kok. Bergoyang seperti ini bisa meredakan bosan asal seimbang agar tidak tersungkur. Itu bisa mempermalukan diri sendiri. Sebenarnya aku sudah terlalu banyak mempermalukan diri sendiri. Sepertinya, aku bisa menjadi ratu tukang mempermalukan diri sendiri. Berkali-kali aku melakukannya dengan sukses dan kali ini yang paling berhasil sampai aku jadi satu-satunya dalam keluarga yang masuk pusat rehabilitasi narkoba.\r\n\r\nProfil Penulis:\r\nHONEY DEE telah menghasilkan sejumlah buku yang diterbitkan secara daring di platform novel dan secara konvensional. Setelah menjuarai lomba-lomba menulis, menjadi Juara pertama lomba menulis novel religi dari GWPxElex merupakan prestasi yang sangat berkesan baginya. Penulis yang juga menyukai olahraga renang, biliar, dan panahan ini berharap suatu hari nanti dia bisa menyajikan lebih banyak kisah religi yang lebih inspiratif lagi. Perempuan yang aktif di media sosial dengan nama @honeydee1710 atau Honey Dee Queens di Facebook ini berharap agar buku-bukunya bisa menjadi separuh jiwanya yang abadi di bumi, bahkan setelah dia tiada.', ' Honey Dee', ' Elex Media Komputindo', 2023, 'kre.jpg', 'Fiksi'),
(7, 'Peter Pan', 'Peter Pan adalah seorang bocah lelaki yang tidak ingin tumbuh dewasa. Ia tinggal di Neverland, sebuah dunia magis yang dihuni oleh peri, bajak laut, dan makhluk-makhluk lain. Suatu malam, Peter Pan mengunjungi kamar Wendy Darling dan adik-adiknya, John dan Michael, dan mengajak mereka untuk pergi bersamanya ke Neverland.\r\n\r\nDi Neverland, mereka bertemu dengan berbagai karakter menarik, termasuk peri Tinker Bell; kelompok Anak Hilang; dan musuh bebuyutan Peter, Kapten Hook.\r\n\r\nSeiring waktu, Wendy dan adik-adiknya mulai merindukan rumah, dan mereka menyadari bahwa meskipun Neverland menawarkan petualangan dan kebebasan, ada nilai penting dalam keluarga dan tempat yang mereka sebut rumah. Pada akhirnya, Wendy dan adik-adiknya memutuskan untuk kembali ke dunia nyata, meninggalkan Peter Pan di Neverland, yang melambangkan pilihan antara masa kanak-kanak dan kedewasaan.\r\n\r\n\r\nTahun Terbit : Cetakan Pertama, Desember 2024\r\n\r\nPernahkah Anda terpikir betapa menariknya dunia yang terbuka lebar lewat lembaran buku? Membaca bukan hanya kegiatan rutin, tetapi juga petualangan tak terbatas ke dalam imajinasi dan pengetahuan.\r\n\r\nMembaca mengasah pikiran, membuka wawasan, dan memperkaya kosakata. Ini adalah pintu menuju dunia di luar kita yang tak terbatas.\r\n\r\nTetapkan waktu khusus untuk membaca setiap hari. Dari membaca sebelum tidur hingga menyempatkan waktu di pagi hari, kebiasaan membaca dapat dibentuk dengan konsistensi.\r\n\r\nPilih buku sesuai minat dan level literasi. Mulailah dengan buku yang sesuai dengan keinginan dan kemampuan membaca.\r\n\r\nTemukan tempat yang tenang dan nyaman untuk membaca. Lampu yang cukup, kursi yang nyaman, dan sedikit musik pelataran bisa menciptakan pengalaman membaca yang lebih baik.\r\n\r\nBuat catatan atau jurnal tentang buku yang telah Anda baca. Tuliskan pemikiran, kesan, dan pelajaran yang Anda dapatkan.', ' J.M. Barrie', 'Anak Hebat Indonesia', 2024, 'ptr.jpg', 'Fiksi'),
(8, 'Dilan: Dia adalah Dilanku Tahun 1990', 'Novel “Dilan: Dia adalah Dilanku Tahun 1990” menceritakan kilas balik Milea pada tahun 1990. Pada tahun tersebut, Milea hanyalah remaja SMA pindahan dari Jakarta ke Bandung. Milea memiliki kehidupan layaknya anak SMA. Semuanya berubah ketika seorang remaja pria bernama Dilan mengajaknya berkenalan di suatu siang pada saat jam pulang sekolah. Kisah pun bergulir. Milea mulai menemukan keseruan berkenalan dengan Dilan yang penuh kejutan dan memiliki segala cara untuk membahagiakan dirinya.\r\n\r\nDari perkenalannya inilah Milea dapat melihat sosok Dilan dari sisi yang berbeda bukan Dilan sebagai biang onar di sekolahnya. Milea juga mendapat kesempatan berkenalan dengan keluarga Dilan yang unik dan mengenal Bundanya Dilan lebih dekat. Ide cerita yang disuguhkan novel ini terbilang sederhana yakni kisah cinta anak SMA. Namun yang menarik adalah bagaimana sang penulis novel ini, Pidi Baiq, merajut kisah tersebut dengan percakapan humoris dan romantis. Pembaca akan masuk ke dalam cara pandang Milea melihat sosok Dilan yang usil, pemberani dan setia kawan.\r\n\r\nDi novel ini banyak cerita yang membuat para remaja senyum-senyum sendiri saat membaca novel ini, karena cerita yang unik dan romantis. Banyak kata-kata dari novel ini yang yang puitis, seperti:\r\n“Milea kamu cantik, tapi aku belum mencintaimu. Nggak tahu kalau sore. Tunggu aja.” (Dilan 1990)\r\n“Milea jangan pernah bilang ke aku ada yang menyakitimu. Nanti besoknya, orang itu akan hilang.” (Dilan 1990)\r\n“Cinta sejati adalah kenyamanan, kepercayaan, dan dukungan. Kalau kamu tidak setuju, aku tidak peduli.” (Dilan 1990).', 'Pidi Baiq', 'Mizan Publishing', 2015, 'dilan.jpg', 'Novel'),
(10, 'After Lives', '\"Megan Marshallâs innovative books, includingÂ , Marshall turns her narrative gift to her ownÂ art,Â life,Â and the people in it.\r\n\r\nIn each of six essays, Marshall reinvents the personal essay form, as a portal to the past and its lessons for living into the future. The bookâs brilliant, assured interplay between memoir and biography places surprising characters on the page, including the twelfth-century Buddhist hermitÂ Kamo no Chomei, a reassuring spiritual presence for Marshall during several otherwise deracinating months in Kyoto.Â In her stunning coming-of-age tale, âFree for a While,âÂ set in 1970s California, Marshall interweaves the story of her adolescence with that of Black Power martyr Jonathan Jackson, the authorâs AP history classmate, gunned down at seventeen in a failed attempt to free his famed older brother George from prison in the case that put Angela Davis on the FBIâs Most Wanted list.Â \r\n\r\nHere too is the authorâs passion for the biographical chase, and for the mysteries at its heart.Â She tells the astonishing story of viewingÂ the disinterred remains of her one-time subject Sophia Peabody Hawthorne, wife of Nathaniel, and their daughter Una,Â the truths of whoseÂ early death Marshall works to reveal.Â \r\n\r\nThroughout these finely wrought essays, Marshall,Â â[at] the front rank of American biographersâ (Dwight Garner,Â ),Â makes palpable her driving impulse to âlearn what I could from others: how to live, how not to live, what it means to live.â\"', 'Megan Marshall', ' Harper Collins', 2025, '67b6940b278c77.10536507.jpg', 'Biografis'),
(12, '#Bincang Akhlak', '\"Buku #Bincang Akhlak ini adalah karya Takdir Alisjahbana Ridwan atau biasa dikenal di Twitter Bang Jek. Di buku ini Bang Jek menceritakan tentang kehidupannya dari kecil hingga akhirnya menikah dengan “My Love”. Ini bukan cerita kehidupan biasa, Bang Jek akan menulis perjalanan Spiritual nya yang dahulu banyak maksiat sampai akhirnya terjadi satu kejadian yang membuatnya hijrah dan meninggalkan segala kemaksiatan. Dikemas dengan sederhana dan humoris sehingga membuat pembaca tidak bosan membacanya, juga banyak pesan positif yang dapat kamu ambil dari kehidupan Bang Jek. Jadi tunggu apalagi? segera beli dan baca buku Bincang Akhlak ini, Selamat membaca !!\r\n\r\nSinopsis\r\n“Bro, kayaknya lagi ada masalah. Ada apa? Coba cerita, kali aja bisa bantu apa gitu,” kataku sambil menatap matanya.\r\n“Iya, nih. Ada orang yang nagih utang ke aku, tapi duitku nggak cukup untuk bayar,” jawabnya sambil balas menatapku.\r\n“Oh, masalah itu. Kurang berapa?”\r\n“Mau bantu, Jek?” Matanya berbinar-binar.\r\n“Berapa emang?”\r\n“Kurang 2 juta.”\r\n\r\nAku langsung ngeluarin dompet, terus ngambil kartu nama. “Di sini ada nomor telepon yang bisa kamu hubungi kalau mau gadai BPKB motor, ya.”\r\nDia langsung pulang nggak pake salam. Sepertinya, suasana hatinya lagi nggak enak.\r\n\r\n#Bincang Akhlak merupakan buku komedi karya Takdir Alisyahbana Ridwan atau biasa dipanggil Jek. Adapun isi buku ini tentang perjalanan hidup Jek yang dialami orang lain. Kejadian nyata hanya 100%, sisanya fiksi sekitar 100% juga.\r\n\r\nTahun Terbit : Cetakan Pertama, 2023', 'Takdir Alisyahbana Ridwan', 'Kawah Media', 2023, '67b6ad787379d3.50982295.jpg', 'Humor'),
(13, 'Koloni Rajasa and the Flag Bearer', 'Siasat demi siasat telah tercipta dari benak orang-orang pemilik kedudukan tanpa menyadari siapa lawan maupun kawan. Sangkakala telah berbunyi mencoreng perdamaian yang sudah diraih berdasar dari sudut pandang Kadari.\r\n\r\nArena sudah digelar, bidak sudah dipasang, namun siapakah yang akan mengibarkan\r\nBENDERA KEMENANGAN?\r\n\r\nKeunggulan :\r\nLanjutan dari Rajasa and the Demon of the Wood\r\nMengangkat tema Kerajaan Singasari di Jawa Timur\r\nImprovisasi dan modifikasi cerita sejarah Ken Arok, pendiri kerajaan Singasari\r\nAction dan goresan gambar yang keren\r\n\r\n****\r\n\r\nDi antara jenis buku lainnya, komik memang disukai oleh semua kalangan mulai dari anak kecil hingga orang dewasa. Alasan komik lebih disukai oleh banyak orang karena disajikan dengan penuh dengan gambar dan cerita yang mengasyikan sehingga mampu menghilangkan rasa bosan di kala waktu senggang.\r\n\r\nKomik seringkali dijadikan sebagai koleksi dan diburu oleh penggemarnya karena serinya yang cukup terkenal dan kepopulerannya terus berlanjut sampai saat ini. Dalam memilih jenis komik, ada baiknya perhatikan terlebih dahulu ringkasan cerita komik di sampul bagian belakang sehingga sesuai dengan preferensi pribadi pembaca.\r\n\r\nM&C! Publishing adalah penerbit di bawah Divisi Ritel dan Penerbitan Grup Kompas Gramedia, perusahaan penerbitan terbesar di Indonesia. Grup Kompas Gramedia memulai usaha dengan fokus di media cetak. Dalam perkembangannya, perusahaan telah berkembang menjadi kelompok usaha dengan berbagai divisi. Di bidang informasi, grup ini juga merambah ke media elektronik dan multimedia. M&C! Penerbitan telah menerbitkan berbagai judul dan jenis buku: komik, komik pendidikan, buku anak-anak, novel, buku nonfiksi. Salah Satunya seperti komik \"Koloni Rajasa and The Flag Bearer”.', 'Akhmad Fadly', 'm&c!', 2024, '67b6ccfe72a539.66796886.jpg', 'Fiksi'),
(17, 'Si Juki', 'ddd', 'juki', 'Kawah Media', 2025, '67b6e3296e4720.65893245.jpg', 'Komik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `favorit_buku`
--

CREATE TABLE `favorit_buku` (
  `ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `BukuID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategoribuku`
--

CREATE TABLE `kategoribuku` (
  `KategoriID` int(11) NOT NULL,
  `NamaKategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategoribuku`
--

INSERT INTO `kategoribuku` (`KategoriID`, `NamaKategori`) VALUES
(2, 'Novel'),
(3, 'Fiksi'),
(4, 'Humor');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategoribuku_relasi`
--

CREATE TABLE `kategoribuku_relasi` (
  `KategoriBukuID` int(11) NOT NULL,
  `BukuID` int(11) NOT NULL,
  `KategoriID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `koleksipribadi`
--

CREATE TABLE `koleksipribadi` (
  `KoleksiID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `BukuID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `koleksipribadi`
--

INSERT INTO `koleksipribadi` (`KoleksiID`, `UserID`, `BukuID`) VALUES
(1, 12, 3),
(2, 12, 4),
(3, 12, 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `PeminjamanID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `BukuID` int(11) DEFAULT NULL,
  `TanggalPeminjaman` date DEFAULT NULL,
  `TanggalPengembalian` date DEFAULT NULL,
  `Status` enum('Dipinjam','Dikembalikan','','') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ulasanbuku`
--

CREATE TABLE `ulasanbuku` (
  `UlasanID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `BukuID` int(11) NOT NULL,
  `Ulasan` text NOT NULL,
  `Rating` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `UserID` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `NamaLengkap` varchar(255) NOT NULL,
  `Alamat` text NOT NULL,
  `role` enum('administrator','petugas','peminjam','','') NOT NULL,
  `status` enum('aktif','diblokir','','') NOT NULL,
  `Foto` varchar(500) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`UserID`, `username`, `password`, `Email`, `NamaLengkap`, `Alamat`, `role`, `status`, `Foto`) VALUES
(7, 'admin', '$2y$10$jAKEyaBz.cvYpAH7ZaR7DOgFfl6mP2ApNs9vYo97vdAjkd1X/zVc.', 'admin@gmail.com', 'Administrator', 'Indonesia', 'administrator', 'aktif', 'admin.png'),
(9, 'petugas', '$2y$10$a2HCq0y99cH26NTpU9mpluH.NwW0HagWh25mvudx5faBnl8JBtEXe', 'eak@gmail.com', 'yeyee', 'tuban', 'petugas', 'aktif', 'pt.png'),
(14, 'nicho', '$2y$10$EDW3qvREEy4TJ9QszEZBTe1PeM.6ARMSQMHH1X8WBxqWoIctdx5Ze', 'bich0@gmail.com', 'nicho dony', 'indonesia', 'peminjam', 'aktif', 'us.png'),
(15, 'gopal', '$2y$10$GDWRj.yJlbZzBygbKKCVIe8N.2xSoQR3rzCZMgHKUUbQj0vZJ.DmO', 'sss@gmail.com', 'Gopal', 'gfjytjutg', 'peminjam', 'aktif', 'us.png');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`BukuID`);

--
-- Indeks untuk tabel `favorit_buku`
--
ALTER TABLE `favorit_buku`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `UserID` (`UserID`,`BukuID`);

--
-- Indeks untuk tabel `kategoribuku`
--
ALTER TABLE `kategoribuku`
  ADD PRIMARY KEY (`KategoriID`);

--
-- Indeks untuk tabel `kategoribuku_relasi`
--
ALTER TABLE `kategoribuku_relasi`
  ADD PRIMARY KEY (`KategoriBukuID`);

--
-- Indeks untuk tabel `koleksipribadi`
--
ALTER TABLE `koleksipribadi`
  ADD PRIMARY KEY (`KoleksiID`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`PeminjamanID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `BukuID` (`BukuID`);

--
-- Indeks untuk tabel `ulasanbuku`
--
ALTER TABLE `ulasanbuku`
  ADD PRIMARY KEY (`UlasanID`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `buku`
--
ALTER TABLE `buku`
  MODIFY `BukuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `favorit_buku`
--
ALTER TABLE `favorit_buku`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `kategoribuku`
--
ALTER TABLE `kategoribuku`
  MODIFY `KategoriID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `kategoribuku_relasi`
--
ALTER TABLE `kategoribuku_relasi`
  MODIFY `KategoriBukuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `koleksipribadi`
--
ALTER TABLE `koleksipribadi`
  MODIFY `KoleksiID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `PeminjamanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `ulasanbuku`
--
ALTER TABLE `ulasanbuku`
  MODIFY `UlasanID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `user` (`UserID`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`BukuID`) REFERENCES `buku` (`BukuID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
