<?php
include "config/server.php";
include "config/pengawasan.php";

header('Content-Type: application/json');

if (!isset($_COOKIE['PESERTA'])) {
    echo json_encode(array('ok' => false, 'locked' => false));
    exit;
}

$user = mysql_real_escape_string($_COOKIE['PESERTA']);

cbt_ensure_pengawasan_table();

$sqlUjian = mysql_query("SELECT XTokenUjian, XKodeSoal FROM cbt_siswa_ujian WHERE XNomerUjian = '$user' AND XStatusUjian = '1' ORDER BY XMulaiUjian DESC LIMIT 1");
if (!$sqlUjian || mysql_num_rows($sqlUjian) < 1) {
    echo json_encode(array('ok' => false, 'locked' => false));
    exit;
}

$uj = mysql_fetch_array($sqlUjian);
$token = mysql_real_escape_string($uj['XTokenUjian']);
$kodesoal = mysql_real_escape_string($uj['XKodeSoal']);

$sqlLock = mysql_query("SELECT XIsLocked FROM cbt_pengawasan WHERE XNomerUjian = '$user' AND XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
if ($sqlLock && mysql_num_rows($sqlLock) > 0) {
    $lk = mysql_fetch_array($sqlLock);
    $locked = ($lk['XIsLocked'] == '1');
    echo json_encode(array('ok' => true, 'locked' => $locked));
    exit;
}

echo json_encode(array('ok' => true, 'locked' => false));
?>
