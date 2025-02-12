<?php
session_start();
session_regenerate_id(true);

include 'config.php';
include 'sidebar.php';

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

function getRekap($periode, $tanggal = null, $bulan = null, $tahun = null, $searchKeyword = '') {
    global $conn;

    $where = "1=1"; 
    
    if ($periode === 'perhari' && $tanggal) {
        $where .= " AND DATE(p.tanggal_penjualan) = '$tanggal'";
    } elseif ($periode === 'perbulan' && $bulan && $tahun) {
        $where .= " AND YEAR(p.tanggal_penjualan) = '$tahun' AND MONTH(p.tanggal_penjualan) = '$bulan'";
    } elseif ($periode === 'pertahun' && $tahun) {
        $where .= " AND YEAR(p.tanggal_penjualan) = '$tahun'";
    }

    if (!empty($searchKeyword)) {
        $where .= " AND (pr.nama_produk LIKE '%$searchKeyword%' OR p.Id_penjualan LIKE '%$searchKeyword%')";
    }

    $query = "SELECT 
                p.Id_penjualan, 
                p.tanggal_penjualan, 
                pr.nama_produk, 
                dp.jumlah_produk, 
                pr.harga, 
                dp.subtotal, 
                p.total_harga,
                pl.nama_pelanggan
              FROM penjual p
              JOIN detail_penjualan dp ON p.Id_penjualan = dp.Id_penjualan
              JOIN pelanggan pl ON p.Id_pelanggan = pl.Id_pelanggan
              JOIN produk pr ON dp.Id_produk = pr.Id_produk
              WHERE $where
              ORDER BY p.tanggal_penjualan ASC, p.Id_penjualan ASC";

    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $totalPenjualanQuery = "SELECT SUM(p.total_harga) AS total_penjualan
                            FROM penjual p
                            WHERE $where";

    $totalPenjualanResult = mysqli_query($conn, $totalPenjualanQuery);
    $totalPenjualanData = mysqli_fetch_assoc($totalPenjualanResult);
    
    return [
        'data' => $data,
        'total_penjualan' => $totalPenjualanData['total_penjualan'] ?? 0 
    ];
}

$periode = isset($_GET['periode']) ? $_GET['periode'] : 'perhari';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$bulan = date('m'); 
if ($periode == 'perbulan' && isset($_GET['bulan'])) {
    list($tahun, $bulan) = explode('-', $_GET['bulan']);
}

$result = getRekap($periode, $tanggal, $bulan, $tahun, $searchKeyword);
$rekap_penjualan = $result['data'];
$total_penjualan = $result['total_penjualan'];
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penjualan</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="tabel.css">
</head>
<body>

<header>

        <h2 class="judul-laporan">
            <label id="menu-toggle">
                 <!-- <span class="uil uil-bars"></span> -->
                 <span class="bars"> <img src="asset/bars.svg" width="25px" height="25px"> </span>
            </label>
            Laporan
        </h2>

    <?php
    $queryUser = "SELECT foto FROM user WHERE username = '$username'";
    $resultUser = mysqli_query($conn, $queryUser);
    $userData = mysqli_fetch_assoc($resultUser);

    if (!$userData) {
        die("User data not found.");
    }
    $fotoUser = !empty($userData['foto']) ? 'uploads/users/' . $userData['foto'] : 'img/default.jpg'; ?>       

    <div class="user-wrapper">
        <img src="<?php echo htmlspecialchars($fotoUser); ?>" width="40px" height="30px" alt="User">
        <div>
            <h4><?php echo htmlspecialchars($username); ?></h4>
            <small><?php echo htmlspecialchars($role); ?></small>
        </div>
    </div>
</header>

<div class="main-content">
    <main>        
        <div class="container">
            <div class="container-filter-search" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
               
                <!-- Form Periode -->
                <form class="filter-form" method="GET">
                    <label>Pilih Periode:</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <select name="periode" onchange="this.form.submit()">
                            <option value="perhari" <?= $periode == 'perhari' ? 'selected' : '' ?>>Harian</option>
                            <option value="perbulan" <?= $periode == 'perbulan' ? 'selected' : '' ?>>Bulanan</option>
                            <option value="pertahun" <?= $periode == 'pertahun' ? 'selected' : '' ?>>Tahunan</option>
                        </select>

                        <?php if ($periode == 'perhari'): ?>
                            <input type="date" name="tanggal" value="<?= $tanggal ?>" onchange="this.form.submit()">
                        <?php elseif ($periode == 'perbulan'): ?>
                            <input type="month" name="bulan" value="<?= $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) ?>" onchange="this.form.submit()">
                        <?php elseif ($periode == 'pertahun'): ?>
                            <input type="number" name="tahun" value="<?= $tahun ?>" min="2000" max="<?= date('Y') ?>" onchange="this.form.submit()">
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Form Pencarian -->
                <form method="GET" action="laporan.php" class="search-box" style="margin-right: 50px;">
                    <input type="text" name="search" placeholder="Search produk..." class="search-input" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" >
                    
                    <input type="hidden" name="periode" value="<?= htmlspecialchars($periode) ?>">
                    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                    <input type="hidden" name="bulan" value="<?= htmlspecialchars($bulan) ?>">
                    <input type="hidden" name="tahun" value="<?= htmlspecialchars($tahun) ?>">
                    
                    <button type="submit" class="search-btn">
                    <i class="uil.search"></i> <img src="asset/search.svg" width="20px" height="20px">
                    </button>
                </form>

                <!-- Tombol Print -->
                <button onclick="window.print()" class="no-print">Print Laporan</button>
            </div>

            <div class="print-section" style="text-align: center;">
                <h2 class="judul-laporan">Laporan Penjualan</h2>
                <h3>Bubble Scarf</h3>
                <p>Periode Laporan: 
                    <?php
                    if ($periode == 'perhari') {
                        echo date('d-m-Y', strtotime($tanggal));
                    } elseif ($periode == 'perbulan') {
                        echo date('F Y', strtotime($tahun . '-' . $bulan . '-01'));
                    } elseif ($periode == 'pertahun') {
                        echo $tahun;
                    }
                    ?>
                </p>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Transaksi Ke</th>
                        <th>Nama Pembeli</th>
                        <th>Tanggal</th>
                        <th>Total Harga</th>
                        <th>Nama Produk</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $last_Id_penjualan = null; 
                    $rowspan_count = []; 

                    foreach ($rekap_penjualan as $data) {
                        if (!isset($rowspan_count[$data['Id_penjualan']])) {
                            $rowspan_count[$data['Id_penjualan']] = 0;
                        }
                        $rowspan_count[$data['Id_penjualan']]++;
                    }

                    foreach ($rekap_penjualan as $data): 
                    ?>
                        <tr>
                            <?php if ($last_Id_penjualan !== $data['Id_penjualan']): ?>
                                <td rowspan="<?= $rowspan_count[$data['Id_penjualan']] ?>"><?= $data['Id_penjualan'] ?></td>
                                <td rowspan="<?= $rowspan_count[$data['Id_penjualan']] ?>"><?= $data['nama_pelanggan'] ?></td>
                                <td rowspan="<?= $rowspan_count[$data['Id_penjualan']] ?>"><?= $data['tanggal_penjualan'] ?></td>
                                <td rowspan="<?= $rowspan_count[$data['Id_penjualan']] ?>">Rp. <?= number_format($data['total_harga'], 0, ',', '.') ?></td>
                                <?php $last_Id_penjualan = $data['Id_penjualan']; ?>
                            <?php endif; ?>
                            <td><?= $data['nama_produk'] ?></td>
                            <td><?= $data['jumlah_produk'] ?></td>
                            <td>Rp. <?= number_format($data['harga'], 0, ',', '.') ?></td>
                            <td>Rp. <?= number_format($data['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" style="text-align: right; font-size: 18px; font-weight: bold;">Total Penjualan:</td>
                        <td style="font-size: 18px; font-weight: bold;">Rp. <?= number_format($total_penjualan, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- TTD -->
            <div class="ttd" style="margin-top: 20px;">
                <h4>Tanggal Cetak: <?php echo date('d-m-Y'); ?></h4>
                <p>Yang Bertanda Tangan,</p>
                <div style="border-top: none; width: 200px; margin-top: 50px; margin-left: 100%;"></div>
                <p><?php echo htmlspecialchars($username); ?></p>
            </div>
        </div>
    </main>
</div>


</body>
</html>