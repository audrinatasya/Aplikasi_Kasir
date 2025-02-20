<?php
session_start();
include 'config.php';
include 'sidebar.php';

session_regenerate_id(true);

$audri_username = $_SESSION['username'];
$audri_role = $_SESSION['role'];

$audri_searchKeyword = $_GET['search'] ?? '';

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
            <label id="menu-toggle">
                 <!-- <span class="uil uil-bars"></span> -->
                 <span class="bars"> <img src="asset/bars.svg" width="25px" height="25px"> </span>
            </label>
                 Transaksi
        </h2>

    

    <!-- FORM SEARCH -->
    <form method="GET" action="cart.php" class="search-wrapper">
        <input type="text" name="search" placeholder="Cari produk..." value="<?php echo htmlspecialchars($audri_searchKeyword); ?>" class="search-input">
        <button type="submit" class="search-btn"><i class="uil.search"></i> <img src="asset/search.svg" width="25px" height="25px"  margin-right: 20px;></button> 
    </form>

    <?php
    $audri_queryUser = "SELECT foto FROM user WHERE username = '$audri_username'";
    $audri_resultUser = mysqli_query($conn, $audri_queryUser);
    $audri_userData = mysqli_fetch_assoc($audri_resultUser);

    if (!$audri_userData) {
        die("User data not found.");
    }
    $audri_fotoUser = !empty($audri_userData['foto']) ? 'uploads/users/' . $audri_userData['foto'] : 'img/default.jpg'; 
    ?>       

    <div class="user-wrapper">
        <img src="<?php echo htmlspecialchars($audri_fotoUser); ?>" width="40px" height="30px" alt="User">
        <div>
            <h4><?php echo htmlspecialchars($audri_username); ?></h4>
            <small><?php echo htmlspecialchars($audri_role); ?></small>
        </div>
    </div>
</header>

<?php
$audri_query = "SELECT Id_produk, nama_produk, harga, stok, foto_produk FROM produk";

if (!empty($audri_searchKeyword)) {
    $audri_query .= " WHERE nama_produk LIKE '%$audri_searchKeyword%'";
}

$audri_result = mysqli_query($conn, $audri_query);
if (!$audri_result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<div class="main-content">
    <main>
        <div class="card-container">
            <?php if (mysqli_num_rows($audri_result) > 0): ?>
                <?php while ($audri_product = mysqli_fetch_assoc($audri_result)): ?>
                    <div class="card" data-id="<?php echo $audri_product['Id_produk']; ?>" data-name="<?php echo $audri_product['nama_produk']; ?>" data-price="<?php echo $audri_product['harga']; ?>" data-stock="<?php echo $audri_product['stok']; ?>">
                        <img src="uploads/produks/<?php echo $audri_product['foto_produk']; ?>" class="card-img-top" alt="<?php echo $audri_product['nama_produk']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $audri_product['nama_produk']; ?></h5>
                            <p class="card-text">Harga: Rp. <?php echo number_format($audri_product['harga'], 0, ',', '.'); ?> | Stok: <?php echo $audri_product['stok']; ?></p>
                            
                            <input type="number" id="quantity-<?php echo $audri_product['Id_produk']; ?>" min="1" value="1" style="width: 40px; margin-bottom: 10px; margin-left: 10px;">
                            
                            <button class="btn-primary" onclick="addToCart(<?php echo $audri_product['Id_produk']; ?>)">Tambah ke Keranjang</button>
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
