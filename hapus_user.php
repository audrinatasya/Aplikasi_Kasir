<?php
include 'config.php';


    if (isset($_GET['hapus']) && $_GET['hapus'] == 'true') {
        $audri_Id_user = $_GET['id'];

        $sql = "DELETE FROM user WHERE Id_user = $audri_Id_user";

        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Barang berhasil dihapus!'); window.location='master_user.php';</script>";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
?>
