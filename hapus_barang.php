<?php
include 'config.php';


    if (isset($_GET['hapus']) && $_GET['hapus'] == 'true') {
        $audri_Id_produk = $_GET['id'];

        $sql = "DELETE FROM produk WHERE Id_produk = $audri_Id_produk";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Barang berhasil dihapus!'); window.location='master_barang.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
?>
