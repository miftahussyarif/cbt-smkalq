<?php
include "config/server.php";
include "config/pengawasan.php";
include_once "cbt_exam_context.php";

header('Content-Type: application/json');

if (!isset($_COOKIE['PESERTA'])) {
    echo json_encode(array('ok' => false, 'locked' => false));
    exit;
}

$user = mysql_real_escape_string($_COOKIE['PESERTA']);

cbt_ensure_pengawasan_table();

$preferToken = isset($_COOKIE['CBT_TOKEN']) ? $_COOKIE['CBT_TOKEN'] : '';
$preferKode = isset($_COOKIE['CBT_KODESOAL']) ? $_COOKIE['CBT_KODESOAL'] : '';
$uj = cbt_get_attempt_context_for_student($user, $preferToken, $preferKode);
if (!$uj) {
    $uj = cbt_get_attempt_context_for_student($user);
}
if (!$uj) {
    $uj = cbt_get_schedule_context_for_student($user, $preferToken);
}
if (!$uj) {
    echo json_encode(array('ok' => false, 'locked' => false));
    exit;
}
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
