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

include "../../config/server.php";

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

    // Hash password dengan MD5 (sesuai dengan cara simpan user baru)
    $password_hash = md5($password_baru);

    // Update password
    $sql = mysql_query("UPDATE cbt_user SET Password = '$password_hash' WHERE Urut = '$urut'");

    if ($sql) {
        echo "<script>alert('Password berhasil diubah!'); window.location='index.php?modul=buat_user';</script>";
    } else {
        echo "<script>alert('Gagal mengubah password!'); window.history.back();</script>";
    }
} else {
    header("Location: index.php?modul=buat_user");
}
?>