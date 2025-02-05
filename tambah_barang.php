<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang</title>
    <link rel="stylesheet" href="tambah_data.css">
</head>
<body>

<?php
include 'config.php';

if (isset($_POST['submit'])) {
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];

    if (!empty($_FILES['foto_produk']['name'])) {
        $namaFile = 'foto_produk_' . time() . '.' . pathinfo($_FILES['foto_produk']['name'], PATHINFO_EXTENSION);
        $pathSimpan = 'uploads/produks/' . $namaFile;

        if (move_uploaded_file($_FILES['foto_produk']['tmp_name'], $pathSimpan)) {
            $query = "INSERT INTO produk (nama_produk, harga, stok, foto_produk) 
                      VALUES ('$nama_produk', '$harga', '$stok', '$namaFile')";

            if (mysqli_query($conn, $query)) {
                echo "<script>alert('Barang berhasil ditambahkan!'); window.location='master_barang.php';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan data ke database!');</script>";
            }
        } else {
            echo "<script>alert('Gagal mengunggah foto!');</script>";
        }
    } else {
        echo "<script>alert('Foto wajib diunggah!');</script>";
    }
}
?>

<div class="container">
    <h2>Tambah Barang</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="nama_produk">Nama Produk</label>
            <input type="text" id="nama_produk" name="nama_produk" required>
        </div>
        <div class="form-group">
            <label for="harga">Harga</label>
            <input type="number" id="harga" name="harga" required>
        </div>
        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" id="stok" name="stok">
        </div>
        <div class="form-group">
            <label for="foto_produk">Foto Produk</label>
            <input type="file" id="foto_produk" name="foto_produk" accept="image/*" required>
        </div>

        <button type="submit" name="submit">Tambah Barang</button>
        <a href="master_barang.php" class="button">Batal</a>
    </form>
</div>

</body>
</html>
