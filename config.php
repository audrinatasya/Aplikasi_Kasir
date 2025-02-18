<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$audri_host = "localhost";
$audri_user = "root";
$audri_password = "";
$audri_dbname = "audri_kasir";

$conn = new mysqli($audri_host, $audri_user, $audri_password, $audri_dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>