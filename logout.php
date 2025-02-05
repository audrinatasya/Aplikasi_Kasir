<?php
session_start();

if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    session_destroy();
    $_SESSION['logout_message'] = 'Anda telah berhasil logout!';
    header("Location: index.php");
    exit();
}

if (isset($_GET['confirm']) && $_GET['confirm'] == 'no') {
    header("Location: index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Logout</title>

    <script type="text/javascript">
        if (confirm('Apakah Anda yakin ingin logout?')) {
            window.location.href = 'logout.php?confirm=yes';
        } else {
            window.location.href = 'dashboard.php?confirm=no';
        }
    </script>
</head>
<body>
</body>
</html>
