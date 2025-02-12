<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include "config.php";  

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$role = isset($_SESSION['role']) ? $_SESSION['role'] : null;

if (!$role) {
    header("Location: logout.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <img class="img-logo" src="img/logo.JPG" alt="Logo">
        <h2><span></span> <?= htmlspecialchars("Bubble Scarf") ?></h2>
    </div>

    <div class="sidebar-menu">
        <ul>
            <li>
                <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
                    <span class="las la-igloo"> </span>
                    <span>Dashboard</span>
                </a>
            </li>
            <?php if ($role === 'Administrator'): ?>
                <li>
                    <a href="master_user.php" class="<?= ($current_page == 'master_user.php') ? 'active' : '' ?>">
                        <span class="las la-users"> </span>
                        <span>Manage Users</span>
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a href="master_barang.php" class="<?= ($current_page == 'master_barang.php') ? 'active' : '' ?>">
                    <span class="las la-shopping-bag"></span>
                    <span>Manage Barang</span>
                </a>
            </li>
            <li>
                <a href="cart.php" class="<?= ($current_page == 'cart.php') ? 'active' : '' ?>">
                    <span class="las la-receipt"></span>
                    <span>Transaksi</span>
                </a>
            </li>
            <li>
                <a href="laporan.php" class="<?= ($current_page == 'laporan.php') ? 'active' : '' ?>">
                    <span class="las la-clipboard-list"></span>
                    <span>Laporan</span>
                </a>
            </li>
            <li class="logout">
                <a href="logout.php" class="<?= ($current_page == 'logout.php') ? 'active' : '' ?>">
                    <span class="las la-sign-out-alt"></span>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
    let toggleButton = document.querySelector('.bars');
    let sidebar = document.getElementById('sidebar');
    let header = document.querySelector('header');
    let mainContent = document.querySelector('.main-content');

    if (toggleButton && sidebar && header && mainContent) {
        toggleButton.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            header.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        });
    } else {
        console.error("Elemen sidebar atau tombol toggle tidak ditemukan.");
    }
});

</script>

</body>
</html>
