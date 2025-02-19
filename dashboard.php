<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';
include 'sidebar.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php?message=Silakan+login+terlebih+dahulu.&action=login");
    exit();
}

$audri_username = $_SESSION['username'];
$audri_role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="style.css">
</head>
<style>

.stat-box h1 {
    font-size: 18px;
    color: #e44e85;
}

.stat-box p {
    font-size: 24px;
    font-weight: bold;
    color: cornflowerblue;
}

.dashboard-stats {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    margin-top: 20px;
}

.stat-box {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
    flex: 1;
}

.chart-container {
    margin-top: 30px;
    background: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
}

canvas {
    max-width: 100%;
    height: auto;
}


</style>
<body>

<!-- Header Content -->
<header>
    <h2>
        Dashboard
    </h2>

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

<!-- Dashboard Content -->
<div class="main-content">
    <main>

        <div class="dashboard-stats">
            <?php
    
                $queryTotalPenjualan = "SELECT SUM(total_harga) AS total_penjualan FROM penjual";
                $resultTotalPenjualan = mysqli_query($conn, $queryTotalPenjualan);
                $dataPenjualan = mysqli_fetch_assoc($resultTotalPenjualan);
                $totalPenjualan = $dataPenjualan['total_penjualan'] ?? 0;

                $queryTotalBarang = "SELECT SUM(jumlah_produk) AS total_barang FROM detail_penjualan";
                $resultTotalBarang = mysqli_query($conn, $queryTotalBarang);
                $dataBarang = mysqli_fetch_assoc($resultTotalBarang);
                $totalBarangTerjual = $dataBarang['total_barang'] ?? 0;

                // Query untuk mendapatkan total penjualan per bulan
                $queryPenjualanPerBulan = "SELECT DATE_FORMAT(tanggal_penjualan, '%Y-%m') AS bulan, 
                                                  SUM(total_harga) AS total 
                                           FROM penjual 
                                           GROUP BY bulan 
                                           ORDER BY bulan ASC";
                $resultPenjualanPerBulan = mysqli_query($conn, $queryPenjualanPerBulan);
                
                $bulanArray = [];
                $totalArray = [];
                while ($row = mysqli_fetch_assoc($resultPenjualanPerBulan)) {
                    // Format bulan agar lebih mudah dibaca (contoh: "2024-01" jadi "Januari 2024")
                    $bulanNama = date("F Y", strtotime($row['bulan'] . "-01"));
                    $bulanArray[] = $bulanNama;
                    $totalArray[] = $row['total'];
                }

            ?>

            <div class="stat-box">
                <h1>Total Penjualan</h1>
                <p>Rp <?php echo number_format($totalPenjualan, 0, ',', '.'); ?></p>
            </div>

            <div class="stat-box">
                <h1>Jumlah Barang Terjual</h1>
                <p><?php echo number_format($totalBarangTerjual, 0, ',', '.'); ?> pcs</p>
            </div>
        </div>

        <div class="chart-container">
             <h3>Grafik Total Penjualan Per Bulan</h3>
            <canvas id="penjualanChart"></canvas>
        </div>

    </main>
</div>


<script>
    document.getElementById("menu-toggle").addEventListener("click", function() {
        document.querySelector(".sidebar").classList.toggle("collapsed");
        document.querySelector(".main-content").classList.toggle("collapsed");
        document.querySelector("header").classList.toggle("collapsed");
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('penjualanChart').getContext('2d');
    var penjualanChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($bulanArray); ?>,
            datasets: [{
                label: 'Total Penjualan (Rp)',
                data: <?php echo json_encode($totalArray); ?>,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>



</body>
</html>
