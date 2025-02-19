<?php
session_start();
session_regenerate_id(true);

include 'config.php';
include 'sidebar.php';

$audri_username = $_SESSION['username'];
$audri_role = $_SESSION['role'];

$audri_searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

function getRekap($audri_periode, $audri_tanggal = null, $audri_bulan = null, $audri_tahun = null, $audri_searchKeyword = '') {
    global $conn;

    $audri_where = "1=1"; 
    
    if ($audri_periode === 'perhari' && $audri_tanggal) {
        $audri_where .= " AND DATE(p.tanggal_penjualan) = '$audri_tanggal'";
    } elseif ($audri_periode === 'perbulan' && $audri_bulan && $audri_tahun) {
        $audri_where .= " AND YEAR(p.tanggal_penjualan) = '$audri_tahun' AND MONTH(p.tanggal_penjualan) = '$audri_bulan'";
    } elseif ($audri_periode === 'pertahun' && $audri_tahun) {
        $audri_where .= " AND YEAR(p.tanggal_penjualan) = '$audri_tahun'";
    }

    if (!empty($audri_searchKeyword)) {
        $audri_where .= " AND (pr.nama_produk LIKE '%$audri_searchKeyword%' OR p.Id_penjualan LIKE '%$audri_searchKeyword%' OR pl.nama_pelanggan LIKE '%$audri_searchKeyword%')";
    }

    // Query untuk mengambil data penjualan
    $audri_query = "SELECT 
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
              WHERE $audri_where
              ORDER BY p.tanggal_penjualan ASC, p.Id_penjualan ASC";

    $audri_result = mysqli_query($conn, $audri_query);
    $audri_data = mysqli_fetch_all($audri_result, MYSQLI_ASSOC);

    // Query untuk menghitung total penjualan
    $audri_totalPenjualanQuery = "SELECT SUM(p.total_harga) AS total_penjualan
                            FROM penjual p
                            WHERE $audri_where";

    $audri_totalPenjualanResult = mysqli_query($conn, $audri_totalPenjualanQuery);
    $audri_totalPenjualanData = mysqli_fetch_assoc($audri_totalPenjualanResult);
    
    return [
        'data' => $audri_data,
        'total_penjualan' => $audri_totalPenjualanData['total_penjualan'] ?? 0 
    ];
}

$audri_periode = isset($_GET['periode']) ? $_GET['periode'] : 'perhari';
$audri_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$audri_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$audri_bulan = date('m'); 
if ($audri_periode == 'perbulan' && isset($_GET['bulan'])) {
    list($audri_tahun, $audri_bulan) = explode('-', $_GET['bulan']);
}

$audri_result = getRekap($audri_periode, $audri_tanggal, $audri_bulan, $audri_tahun, $audri_searchKeyword);
$audri_rekapPenjualan = $audri_result['data'];
$audri_totalPenjualan = $audri_result['total_penjualan'];

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
                <span class="uil uil-bars"></span>
            </label>
            Laporan
        </h2>

    <?php
    $audri_queryUser = "SELECT foto FROM user WHERE username = '$audri_username'";
    $audri_resultUser = mysqli_query($conn, $audri_queryUser);
    $audri_userData = mysqli_fetch_assoc($audri_resultUser);

    if (!$audri_userData) {
        die("User data not found.");
    }
    $audri_fotoUser = !empty($audri_userData['foto']) ? 'uploads/users/' . $audri_userData['foto'] : 'img/default.jpg'; ?>       

    <div class="user-wrapper">
        <img src="<?php echo htmlspecialchars($audri_fotoUser); ?>" width="40px" height="30px" alt="User">
        <div>
            <h4><?php echo htmlspecialchars($audri_username); ?></h4>
            <small><?php echo htmlspecialchars($audri_role); ?></small>
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
                            <option value="perhari" <?= $audri_periode == 'perhari' ? 'selected' : '' ?>>Harian</option>
                            <option value="perbulan" <?= $audri_periode == 'perbulan' ? 'selected' : '' ?>>Bulanan</option>
                            <option value="pertahun" <?= $audri_periode == 'pertahun' ? 'selected' : '' ?>>Tahunan</option>
                        </select>

                        <?php if ($audri_periode == 'perhari'): ?>
                            <input type="date" name="tanggal" value="<?= $audri_tanggal ?>" onchange="this.form.submit()">
                        <?php elseif ($audri_periode == 'perbulan'): ?>
                            <input type="month" name="bulan" value="<?= $audri_tahun . '-' . str_pad($audri_bulan, 2, '0', STR_PAD_LEFT) ?>" onchange="this.form.submit()">
                        <?php elseif ($audri_periode == 'pertahun'): ?>
                            <input type="number" name="tahun" value="<?= $audri_tahun ?>" min="2000" max="<?= date('Y') ?>" onchange="this.form.submit()">
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Form Pencarian -->
                <form method="GET" action="laporan.php" class="search-box" style="margin-right: 50px;">
                    <input type="text" name="search" placeholder="Search produk..." class="search-input" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" >
                    
                    <input type="hidden" name="periode" value="<?= htmlspecialchars($audri_periode) ?>">
                    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($audri_tanggal) ?>">
                    <input type="hidden" name="bulan" value="<?= htmlspecialchars($audri_bulan) ?>">
                    <input type="hidden" name="tahun" value="<?= htmlspecialchars($audri_tahun) ?>">
                    
                    <button type="submit" class="search-btn">
                        <i class="uil uil-search uil-search"></i>
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
                    if ($audri_periode == 'perhari') {
                        echo date('d-m-Y', strtotime($audri_tanggal));
                    } elseif ($audri_periode == 'perbulan') {
                        echo date('F Y', strtotime($audri_tahun . '-' . $audri_bulan . '-01'));
                    } elseif ($audri_periode == 'pertahun') {
                        echo $audri_tahun;
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
                    $audri_last_Id_penjualan = null; 
                    $audri_rowspan_count = []; 

                    foreach ($audri_rekapPenjualan as $audri_data) {
                        if (!isset($audri_rowspan_count[$audri_data['Id_penjualan']])) {
                            $audri_rowspan_count[$audri_data['Id_penjualan']] = 0;
                        }
                        $audri_rowspan_count[$audri_data['Id_penjualan']]++;
                    }

                    foreach ($audri_rekapPenjualan as $audri_data): 
                    ?>
                        <tr>
                            <?php if ($audri_last_Id_penjualan !== $audri_data['Id_penjualan']): ?>
                                <td rowspan="<?= $audri_rowspan_count[$audri_data['Id_penjualan']] ?>"><?= $audri_data['Id_penjualan'] ?></td>
                                <td rowspan="<?= $audri_rowspan_count[$audri_data['Id_penjualan']] ?>"><?= $audri_data['nama_pelanggan'] ?></td>
                                <td rowspan="<?= $audri_rowspan_count[$audri_data['Id_penjualan']] ?>"><?= $audri_data['tanggal_penjualan'] ?></td>
                                <td rowspan="<?= $audri_rowspan_count[$audri_data['Id_penjualan']] ?>">Rp. <?= number_format($audri_data['total_harga'], 0, ',', '.') ?></td>
                                <?php $audri_last_Id_penjualan = $audri_data['Id_penjualan']; ?>
                            <?php endif; ?>
                            <td><?= $audri_data['nama_produk'] ?></td>
                            <td><?= $audri_data['jumlah_produk'] ?></td>
                            <td>Rp. <?= number_format($audri_data['harga'], 0, ',', '.') ?></td>
                            <td>Rp. <?= number_format($audri_data['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="7" style="text-align: right; font-size: 18px; font-weight: bold;">Total Penjualan:</td>
                        <td style="font-size: 18px; font-weight: bold;">Rp. <?= number_format($audri_totalPenjualan, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- TTD -->
            <div class="ttd" style="margin-top: 20px;">
                <h4>Tanggal Cetak: <?php echo date('d-m-Y'); ?></h4>
                <p>Yang Bertanda Tangan,</p>
                <div style="border-top: none; width: 200px; margin-top: 50px; margin-left: 100%;"></div>
                <p><?php echo htmlspecialchars($audri_username); ?></p>
            </div>
        </div>
    </main>
</div>


</body>
</html>