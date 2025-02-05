
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.8/css/line.css">
    <link rel="stylesheet" href="login.css">
</head>
<body>

<?php
include 'config.php';
?>

<div class="notification-container" id="notification">
    <?php
    if (isset($_GET['message']) && !empty($_GET['message'])) {
        $alert_class = strpos($_GET['message'], 'salah') !== false || strpos($_GET['message'], 'tidak ditemukan') !== false ? 'alert-danger' : 'alert-success';
        echo "<div class='alert $alert_class' role='alert'>" . htmlspecialchars(urldecode($_GET['message'])) . "</div>";
    }

    
    ?>
</div>

<section class="home">
    <div class="form-container">
        <div class="form login_form">
            <form action="login.php" method="POST">
                <h2>LOGIN</h2>
                <div class="input-box">
                    <input type="text" name="username" placeholder="Enter your username" required />
                    <i class="uil uil-user username"></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" placeholder="Enter your password" required />
                    <i class="uil uil-lock password"></i>
                    <i class="uil uil-eye-slash pw-hide" aria-label="Show/Hide Password"></i>
                </div>
                <div class="option-field">
                    <span class="checkbox">
                        <input type="checkbox" id="check">
                        <label for="check"> Remember me</label>
                    </span>
                   
                </div>
                <button type="submit" class="button"> Login </button>
            </form>
        </div>
    </div>
</section>


<script src="index.js"></script>
<script src="login.js"></script>

</body>
</html>
