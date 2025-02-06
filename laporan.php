<?php
session_start();

session_regenerate_Id(true); ?>

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

<?php
include 'config.php';
include 'sidebar.php';

 /* FUNGSI PHP */

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$searchKeyword = isset($_GET['search']) ? trim($_GET['search']) : '';

function getRekap($periode, $tanggal = null, $bulan = null, $tahun = null, $searchKeyword = '', $limit = 10, $offset = 0) {
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
        $where .= " AND (pr.nama_produk LIKE '%$searchKeyword%' OR p.id_penjualan LIKE '%$searchKeyword%')";
    }

        $query = "SELECT 
                    (@row_number := @row_number + 1) AS nomor_transaksi,
                    p.id_penjualan, 
                    p.tanggal_penjualan, 
                    pr.nama_produk, 
                    dp.jumlah_produk, 
                    pr.harga, 
                    dp.subtotal, 
                    p.total_harga,
                    pl.nama_pelanggan
                    FROM (SELECT @row_number := 0) AS init, penjual p
                    JOIN detail_penjualan dp ON p.id_penjualan = dp.id_penjualan
                    JOIN pelanggan pl ON dp.id_penjualan = pl.id_pelanggan
                    JOIN produk pr ON dp.id_produk = pr.id_produk
                    WHERE $where
                    ORDER BY p.tanggal_penjualan ASC, p.id_penjualan ASC
                    LIMIT $limit OFFSET $offset";

    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $totalQuery = "SELECT COUNT(*) AS total_rows
                   FROM penjual p
                   JOIN detail_penjualan dp ON p.id_penjualan = dp.id_penjualan
                   JOIN produk pr ON dp.id_produk = pr.id_produk
                   JOIN pelanggan pl ON dp.id_penjualan = pl.id_pelanggan
                   WHERE $where";
    $totalResult = mysqli_query($conn, $totalQuery);
    $totalData = mysqli_fetch_assoc($totalResult);

    $totalPenjualanQuery = "SELECT SUM(p.total_harga) AS total_penjualan
                            FROM penjual p
<<<<<<< HEAD
                            JOIN detail_penjualan dp ON p.id_penjualan = dp.id_penjualan
                            JOIN produk pr ON dp.id_produk = pr.id_produk
                            JOIN pelanggan pl ON dp.id_penjualan = pl.id_pelanggan
                            WHERE $where";

=======
                            WHERE $where";
>>>>>>> 4c57d735eb0f60fb605e50dd8f07a6a1da009448
    $totalPenjualanResult = mysqli_query($conn, $totalPenjualanQuery);
    $totalPenjualanData = mysqli_fetch_assoc($totalPenjualanResult);
    
    return [
        'data' => $data,
        'total_rows' => $totalData['total_rows'],
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

$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$result = getRekap($periode, $tanggal, $bulan, $tahun, $searchKeyword, $limit, $offset);

$rekap_penjualan = $result['data'];
$totalRows = $result['total_rows'];
$total_penjualan = $result['total_penjualan'];

$totalPages = ceil($totalRows / $limit);

?>


  <!-- HTML -->

<header>
    <h2 class="judul-laporan">
        <label>
            <span class="uil uil-slack"></span>
        </label>
        Laporan Penjualan
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
                        <i class="uil uil-search uil-search"></i>
                    </button>
                </form>

                 <!-- Tombol Print -->
                 <button onclick="window.print()" class="no-print">Print Laporan</button>
                </div>

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
                    $last_id_penjualan = null; 
                    $rowspan_count = []; 

                    foreach ($rekap_penjualan as $data) {
                        if (!isset($rowspan_count[$data['id_penjualan']])) {
                            $rowspan_count[$data['id_penjualan']] = 0;
                        }
                        $rowspan_count[$data['id_penjualan']]++;
                    }

                    foreach ($rekap_penjualan as $data): 
                    ?>
                        <tr>
                            <?php if ($last_id_penjualan !== $data['id_penjualan']): ?>
                                <td rowspan="<?= $rowspan_count[$data['id_penjualan']] ?>"><?= $data['id_penjualan'] ?></td>
                                <td rowspan="<?= $rowspan_count[$data['id_penjualan']] ?>"><?= $data['nama_pelanggan'] ?></td>
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
                <tfoot>
                    <tr>
                        <td colspan="7" style="text-align: right; font-size: 18px; font-weight: bold;">Total Penjualan:</td>
                        <td style="font-size: 18px; font-weight: bold;">Rp. <?= number_format($total_penjualan, 0, ',', '.') ?></td>
                    </tr>
                </tfoot>
            </table>
         
           
                       <!-- Pagination -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($searchKeyword); ?>&periode=<?php echo htmlspecialchars($periode); ?>&tanggal=<?php echo htmlspecialchars($tanggal); ?>&bulan=<?php echo htmlspecialchars($bulan); ?>&tahun=<?php echo htmlspecialchars($tahun); ?>" class="page-link">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($searchKeyword); ?>&periode=<?php echo htmlspecialchars($periode); ?>&tanggal=<?php echo htmlspecialchars($tanggal); ?>&bulan=<?php echo htmlspecialchars($bulan); ?>&tahun=<?php echo htmlspecialchars($tahun); ?>" class="page-link <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($searchKeyword); ?>&periode=<?php echo htmlspecialchars($periode); ?>&tanggal=<?php echo htmlspecialchars($tanggal); ?>&bulan=<?php echo htmlspecialchars($bulan); ?>&tahun=<?php echo htmlspecialchars($tahun); ?>" class="page-link">Next</a>
                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

</body>
</html>
