<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function wajib_login(): void
{
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        header("Location: login.php");
        exit;
    }
}

function tolak_jika_sudah_login(): void
{
    if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
        header("Location: index.php");
        exit;
    }
}

function wajib_level(array $allowed_levels): void
{
    wajib_login();
    $level = (int) ($_SESSION['level'] ?? 0);
    if (!in_array($level, $allowed_levels, true)) {
        echo "<script>alert('Akses ditolak! Anda tidak memiliki hak akses ke halaman ini.'); window.location='index.php';</script>";
        exit;
    }
}