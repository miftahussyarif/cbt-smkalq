<?php
include "config/server.php";
include "config/pengawasan.php";

header('Content-Type: application/json');

if (!isset($_COOKIE['PESERTA'])) {
    echo json_encode(array('ok' => false, 'error' => 'no_user'));
    exit;
}

$user = $_COOKIE['PESERTA'];
$event = isset($_POST['event']) ? $_POST['event'] : '';
$auto_lock = isset($_POST['auto_lock']) && $_POST['auto_lock'] == '1';
$allowed = array('aman', 'pindah_tab', 'printscreen', 'tab_hidden', 'tab_close', 'rto', 'lock_admin');
if (!in_array($event, $allowed, true)) {
    echo json_encode(array('ok' => false, 'error' => 'invalid_event'));
    exit;
}

cbt_ensure_pengawasan_table();

$sqlUjian = db_query(
    $db,
    "SELECT XTokenUjian, XKodeSoal FROM cbt_siswa_ujian WHERE XNomerUjian = :user AND XStatusUjian = '1' ORDER BY XMulaiUjian DESC LIMIT 1",
    array('user' => $user)
);
$uj = db_fetch_one($sqlUjian);
if (!$uj) {
    echo json_encode(array('ok' => false, 'error' => 'no_active_exam'));
    exit;
}

$token = $uj['XTokenUjian'];
$kodesoal = $uj['XKodeSoal'];
$now = date("Y-m-d H:i:s");

$incPindah = ($event === 'pindah_tab' || $event === 'tab_hidden' || $event === 'tab_close') ? 1 : 0;
$incPrint = ($event === 'printscreen') ? 1 : 0;

$existing = null;
if ($event === 'aman') {
    $sqlExisting = db_query(
        $db,
        "SELECT XPindahTabCount, XPrintscreenCount, XIsLocked FROM cbt_pengawasan WHERE XNomerUjian = :user AND XTokenUjian = :token AND XKodeSoal = :kodesoal",
        array('user' => $user, 'token' => $token, 'kodesoal' => $kodesoal)
    );
    $existing = db_fetch_one($sqlExisting);
    if ($existing) {
        if ($existing['XIsLocked'] == '1' || (int) $existing['XPindahTabCount'] > 0 || (int) $existing['XPrintscreenCount'] > 0) {
            db_query(
                $db,
                "UPDATE cbt_pengawasan SET XUpdatedAt = :now WHERE XNomerUjian = :user AND XTokenUjian = :token AND XKodeSoal = :kodesoal",
                array('now' => $now, 'user' => $user, 'token' => $token, 'kodesoal' => $kodesoal)
            );
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
    XLockedAt = :now_locked";
}

$sql = "INSERT INTO cbt_pengawasan
    (XNomerUjian, XTokenUjian, XKodeSoal, XLastEvent, XLastEventAt, XPindahTabCount, XPrintscreenCount, XIsLocked, XUpdatedAt)
    VALUES
    (:user, :token, :kodesoal, :event_ins, :now_ins, :inc_pindah_ins, :inc_print_ins, '0', :now_ins2)
    ON DUPLICATE KEY UPDATE
    XLastEvent = :event_up,
    XLastEventAt = :now_up,
    XPindahTabCount = XPindahTabCount + :inc_pindah_up,
    XPrintscreenCount = XPrintscreenCount + :inc_print_up,
    XUpdatedAt = :now_up2$lockSql";

$params = array(
    'user' => $user,
    'token' => $token,
    'kodesoal' => $kodesoal,
    'event_ins' => $event,
    'now_ins' => $now,
    'inc_pindah_ins' => $incPindah,
    'inc_print_ins' => $incPrint,
    'now_ins2' => $now,
    'event_up' => $event,
    'now_up' => $now,
    'inc_pindah_up' => $incPindah,
    'inc_print_up' => $incPrint,
    'now_up2' => $now,
);
if ($auto_lock) {
    $params['now_locked'] = $now;
}

db_query($db, $sql, $params);

echo json_encode(array('ok' => true));
?>
