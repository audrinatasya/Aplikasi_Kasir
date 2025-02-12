<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $id_produk = $input['Id_produk'];
    $produk_sudah_ada = false;

    foreach ($_SESSION['cart'] as $key => $item) {
        if ((int) $item['Id_produk'] === (int) $id_produk) {
            $_SESSION['cart'][$key]['jumlah'] += $input['jumlah'];
            $produk_sudah_ada = true;
            break;
        }
    }

    if (!$produk_sudah_ada) {
        $_SESSION['cart'][] = $input;
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
