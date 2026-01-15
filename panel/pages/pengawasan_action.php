<?php
include "../../config/server.php";
include "../../config/pengawasan.php";

header('Content-Type: application/json');

$role = isset($_COOKIE['beelogin']) ? $_COOKIE['beelogin'] : '';
if (!isset($_COOKIE['beeuser']) || ($role !== '' && $role != 'admin' && $role != 'guru' && $role != 'pengawas')) {
    echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$nomer = isset($_POST['nomer']) ? mysql_real_escape_string($_POST['nomer']) : '';
$token = isset($_POST['token']) ? mysql_real_escape_string($_POST['token']) : '';
$kodesoal = isset($_POST['kodesoal']) ? mysql_real_escape_string($_POST['kodesoal']) : '';

if ($nomer === '' || $token === '' || $kodesoal === '' || ($action !== 'lock' && $action !== 'unlock')) {
    echo json_encode(array('ok' => false, 'error' => 'invalid_params'));
    exit;
}

cbt_ensure_pengawasan_table();

$now = date("Y-m-d H:i:s");
$lockedBy = isset($_COOKIE['beeuser']) ? mysql_real_escape_string($_COOKIE['beeuser']) : 'admin';

if ($action === 'lock') {
    $sql = "INSERT INTO cbt_pengawasan
        (XNomerUjian, XTokenUjian, XKodeSoal, XLastEvent, XLastEventAt, XPindahTabCount, XPrintscreenCount, XIsLocked, XLockedBy, XLockedAt, XUpdatedAt)
        VALUES
        ('$nomer', '$token', '$kodesoal', 'lock_admin', '$now', '0', '0', '1', '$lockedBy', '$now', '$now')
        ON DUPLICATE KEY UPDATE
        XLastEvent = 'lock_admin',
        XIsLocked = '1',
        XLockedBy = '$lockedBy',
        XLockedAt = '$now',
        XUpdatedAt = '$now'";
} else {
    $sql = "INSERT INTO cbt_pengawasan
        (XNomerUjian, XTokenUjian, XKodeSoal, XLastEvent, XLastEventAt, XPindahTabCount, XPrintscreenCount, XIsLocked, XLockedBy, XLockedAt, XUpdatedAt)
        VALUES
        ('$nomer', '$token', '$kodesoal', 'aman', '$now', '0', '0', '0', NULL, NULL, '$now')
        ON DUPLICATE KEY UPDATE
        XLastEvent = 'aman',
        XIsLocked = '0',
        XLockedBy = NULL,
        XLockedAt = NULL,
        XUpdatedAt = '$now'";
}

mysql_query($sql);

echo json_encode(array('ok' => true));
?>
