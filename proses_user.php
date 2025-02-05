<?php
include 'config.php';

if (isset($_GET['id'])) {
    $Id_user = $_GET['id'];

    if (!isset($_POST['hapus'])) {
        echo "<script>
                if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
                    window.location.href = 'hapus_user.php?id=$Id_user&hapus=true';
                } else {
                    window.location.href = 'master_user.php';
                }
              </script>";
    }

}
?>
