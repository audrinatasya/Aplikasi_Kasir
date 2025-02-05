<?php
include 'config.php';


    if (isset($_GET['hapus']) && $_GET['hapus'] == 'true') {
        $Id_user = $_GET['id'];

        $sql = "DELETE FROM user WHERE Id_user = $Id_user";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Barang berhasil dihapus!'); window.location='master_user.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
?>
