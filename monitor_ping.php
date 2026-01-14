<?php
include "config/server.php";
include "config/pengawasan.php";

header('Content-Type: application/json');

if (!isset($_COOKIE['PESERTA'])) {
    echo json_encode(array('ok' => false, 'rto' => false));
    exit;
}

$user = mysql_real_escape_string($_COOKIE['PESERTA']);

$sqlUjian = mysql_query("SELECT XTokenUjian, XKodeSoal FROM cbt_siswa_ujian WHERE XNomerUjian = '$user' AND XStatusUjian = '1' ORDER BY XMulaiUjian DESC LIMIT 1");
if (!$sqlUjian || mysql_num_rows($sqlUjian) < 1) {
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
