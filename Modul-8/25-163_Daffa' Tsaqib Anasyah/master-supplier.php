<?php
include "koneksi.php";
include "auth.php";
wajib_level([1]);

if (isset($_POST['tambah'])) {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $telp   = mysqli_real_escape_string($conn, $_POST['telp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    mysqli_query($conn, "INSERT INTO supplier (nama, telp, alamat) VALUES ('$nama', '$telp', '$alamat')");
    header("Location: master-supplier.php");
    exit;
}

if (isset($_POST['edit'])) {
    $id     = (int) $_POST['id'];
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $telp   = mysqli_real_escape_string($conn, $_POST['telp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    mysqli_query($conn, "UPDATE supplier SET nama='$nama', telp='$telp', alamat='$alamat' WHERE id='$id'");
    header("Location: master-supplier.php");
    exit;
}

if (isset($_GET['hapus'])) {
    $id  = (int) $_GET['hapus'];
    $cek = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS jml FROM barang WHERE supplier_id='$id'"));
    if ($cek['jml'] > 0) {
        echo "<script>alert('Gagal hapus! Supplier masih digunakan pada data barang.'); window.location='master-supplier.php';</script>";
        exit;
    }
    mysqli_query($conn, "DELETE FROM supplier WHERE id='$id'");
    header("Location: master-supplier.php");
    exit;
}

$edit = isset($_GET['edit']) ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM supplier WHERE id=" . (int)$_GET['edit'])) : null;
$data = mysqli_query($conn, "SELECT * FROM supplier ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Data Supplier</title>
    <style>
        body { font-family: Arial, sans-serif; color: darkslategray; background-color: whitesmoke; margin: 0; }
        .container { padding: 0 30px 30px; }
        h2 { color: midnightblue; border-bottom: 2px solid lightgray; padding-bottom: 5px; }
        table { border-collapse: collapse; width: 100%; background: white; }
        th, td { border: 1px solid silver; padding: 10px; text-align: left; }
        th { background-color: aliceblue; color: navy; }
        .box-form { background: white; border: 1px solid silver; padding: 20px; margin-bottom: 20px; width: 400px; }
        input { width: 100%; padding: 8px; margin: 5px 0 12px; box-sizing: border-box; }
        button { background: navy; color: white; padding: 10px 15px; border: none; font-weight: bold; cursor: pointer; }
    </style>
</head>
<body>
<?php include "navbar.php"; ?>
<div class="container">
    <h2>Kelola Data Master Supplier</h2>
    <button><a href="index.php" style="text-decoration: none; color: white;">Kembali ke Halaman Utama</a></button>
    <br><br>
    <div class="box-form">
        <form method="POST">
            <?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$edit['id'] ?>"><?php endif; ?>
            <label>Nama Supplier:</label>
            <input type="text" name="nama" value="<?= htmlspecialchars($edit['nama'] ?? '') ?>" required>
            <label>No. Telepon:</label>
            <input type="text" name="telp" value="<?= htmlspecialchars($edit['telp'] ?? '') ?>" required>
            <label>Alamat:</label>
            <input type="text" name="alamat" value="<?= htmlspecialchars($edit['alamat'] ?? '') ?>" required>
            <button type="submit" name="<?= $edit ? 'edit' : 'tambah' ?>"><?= $edit ? 'Update' : 'Simpan' ?></button>
            <?php if ($edit): ?><a href="master-supplier.php" style="margin-left:10px;">Batal</a><?php endif; ?>
        </form>
    </div>
    <table>
        <tr><th>ID</th><th>Nama Supplier</th><th>Telepon</th><th>Alamat</th><th>Aksi</th></tr>
        <?php while ($r = mysqli_fetch_assoc($data)): ?>
        <tr>
            <td><?= (int)$r['id'] ?></td>
            <td><?= htmlspecialchars($r['nama']) ?></td>
            <td><?= htmlspecialchars($r['telp']) ?></td>
            <td><?= htmlspecialchars($r['alamat']) ?></td>
            <td>
                <a href="master-supplier.php?edit=<?= (int)$r['id'] ?>">Edit</a> |
                <a href="master-supplier.php?hapus=<?= (int)$r['id'] ?>" onclick="return confirm('Hapus supplier?')">Hapus</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>