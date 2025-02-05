-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Feb 2025 pada 08.26
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `audri_kasir`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_penjualan`
--

CREATE TABLE `detail_penjualan` (
  `Id_detail` int(11) NOT NULL,
  `Id_penjualan` int(11) NOT NULL,
  `Id_produk` varchar(11) NOT NULL,
  `jumlah_produk` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_penjualan`
--

INSERT INTO `detail_penjualan` (`Id_detail`, `Id_penjualan`, `Id_produk`, `jumlah_produk`, `subtotal`) VALUES
(1, 1, '3', 1, 35000.00),
(2, 1, '5', 1, 35000.00),
(3, 2, '3', 1, 35000.00),
(4, 3, '6', 2, 70000.00),
(5, 3, '6', 2, 70000.00),
(6, 4, '5', 2, 70000.00),
(7, 4, '6', 3, 105000.00),
(8, 5, '1', 2, 70000.00),
(9, 5, '3', 1, 35000.00),
(10, 5, '4', 3, 105000.00),
(11, 6, '2', 1, 35000.00),
(12, 6, '5', 2, 70000.00),
(13, 6, '6', 1, 35000.00),
(14, 7, '1', 2, 70000.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_barang`
--

CREATE TABLE `kategori_barang` (
  `Id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori_barang`
--

INSERT INTO `kategori_barang` (`Id_kategori`, `nama_kategori`) VALUES
(1, 'Segi Empat '),
(2, 'Pashmina Ceruty'),
(3, 'Pashmina Dubai');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pelanggan`
--

CREATE TABLE `pelanggan` (
  `Id_pelanggan` int(11) NOT NULL,
  `nama_pelanggan` varchar(255) NOT NULL,
  `alamat` text NOT NULL,
  `no_telepon` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pelanggan`
--

INSERT INTO `pelanggan` (`Id_pelanggan`, `nama_pelanggan`, `alamat`, `no_telepon`) VALUES
(1, 'a', 'a', '0897537'),
(2, 'p', 'a', '0'),
(3, 'Aud', 'Cmh', '8763'),
(4, 'Audri', 'Bdg', '836749'),
(5, 'Audrina', 'Cimahi', '085937640210'),
(6, 'Tasya', 'Bandung', '0834952819'),
(7, 'zahra', 'padalarang', '08123426452');

-- --------------------------------------------------------

--
-- Struktur dari tabel `penjual`
--

CREATE TABLE `penjual` (
  `Id_penjualan` int(11) NOT NULL,
  `tanggal_penjualan` date NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `Id_pelanggan` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `penjual`
--

INSERT INTO `penjual` (`Id_penjualan`, `tanggal_penjualan`, `total_harga`, `Id_pelanggan`) VALUES
(1, '2025-01-31', 70000.00, 1),
(2, '2025-01-31', 35000.00, 2),
(3, '2025-01-31', 140000.00, 3),
(4, '2025-01-31', 175000.00, 4),
(5, '2025-02-05', 210000.00, 5),
(6, '2025-02-05', 140000.00, 6),
(7, '2025-02-05', 70000.00, 7);

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
--

CREATE TABLE `produk` (
  `Id_produk` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int(11) NOT NULL,
  `foto_produk` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`Id_produk`, `nama_produk`, `harga`, `stok`, `foto_produk`) VALUES
(1, 'Scarf Paris Lavender', 35000.00, 81, 'silver-lavender-coksu.JPG'),
(2, 'Scarf Paris Rose Pink', 35000.00, 36, 'biscuit-rosepink.JPG'),
(3, 'Scarf Paris Silver', 35000.00, 87, 'silver-lavender-coksu.JPG'),
(4, 'Scarf Paris Biscuit', 35000.00, 84, 'biscuit-rosepink.JPG'),
(5, 'Scarf Paris Pistachio', 35000.00, 85, 'khaki-pistachio.JPG'),
(6, 'Scarf Paris Coksu', 35000.00, 82, 'silver-lavender-coksu.JPG');

-- --------------------------------------------------------

--
-- Struktur dari tabel `role`
--

CREATE TABLE `role` (
  `Id_role` int(11) NOT NULL,
  `nama_role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `role`
--

INSERT INTO `role` (`Id_role`, `nama_role`) VALUES
(1, 'Administrator'),
(2, 'Petugas');

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `Id_user` int(11) NOT NULL,
  `Id_role` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `TTL` date NOT NULL,
  `jenis_kelamin` enum('Perempuan','Laki-laki','','') NOT NULL,
  `alamat` text NOT NULL,
  `no_tlp` varchar(13) NOT NULL,
  `foto` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`Id_user`, `Id_role`, `username`, `password`, `TTL`, `jenis_kelamin`, `alamat`, `no_tlp`, `foto`) VALUES
(1, 1, 'Admin', '21232f297a57a5a743894a0e4a801fc3', '2025-01-01', 'Perempuan', 'Bandung', '08132534796', 'user-cewe.jpg'),
(2, 2, 'Petugas', 'afb91ef692fd08c445e8cb1bab2ccf9c', '2025-01-02', 'Laki-laki', 'Cimahi', '0896573421', 'user-cowo.jpg');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  ADD PRIMARY KEY (`Id_detail`),
  ADD KEY `Id_produk` (`Id_produk`),
  ADD KEY `Id_penjualan` (`Id_penjualan`);

--
-- Indeks untuk tabel `kategori_barang`
--
ALTER TABLE `kategori_barang`
  ADD PRIMARY KEY (`Id_kategori`);

--
-- Indeks untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`Id_pelanggan`);

--
-- Indeks untuk tabel `penjual`
--
ALTER TABLE `penjual`
  ADD PRIMARY KEY (`Id_penjualan`),
  ADD KEY `Id_pelanggan` (`Id_pelanggan`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`Id_produk`);

--
-- Indeks untuk tabel `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`Id_role`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`Id_user`),
  ADD KEY `Id_role` (`Id_role`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `detail_penjualan`
--
ALTER TABLE `detail_penjualan`
  MODIFY `Id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `kategori_barang`
--
ALTER TABLE `kategori_barang`
  MODIFY `Id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `Id_pelanggan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `penjual`
--
ALTER TABLE `penjual`
  MODIFY `Id_penjualan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `Id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `role`
--
ALTER TABLE `role`
  MODIFY `Id_role` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `Id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
