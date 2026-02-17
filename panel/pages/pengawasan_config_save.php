<?php
include "../../config/server.php";
header('Content-Type: application/json');

if (!isset($_COOKIE['beelogin']) || $_COOKIE['beelogin'] != 'admin') {
    echo json_encode(array('ok' => false, 'error' => 'access_denied'));
    exit;
}

$configsJson = isset($_POST['configs']) ? $_POST['configs'] : '';
if (empty($configsJson)) {
    echo json_encode(array('ok' => false, 'error' => 'no_data'));
    exit;
}

$configs = json_decode($configsJson, true);
if (!is_array($configs)) {
    echo json_encode(array('ok' => false, 'error' => 'invalid_data'));
    exit;
}

$now = date('Y-m-d H:i:s');
$admin = mysql_real_escape_string($_COOKIE['beelogin']);

foreach ($configs as $key => $value) {
    $key = mysql_real_escape_string($key);
    $value = (int)$value;
    mysql_query("UPDATE cbt_pengawasan_config SET config_value = '$value', updated_at = '$now', updated_by = '$admin' WHERE config_key = '$key'");
}

bee_log('INFO', 'PENGAWASAN_CONFIG_SAVE', 'Konfigurasi pengawasan diperbarui', array(
    'updated_by' => isset($_COOKIE['beeuser']) ? $_COOKIE['beeuser'] : 'admin',
    'keys' => array_keys($configs)
));

echo json_encode(array('ok' => true));
?>
