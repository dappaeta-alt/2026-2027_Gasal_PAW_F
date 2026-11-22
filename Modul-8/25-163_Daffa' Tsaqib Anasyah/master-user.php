<?php
include "koneksi.php";
include "auth.php";

wajib_level([1]);

// Tambah User
if (isset($_POST['tambah_user'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = md5($_POST['password']);
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $alamat   = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $hp       = mysqli_real_escape_string($conn, trim($_POST['hp']));
    $level    = (int) $_POST['level'];

    $q = "INSERT INTO user (username, password, nama, alamat, hp, level) VALUES ('$username', '$password', '$nama', '$alamat', '$hp', '$level')";
    if (mysqli_query($conn, $q)) {
        echo "<script>alert('Data user berhasil ditambahkan!'); window.location='master-user.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menambah user: " . mysqli_error($conn) . "');</script>";
    }
}

// Edit User
if (isset($_POST['edit_user'])) {
    $id_user  = (int) $_POST['id_user'];
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $nama     = mysqli_real_escape_string($conn, trim($_POST['nama']));
    $alamat   = mysqli_real_escape_string($conn, trim($_POST['alamat']));
    $hp       = mysqli_real_escape_string($conn, trim($_POST['hp']));
    $level    = (int) $_POST['level'];

    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $q = "UPDATE user SET username='$username', password='$password', nama='$nama', alamat='$alamat', hp='$hp', level='$level' WHERE id_user='$id_user'";
    } else {
        $q = "UPDATE user SET username='$username', nama='$nama', alamat='$alamat', hp='$hp', level='$level' WHERE id_user='$id_user'";
    }

    if (mysqli_query($conn, $q)) {
        if ($id_user === (int) $_SESSION['id_user']) {
            $_SESSION['nama']  = $_POST['nama'];
            $_SESSION['level'] = $level;
        }
        echo "<script>alert('Data user berhasil diupdate!'); window.location='master-user.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal mengupdate user: " . mysqli_error($conn) . "');</script>";
    }
}

// Hapus User
if (isset($_GET['hapus'])) {
    $id_hapus = (int) $_GET['hapus'];
    if ($id_hapus === (int) $_SESSION['id_user']) {
        echo "<script>alert('Tidak bisa menghapus akun yang sedang login!'); window.location='master-user.php';</script>";
        exit;
    }
    mysqli_query($conn, "DELETE FROM user WHERE id_user = '$id_hapus'");
    echo "<script>alert('Data user berhasil dihapus.'); window.location='master-user.php';</script>";
    exit;
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit   = (int) $_GET['edit'];
    $q_edit    = mysqli_query($conn, "SELECT * FROM user WHERE id_user = '$id_edit'");
    $edit_data = mysqli_fetch_assoc($q_edit);
}

$result_user = mysqli_query($conn, "SELECT * FROM user ORDER BY id_user ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Data User - Master</title>
    <style>
        body { font-family: Arial, sans-serif; color: darkslategray; background-color: whitesmoke; margin: 0; }
        .container { padding: 0 30px 30px; }
        h2, h3 { color: midnightblue; border-bottom: 2px solid lightgray; padding-bottom: 5px; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 25px; background-color: white; }
        th, td { border: 1px solid silver; padding: 10px; text-align: left; }
        th { background-color: aliceblue; color: navy; font-weight: bold; }
        tr:hover { background-color: floralwhite; }
        a { color: royalblue; text-decoration: none; font-weight: bold; }
        a:hover { color: crimson; text-decoration: underline; }
        .box-form { background-color: white; border: 1px solid silver; padding: 20px; margin-bottom: 25px; width: 420px; }
        input, select { width: 100%; padding: 8px; margin-top: 5px; margin-bottom: 12px; box-sizing: border-box; }
        button { background-color: navy; color: white; padding: 10px 15px; border: none; font-weight: bold; cursor: pointer; }
        button:hover { background-color: midnightblue; }
    </style>
</head>
<body>
<?php include "navbar.php"; ?>
<div class="container">
    <h2>Kelola Data Master User</h2>
    <button><a href="index.php" style="text-decoration: none; color: white;">Kembali ke Halaman Utama</a></button>
    <br><br>
    <div class="box-form">
        <h3 style="margin-top:0; color: midnightblue;"><?= $edit_data ? 'Edit Data User' : 'Tambah User Baru' ?></h3>
        <form method="POST">
            <?php if ($edit_data): ?>
                <input type="hidden" name="id_user" value="<?= (int)$edit_data['id_user'] ?>">
            <?php endif; ?>

            <label>Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($edit_data['username'] ?? '') ?>" required>

            <label>Password <?= $edit_data ? '(Kosongkan jika tidak diubah)' : '' ?>:</label>
            <input type="password" name="password" <?= $edit_data ? '' : 'required' ?>>

            <label>Nama Lengkap:</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($edit_data['nama'] ?? '') ?>" required>

            <label>Alamat:</label>
            <input type="text" name="alamat" value="<?= htmlspecialchars($edit_data['alamat'] ?? '') ?>" required>

            <label>No. HP:</label>
            <input type="text" name="hp" value="<?= htmlspecialchars($edit_data['hp'] ?? '') ?>" required>

            <label>Level:</label>
            <select name="level" required>
                <option value="1" <?= ((int)($edit_data['level'] ?? 0) === 1) ? 'selected' : '' ?>>1 - Owner</option>
                <option value="2" <?= ((int)($edit_data['level'] ?? 0) === 2) ? 'selected' : '' ?>>2 - Kasir</option>
            </select>

            <button type="submit" name="<?= $edit_data ? 'edit_user' : 'tambah_user' ?>">
                <?= $edit_data ? 'Simpan Perubahan' : 'Tambah User' ?>
            </button>
            <?php if ($edit_data): ?>
                <a href="master-user.php" style="margin-left: 10px;">Batal</a>
            <?php endif; ?>
        </form>
    </div>

    <h3>Daftar Data User</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>No. HP</th>
            <th>Level</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($result_user)): ?>
        <tr>
            <td><?= (int)$row['id_user'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['nama']) ?></td>
            <td><?= htmlspecialchars($row['alamat']) ?></td>
            <td><?= htmlspecialchars($row['hp']) ?></td>
            <td><?= (int)$row['level'] === 1 ? '1 (Owner)' : ((int)$row['level'] === 2 ? '2 (Kasir)' : (int)$row['level']) ?></td>
            <td>
                <a href="master-user.php?edit=<?= (int)$row['id_user'] ?>">Edit</a> |
                <a href="master-user.php?hapus=<?= (int)$row['id_user'] ?>" onclick="return confirm('Yakin ingin menghapus user ini?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>