<?php
include 'config.php';

if (isset($_GET['id'])) {
    $audri_Id_produk = $_GET['id']; 

    $audri_sql = "SELECT * FROM produk WHERE Id_produk = $audri_Id_produk";
    $audri_result = $conn->query($audri_sql);

    if ($audri_result && $audri_result->num_rows > 0) {
        $audri_produk = $audri_result->fetch_assoc(); 
    } else {
        echo "<script>alert('Produk tidak ditemukan!'); window.location='master_barang.php';</script>";
        exit;
    }
}

if (isset($_POST['submit'])) {
    $audri_Id_produk = $_POST['Id_produk'];
    $audri_nama_produk = $_POST['nama_produk'];
    $audri_harga = $_POST['harga']; 
    $audri_stok = $_POST['stok'];

    $audri_foto = $audri_produk['foto_produk']; 
    
    if (isset($_FILES['foto_produk']) && $_FILES['foto_produk']['error'] == 0) {
        $targetDir = "uploads/produks/";
        $fileName = basename($_FILES['foto_produk']['name']);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['foto_produk']['tmp_name'], $targetFile)) {
                $audri_foto = $fileName; 
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    // Ambil stok saat ini dari database
    $current_stok = $audri_produk['stok'];

    // Cek apakah stok yang diinputkan lebih besar dari stok saat ini
    if ($audri_stok > $current_stok) {
        $audri_sql = "UPDATE produk 
                SET nama_produk = '$audri_nama_produk', 
                    harga = '$audri_harga', 
                    stok = '$audri_stok', 
                    foto_produk = '$audri_foto'
                WHERE Id_produk = $audri_Id_produk";

        if ($conn->query($audri_sql) === TRUE) {
            echo "<script>alert('Barang berhasil diperbarui!'); window.location='master_barang.php';</script>";
        } else {
            echo "Error: " . $audri_sql . "<br>" . $conn->error;
        }
    } else {
        echo "<script>alert('Stok hanya bisa ditambahkan, tidak bisa dikurangi!'); window.location='edit_barang.php?id=$audri_Id_produk';</script>";
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

            <input type="hidden" name="Id_produk" value="<?php echo $audri_produk['Id_produk']; ?>">

            <div class="form-group">
                <label for="nama_produk">Nama Produk</label>
                <input type="text" id="nama_produk" name="nama_produk" value="<?php echo $audri_produk['nama_produk']; ?>" required>
            </div>

            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" id="harga" name="harga" value="<?php echo $audri_produk['harga']; ?>">
            </div>

            <div class="form-group">
                <label for="stok">Stok</label>
                <input type="number" id="stok" name="stok" value="<?php echo $audri_produk['stok']; ?>" required>
            </div>

            <div class="form-group">
                <label for="foto_produk">Foto Produk</label>
                <input type="file" id="foto_produk" name="foto_produk">
                <br>
                <?php if (!empty($audri_produk['foto_produk'])): ?>
                    <img src="uploads/produks/<?php echo $audri_produk['foto_produk']; ?>" alt="Foto Produk" width="100" height="100">
                <?php endif; ?>
            </div>

            <button type="submit" name="submit">Update Barang</button>
            <a href="master_barang.php" class="button">Batal</a>
        </form>
    </div>
</body>
</html>