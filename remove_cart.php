<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $audri_input = json_decode(file_get_contents('php://input'), true);
    $audri_productId = $audri_input['Id_produk'];

    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['Id_produk'] == $audri_productId) {
                unset($_SESSION['cart'][$key]);
                $_SESSION['cart'] = array_values($_SESSION['cart']);
                break;
            }
        }
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
