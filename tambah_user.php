<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>
    <link rel="stylesheet" href="tambah_data.css">
</head>
<body>

<?php
include 'config.php';

if (isset($_POST['submit'])) {
    $audri_username = $_POST['username'];
    $audri_password = md5($_POST['password']); 
    $audri_Id_role = $_POST['Id_role'];
    $audri_TTL = $_POST['TTL'];
    $audri_jenis_kelamin = $_POST['jenis_kelamin'];
    $audri_alamat = $_POST['alamat'];
    $audri_no_tlp = $_POST['no_tlp'];

    if (!empty($_FILES['foto']['name'])) {
        $audri_namaFile = 'foto_' . time() . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $audri_pathSimpan = 'uploads/users/' . $audri_namaFile;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $audri_pathSimpan)) {
            $audriQuery = "INSERT INTO user (username, password, Id_role, TTL, jenis_kelamin, alamat, no_tlp, foto) 
                      VALUES ('$audri_username', '$audri_password', '$audri_Id_role', '$audri_TTL', '$audri_jenis_kelamin', '$audri_alamat', '$audri_no_tlp', '$audri_namaFile')";

            if (mysqli_query($conn, $audriQuery)) {
                echo "<script>alert('User berhasil ditambahkan!'); window.location='master_user.php';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan data ke database!');</script>";
            }
        } else {
            echo "<script>alert('Gagal mengunggah foto!');</script>";
        }
    } else {
        echo "<script>alert('Foto wajib diunggah!');</script>";
    }
}
?>

<div class="container">
    <h2>Tambah User</h2>
    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="Id_role">Role</label>
            <select id="Id_role" name="Id_role" required>
                <option value="">-- Pilih Role --</option>
                <?php
                $audri_roles = mysqli_query($conn, "SELECT Id_role, nama_role FROM role");
                while ($audri_role = mysqli_fetch_assoc($audri_roles)) {
                    echo "<option value='{$audri_role['Id_role']}'>{$audri_role['nama_role']}</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="TTL">Tempat Tanggal Lahir</label>
            <input type="date" id="TTL" name="TTL">
        </div>
        <div class="form-group">
            <label for="jenis_kelamin">Jenis Kelamin</label>
            <select id="jenis_kelamin" name="jenis_kelamin" required>
                <option value="">-- Pilih Jenis Kelamin --</option>
                <option value="Laki-laki">Laki-laki</option>
                <option value="Perempuan">Perempuan</option>
            </select>
        </div>
        <div class="form-group">
            <label for="alamat">Alamat</label>
            <input type="text" id="alamat" name="alamat" required>
        </div>
        <div class="form-group">
            <label for="no_tlp">No Telepon</label>
            <input type="number" id="no_tlp" name="no_tlp" required>
        </div>
        <div class="form-group">
            <label for="foto">Foto User</label>
            <input type="file" id="foto" name="foto" accept="image/*" required>
        </div>

        <button type="submit" name="submit">Tambah User</button>
        <a href="master_user.php" class="button">Batal</a>
    </form>
</div>

</body>
</html>
