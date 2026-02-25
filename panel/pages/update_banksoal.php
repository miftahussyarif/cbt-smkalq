<?php
if (!isset($_COOKIE['beeuser'])) {
    header('Content-Type: application/json');
    echo json_encode(array('ok' => false, 'message' => 'Session login tidak ditemukan.'));
    exit;
}

include "../../config/server.php";

header('Content-Type: application/json');

if (!isset($_POST['aksi']) || $_POST['aksi'] !== 'update') {
    echo json_encode(array('ok' => false, 'message' => 'Aksi tidak valid.'));
    exit;
}

$urut = isset($_POST['txt_urut']) ? intval($_POST['txt_urut']) : 0;
$kodeSoal = isset($_POST['txt_soal']) ? mysql_real_escape_string(trim($_POST['txt_soal'])) : '';
$kelas = isset($_POST['txt_kelas']) ? mysql_real_escape_string(trim($_POST['txt_kelas'])) : '';
$jurusan = isset($_POST['txt_jurusan']) ? mysql_real_escape_string(trim($_POST['txt_jurusan'])) : '';
$jumPilihan = isset($_POST['txt_jawab']) ? intval($_POST['txt_jawab']) : 5;
$jumPG = isset($_POST['txt_jumsoalz1']) ? intval($_POST['txt_jumsoalz1']) : 0;
$jumEsai = isset($_POST['txt_jumsoalz2']) ? intval($_POST['txt_jumsoalz2']) : 0;
$bobotPG = isset($_POST['txt_bobotsoalz1']) ? intval($_POST['txt_bobotsoalz1']) : 0;
$bobotEsai = isset($_POST['txt_bobotsoalz2']) ? intval($_POST['txt_bobotsoalz2']) : 0;

if ($urut <= 0 || $kodeSoal === '') {
    echo json_encode(array('ok' => false, 'message' => 'Data bank soal tidak valid.'));
    exit;
}

if ($kelas === '') {
    $kelas = 'ALL';
}
if ($jurusan === '') {
    $jurusan = 'ALL';
}
if ($jumPilihan <= 0) {
    $jumPilihan = 5;
}
if ($jumPilihan < 4) {
    $jumPilihan = 4;
}
if ($jumPilihan > 5) {
    $jumPilihan = 5;
}
if ($jumPG < 0) {
    $jumPG = 0;
}
if ($jumEsai < 0) {
    $jumEsai = 0;
}
if ($bobotPG < 0) {
    $bobotPG = 0;
}
if ($bobotEsai < 0) {
    $bobotEsai = 0;
}

$sqlGet = mysql_query("SELECT Urut, XKodeSoal, XGuru, XLevel FROM cbt_paketsoal WHERE Urut = '$urut' LIMIT 1");
if (!$sqlGet || mysql_num_rows($sqlGet) < 1) {
    echo json_encode(array('ok' => false, 'message' => 'Bank soal tidak ditemukan.'));
    exit;
}

$paket = mysql_fetch_array($sqlGet);
if ($paket['XKodeSoal'] !== $kodeSoal) {
    echo json_encode(array('ok' => false, 'message' => 'Kode soal tidak cocok.'));
    exit;
}

$isAdmin = (isset($_COOKIE['beelogin']) && $_COOKIE['beelogin'] === 'admin');
$userLogin = isset($_COOKIE['beeuser']) ? $_COOKIE['beeuser'] : '';
if (!$isAdmin && $paket['XGuru'] !== $userLogin) {
    echo json_encode(array('ok' => false, 'message' => 'Anda tidak berhak mengubah bank soal ini.'));
    exit;
}

$jumSoal = $jumPG + $jumEsai;
$levelSoal = mysql_real_escape_string($paket['XLevel']);

$sqlUpdatePaket = mysql_query("UPDATE cbt_paketsoal SET
    XKodeKelas = '$kelas',
    XKodeJurusan = '$jurusan',
    XJumPilihan = '$jumPilihan',
    XPilGanda = '$jumPG',
    XEsai = '$jumEsai',
    XPersenPil = '$bobotPG',
    XPersenEsai = '$bobotEsai',
    XJumSoal = '$jumSoal'
    WHERE Urut = '$urut'
    LIMIT 1");

if (!$sqlUpdatePaket) {
    echo json_encode(array('ok' => false, 'message' => 'Gagal update bank soal: ' . mysql_error()));
    exit;
}

mysql_query("UPDATE cbt_soal SET XKodeKelas = '$kelas', XLevel = '$levelSoal' WHERE XKodeSoal = '$kodeSoal'");

if (function_exists('bee_log')) {
    bee_log('INFO', 'BANK_SOAL_UPDATE', 'Update setting bank soal dari daftar soal', array(
        'urut' => $urut,
        'kodesoal' => $kodeSoal,
        'kelas' => $kelas,
        'jurusan' => $jurusan,
        'jum_pilihan' => $jumPilihan,
        'pg' => $jumPG,
        'esai' => $jumEsai,
        'bobot_pg' => $bobotPG,
        'bobot_esai' => $bobotEsai
    ));
}

echo json_encode(array('ok' => true, 'message' => 'Setting bank soal berhasil diperbarui.'));
exit;
?>
