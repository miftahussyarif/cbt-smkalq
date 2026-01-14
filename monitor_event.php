<?php
include "config/server.php";
include "config/pengawasan.php";

header('Content-Type: application/json');

if (!isset($_COOKIE['PESERTA'])) {
    echo json_encode(array('ok' => false, 'error' => 'no_user'));
    exit;
}

$user = mysql_real_escape_string($_COOKIE['PESERTA']);
$event = isset($_POST['event']) ? $_POST['event'] : '';
$auto_lock = isset($_POST['auto_lock']) && $_POST['auto_lock'] == '1';
$allowed = array('aman', 'pindah_tab', 'printscreen', 'tab_hidden', 'tab_close', 'rto', 'lock_admin');
if (!in_array($event, $allowed, true)) {
    echo json_encode(array('ok' => false, 'error' => 'invalid_event'));
    exit;
}

cbt_ensure_pengawasan_table();

$sqlUjian = mysql_query("SELECT XTokenUjian, XKodeSoal FROM cbt_siswa_ujian WHERE XNomerUjian = '$user' AND XStatusUjian = '1' ORDER BY XMulaiUjian DESC LIMIT 1");
if (!$sqlUjian || mysql_num_rows($sqlUjian) < 1) {
    echo json_encode(array('ok' => false, 'error' => 'no_active_exam'));
    exit;
}

$uj = mysql_fetch_array($sqlUjian);
$token = mysql_real_escape_string($uj['XTokenUjian']);
$kodesoal = mysql_real_escape_string($uj['XKodeSoal']);
$now = date("Y-m-d H:i:s");

$incPindah = ($event === 'pindah_tab' || $event === 'tab_hidden' || $event === 'tab_close') ? 1 : 0;
$incPrint = ($event === 'printscreen') ? 1 : 0;

$existing = null;
if ($event === 'aman') {
    $sqlExisting = mysql_query("SELECT XPindahTabCount, XPrintscreenCount, XIsLocked FROM cbt_pengawasan WHERE XNomerUjian = '$user' AND XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
    if ($sqlExisting && mysql_num_rows($sqlExisting) > 0) {
        $existing = mysql_fetch_array($sqlExisting);
        if ($existing['XIsLocked'] == '1' || (int) $existing['XPindahTabCount'] > 0 || (int) $existing['XPrintscreenCount'] > 0) {
            mysql_query("UPDATE cbt_pengawasan SET XUpdatedAt = '$now' WHERE XNomerUjian = '$user' AND XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
            echo json_encode(array('ok' => true));
            exit;
        }
    }
}

$lockSql = '';
if ($auto_lock) {
    $lockSql = ",
    XIsLocked = '1',
    XLockedBy = 'system',
    XLockedAt = '$now'";
}

$sql = "INSERT INTO cbt_pengawasan
    (XNomerUjian, XTokenUjian, XKodeSoal, XLastEvent, XLastEventAt, XPindahTabCount, XPrintscreenCount, XIsLocked, XUpdatedAt)
    VALUES
    ('$user', '$token', '$kodesoal', '$event', '$now', '$incPindah', '$incPrint', '0', '$now')
    ON DUPLICATE KEY UPDATE
    XLastEvent = '$event',
    XLastEventAt = '$now',
    XPindahTabCount = XPindahTabCount + $incPindah,
    XPrintscreenCount = XPrintscreenCount + $incPrint,
    XUpdatedAt = '$now'$lockSql";

mysql_query($sql);

echo json_encode(array('ok' => true));
?>
