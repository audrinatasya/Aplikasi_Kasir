<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'config.php';

$username = $_SESSION['username'] ?? null;

$fotoPath = 'img/default.jpg';
$userRole = 'Guest';

if ($username) {
    $queryUser = "SELECT foto, role FROM user WHERE username = ?";
    $stmt = $conn->prepare($queryUser);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $resultUser = $stmt->get_result();
    $userData = $resultUser->fetch_assoc();

    if ($userData) {
        $fotoPath = !empty($userData['foto']) ? 'uploads/users/' . $userData['foto'] : $fotoPath;
        $userRole = $userData['role'];
    }
}
?>

<header>
    <h2>
        <label>
            <span class="las la-bars"></span>
        </label>
        Master User
    </h2>


    <div class="user-wrapper">
        <img src="<?php echo htmlspecialchars($fotoPath); ?>" width="40px" height="30px" alt="User">
        <div>
            <h4><?php echo htmlspecialchars($username); ?></h4>
            <small><?php echo htmlspecialchars($userRole); ?></small>
        </div>
    </div>
</header>
