<?php
include "koneksi.php";
include "auth.php";

wajib_level([1, 2]);

// 1. Validasi Keamanan: Wajib melalui master-transaksi.php (atau detil-transaksi.php saat refresh POST) dan membawa parameter ID
if (!isset($_SERVER['HTTP_REFERER']) || (strpos($_SERVER['HTTP_REFERER'], 'master-transaksi.php') === false && strpos($_SERVER['HTTP_REFERER'], 'detil-transaksi.php') === false) || !isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Akses ditolak! Anda harus melalui halaman Master Transaksi.'); window.location='master-transaksi.php';</script>";
    exit;
}

$id_transaksi = $_GET['id'];
$id_aman = mysqli_real_escape_string($conn, $id_transaksi);

// 2. Ambil data informasi master transaksi & nama pelanggan
$query_header = "SELECT transaksi.*, pelanggan.nama FROM transaksi JOIN pelanggan ON transaksi.pelanggan_id = pelanggan.id WHERE transaksi.id = '$id_aman'";
$result_header = mysqli_query($conn, $query_header);
$data_transaksi = mysqli_fetch_assoc($result_header);

// Jika ID transaksi tidak valid atau tidak ada di database
if (!$data_transaksi) {
    echo "<script>alert('Data transaksi tidak ditemukan!'); window.location='master-transaksi.php';</script>";
    exit;
}

// 3. Ambil data rincian (detil) barang untuk transaksi ini
$query_detil = "SELECT transaksi_detail.*, barang.nama_barang, barang.harga FROM transaksi_detail JOIN barang ON transaksi_detail.barang_id = barang.id WHERE transaksi_detail.transaksi_id = '$id_aman'";
$result_detil = mysqli_query($conn, $query_detil);

// Kumpulkan ID barang yang sudah dipilih untuk dropdown unik (disabled)
$barang_sudah_dipilih = [];
if ($result_detil) {
    while ($row_det = mysqli_fetch_assoc($result_detil)) {
        $barang_sudah_dipilih[] = $row_det['barang_id'];
    }
    mysqli_data_seek($result_detil, 0);
}

// 4. Ambil seluruh data barang untuk pilihan dropdown
$query_barang = "SELECT * FROM barang";
$result_barang = mysqli_query($conn, $query_barang);

// 5. Proses Tambah Item Detil & Hitung Otomatis Total Transaksi
if (isset($_POST['tambah_item'])) {
    $id_b = mysqli_real_escape_string($conn, $_POST['id_barang']);
    $qty  = (int) $_POST['qty'];

    // Ambil harga satuan barang
    $q_harga = mysqli_query($conn, "SELECT harga FROM barang WHERE id = '$id_b'");
    $d_harga = mysqli_fetch_assoc($q_harga);
    $harga_satuan = $d_harga['harga'] ?? 0;

    // A. Simpan ke tabel transaksi_detail
    $query_insert = "INSERT INTO transaksi_detail (transaksi_id, barang_id, harga, qty) VALUES ('$id_aman', '$id_b', '$harga_satuan', '$qty')";
    mysqli_query($conn, $query_insert);

    // B. Hitung ulang total transaksi dengan SUM(harga * qty) dari seluruh detail milik transaksi yang sama
    $q_hitung = mysqli_query($conn, "SELECT SUM(harga * qty) AS total_baru FROM transaksi_detail WHERE transaksi_id = '$id_aman'");
    $d_hitung = mysqli_fetch_assoc($q_hitung);
    $total_baru = $d_hitung['total_baru'] ?? 0;

    // C. Update kolom total pada tabel transaksi utama
    mysqli_query($conn, "UPDATE transaksi SET total = '$total_baru' WHERE id = '$id_aman'");

    // Refresh halaman detail
    header("Location: detil-transaksi.php?id=" . $id_aman);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rincian Detil Transaksi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: darkslategray;
            background-color: whitesmoke;
            margin: 0;
        }

        .container {
            max-width: 900px;
            margin: 0 auto 30px auto;
            background: white;
            padding: 30px;
            border: 1px solid silver;
        }

        h2, h3 {
            color: midnightblue;
            margin-top: 0;
            border-bottom: 2px solid lightgray;
            padding-bottom: 8px;
        }

        .panel-info {
            background-color: aliceblue;
            border-left: 5px solid navy;
            padding: 15px;
            margin-bottom: 25px;
        }

        .panel-info p {
            margin: 5px 0;
        }

        .form-panel {
            background-color: white;
            border: 1px solid silver;
            padding: 20px;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: darkslategray;
        }

        select, input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid silver;
            box-sizing: border-box;
        }

        .btn-submit {
            background-color: navy;
            color: white;
            padding: 10px 20px;
            border: none;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: midnightblue;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: white;
        }

        th, td {
            border: 1px solid silver;
            padding: 12px;
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

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: royalblue;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link:hover {
            color: crimson;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container">
    <a href="master-transaksi.php" class="back-link">&larr; Kembali ke Daftar Master Transaksi</a>
    
    <h2>Form Rincian Detil Transaksi</h2>

    <!-- Informasi Ringkas Transaksi -->
    <div class="panel-info">
        <p><strong>ID Transaksi:</strong> #<?php echo $data_transaksi['id']; ?></p>
        <p><strong>Waktu:</strong> <?php echo $data_transaksi['waktu_transaksi']; ?></p>
        <p><strong>Pelanggan:</strong> <?php echo htmlspecialchars($data_transaksi['nama']); ?></p>
        <p><strong>Keterangan:</strong> <?php echo htmlspecialchars($data_transaksi['keterangan']); ?></p>
        <p style="font-size: 1.15em; margin-top: 10px;"><strong>Total Tagihan:</strong> <span style="color: darkgreen; font-weight: bold;">Rp <?php echo number_format($data_transaksi['total'], 0, ',', '.'); ?></span></p>
    </div>

    <!-- Form Tambah Item Detil -->
    <div class="form-panel">
        <h3>Tambah Rincian Barang</h3>
        <form method="POST">
            <label>Pilih Barang:</label>
            <select name="id_barang" required>
                <option value="">-- Pilih Barang dari Stok --</option>
                <?php while ($b = mysqli_fetch_assoc($result_barang)): ?>
                    <?php $sudah_dipilih = in_array($b['id'], $barang_sudah_dipilih); ?>
                    <option value="<?php echo $b['id']; ?>" <?php echo $sudah_dipilih ? 'disabled style="color: #999;"' : ''; ?>>
                        <?php echo $b['nama_barang']; ?> (Stok: <?php echo $b['stok']; ?> - Rp <?php echo number_format($b['harga'], 0, ',', '.'); ?>) <?php echo $sudah_dipilih ? '[Sudah Dipilih]' : ''; ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Jumlah (Qty):</label>
            <input type="number" name="qty" value="1" min="1" required>

            <button type="submit" name="tambah_item" class="btn-submit">+ Tambahkan ke Transaksi</button>
        </form>
    </div>

    <!-- Tabel Rincian Barang -->
    <h3>Daftar Item Pembelian</h3>
    <?php if ($result_detil && mysqli_num_rows($result_detil) > 0): ?>
        <table>
            <tr>
                <th>Nama Barang</th>
                <th>Harga Satuan</th>
                <th>Qty</th>
                <th>Subtotal</th>
            </tr>
            <?php while ($detil = mysqli_fetch_assoc($result_detil)): ?>
            <tr>
                <td><?php echo htmlspecialchars($detil['nama_barang']); ?></td>
                <td>Rp <?php echo number_format($detil['harga'], 0, ',', '.'); ?></td>
                <td><?php echo $detil['qty']; ?></td>
                <td style="font-weight: bold; color: navy;">Rp <?php echo number_format($detil['harga'] * $detil['qty'], 0, ',', '.'); ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p style="color: crimson; font-style: italic;">Belum ada rincian barang pada transaksi ini.</p>
    <?php endif; ?>
</div>

</body>
</html>