<?php
require_once __DIR__ . '/db.php';
include "ipserver.php";

date_default_timezone_set("Asia/Jakarta");

try {
    $db_pusat = db_pusat();
    $status_konek = "1";
} catch (PDOException $e) {
    $status_konek = "0";
}
?>
