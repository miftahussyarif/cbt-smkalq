<?php
include "config/server.php";
include "config/pengawasan.php";

header('Content-Type: application/json');

if (!isset($_COOKIE['PESERTA'])) {
    echo json_encode(array('ok' => false, 'locked' => false));
    exit;
}

$user = $_COOKIE['PESERTA'];

cbt_ensure_pengawasan_table();

$sqlUjian = db_query(
    $db,
    "SELECT XTokenUjian, XKodeSoal FROM cbt_siswa_ujian WHERE XNomerUjian = :user AND XStatusUjian = '1' ORDER BY XMulaiUjian DESC LIMIT 1",
    array('user' => $user)
);
$uj = db_fetch_one($sqlUjian);
if (!$uj) {
    echo json_encode(array('ok' => false, 'locked' => false));
    exit;
}

$token = $uj['XTokenUjian'];
$kodesoal = $uj['XKodeSoal'];

$sqlLock = db_query(
    $db,
    "SELECT XIsLocked FROM cbt_pengawasan WHERE XNomerUjian = :user AND XTokenUjian = :token AND XKodeSoal = :kodesoal",
    array('user' => $user, 'token' => $token, 'kodesoal' => $kodesoal)
);
$lk = db_fetch_one($sqlLock);
if ($lk) {
    $locked = ($lk['XIsLocked'] == '1');
    echo json_encode(array('ok' => true, 'locked' => $locked));
    exit;
}

echo json_encode(array('ok' => true, 'locked' => false));
?>
