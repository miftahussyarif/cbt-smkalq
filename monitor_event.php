<?php
include "config/server.php";
include "config/pengawasan.php";

header('Content-Type: application/json');

if (!isset($_COOKIE['PESERTA'])) {
    echo json_encode(array('ok' => false, 'error' => 'no_user'));
    exit;
}

$user = mysql_real_escape_string($_COOKIE['PESERTA']);
$event = isset($_POST['event']) ? trim($_POST['event']) : '';
$auto_lock = isset($_POST['auto_lock']) && $_POST['auto_lock'] == '1';
$allowed = array('aman', 'pindah_tab', 'app_switch', 'pointer_leave', 'split_view', 'key_violation', 'printscreen', 'tab_hidden', 'tab_close', 'rto', 'lock_admin');
if (!in_array($event, $allowed, true)) {
    if (function_exists('bee_log')) {
        bee_log('WARN', 'MONITOR_EVENT_INVALID', 'Event pengawasan tidak valid', array(
            'event' => $event,
            'user' => $user
        ));
    }
    echo json_encode(array('ok' => false, 'error' => 'invalid_event'));
    exit;
}

cbt_ensure_pengawasan_table();

$sqlUjian = mysql_query("SELECT XTokenUjian, XKodeSoal FROM cbt_siswa_ujian WHERE XNomerUjian = '$user' AND XStatusUjian = '1' ORDER BY XTglUjian DESC, XMulaiUjian DESC LIMIT 1");
if (!$sqlUjian || mysql_num_rows($sqlUjian) < 1) {
    // Fallback: on fast page close/submit race, status may already change from active.
    $sqlUjian = mysql_query("SELECT XTokenUjian, XKodeSoal, XStatusUjian FROM cbt_siswa_ujian WHERE XNomerUjian = '$user' ORDER BY XTglUjian DESC, XMulaiUjian DESC LIMIT 1");
    if (!$sqlUjian || mysql_num_rows($sqlUjian) < 1) {
        if (function_exists('bee_log')) {
            bee_log('WARN', 'MONITOR_EVENT_NO_EXAM', 'Event pengawasan tidak menemukan data ujian siswa', array(
                'event' => $event,
                'user' => $user
            ));
        }
        echo json_encode(array('ok' => false, 'error' => 'no_active_exam'));
        exit;
    }
}

$uj = mysql_fetch_array($sqlUjian);
$token = mysql_real_escape_string($uj['XTokenUjian']);
$kodesoal = mysql_real_escape_string($uj['XKodeSoal']);
$now = date("Y-m-d H:i:s");
if ($event === 'tab_close' && function_exists('bee_log')) {
    bee_log('INFO', 'MONITOR_TAB_CLOSE', 'Deteksi siswa menutup tab ujian', array(
        'user' => $user,
        'token' => $token,
        'kodesoal' => $kodesoal,
        'auto_lock' => $auto_lock ? 1 : 0,
        'reason' => isset($_POST['reason']) ? $_POST['reason'] : ''
    ));
}

$incPindah = ($event === 'pindah_tab' || $event === 'tab_hidden' || $event === 'tab_close') ? 1 : 0;
$incAppSwitch = ($event === 'app_switch' || $event === 'pointer_leave' || $event === 'split_view' || $event === 'key_violation') ? 1 : 0;
$incPrint = ($event === 'printscreen') ? 1 : 0;

$existing = null;
if ($event === 'aman') {
    $sqlExisting = mysql_query("SELECT XPindahTabCount, XAppSwitchCount, XPrintscreenCount, XIsLocked FROM cbt_pengawasan WHERE XNomerUjian = '$user' AND XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
    if ($sqlExisting && mysql_num_rows($sqlExisting) > 0) {
        $existing = mysql_fetch_array($sqlExisting);
        if ($existing['XIsLocked'] == '1' || (int) $existing['XPindahTabCount'] > 0 || (int) $existing['XAppSwitchCount'] > 0 || (int) $existing['XPrintscreenCount'] > 0) {
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
    (XNomerUjian, XTokenUjian, XKodeSoal, XLastEvent, XLastEventAt, XPindahTabCount, XAppSwitchCount, XPrintscreenCount, XIsLocked, XUpdatedAt)
    VALUES
    ('$user', '$token', '$kodesoal', '$event', '$now', '$incPindah', '$incAppSwitch', '$incPrint', '0', '$now')
    ON DUPLICATE KEY UPDATE
    XLastEvent = '$event',
    XLastEventAt = '$now',
    XPindahTabCount = XPindahTabCount + $incPindah,
    XAppSwitchCount = XAppSwitchCount + $incAppSwitch,
    XPrintscreenCount = XPrintscreenCount + $incPrint,
    XUpdatedAt = '$now'$lockSql";

if (!mysql_query($sql)) {
    if (function_exists('bee_log')) {
        bee_log('ERROR', 'MONITOR_EVENT_SAVE_FAILED', 'Gagal menyimpan event pengawasan', array(
            'event' => $event,
            'user' => $user,
            'token' => $token,
            'kodesoal' => $kodesoal,
            'mysql_error' => mysql_error()
        ));
    }
    echo json_encode(array('ok' => false, 'error' => 'db_error'));
    exit;
}

echo json_encode(array('ok' => true));
?>
