<?php
session_start();
include 'config.php'; 

$audri_nama_petugas = isset($_SESSION['username']) ? $_SESSION['username'] : 'Petugas Tidak Dikenal';

if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit; 
}

if (isset($_GET['action']) && isset($_GET['Id_produk'])) {
    $audri_Id_produk = (int) $_GET['Id_produk'];
    
    foreach ($_SESSION['cart'] as $key => $audri_item) {
        if ((int) $audri_item['Id_produk'] === $audri_Id_produk) {
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
    $audri_Id_produk_hapus = (int) $_GET['hapus_item'];

    foreach ($_SESSION['cart'] as $key => $audri_item) {
        if ((int) $audri_item['Id_produk'] === $audri_Id_produk_hapus) {
            unset($_SESSION['cart'][$key]);
            break;
        }
    }

    $_SESSION['cart'] = array_values($_SESSION['cart']);

    header("Location: transaksi.php");
    exit;
}

$audri_cart = $_SESSION['cart'];

if (isset($_SESSION['Id_user'])) {
    $audri_Id_pelanggan = $_SESSION['Id_user'];
} else {
    $audri_result = mysqli_query($conn, "SELECT IFNULL(MAX(Id_pelanggan), 0) + 1 AS next_id FROM pelanggan");
    $audri_row = mysqli_fetch_assoc($audri_result);
    $audri_Id_pelanggan = $audri_row['next_id'];
}

$audri_totalHarga = array_reduce($audri_cart, function ($carry, $audri_item) {
    return $carry + ($audri_item['harga'] * $audri_item['jumlah']);
}, 0);

$audri_tanggalPenjualan = date('Y-m-d');
$audri_kembalian = 0;
$error_message = '';

$audri_nama_pelanggan = '';
$audri_alamat = '';
$audri_nomor_telepon = '';
$audri_jumlah_pembayaran = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jumlah_pembayaran'])) {
    $audri_jumlah_pembayaran = (float) $_POST['jumlah_pembayaran'];

    if ($audri_jumlah_pembayaran < $audri_totalHarga) {
        $error_message = "Maaf, uang yang Anda bayarkan kurang. Silakan masukkan jumlah yang cukup.";
    } else {
    
        if (isset($_POST['jenis_pelanggan'])) {
            if ($_POST['jenis_pelanggan'] === 'member') {
                if (isset($_POST['nama_pelanggan_member'])) {
                    $audri_Id_pelanggan = (int) $_POST['nama_pelanggan_member'];
                    
              
                    error_log("ID Pelanggan: " . $audri_Id_pelanggan);
                 
                    $result = mysqli_query($conn, "SELECT nama_pelanggan FROM pelanggan WHERE Id_pelanggan = $audri_Id_pelanggan");
                    $row = mysqli_fetch_assoc($result);
                    
              
                    error_log("Hasil Query: " . print_r($row, true));
                    
                
                    if ($row) {
                        $audri_nama_pelanggan = $row['nama_pelanggan'];
                    } else {
                        $error_message = "Pelanggan tidak ditemukan.";
                    }
                } else {
                    $error_message = "ID pelanggan tidak ditemukan.";
                }
            } elseif ($_POST['jenis_pelanggan'] === 'new_member') {
                $audri_nama_pelanggan = mysqli_real_escape_string($conn, $_POST['nama_pelanggan']);
                $audri_alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
                $audri_nomor_telepon = mysqli_real_escape_string($conn, $_POST['nomor_telepon']);

                $audri_sql_pelanggan = "INSERT INTO pelanggan (nama_pelanggan, alamat, no_telepon) 
                                        VALUES ('$audri_nama_pelanggan', '$audri_alamat', '$audri_nomor_telepon')";
                mysqli_query($conn, $audri_sql_pelanggan);
                $audri_Id_pelanggan = mysqli_insert_id($conn);
            } else {
                $audri_Id_pelanggan = NULL; 
            }
        }


        $audri_sql_penjualan = "INSERT INTO penjual (tanggal_penjualan, total_harga, Id_pelanggan) 
                                VALUES ('$audri_tanggalPenjualan', '$audri_totalHarga', '$audri_Id_pelanggan')";
        mysqli_query($conn, $audri_sql_penjualan);
        $audri_ID_penjualan = mysqli_insert_id($conn);

   
        foreach ($audri_cart as $audri_item) {
            $audri_subtotal = $audri_item['harga'] * $audri_item['jumlah'];
            $audri_Id_produk = $audri_item['Id_produk'];
            $audri_jumlah_produk = $audri_item['jumlah'];

            $audri_sql_detail = "INSERT INTO detail_penjualan (Id_penjualan, Id_produk, jumlah_produk, subtotal) 
                                 VALUES ('$audri_ID_penjualan', '$audri_Id_produk', '$audri_jumlah_produk', '$audri_subtotal')";
            mysqli_query($conn, $audri_sql_detail);

            $audri_sql_update_stock = "UPDATE produk SET stok = stok - $audri_jumlah_produk WHERE Id_produk = '$audri_Id_produk'";
            mysqli_query($conn, $audri_sql_update_stock);
        }

        $audri_kembalian = $audri_jumlah_pembayaran - $audri_totalHarga;
        unset($_SESSION['cart']);
    }
}

$audri_daftarBarang = isset($audri_cart) ? $audri_cart : [];
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
                left: 50%;
                top: 0;
                transform: translateX(-50%);
                text-align: center;
                width: 100%;
                margin-top: 20px; 
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
        <?php foreach ($audri_cart as $audri_item): ?>
            <div class="product-card">
                <h3><?= htmlspecialchars($audri_item['nama_produk']) ?></h3>
                <p><strong>Harga:</strong> Rp. <?= number_format($audri_item['harga'], 0, ',', '.') ?></p>
                <p><strong>Jumlah:</strong> <?= $audri_item['jumlah'] ?></p>
                <p><strong>Total:</strong> Rp. <?= number_format($audri_item['harga'] * $audri_item['jumlah'], 0, ',', '.') ?></p>
                
                <!-- Tombol Tambah dan Kurang -->
                <a href="?action=kurang&Id_produk=<?= $audri_item['Id_produk'] ?>" >-</a>
                <a href="?action=tambah&Id_produk=<?= $audri_item['Id_produk'] ?>" >+</a>
                <a href="?hapus_item=<?= $audri_item['Id_produk'] ?>" class="delete-button"><i class='uil uil-trash-alt'></i></a>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="grand-total">
        <strong>Grand Total:</strong> Rp. <?= number_format($audri_totalHarga, 0, ',', '.') ?>
    </div>
    <a href="cart.php"><button>Kembali ke Keranjang</button></a>
</div>

<!-- Form Data Pembeli -->
<div class="container">
    <h2>Data Pembeli</h2>
    <form method="POST">
        <div class="radio-group">
            <label class="group-label">Jenis Pelanggan:</label>
            
            <div class="radio-item">
                <input type="radio" name="jenis_pelanggan" value="member" id="memberRadio">
                <label for="memberRadio">Member</label>
            </div>
            
            <div class="radio-item">
                <input type="radio" name="jenis_pelanggan" value="new_member" id="newMemberRadio">
                <label for="newMemberRadio">New Member</label>
            </div>
            
            <div class="radio-item">
                <input type="radio" name="jenis_pelanggan" value="no_member" id="noMemberRadio">
                <label style="margin-bottom: 20px;" for="noMemberRadio">No Member</label>
            </div>
        </div>

        <div id="memberForm" style="display:none;">
            <div class="select-member">
                <label>Nama Pelanggan:</label>
                <input type="text" id="searchPelanggan" placeholder="Cari nama pelanggan..." autocomplete="off">
                <div id="pelangganResults" class="dropdown-content"></div>
            </div>
            <div class="form-group">
                <label>Alamat:</label>
                <input type="text" id="alamat_member" disabled>
            </div>
            <div class="form-group">
                <label>Nomor Telepon:</label>
                <input type="text" id="nomor_telepon_member" disabled>
            </div>
            <!-- Input hidden untuk menyimpan ID pelanggan -->
            <input type="hidden" name="nama_pelanggan_member" id="nama_pelanggan_member">
        </div>

        <div id="newMemberForm" style="display:none;">
            <div class="form-group">
                <label>Nama Pelanggan:</label>
                <input type="text" name="nama_pelanggan" value="<?= htmlspecialchars($audri_nama_pelanggan) ?>">
            </div>
            <div class="form-group">
                <label>Alamat:</label>
                <input type="text" name="alamat" value="<?= htmlspecialchars($audri_alamat) ?>">
            </div>
            <div class="form-group">
                <label>Nomor Telepon:</label>
                <input type="number" name="nomor_telepon" value="<?= htmlspecialchars($audri_nomor_telepon) ?>">
            </div>
        </div>

        <!-- Data Pembayaran -->
        <div class="form-group">
            <label>Total Harga:</label>
            <input type="text" value="Rp. <?= number_format($audri_totalHarga, 0, ',', '.') ?>" disabled>
        </div>
        <div class="form-group">
            <label>Jumlah Bayar:</label>
            <input type="number" name="jumlah_pembayaran" value="<?= htmlspecialchars($audri_jumlah_pembayaran) ?>" required>
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
<?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($audri_jumlah_pembayaran) && empty($error_message)): ?>
    <div class="transaction-success">
        <h2>Transaksi Berhasil</h2>
       
        <div class="info">
            <p><strong>Nama Petugas:</strong><span><?= htmlspecialchars($audri_nama_petugas) ?></span></p>
            <p><strong>Nama Pelanggan:</strong><span><?= htmlspecialchars($audri_nama_pelanggan) ?></span></p>
            <p><strong>Tanggal Pembelian:</strong><span><?= $audri_tanggalPenjualan ?></span></p>
        </div>

        <hr class="divider">
        <h4>Detail Barang:</h4>
        <ul class="item-list">
            <?php foreach ($audri_daftarBarang as $audri_barang): 
                $audri_subtotal = $audri_barang['jumlah'] * $audri_barang['harga'];
            ?>
                <li>
                    <span><?= htmlspecialchars($audri_barang['nama_produk']) ?> (<?= $audri_barang['jumlah'] ?> pcs)</span>
                    <span>Rp. <?= number_format($audri_subtotal, 0, ',', '.') ?></span> 
                </li>
            <?php endforeach; ?>
        </ul>

        <hr class="divider">
        <div class="info">
            <p><strong>Total Harga:</strong><span>Rp. <?= number_format($audri_totalHarga, 0, ',', '.') ?></span></p>
            <p><strong>Jumlah Bayar:</strong><span>Rp. <?= number_format($audri_jumlah_pembayaran, 0, ',', '.') ?></span></p>
            <p><strong>Kembalian:</strong><span>Rp. <?= number_format($audri_kembalian, 0, ',', '.') ?></span></p>
        </div>
        <hr class="divider">
        <div class="action-buttons no-print">
            <a href="cart.php" class="btn">Kembali ke Transaksi</a>
            <button onclick="window.print()" class="btn">Cetak Struk</button>
        </div>
    </div>
<?php endif; ?>

<script>
    document.getElementById('memberRadio').addEventListener('change', function() {
        document.getElementById('memberForm').style.display = 'block';
        document.getElementById('newMemberForm').style.display = 'none';
    });

    document.getElementById('newMemberRadio').addEventListener('change', function() {
        document.getElementById('memberForm').style.display = 'none';
        document.getElementById('newMemberForm').style.display = 'block';
    });

    document.getElementById('noMemberRadio').addEventListener('change', function() {
        document.getElementById('memberForm').style.display = 'none';
        document.getElementById('newMemberForm').style.display = 'none';
    });

</script>

<script src="member.js"> </script>

</body>
</html>