<?php
session_start();

include 'config.php';
include 'sidebar.php';

session_regenerate_Id(true);

$username = $_SESSION['username'];
$role = $_SESSION['role'];

$searchKeyword = $_GET['search'] ?? '';

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
                <label>
                    <span class="uil uil-slack"></span>
                </label>
                Master Barang
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

    <!-- Table Barang Content -->

    <div class="main-content">
    <main>
        <div class="container">
            <div class="header-tools">
                <form method="GET" action="master_barang.php" class="search-box">
                    <input type="text" name="search" placeholder="Search produk..." class="search-input" value="<?php echo htmlspecialchars($searchKeyword); ?>">
                    <button type="submit" class="search-btn"><i class="uil uil-search uil-search"></i></button>
                </form>

                <a href="tambah_barang.php" class="btn-tambah-data"> 
                    <i class="uil uil-user-plus user-plus"></i> Tambah barang
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
                     $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                     $limit = 5; 
                     $offset = ($page - 1) * $limit; 

                    $sql = "SELECT * FROM produk";

                    if (!empty($searchKeyword)) {
                        $sql .= " WHERE Id_produk LIKE '%$searchKeyword%'
                                  OR nama_produk LIKE '%$searchKeyword%'  
                                  OR harga LIKE '%$searchKeyword%' 
                                  OR stok LIKE '%$searchKeyword%'";
                    }

                    $sql .= " LIMIT $limit OFFSET $offset"; 
                    $result = $conn->query($sql);

                    $totalDataQuery = "SELECT COUNT(*) as total FROM produk";

                    if (!empty($searchKeyword)) {
                    $totalDataQuery .= " WHERE Id_produk LIKE '%$searchKeyword%'
                                  OR nama_produk LIKE '%$searchKeyword%'  
                                  OR harga LIKE '%$searchKeyword%' 
                                  OR stok LIKE '%$searchKeyword%'";
                    }

                    $totalDataResult = $conn->query($totalDataQuery);
                    $totalData = $totalDataResult->fetch_assoc()['total'];
                    $totalPages = ceil($totalData / $limit); 
                    ?>

                    <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $fotoPath = !empty($row['foto_produk']) ? 'uploads/produks/' . $row['foto_produk'] : 'img/default.jpg';
                           
                            echo "<tr>
                                    <td>" . $row['Id_produk'] . "</td>
                                    <td>" . $row['nama_produk'] . "</td>
                                    <td>" . $row['harga'] . "</td>
                                    <td>" . $row['stok'] . "</td>
                                    <td>";
                                    if ($fotoPath && file_exists($fotoPath)) {
                                        echo "<img src='" . htmlspecialchars($fotoPath) . "' width='50' height='50' alt='Foto Produk'>";
                                    } else {
                                        echo "<p>No photo available</p>";
                                    }
                            
                             echo "</td>
                                    <td>
                                        <a href='edit_barang.php?id=" . $row['Id_produk'] . "' class='btn btn-edit'> <i class='uil uil-edit'></i> </a>
                                        <a href='proses_barang.php?id=" . $row['Id_produk'] . "' class='btn btn-delete'> <i class='uil uil-trash-alt'></i> </a>
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
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo htmlspecialchars($searchKeyword); ?>" class="page-link">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo htmlspecialchars($searchKeyword); ?>" class="page-link <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo htmlspecialchars($searchKeyword); ?>" class="page-link">Next</a>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>


</body>
</html>