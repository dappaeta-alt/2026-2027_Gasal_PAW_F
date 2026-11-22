<?php
wajib_login();
$level    = (int) ($_SESSION['level'] ?? 0);
$namaUser = htmlspecialchars($_SESSION['nama'] ?? '');
?>
<style>
    .navbar { background-color: #1d4ed8; color: white; padding: 0 24px; height: 50px; display: flex; justify-content: space-between; align-items: center; font-family: Arial, sans-serif; margin-bottom: 24px; }
    .nav-left { display: flex; align-items: center; gap: 14px; }
    .brand { font-weight: bold; font-size: 16px; color: white; text-decoration: none; margin-right: 8px; }
    .nav-link { color: white; text-decoration: none; font-size: 14px; padding: 8px 10px; border-radius: 4px; }
    .nav-link:hover { background: rgba(255,255,255,0.18); }
    .dropdown { position: relative; display: inline-block; }
    .dropdown-content { display: none; position: absolute; top: 100%; left: 0; background: white; min-width: 170px; border: 1px solid silver; box-shadow: 0 4px 10px rgba(0,0,0,0.12); z-index: 100; }
    .dropdown-content a { display: block; padding: 10px 14px; color: darkslategray; text-decoration: none; font-size: 14px; }
    .dropdown-content a:hover { background: aliceblue; color: navy; }
    .dropdown:hover .dropdown-content { display: block; }
    .nav-right { display: flex; align-items: center; gap: 14px; font-size: 14px; }
    .btn-logout { background: crimson; color: white; text-decoration: none; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: bold; }
    .btn-logout:hover { background: darkred; }
</style>
<div class="navbar">
    <div class="nav-left">
        <a href="index.php" class="brand">Sistem Penjualan</a>
        <a href="index.php" class="nav-link">Home</a>

        <?php if ($level === 1): ?>
            <div class="dropdown">
                <a href="#" class="nav-link">Data Master &#9662;</a>
                <div class="dropdown-content">
                    <a href="master-barang.php">Data Barang</a>
                    <a href="master-supplier.php">Data Supplier</a>
                    <a href="master-pelanggan.php">Data Pelanggan</a>
                    <a href="master-user.php">Data User</a>
                </div>
            </div>
        <?php endif; ?>

        <a href="master-transaksi.php" class="nav-link">Transaksi</a>
        <a href="laporan.php" class="nav-link">Laporan</a>
    </div>

    <div class="nav-right">
        <span><?= $namaUser ?></span>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>
</div>