<?php
// Cek login
if (!isset($_COOKIE['beeuser'])) {
    header("Location: login.php");
    exit;
}

// Hanya admin yang bisa mengubah password
if ($_COOKIE['beelogin'] != 'admin') {
    echo "<script>alert('Anda tidak memiliki akses!'); window.history.back();</script>";
    exit;
}

require_once __DIR__ . "/../../config/server.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $urut = $_POST['urut'];
    $password_baru = $_POST['password_baru'];
    $password_konfirmasi = $_POST['password_konfirmasi'];

    // Validasi password
    if ($password_baru != $password_konfirmasi) {
        echo "<script>alert('Password tidak cocok!'); window.history.back();</script>";
        exit;
    }

    if (strlen($password_baru) < 4) {
        echo "<script>alert('Password minimal 4 karakter!'); window.history.back();</script>";
        exit;
    }

    // Hash password dengan password_hash untuk kompatibilitas PHP 8.3
    $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);

    // Update password
    try {
        db_query(
            $db,
            "UPDATE cbt_user SET Password = :password WHERE Urut = :urut",
            array(':password' => $password_hash, ':urut' => $urut)
        );
        echo "<script>alert('Password berhasil diubah!'); window.location='index.php?modul=buat_user';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Gagal mengubah password!'); window.history.back();</script>";
    }
} else {
    header("Location: index.php?modul=buat_user");
}
?>
