<?php
include "koneksi.php";
include "auth.php";

tolak_jika_sudah_login();

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username'] ?? ''));
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = "Username dan password wajib diisi.";
    } else {
        $query  = "SELECT * FROM user WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($conn, $query);
        $user   = mysqli_fetch_assoc($result);

        $validPass = false;
        if ($user) {
            if (password_get_info($user['password'])['algo'] !== null) {
                $validPass = password_verify($password, $user['password']);
            } else {
                $validPass = hash_equals(strtolower($user['password']), md5($password));
            }
        }

        if ($user && $validPass) {
            if (!in_array((int)$user['level'], [1, 2], true)) {
                $error = "Akun ini bukan level Owner (1) atau Kasir (2).";
            } else {
                session_regenerate_id(true);
                $_SESSION['login']    = true;
                $_SESSION['id_user']  = (int) $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama']     = $user['nama'];
                $_SESSION['level']    = (int) $user['level'];

                header("Location: index.php");
                exit;
            }
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistem Penjualan</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: whitesmoke; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 28px; border: 1px solid silver; border-radius: 6px; width: 340px; }
        h2 { color: midnightblue; margin-top: 0; border-bottom: 2px solid lightgray; padding-bottom: 8px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: darkslategray; font-size: 14px; }
        input { width: 100%; padding: 9px; margin-bottom: 14px; border: 1px solid silver; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: navy; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer; }
        button:hover { background: midnightblue; }
        .error { background: #fee2e2; color: #991b1b; padding: 9px; border-radius: 4px; margin-bottom: 14px; font-size: 13px; }
    </style>
</head>
<body>
<div class="login-box">
    <h2>Login Sistem Penjualan</h2>
    <?php if ($error !== ""): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button type="submit" name="login">Login</button>
    </form>
</div>
</body>
</html>