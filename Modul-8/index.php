<?php
include "auth.php";
wajib_login();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Home - Sistem Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; color: darkslategray; background-color: whitesmoke; margin: 0; }
        .container { padding: 0 30px 30px; }
        h2 { color: midnightblue; border-bottom: 2px solid lightgray; padding-bottom: 5px; }
    </style>
</head>
<body>
<?php include "navbar.php"; ?>
<div class="container">
    <h2>Home</h2>
    <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong> (<?= (int)$_SESSION['level'] === 1 ? 'Owner' : 'Kasir' ?>).</p>
</div>
</body>
</html>