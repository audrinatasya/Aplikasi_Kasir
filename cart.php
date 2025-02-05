<?php
session_start();
include 'config.php';
include 'sidebar.php';

session_regenerate_id(true);

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Tangkap keyword pencarian jika ada
$searchKeyword = $_GET['search'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="cart.css">
</head>
<body>

<header>
    <h2>
        <label>
            <span class="las la-bars"></span>
        </label>
        Transaksi
    </h2>

    <!-- FORM SEARCH -->
    <form method="GET" action="cart.php" class="search-wrapper">
        <input type="text" name="search" placeholder="Cari produk..." value="<?php echo htmlspecialchars($searchKeyword); ?>" class="search-input">
        <button type="submit" class="search-btn"><i class="uil uil-search uil-search"></i></button>
    </form>

    <?php
    $queryUser = "SELECT foto FROM user WHERE username = '$username'";
    $resultUser = mysqli_query($conn, $queryUser);
    $userData = mysqli_fetch_assoc($resultUser);

    if (!$userData) {
        die("User data not found.");
    }
    $fotoUser = !empty($userData['foto']) ? 'uploads/users/' . $userData['foto'] : 'img/default.jpg'; 
    ?>       

    <div class="user-wrapper">
        <img src="<?php echo htmlspecialchars($fotoUser); ?>" width="40px" height="30px" alt="User">
        <div>
            <h4><?php echo htmlspecialchars($username); ?></h4>
            <small><?php echo htmlspecialchars($role); ?></small>
        </div>
    </div>
</header>

<?php
// Query produk
$query = "SELECT Id_produk, nama_produk, harga, stok, foto_produk FROM produk";

// Jika ada keyword search, tambahkan filter
if (!empty($searchKeyword)) {
    $query .= " WHERE nama_produk LIKE '%$searchKeyword%'";
}

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<div class="main-content">
    <main>
        <div class="card-container">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($product = mysqli_fetch_assoc($result)): ?>
                    <div class="card" data-id="<?php echo $product['Id_produk']; ?>" data-name="<?php echo $product['nama_produk']; ?>" data-price="<?php echo $product['harga']; ?>" data-stock="<?php echo $product['stok']; ?>">
                        <img src="uploads/produks/<?php echo $product['foto_produk']; ?>" class="card-img-top" alt="<?php echo $product['nama_produk']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $product['nama_produk']; ?></h5>
                            <p class="card-text">Harga: Rp. <?php echo number_format($product['harga'], 0, ',', '.'); ?> | Stok: <?php echo $product['stok']; ?></p>
                            
                            <input type="number" id="quantity-<?php echo $product['Id_produk']; ?>" min="1" value="1" style="width: 40px; margin-bottom: 10px; margin-left: 10px;">
                            
                            <button class="btn-primary" onclick="addToCart(<?php echo $product['Id_produk']; ?>)">Tambah ke Keranjang</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align: center; font-weight: bold; margin-top: 20px;">Produk tidak ditemukan.</p>
            <?php endif; ?>
        </div>

        <div class="floating-cart">
            <h4>Produk yang Dipilih:</h4>
            <div id="cart-items" class="selected-products">
                <span id="no-selection-text">Belum ada yang dipilih</span>
            </div>
            <button id="checkout-button" style="display: none;" class="btn-primary" onclick="redirectToCheckout()">Lanjut ke Pembayaran</button>
        </div>
    </main>
</div>

<script src="cart.js"></script>

</body>
</html>
