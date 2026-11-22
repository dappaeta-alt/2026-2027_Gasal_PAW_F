<?php
include "koneksi.php";
include "auth.php";

wajib_level([1, 2]);

$labels = [];
$data_total = [];
$rekap = [];
$total_pendapatan = 0;
$total_pelanggan = 0;

if (!empty($_GET['mulai']) && !empty($_GET['selesai'])) {
    $mulai = $_GET['mulai'];
    $selesai = $_GET['selesai'];

    // Query agregasi total pendapatan harian
    $q = "SELECT DATE(waktu_transaksi) AS tgl, SUM(total) AS total_harian 
        FROM transaksi WHERE waktu_transaksi BETWEEN '$mulai' AND '$selesai' 
        GROUP BY DATE(waktu_transaksi) ORDER BY tgl ASC";
    $res = mysqli_query($conn, $q);
    while ($r = mysqli_fetch_assoc($res)) {
        $labels[] = $r['tgl'];
        $data_total[] = $r['total_harian'];
        $rekap[] = $r;
        $total_pendapatan += $r['total_harian'];
    }

    // Query jumlah pelanggan unik
    $qp = "SELECT COUNT(DISTINCT pelanggan_id) AS jml FROM transaksi 
        WHERE waktu_transaksi BETWEEN '$mulai' AND '$selesai'";
    $rp = mysqli_fetch_assoc(mysqli_query($conn, $qp));
    $total_pelanggan = $rp['jml'] ?? 0;

    // Proses export Excel langsung dari file ini
    if (isset($_GET['export']) && $_GET['export'] == 'excel') {
        header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=Laporan_Penjualan.xls");
        ?>
        <h3>Laporan Penjualan (<?= $mulai; ?> s/d <?= $selesai; ?>)</h3>
        <table border="1">
            <tr>
                <th>No</th>
                <th>Total</th>
                <th>Tanggal</th>
            </tr>
            <?php $no = 1; foreach ($rekap as $row): ?>
                <tr>
                    <td><?= $no++; ?></td>
                    <td>Rp. <?= number_format($row['total_harian'], 0, ',', '.'); ?></td>
                    <td><?= $row['tgl']; ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="2"><b>Jumlah Pelanggan</b></td>
                <td><b><?= $total_pelanggan; ?> Orang</b></td>
            </tr>
            <tr>
                <td colspan="2"><b>Jumlah Pendapatan</b></td>
                <td><b>Rp. <?= number_format($total_pendapatan, 0, ',', '.'); ?></b></td>
            </tr>
        </table>
        <?php
        exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan dan Grafik</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        h1, h2 {
            color: midnightblue;
            border-bottom: 2px solid lightgray;
            padding-bottom: 5px;
        }
        .box-form {
            background-color: white;
            border: 1px solid silver;
            padding: 15px 20px;
            margin-top: 15px;
            margin-bottom: 20px;
        }
        input[type="date"] {
            padding: 7px 10px;
            border: 1px solid silver;
            margin-right: 10px;
        }
        button {
            background-color: navy;
            color: white;
            padding: 8px 15px;
            border: none;
            font-weight: bold;
            cursor: pointer;
            margin-right: 5px;
        }
        button:hover {
            background-color: midnightblue;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
            margin-bottom: 15px;
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
        @media print {
            .no-print, .navbar {
                display: none !important;
            }
            body {
                background-color: white;
            }
            .container {
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <?php include "navbar.php"; ?>

    <div class="container">
        <h2>Laporan Penjualan</h2>
        <div class="no-print">
            <a href="index.php"><button type="button">Kembali ke Halaman Utama</button></a>
        </div>

        <div class="no-print box-form">
            <h1>Filter Laporan Penjualan</h1>
            <form method="GET" style="display: inline;">
                Dari: <input type="date" name="mulai" value="<?= $_GET['mulai'] ?? '' ?>" required>
                Sampai: <input type="date" name="selesai" value="<?= $_GET['selesai'] ?? '' ?>" required>
                <button type="submit">Tampilkan</button>
            </form>
            <button onclick="window.print()">Cetak</button>
            <a href="?mulai=<?= $_GET['mulai'] ?? '' ?>&selesai=<?= $_GET['selesai'] ?? '' ?>&export=excel">
                <button type="button">Excel</button>
            </a>
        </div>

        <?php if (!empty($labels)): ?>
            <h2>Grafik Penjualan</h2>
            <div style="width: 600px; height: 300px; background: white; border: 1px solid silver; padding: 15px; margin-bottom: 20px;">
                <canvas id="myChart"></canvas>
            </div>
            <script>
                const ctx = document.getElementById('myChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($labels); ?>,
                        datasets: [{
                            label: 'Total Penjualan',
                            data: <?= json_encode($data_total); ?>,
                            backgroundColor: 'navy'
                        }]
                    }
                });
            </script>

            <h2>Rekapitulasi</h2>
            <table>
                <tr>
                    <th>No</th>
                    <th>Total</th>
                    <th>Tanggal</th>
                </tr>
                <?php $no = 1; foreach ($rekap as $row): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>Rp. <?= number_format($row['total_harian'], 0, ',', '.'); ?></td>
                        <td><?= $row['tgl']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
            <p><b>Jumlah Pelanggan:</b> <?= $total_pelanggan; ?> Orang | <b>Jumlah Pendapatan:</b> Rp. <?= number_format($total_pendapatan, 0, ',', '.'); ?></p>
        <?php endif; ?>
    </div>

</body>
</html>