<?php
include 'config.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';

// cegah SQL injection
$search = $conn->real_escape_string($search);

$query = "SELECT * FROM pelanggan WHERE nama_pelanggan LIKE '%$search%'";
$result = $conn->query($query);

$pelanggan = [];
while ($row = $result->fetch_assoc()) {
    $pelanggan[] = $row;
}

echo json_encode($pelanggan);
?>