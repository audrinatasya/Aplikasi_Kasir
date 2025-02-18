<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $audri_username = mysqli_real_escape_string($conn, $_POST['username']);
    $audri_password = mysqli_real_escape_string($conn, $_POST['password']);

    $audri_password_hashed = md5($audri_password);

    $audri_query = "SELECT user.username, role.nama_role 
              FROM user 
              JOIN role ON user.Id_role = role.Id_role 
              WHERE user.username = '$audri_username' AND user.password = '$audri_password_hashed'";

    $audri_result = mysqli_query($conn, $audri_query);

    if ($audri_result && mysqli_num_rows($audri_result) > 0) {
        $audri_user = mysqli_fetch_assoc($audri_result);

        $_SESSION['username'] = $audri_user['username'];
        $_SESSION['role'] = $audri_user['nama_role'];

        header("Location: dashboard.php?message=" . urlencode("🎉 Selamat datang, {$audri_user['username']}! Anda berhasil login."));
        exit();
    } else {
        header("Location: index.php?message=" . urlencode("❌ Password / Username salah! / Silakan coba lagi."));
        exit();
    }
} else {
    header("Location: index.php?message=" . urlencode("⚠️ Username tidak ditemukan! Periksa kembali."));
    exit();
}
?>
