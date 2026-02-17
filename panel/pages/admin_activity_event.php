<?php
include "../../config/server.php";

header('Content-Type: application/json');

if (!isset($_COOKIE['beeuser']) || !isset($_COOKIE['beelogin'])) {
    echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
    exit;
}

$role = $_COOKIE['beelogin'];
if ($role !== 'admin' && $role !== 'guru' && $role !== 'pengawas') {
    echo json_encode(array('ok' => false, 'error' => 'forbidden'));
    exit;
}

$level = isset($_POST['level']) ? strtoupper(trim($_POST['level'])) : 'INFO';
$action = isset($_POST['action']) ? trim($_POST['action']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if ($action === '') {
    echo json_encode(array('ok' => false, 'error' => 'missing_action'));
    exit;
}

if ($message === '') {
    $message = 'Admin activity event';
}

if (!in_array($level, array('INFO', 'WARN', 'ERROR', 'FATAL'), true)) {
    $level = 'INFO';
}

$context = array();
$allowedContextKeys = array('module', 'target', 'method', 'status', 'url', 'extra');
foreach ($allowedContextKeys as $k) {
    if (isset($_POST[$k])) {
        $val = $_POST[$k];
        if (is_string($val)) {
            $val = trim($val);
            if (strlen($val) > 500) {
                $val = substr($val, 0, 500);
            }
        }
        $context[$k] = $val;
    }
}

if (function_exists('bee_log')) {
    bee_log($level, $action, $message, $context);
}

echo json_encode(array('ok' => true));
exit;
?>
