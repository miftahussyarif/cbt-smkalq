<?php
require_once __DIR__ . '/db.php';
include "ipserver.php";

date_default_timezone_set("Asia/Jakarta");

try {
    $db_pusat = db_pusat();
} catch (PDOException $e) {
    die('Koneksi2 CBT BEESMART belum di setting');
}
?>
