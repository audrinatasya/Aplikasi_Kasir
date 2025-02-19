<?php
session_start();

include 'config.php';
include 'sidebar.php';

session_regenerate_Id(true);

$audri_username = $_SESSION['username'];
$audri_role = $_SESSION['role'];

$audri_searchKeyword = $_GET['search'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="wIdth=device-wIdth, initial-scale=1.0">
    <title>Master Barang</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="tabel.css">

    
</head>
<body>


  <!-- Header Content -->

    <header>
        <h2>
            <label id="menu-toggle">
                 <!-- <span class="uil uil-bars"></span> -->
                 <span class="bars"> <img src="asset/bars.svg" width="25px" height="25px"> </span>
            </label>
            Master Barang
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

    <!-- Table Barang Content -->

    <div class="main-content">
    <main>
        <div class="container">
            <div class="header-tools">
                <form method="GET" action="master_barang.php" class="search-box">
                    <input type="text" name="search" placeholder="Search ..." class="search-input" value="<?php echo htmlspecialchars($audri_searchKeyword); ?>">
                    <button type="submit" class="search-btn"><i class="uil.search"></i> <img src="asset/search.svg" width="20px" height="20px"></button> 
                </form>

                <a href="tambah_barang.php" class="btn-tambah-data"> 
                <i class="user.plus"><img src="asset/user-plus.svg" width="15px" height="15px"></i> Tambah barang
                </a>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID Produk</th>
                        <th>Nama Produk</th>
                        <th>Harga</th>
                        <th>Stock</th>
                        <th>Foto</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            
                    <?php
                     $audri_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                     $audri_limit = 5; 
                     $audri_offset = ($audri_page - 1) * $audri_limit; 

                    $audri_sql = "SELECT * FROM produk";

                    if (!empty($audri_searchKeyword)) {
                        $audri_sql .= " WHERE Id_produk LIKE '%$audri_searchKeyword%'
                                  OR nama_produk LIKE '%$audri_searchKeyword%'  
                                  OR harga LIKE '%$audri_searchKeyword%' 
                                  OR stok LIKE '%$audri_searchKeyword%'";
                    }

                    $audri_sql .= " LIMIT $audri_limit OFFSET $audri_offset"; 
                    $audri_result = $conn->query($audri_sql);

                    $audri_totalDataQuery = "SELECT COUNT(*) as total FROM produk";

                    if (!empty($audri_searchKeyword)) {
                    $audri_totalDataQuery .= " WHERE Id_produk LIKE '%$audri_searchKeyword%'
                                  OR nama_produk LIKE '%$audri_searchKeyword%'  
                                  OR harga LIKE '%$audri_searchKeyword%' 
                                  OR stok LIKE '%$audri_searchKeyword%'";
                    }

                    $audri_totalDataResult = $conn->query($audri_totalDataQuery);
                    $audri_totalData = $audri_totalDataResult->fetch_assoc()['total'];
                    $audri_totalPages = ceil($audri_totalData / $audri_limit); 
                    ?>

                    <tbody>
                    <?php
                    if ($audri_result->num_rows > 0) {
                        while ($audri_row = $audri_result->fetch_assoc()) {
                            $audri_fotoPath = !empty($audri_row['foto_produk']) ? 'uploads/produks/' . $audri_row['foto_produk'] : 'img/default.jpg';
                           
                            echo "<tr>
                                    <td>" . $audri_row['Id_produk'] . "</td>
                                    <td>" . $audri_row['nama_produk'] . "</td>
                                    <td>" . $audri_row['harga'] . "</td>
                                    <td>" . $audri_row['stok'] . "</td>
                                    <td>";
                                    if ($audri_fotoPath && file_exists($audri_fotoPath)) {
                                        echo "<img src='" . htmlspecialchars($audri_fotoPath) . "' width='50' height='50' alt='Foto Produk'>";
                                    } else {
                                        echo "<p>No photo available</p>";
                                    }
                            
                             echo "</td>
                                    <td>
                                        <a href='edit_barang.php?id=" . $audri_row['Id_produk'] . "' class='btn btn-edit'> <img src='asset/edit.svg' width='25px' height='25px'> </a>
                                        <a href='proses_barang.php?id=" . $audri_row['Id_produk'] . "' class='btn btn-delete'> <img src='asset/trash-alt.svg' width='25px' height='25px'> </a>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No products found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>

            <div class="pagination">
                <?php if ($audri_page > 1): ?>
                    <a href="?page=<?php echo $audri_page - 1; ?>&search=<?php echo htmlspecialchars($audri_searchKeyword); ?>" class="page-link">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $audri_totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($audri_searchKeyword); ?>" class="page-link <?php echo ($audri_page == $i) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($audri_page < $audri_totalPages): ?>
                    <a href="?page=<?php echo $audri_page + 1; ?>&search=<?php echo htmlspecialchars($audri_searchKeyword); ?>" class="page-link">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>


</body>
</html>