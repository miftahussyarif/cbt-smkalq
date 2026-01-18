<?php
include "config/server.php";
include "config/pengawasan.php";

header('Content-Type: application/json');

cbt_ensure_pengawasan_config_table();
$config = cbt_get_pengawasan_config();

echo json_encode(array('ok' => true, 'config' => $config));
?>
