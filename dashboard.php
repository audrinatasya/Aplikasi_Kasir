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

$username = $_SESSION['username'];
$role = $_SESSION['role'];

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://maxst.icons8.com/vue-static/landings/line-awesome/line-awesome/1.3.0/css/line-awesome.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>


<!-- Header Content -->
<header>
<h2>
    <label>
        <span class="uil uil-slack"></span>
    </label>
    Dashboard
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

<!-- Dashboard Content -->
<div class="main-content">
    <main>
        <div class="img-dashboard">
           <img src="img/dashboard_BubbleScarf.jpg" alt="dashboard">
        </div>
    </main>
</div>

</body>
</html>
