<?php
session_start();
include 'config.php';
include 'sidebar.php';

session_regenerate_Id(true);

$username = $_SESSION['username'];
$role = $_SESSION['role'];

// Ambil kata kunci pencarian jika ada
$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fungsi untuk mendapatkan rekap data penjualan dengan filter & search
// Fungsi untuk mendapatkan rekap data penjualan dengan filter & search
function getRekap($periode, $tanggal = null, $bulan = null, $tahun = null, $searchKeyword = '') {
    global $conn;

    $where = "1=1"; // Default kondisi (agar tidak error jika tidak ada filter)
    
    if ($periode === 'perhari' && $tanggal) {
        $where .= " AND DATE(p.tanggal_penjualan) = '$tanggal'";
    } elseif ($periode === 'perminggu') {
        $where .= " AND YEARWEEK(p.tanggal_penjualan, 1) = YEARWEEK(NOW(), 1)";
    } elseif ($periode === 'perbulan' && $bulan && $tahun) {
        $where .= " AND YEAR(p.tanggal_penjualan) = '$tahun' AND MONTH(p.tanggal_penjualan) = '$bulan'";
    } elseif ($periode === 'pertahun' && $tahun) {
        $where .= " AND YEAR(p.tanggal_penjualan) = '$tahun'";
    }

    // Tambahkan filter pencarian jika ada
    if (!empty($searchKeyword)) {
        $where .= " AND (pr.nama_produk LIKE '%$searchKeyword%' OR p.id_penjualan LIKE '%$searchKeyword%')";
    }

    // Query untuk mendapatkan data rekap penjualan
    $query = "SELECT p.id_penjualan, p.tanggal_penjualan, pr.nama_produk, dp.jumlah_produk, pr.harga, dp.subtotal, p.total_harga
              FROM penjual p
              JOIN detail_penjualan dp ON p.id_penjualan = dp.id_penjualan
              JOIN produk pr ON dp.id_produk = pr.id_produk
              WHERE $where
              ORDER BY p.tanggal_penjualan DESC, p.id_penjualan ASC";

    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // Query untuk mendapatkan total penjualan
    $totalQuery = "SELECT SUM(p.total_harga) AS total_penjualan
                   FROM penjual p
                   JOIN detail_penjualan dp ON p.id_penjualan = dp.id_penjualan
                   JOIN produk pr ON dp.id_produk = pr.id_produk
                   WHERE $where";
    $totalResult = mysqli_query($conn, $totalQuery);
    $totalData = mysqli_fetch_assoc($totalResult);
    
    return [$data, $totalData['total_penjualan']];
}


// Ambil filter dari URL
$periode = isset($_GET['periode']) ? $_GET['periode'] : 'perhari';
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Jika periode perbulan dipilih, pecah nilai bulan dan tahun dari input "YYYY-MM"
$bulan = date('m'); // Default bulan saat ini
if ($periode == 'perbulan' && isset($_GET['bulan'])) {
    list($tahun, $bulan) = explode('-', $_GET['bulan']);
}

// Ambil data berdasarkan filter & search
// Ambil data berdasarkan filter & search
list($rekap_penjualan, $total_penjualan) = getRekap($periode, $tanggal, $bulan, $tahun, $searchKeyword);

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="tabel.css">
</head>
<body>

<header>
    <h2>
        <label>
            <span class="las la-bars"></span>
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
                <!-- Form Filter Periode -->
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
                    
                    <!-- Menambahkan input hidden untuk mempertahankan filter periode -->
                    <input type="hidden" name="periode" value="<?= htmlspecialchars($periode) ?>">
                    <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>">
                    <input type="hidden" name="bulan" value="<?= htmlspecialchars($bulan) ?>">
                    <input type="hidden" name="tahun" value="<?= htmlspecialchars($tahun) ?>">
                    
                    <button type="submit" class="search-btn">
                        <i class="uil uil-search uil-search"></i>
                    </button>
                </form>

            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Transaksi Ke</th>
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
                    $last_id_penjualan = null; // Menyimpan ID transaksi sebelumnya
                    $rowspan_count = []; // Menyimpan jumlah baris untuk setiap transaksi

                    // Hitung jumlah baris per transaksi
                    foreach ($rekap_penjualan as $data) {
                        if (!isset($rowspan_count[$data['id_penjualan']])) {
                            $rowspan_count[$data['id_penjualan']] = 0;
                        }
                        $rowspan_count[$data['id_penjualan']]++;
                    }

                    // Tampilkan data dengan rowspan
                    foreach ($rekap_penjualan as $data): 
                    ?>
                        <tr>
                            <?php if ($last_id_penjualan !== $data['id_penjualan']): ?>
                                <td rowspan="<?= $rowspan_count[$data['id_penjualan']] ?>"><?= $data['id_penjualan'] ?></td>
                                <td rowspan="<?= $rowspan_count[$data['id_penjualan']] ?>"><?= $data['tanggal_penjualan'] ?></td>
                                <td rowspan="<?= $rowspan_count[$data['id_penjualan']] ?>">Rp. <?= number_format($data['total_harga'], 0, ',', '.') ?></td>
                                <?php $last_id_penjualan = $data['id_penjualan']; ?>
                            <?php endif; ?>
                            <td><?= $data['nama_produk'] ?></td>
                            <td><?= $data['jumlah_produk'] ?></td>
                            <td>Rp. <?= number_format($data['harga'], 0, ',', '.') ?></td>
                            <td>Rp. <?= number_format($data['subtotal'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Menampilkan Total Penjualan -->
            <div style="margin-top: 20px; font-weight: bold;">
                <h3>Total Penjualan: Rp. <?= number_format($total_penjualan, 0, ',', '.') ?></h3>
            </div>

            

        </div>
    </main>
</div>

</body>
</html>
