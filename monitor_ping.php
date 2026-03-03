<?php
include "config/server.php";
include "config/pengawasan.php";
include_once "cbt_exam_context.php";

header('Content-Type: application/json');

if (!isset($_COOKIE['PESERTA'])) {
    echo json_encode(array('ok' => false, 'rto' => false));
    exit;
}

$user = mysql_real_escape_string($_COOKIE['PESERTA']);

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
    echo json_encode(array('ok' => false, 'rto' => false));
    exit;
}

$host = '8.8.8.8';
$port = 53;
$timeout = 2;
$errno = 0;
$errstr = '';

$fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
if ($fp) {
    fclose($fp);
    echo json_encode(array('ok' => true, 'rto' => false));
    exit;
}

echo json_encode(array('ok' => true, 'rto' => true));
?>
