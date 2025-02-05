<?php
include 'config.php';

if (isset($_GET['id'])) {
    $Id_user = $_GET['id'];

    $sql = "SELECT user.*, role.nama_role 
            FROM user 
            JOIN role ON user.Id_role = role.Id_role 
            WHERE user.Id_user = $Id_user";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc(); 
    } else {
        echo "<script>alert('User tidak ditemukan!'); window.location='master_user.php';</script>";
        exit;
    }
}

if (isset($_POST['submit'])) {
    $Id_user = $_POST['Id_user'];
    $Id_role = $_POST['Id_role'];
    $username = $_POST['username'];
    $password = $_POST['password']; 
    $TTL = $_POST['TTL'];
    $jenis_kelamin = $_POST['jenis_kelamin'];
    $alamat = $_POST['alamat'];
    $no_tlp = $_POST['no_tlp'];


    $foto = $user['foto']; 
    
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $targetDir = "uploads/users/";
        $fileName = basename($_FILES['foto']['name']);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        if (in_array($fileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
                $foto = $fileName; 
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        } else {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
    }

    if (!empty($password)) {
        $hashedPassword = md5($password); 
        $sql = "UPDATE user 
                SET username = '$username', 
                    password = '$hashedPassword', 
                    Id_role = '$Id_role', 
                    TTL = '$TTL', 
                    jenis_kelamin = '$jenis_kelamin', 
                    alamat = '$alamat', 
                    no_tlp = '$no_tlp',
                    foto = '$foto'
                WHERE Id_user = $Id_user";
    } else {
        $sql = "UPDATE user 
                SET username = '$username', 
                    Id_role = '$Id_role', 
                    TTL = '$TTL', 
                    jenis_kelamin = '$jenis_kelamin', 
                    alamat = '$alamat', 
                    no_tlp = '$no_tlp',
                    foto = '$foto'
                WHERE Id_user = $Id_user";
    }

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('User berhasil diperbarui!'); window.location='master_user.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="tambah_data.css">
</head>
<body>
    <div class="container">
        <h2>Edit User</h2>
        <form method="POST" action="" enctype="multipart/form-data"> 

            <input type="hidden" name="Id_user" value="<?php echo $user['Id_user']; ?>">

        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password">
        </div>

        <div class="form-group">
            <label for="Id_role">Role</label>
            <select class="form-control" id="Id_role" name="Id_role" required>
                <option value="">-- Pilih Role --</option>
                <?php
                $queryRole = "SELECT Id_role, nama_role FROM role";
                $roles = $conn->query($queryRole);

                while ($role = $roles->fetch_assoc()) {
                    $selected = ($role['Id_role'] == $user['Id_role']) ? 'selected' : '';
                    echo "<option value='" . $role['Id_role'] . "' $selected>" . $role['nama_role'] . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label for="TTL">Tempat Tanggal Lahir</label>
            <input type="date" id="TTL" name="TTL" value="<?php echo $user['TTL']; ?>">
        </div>
            
        <div class="form-group">
            <label for="jenis_kelamin">Jenis Kelamin</label>
            <select class="form-control" id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="Laki-laki" <?php echo (isset($user['jenis_kelamin']) && $user['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                <option value="Perempuan" <?php echo (isset($user['jenis_kelamin']) && $user['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
            </select> 
        </div>
           
        <div class="form-group">
            <label for="alamat">Alamat</label>
            <input type="text" id="alamat" name="alamat" value="<?php echo $user['alamat']; ?>" required>
        </div>

        <div class="form-group">
            <label for="no_tlp">No Telepon</label>
            <input type="number" id="no_tlp" name="no_tlp" value="<?php echo $user['no_tlp']; ?>" required>
        </div>

        <div class="form-group">
            <label for="foto">Foto User</label>
            <input type="file" id="foto" name="foto">
            <br>
            <?php if (!empty($user['foto'])): ?>
                <img src="uploads/users/<?php echo $user['foto']; ?>" alt="Foto User" width="100" height="100">
            <?php endif; ?>
        </div>

            <button type="submit" name="submit">Update User</button>
            <a href="master_user.php" class="button">Batal</a>
        </form>
    </div>
</body>
</html>
