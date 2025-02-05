<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $password_hashed = md5($password);

    $query = "SELECT user.username, role.nama_role 
              FROM user 
              JOIN role ON user.Id_role = role.Id_role 
              WHERE user.username = '$username' AND user.password = '$password_hashed'";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['nama_role'];

        header("Location: dashboard.php");
        exit();
    } else {
    
        $message = "Username atau Password salah!";
        header("Location: login.php?message=" . urlencode($message));
        exit();
    }
}
?>
