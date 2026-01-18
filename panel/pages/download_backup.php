<?php
if (!isset($_COOKIE['beeuser'])) {
    header("Location: login.php");
    exit;
}

$backupDir = '/opt/lampp/backup';
$file = isset($_GET['file']) ? basename($_GET['file']) : '';

if ($file == '' || strpos($file, '..') !== false) {
    http_response_code(400);
    echo "File tidak valid.";
    exit;
}

$allowedPrefixes = array('dbee-ujian_', 'dbee-siswa_', 'dbee_', 'dbee-files_');
$allowed = false;
foreach ($allowedPrefixes as $prefix) {
    if (strpos($file, $prefix) === 0) {
        $allowed = true;
        break;
    }
}

if (!$allowed) {
    http_response_code(403);
    echo "Akses ditolak.";
    exit;
}

$path = $backupDir . '/' . $file;
if (!is_file($path)) {
    http_response_code(404);
    echo "File tidak ditemukan.";
    exit;
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
