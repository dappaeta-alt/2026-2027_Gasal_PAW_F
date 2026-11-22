<?php
include "koneksi.php";
include "auth.php";

wajib_level([1]);

if (isset($_GET['hapus'])) {
    $id_barang = $_GET['hapus'];
    $id_aman = mysqli_real_escape_string($conn, $id_barang);

    $cek_relasi = mysqli_query($conn, "SELECT COUNT(*) AS jumlah FROM transaksi_detail WHERE barang_id = '$id_aman'");
    $data_cek = mysqli_fetch_assoc($cek_relasi);

    if ($data_cek['jumlah'] > 0) {
        echo "<script>alert('Gagal Hapus! Barang ini masih digunakan dalam transaksi.'); window.location='master-barang.php';</script>";
    } else {
        mysqli_query($conn, "DELETE FROM barang WHERE id = '$id_aman'");
        echo "<script>alert('Data barang berhasil dihapus.'); window.location='master-barang.php';</script>";
    }
}

if (isset($_POST['tambah_barang'])) {
    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $harga       = (int) $_POST['harga'];
    $stok        = (int) $_POST['stok'];
    $supplier_id = (int) $_POST['supplier_id'];

    $query_insert = "INSERT INTO barang (kode_barang, nama_barang, harga, stok, supplier_id) VALUES ('$kode_barang', '$nama_barang', '$harga', '$stok', '$supplier_id')";
    
    if (mysqli_query($conn, $query_insert)) {
        echo "<script>alert('Barang berhasil ditambahkan!'); window.location='master-barang.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan barang: " . mysqli_error($conn) . "');</script>";
    }
}

if (isset($_POST['edit_barang'])) {
    $id_barang   = (int) $_POST['id_barang'];
    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $harga       = (int) $_POST['harga'];
    $stok        = (int) $_POST['stok'];
    $supplier_id = (int) $_POST['supplier_id'];

    $query_update = "UPDATE barang SET kode_barang='$kode_barang', nama_barang='$nama_barang', harga='$harga', stok='$stok', supplier_id='$supplier_id' WHERE id='$id_barang'";

    if (mysqli_query($conn, $query_update)) {
        echo "<script>alert('Data barang berhasil diperbarui!'); window.location='master-barang.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui barang: " . mysqli_error($conn) . "');</script>";
    }
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id_edit = (int) $_GET['edit'];
    $result_edit = mysqli_query($conn, "SELECT * FROM barang WHERE id='$id_edit'");
    $edit_data = mysqli_fetch_assoc($result_edit);
}

$result_supplier = mysqli_query($conn, "SELECT * FROM supplier");

$result_barang = mysqli_query($conn, "SELECT barang.*, supplier.nama AS nama_supplier FROM barang LEFT JOIN supplier ON barang.supplier_id = supplier.id");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Data Barang - Master</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: darkslategray;
            background-color: whitesmoke;
            margin: 0;
        }

        .container {
            padding: 0 30px 30px 30px;
        }

        h2, h3 {
            color: midnightblue;
            border-bottom: 2px solid lightgray;
            padding-bottom: 5px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 25px;
            background-color: white;
        }

        th, td {
            border: 1px solid silver;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: aliceblue;
            color: navy;
            font-weight: bold;
        }

        tr:hover {
            background-color: floralwhite;
        }

        a {
            color: royalblue;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            color: crimson;
            text-decoration: underline;
        }

        .box-form {
            background-color: white;
            border: 1px solid silver;
            padding: 20px;
            margin-bottom: 25px;
            width: 400px;
        }

        input, select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        button {
            background-color: navy;
            color: white;
            padding: 10px 15px;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: midnightblue;
        }
    </style>
</head>
<body>
    <?php include "navbar.php"; ?>

    <div class="container">
        <h2>Kelola Data Master Barang</h2>
        <button><a href="index.php" style="text-decoration: none; color: white;">Kembali ke Halaman Utama</a></button>
        <br><br>

        <!-- Form Tambah Barang -->
        <div class="box-form">
            <h3 style="margin-top:0; color: midnightblue;"><?php echo $edit_data ? 'Edit Data Barang' : 'Tambah Barang Baru'; ?></h3>
            <form method="POST">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id_barang" value="<?php echo (int) $edit_data['id']; ?>">
                <?php endif; ?>

                <label>Kode Barang:</label>
                <input type="text" name="kode_barang" placeholder="Contoh: BRG01" value="<?php echo htmlspecialchars($edit_data['kode_barang'] ?? ''); ?>" required>

                <label>Nama Barang:</label>
                <input type="text" name="nama_barang" value="<?php echo htmlspecialchars($edit_data['nama_barang'] ?? ''); ?>" required>
                
                <label>Harga (Rp):</label>
                <input type="number" name="harga" value="<?php echo htmlspecialchars($edit_data['harga'] ?? ''); ?>" required>
                
                <label>Stok:</label>
                <input type="number" name="stok" value="<?php echo htmlspecialchars($edit_data['stok'] ?? ''); ?>" required>

                <label>Supplier:</label>
                <select name="supplier_id" required>
                    <option value="">-- Pilih Supplier --</option>
                    <?php while ($sup = mysqli_fetch_assoc($result_supplier)): ?>
                        <option value="<?php echo $sup['id']; ?>" <?php echo (($edit_data['supplier_id'] ?? '') == $sup['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($sup['nama']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit" name="<?php echo $edit_data ? 'edit_barang' : 'tambah_barang'; ?>">
                    <?php echo $edit_data ? 'Simpan Perubahan' : 'Simpan Barang'; ?>
                </button>
                <?php if ($edit_data): ?>
                    <a href="master-barang.php" style="margin-left:10px;">Batal</a>
                <?php endif; ?>
            </form>
        </div>

        <h3>Daftar Barang</h3>
        <table>
            <tr>
                <th>ID</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Supplier</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result_barang)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['kode_barang']); ?></td>
                <td><?php echo htmlspecialchars($row['nama_barang']); ?></td>
                <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                <td><?php echo $row['stok']; ?></td>
                <td><?php echo htmlspecialchars($row['nama_supplier']); ?></td>
                <td>
                    <a href="master-barang.php?edit=<?php echo (int) $row['id']; ?>">Edit</a> |
                    <a href="master-barang.php?hapus=<?php echo (int) $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus barang ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>
</html>