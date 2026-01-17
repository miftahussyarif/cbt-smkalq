<?php
require_once __DIR__ . '/db.php';

date_default_timezone_set("Asia/Jakarta");

try {
    $db = db_local(true);
} catch (PDOException $e) {
    die('Could not connect: ' . $e->getMessage());
}

$mode = "lokal"; // pilih 'lokal' atau 'pusat'
?>
