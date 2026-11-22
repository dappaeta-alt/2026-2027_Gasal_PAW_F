<?php
include "koneksi.php";
include "auth.php";

wajib_level([1, 2]);

// Query Data Master Transaksi & Pelanggan
$query_master = "SELECT transaksi.*, pelanggan.nama FROM transaksi JOIN pelanggan ON transaksi.pelanggan_id = pelanggan.id ORDER BY transaksi.id DESC";
$result_master = mysqli_query($conn, $query_master);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Master Transaksi Penjualan</title>
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
        <h2>Kelola Master Transaksi Penjualan</h2>
        <button><a href="index.php" style="text-decoration: none; color: white;">Kembali ke Halaman Utama</a></button>
        <br><br>
        <button><a href="master-barang.php" style="text-decoration: none; color: white;">Kelola Data Barang</a></button>
        <br><br>

        <h3>Daftar Master Transaksi</h3>
        <table>
            <tr>
                <th>ID Transaksi</th>
                <th>Waktu Transaksi</th>
                <th>Keterangan</th>
                <th>Pelanggan</th>
                <th>Total Harga</th>
                <th>Aksi</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result_master)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['waktu_transaksi']; ?></td>
                <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                <td style="color: darkgreen; font-weight: bold;">Rp <?php echo number_format($row['total'], 0, ',', '.'); ?></td>
                <td>
                    <a href="detil-transaksi.php?id=<?php echo $row['id']; ?>">Pilih & Kelola Detil</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>
</html>