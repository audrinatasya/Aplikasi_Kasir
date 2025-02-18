<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $audri_input = json_decode(file_get_contents('php://input'), true);

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $audri_Id_produk = $audri_input['Id_produk'];
    $audri_produkSudahAda = false;

    foreach ($_SESSION['cart'] as $key => $item) {
        if ((int) $item['Id_produk'] === (int) $audri_Id_produk) {
            $_SESSION['cart'][$key]['jumlah'] += $audri_input['jumlah'];
            $audri_produkSudahAda = true;
            break;
        }
    }

    if (!$audri_produkSudahAda) {
        $_SESSION['cart'][] = $audri_input;
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
