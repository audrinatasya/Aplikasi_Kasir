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
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Users</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="tabel.css">
</head>
<body>

<header>
        <h2>
            <label id="menu-toggle">
                <!-- <span class="uil uil-bars"></span> -->
                <span class="bars"> <img src="asset/bars.svg" width="25px" height="25px"> </span>
            </label>
                 Master User
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
            <div class="header-tools">
                <form method="GET" action="master_user.php" class="search-box">
                    <input type="text" name="search" placeholder="Search user..." class="search-input" value="<?php echo htmlspecialchars($searchKeyword); ?>">
                    <button type="submit" class="search-btn"><i class="uil.search"></i> <img src="asset/search.svg" width="20px" height="20px"></button> 
                </form>

                <a href="tambah_user.php" class="btn-tambah-data"> 
                    <i class="user.plus"><img src="asset/user-plus.svg" width="15px" height="15px"></i> Tambah User
                </a>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID User</th>
                        <th>Nama Role</th>
                        <th>Username</th>
                        <th>Tempat <br> Tanggal Lahir </br></th>
                        <th>Jenis <br> Kelamin </br></th>
                        <th>Alamat</th>
                        <th>No Telepon</th>
                        <th>Foto</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                    <?php
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $limit = 5; 
                    $offset = ($page - 1) * $limit;

                    $sql = "SELECT u.Id_user, u.Id_role, r.nama_role, u.username, u.TTL, u.jenis_kelamin, u.alamat, u.no_tlp, u.foto
                            FROM user u 
                            JOIN role r ON u.Id_role = r.Id_role";

                    if (!empty($searchKeyword)) {
                        $sql .= " WHERE u.username LIKE '%$searchKeyword%' 
                                  OR r.nama_role LIKE '%$searchKeyword%' 
                                  OR u.TTL LIKE '%$searchKeyword%' 
                                  OR u.alamat LIKE '%$searchKeyword%'
                                  OR u.no_tlp LIKE '%$searchKeyword%'";
                    }

                    $sql .= " LIMIT $limit OFFSET $offset"; 
                    $result = $conn->query($sql);

                    $totalDataQuery = "SELECT COUNT(*) as total FROM user u 
                                    JOIN role r ON u.Id_role = r.Id_role";

                    if (!empty($searchKeyword)) {
                        $totalDataQuery .= " WHERE u.username LIKE '%$searchKeyword%' 
                                            OR r.nama_role LIKE '%$searchKeyword%' 
                                            OR u.TTL LIKE '%$searchKeyword%' 
                                            OR u.alamat LIKE '%$searchKeyword%'
                                            OR u.no_tlp LIKE '%$searchKeyword%'";
                    }

                    $totalDataResult = $conn->query($totalDataQuery);
                    $totalData = $totalDataResult->fetch_assoc()['total'];
                    $totalPages = ceil($totalData / $limit); 
                    ?>

                    <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $fotoPath = !empty($row['foto']) ? 'uploads/users/' . $row['foto'] : 'img/default.jpg';
                            
                            echo "<tr>
                                    <td>" . $row['Id_user'] . "</td>
                                    <td>{$row['nama_role']}</td>
                                    <td>{$row['username']}</td>
                                    <td>{$row['TTL']}</td>
                                    <td>{$row['jenis_kelamin']}</td>
                                    <td>{$row['alamat']}</td>
                                    <td>{$row['no_tlp']}</td>
                                    <td>";
                                    
                            if ($fotoPath && file_exists($fotoPath)) {
                                echo "<img src='" . htmlspecialchars($fotoPath) . "' width='50' height='50' alt='Foto User'>";
                            } else {
                                echo "<p>No photo available</p>";
                            }

                            echo "</td>
                                    <td>
                                        <a href='edit_user.php?id={$row['Id_user']}' class='btn btn-edit'><img src='asset/edit.svg' width='25px' height='25px'></a>
                                        <a href='proses_user.php?id={$row['Id_user']}' class='btn btn-delete'><img src='asset/trash-alt.svg' width='25px' height='25px'></a>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>Tidak ada data ditemukan</td></tr>";
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
