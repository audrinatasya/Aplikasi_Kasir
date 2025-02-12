<?php
session_start();
include 'config.php'; 

if (empty($_SESSION['cart'])) {
   
    header('Location: cart.php');
    exit; 
}


if (isset($_GET['action']) && isset($_GET['id_produk'])) {
    $id_produk = (int) $_GET['id_produk'];
    
    foreach ($_SESSION['cart'] as $key => $item) {
        if ((int) $item['Id_produk'] === $id_produk) {
            if ($_GET['action'] === 'tambah') {
                $_SESSION['cart'][$key]['jumlah'] += 1;
            } elseif ($_GET['action'] === 'kurang') {
                if ($_SESSION['cart'][$key]['jumlah'] > 1) {
                    $_SESSION['cart'][$key]['jumlah'] -= 1;
                } else {
                    unset($_SESSION['cart'][$key]);
                }
            }
            $_SESSION['cart'] = array_values($_SESSION['cart']); 
            break;
        }
    }
    header("Location: transaksi.php");
    exit;
}

if (isset($_GET['hapus_item']) && isset($_SESSION['cart'])) {
    $id_produk_hapus = (int) $_GET['hapus_item'];

    foreach ($_SESSION['cart'] as $key => $item) {
        if ((int) $item['Id_produk'] === $id_produk_hapus) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }

    $_SESSION['cart'] = array_values($_SESSION['cart']);

    header("Location: transaksi.php");
    exit;
}

$cart = $_SESSION['cart'];

if (isset($_SESSION['user_id'])) {
    $id_pelanggan = $_SESSION['user_id'];
} else {
    $result = mysqli_query($conn, "SELECT IFNULL(MAX(Id_pelanggan), 0) + 1 AS next_id FROM pelanggan");
    $row = mysqli_fetch_assoc($result);
    $id_pelanggan = $row['next_id'];
}

$total_harga = array_reduce($cart, function ($carry, $item) {
    return $carry + ($item['harga'] * $item['jumlah']);
}, 0);

//$tanggal_penjualan = date('Y-m-d H:i:s');
$tanggal_penjualan = date('Y-m-d');
$kembalian = 0;
$error_message = '';


$nama_pelanggan = '';
$alamat = '';
$nomor_telepon = '';
$jumlah_pembayaran = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jumlah_pembayaran'])) {
    $nama_pelanggan = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $nomor_telepon = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);
    $jumlah_pembayaran = (float) $_POST['jumlah_pembayaran'];

    if ($jumlah_pembayaran < $total_harga) {
        $error_message = "Maaf, uang yang Anda bayarkan kurang. Silakan masukkan jumlah yang cukup.";
    } else {
        if (!isset($_SESSION['user_id'])) {
            $sql_pelanggan = "INSERT INTO pelanggan (nama_pelanggan, alamat, no_telepon) 
                              VALUES ('$nama_pelanggan', '$alamat', '$nomor_telepon')";
            mysqli_query($conn, $sql_pelanggan);
            $id_pelanggan = mysqli_insert_id($conn);
        }

        $sql_penjualan = "INSERT INTO penjual (tanggal_penjualan, total_harga, Id_pelanggan) 
                          VALUES ('$tanggal_penjualan', '$total_harga', '$id_pelanggan')";
        mysqli_query($conn, $sql_penjualan);
        $id_penjualan = mysqli_insert_id($conn);

        foreach ($cart as $item) {
            $subtotal = $item['harga'] * $item['jumlah'];
            $id_produk = $item['Id_produk'];
            $jumlah_produk = $item['jumlah'];

            $sql_detail = "INSERT INTO detail_penjualan (Id_penjualan, Id_produk, jumlah_produk, subtotal) 
                           VALUES ('$id_penjualan', '$id_produk', '$jumlah_produk', '$subtotal')";
            mysqli_query($conn, $sql_detail);

            $sql_update_stock = "UPDATE produk SET stok = stok - $jumlah_produk WHERE Id_produk = '$id_produk'";
            mysqli_query($conn, $sql_update_stock);
        }

        $kembalian = $jumlah_pembayaran - $total_harga;
        unset($_SESSION['cart']);
    }
}

$daftar_barang = isset($cart) ? $cart : [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran</title>
    <link rel="stylesheet" href="transaksi.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <style>
         @media print {
        body * {
            visibility: hidden;
        }
        .transaction-success, .transaction-success * {
            visibility: visible;
        }
        .transaction-success {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
                display: none !important;
            }
    }
    </style>
</head>
<body>
<div class="container">
    <h2>Keranjang Belanja</h2>
    <div class="product-list">
        <?php foreach ($cart as $item): ?>
            <div class="product-card">
                <h3><?= htmlspecialchars($item['nama_produk']) ?></h3>
                <p><strong>Harga:</strong> Rp. <?= number_format($item['harga'], 0, ',', '.') ?></p>
                <p><strong>Jumlah:</strong> <?= $item['jumlah'] ?></p>
                <p><strong>Total:</strong> Rp. <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></p>
                
                <!-- Tombol Tambah dan Kurang -->
                <a href="?action=kurang&id_produk=<?= $item['Id_produk'] ?>" >-</a>
                <a href="?action=tambah&id_produk=<?= $item['Id_produk'] ?>" >+</a>
                <a href="?hapus_item=<?= $item['Id_produk'] ?>" class="delete-button"><i class='uil uil-trash-alt'></i></a>

            </div>
        <?php endforeach; ?>
    </div>
    <div class="grand-total">
        <strong>Grand Total:</strong> Rp. <?= number_format($total_harga, 0, ',', '.') ?>
    </div>
    <a href="cart.php"><button>Kembali ke Keranjang</button></a>
</div>

<!-- Form Data Pembeli -->
<div class="container">
    <h2>Data Pembeli</h2>
    <form method="POST">
        <div class="form-group">
            <label>ID Pelanggan:</label>
            <input type="text" value="<?= htmlspecialchars($id_pelanggan) ?>" disabled>
        </div>
        <div class="form-group">
            <label>Nama Pelanggan:</label>
            <input type="text" name="nama_pelanggan" value="<?= htmlspecialchars($nama_pelanggan) ?>" required>
        </div>
        <div class="form-group">
            <label>Alamat:</label>
            <input type="text" name="alamat" value="<?= htmlspecialchars($alamat) ?>" required>
        </div>
        <div class="form-group">
            <label>Nomor Telepon:</label>
            <input type="number" name="nomor_telepon" value="<?= htmlspecialchars($nomor_telepon) ?>" required>
        </div>

        <!-- Data Pembayaran -->
        <div class="form-group">
            <label>Total Harga:</label>
            <input type="text" value="Rp. <?= number_format($total_harga, 0, ',', '.') ?>" disabled>
        </div>
        <div class="form-group">
            <label>Jumlah Bayar:</label>
            <input type="number" name="jumlah_pembayaran" value="<?= htmlspecialchars($jumlah_pembayaran) ?>" required>
        </div>

        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?= $error_message ?>
            </div>
        <?php endif; ?>

        <button type="submit">Bayar</button>
    </form>
</div>

<!-- Transaksi Berhasil -->
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($jumlah_pembayaran) && empty($error_message)): ?>
    <div class="transaction-success">
        <h2>Transaksi Berhasil</h2>
       
        <div class="info">
            <p><strong>Nama Pelanggan:</strong><span><?= htmlspecialchars($nama_pelanggan) ?></span></p>
            <p><strong>Tanggal Pembelian:</strong><span><?= $tanggal_penjualan ?></span></p>
        </div>

        <hr class="divider">
        <h4>Detail Barang:</h4>
        <ul class="item-list">
            <?php foreach ($daftar_barang as $barang): 
                $subtotal = $barang['jumlah'] * $barang['harga'];
            ?>
                <li>
                    <span><?= htmlspecialchars($barang['nama_produk']) ?> (<?= $barang['jumlah'] ?> pcs)</span>
                    <span>Rp. <?= number_format($subtotal, 0, ',', '.') ?></span> 
                </li>
            <?php endforeach; ?>
        </ul>

        <hr class="divider">
        <div class="info">
            <p><strong>Total Harga:</strong><span>Rp. <?= number_format($total_harga, 0, ',', '.') ?></span></p>
            <p><strong>Jumlah Bayar:</strong><span>Rp. <?= number_format($jumlah_pembayaran, 0, ',', '.') ?></span></p>
            <p><strong>Kembalian:</strong><span>Rp. <?= number_format($kembalian, 0, ',', '.') ?></span></p>
        </div>
        <hr class="divider">
        <div class="action-buttons no-print">
            <a href="cart.php" class="btn">Kembali ke Transaksi</a>
            <button onclick="window.print()" class="btn">Cetak Struk</button>
        </div>
    </div>
<?php endif; ?>

</body>
</html>

</body>
</html>