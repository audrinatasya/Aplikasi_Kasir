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
                <div class="header-tools">
                    <form method="GET" action="master_user.php" class="search-box">
                        <input type="text" name="search" placeholder="Search user..." class="search-input" value="<?php echo htmlspecialchars($audri_searchKeyword); ?>">
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
                    $audri_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $audri_limit = 5;
                    $audri_offset = ($audri_page - 1) * $audri_limit;

                    $audri_sql = "SELECT u.Id_user, u.Id_role, r.nama_role, u.username, u.TTL, u.jenis_kelamin, u.alamat, u.no_tlp, u.foto
                            FROM user u 
                            JOIN role r ON u.Id_role = r.Id_role";

                    if (!empty($audri_searchKeyword)) {
                        $audri_sql .= " WHERE u.username LIKE '%$audri_searchKeyword%' 
                                  OR r.nama_role LIKE '%$audri_searchKeyword%' 
                                  OR u.TTL LIKE '%$audri_searchKeyword%' 
                                  OR u.alamat LIKE '%$audri_searchKeyword%'
                                  OR u.no_tlp LIKE '%$audri_searchKeyword%'";
                    }

                    $audri_sql .= " LIMIT $audri_limit OFFSET $audri_offset";
                    $audri_result = $conn->query($audri_sql);

                    $audri_totalDataQuery = "SELECT COUNT(*) as total FROM user u 
                                    JOIN role r ON u.Id_role = r.Id_role";

                    if (!empty($audri_searchKeyword)) {
                        $audri_totalDataQuery .= " WHERE u.username LIKE '%$audri_searchKeyword%' 
                                            OR r.nama_role LIKE '%$audri_searchKeyword%' 
                                            OR u.TTL LIKE '%$audri_searchKeyword%' 
                                            OR u.alamat LIKE '%$audri_searchKeyword%'
                                            OR u.no_tlp LIKE '%$audri_searchKeyword%'";
                    }

                    $audri_totalDataResult = $conn->query($audri_totalDataQuery);
                    $audri_totalData = $audri_totalDataResult->fetch_assoc()['total'];
                    $audri_totalPages = ceil($audri_totalData / $audri_limit);
                    ?>

                    <tbody>
                        <?php
                        if ($audri_result->num_rows > 0) {
                            while ($audri_row = $audri_result->fetch_assoc()) {
                                $audri_fotoPath = !empty($audri_row['foto']) ? 'uploads/users/' . $audri_row['foto'] : 'img/default.jpg';

                                echo "<tr>
                                    <td>" . $audri_row['Id_user'] . "</td>
                                    <td>{$audri_row['nama_role']}</td>
                                    <td>{$audri_row['username']}</td>
                                    <td>{$audri_row['TTL']}</td>
                                    <td>{$audri_row['jenis_kelamin']}</td>
                                    <td>{$audri_row['alamat']}</td>
                                    <td>{$audri_row['no_tlp']}</td>
                                    <td>";

                                if ($audri_fotoPath && file_exists($audri_fotoPath)) {
                                    echo "<img src='" . htmlspecialchars($audri_fotoPath) . "' width='50' height='50' alt='Foto User'>";
                                } else {
                                    echo "<p>No photo available</p>";
                                }

                                echo "</td>
                                    <td>
                                        <a href='edit_user.php?id={$audri_row['Id_user']}' class='btn btn-edit'><img src='asset/edit.svg' width='25px' height='25px'></a>
                                        <a href='proses_user.php?id={$audri_row['Id_user']}' class='btn btn-delete'><img src='asset/trash-alt.svg' width='25px' height='25px'></a>
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