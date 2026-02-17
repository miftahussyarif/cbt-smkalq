<?php
if (!isset($_COOKIE['beeuser'])) {
    header("Location: login.php");
    exit;
}

include "../../config/server.php";

$urut = isset($_REQUEST['urut']) ? mysql_real_escape_string($_REQUEST['urut']) : '';
$usernameLama = isset($_REQUEST['username_lama']) ? trim($_REQUEST['username_lama']) : '';
$username = isset($_REQUEST['txt_user']) ? trim($_REQUEST['txt_user']) : null;
$nama = isset($_REQUEST['txt_nama']) ? trim($_REQUEST['txt_nama']) : null;
$password = isset($_REQUEST['txt_pass']) ? trim($_REQUEST['txt_pass']) : '';
$nik = isset($_REQUEST['txt_nik']) ? trim($_REQUEST['txt_nik']) : '';
$hp = isset($_REQUEST['txt_hp']) ? trim($_REQUEST['txt_hp']) : '';
$email = isset($_REQUEST['txt_email']) ? trim($_REQUEST['txt_email']) : '';

if ($urut === '') {
    $fallbackUser = $usernameLama !== '' ? $usernameLama : ((isset($username) && $username !== null) ? $username : '');
    if ($fallbackUser !== '') {
        $fallbackUserEsc = mysql_real_escape_string($fallbackUser);
        $qFind = mysql_query("SELECT Urut FROM cbt_user WHERE Username = '$fallbackUserEsc' LIMIT 1");
        if ($qFind && mysql_num_rows($qFind) > 0) {
            $rowFind = mysql_fetch_assoc($qFind);
            $urut = mysql_real_escape_string($rowFind['Urut']);
        }
    }
}

if ($urut === '') {
    echo "<script>alert('Data wajib belum lengkap (ID user tidak ditemukan). Silakan refresh halaman lalu ulangi edit user.');window.location='?modul=buat_user';</script>";
    exit;
}

$sqlCurrent = mysql_query("SELECT Username, Nama FROM cbt_user WHERE Urut = '$urut' LIMIT 1");
if (!$sqlCurrent || mysql_num_rows($sqlCurrent) < 1) {
    echo "<script>alert('Data user tidak ditemukan.');window.location='?modul=buat_user';</script>";
    exit;
}
$current = mysql_fetch_assoc($sqlCurrent);

if ($username === null || $username === '') {
    $username = isset($current['Username']) ? trim($current['Username']) : '';
}
if ($nama === null || $nama === '') {
    $nama = isset($current['Nama']) ? trim($current['Nama']) : '';
}
if ($username === '' || $nama === '') {
    echo "<script>alert('Data wajib belum lengkap (Username/Nama).');window.location='?modul=buat_user';</script>";
    exit;
}

$usernameEsc = mysql_real_escape_string($username);
$namaEsc = mysql_real_escape_string($nama);
$nikEsc = mysql_real_escape_string($nik);
$hpEsc = mysql_real_escape_string($hp);
$emailEsc = mysql_real_escape_string($email);

$cekUser = mysql_query("SELECT Urut FROM cbt_user WHERE Username = '$usernameEsc' AND Urut <> '$urut' LIMIT 1");
if ($cekUser && mysql_num_rows($cekUser) > 0) {
    echo "<script>alert('Username sudah digunakan user lain.');window.location='?modul=buat_user';</script>";
    exit;
}

$sql = "UPDATE cbt_user SET 
    Username = '$usernameEsc',
    Nama = '$namaEsc',
    NIP = '$nikEsc',
    HP = '$hpEsc',
    FacebookID = '$emailEsc'";

if ($password !== '') {
    $passEnc = md5($password);
    $sql .= ", Password = '$passEnc'";
}

$sql .= " WHERE Urut = '$urut'";
$ok = mysql_query($sql);

if (function_exists('bee_log')) {
    bee_log($ok ? 'INFO' : 'ERROR', 'ADMIN_UPDATE_USER', $ok ? 'Berhasil ubah data user' : 'Gagal ubah data user', array(
        'target_urut' => $urut,
        'target_user' => $username,
        'mysql_error' => $ok ? '' : mysql_error()
    ));
}

if ($ok) {
    echo "<script>alert('Data user berhasil diubah.');window.location='?modul=buat_user';</script>";
} else {
    echo "<script>alert('Gagal ubah data user.');window.location='?modul=buat_user';</script>";
}
