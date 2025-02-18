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
        <div class="img-dashboard">
            <img src="img/dashboard_BubbleScarf.jpg" alt="dashboard">
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

</body>
</html>
