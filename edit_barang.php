<?php
include 'config.php';

if (isset($_GET['id'])) {
    $Id_produk = $_GET['id']; 

    $sql = "SELECT * FROM produk WHERE Id_produk = $Id_produk";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $produk = $result->fetch_assoc(); 
    } else {
        echo "<script>alert('Produk tidak ditemukan!'); window.location='master_barang.php';</script>";
        exit;
    }
}

if (isset($_POST['submit'])) {
    $Id_produk = $_POST['Id_produk'];
    $nama_produk = $_POST['nama_produk'];
    $harga = $_POST['harga']; 
    $stok = $_POST['stok'];

    $foto = $produk['foto_produk']; 
    
    if (isset($_FILES['foto_produk']) && $_FILES['foto_produk']['error'] == 0) {
        $targetDir = "uploads/produks/";
        $fileName = basename($_FILES['foto_produk']['name']);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['foto_produk']['tmp_name'], $targetFile)) {
                $foto = $fileName; 
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }
 
    $sql = "UPDATE produk 
            SET nama_produk = '$nama_produk', 
                harga = '$harga', 
                stok = '$stok', 
                foto_produk = '$foto'
            WHERE Id_produk = $Id_produk";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Barang berhasil diperbarui!'); window.location='master_barang.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang</title>
    <link rel="stylesheet" href="tambah_data.css">
</head>
<body>
    <div class="container">
        <h2>Edit Barang</h2>
        <form method="POST" action="" enctype="multipart/form-data">

            <input type="hidden" name="Id_produk" value="<?php echo $produk['Id_produk']; ?>">

            <div class="form-group">
                <label for="nama_produk">Nama Produk</label>
                <input type="text" id="nama_produk" name="nama_produk" value="<?php echo $produk['nama_produk']; ?>" required>
            </div>

            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" id="harga" name="harga" value="<?php echo $produk['harga']; ?>">
            </div>

            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" id="stok" name="stok" value="<?php echo $produk['stok']; ?>" required>
            </div>

            <div class="form-group">
                <label for="foto_produk">Foto Produk</label>
                <input type="file" id="foto_produk" name="foto_produk">
                <br>
                <?php if (!empty($produk['foto_produk'])): ?>
                    <img src="uploads/produks/<?php echo $produk['foto_produk']; ?>" alt="Foto Produk" width="100" height="100">
                <?php endif; ?>
            </div>

            <button type="submit" name="submit">Update Barang</button>
            <a href="master_barang.php" class="button">Batal</a>
        </form>
    </div>
</body>
</html>
