<?php
include "../../config/server.php";
header('Content-Type: application/json; charset=utf-8');

if (!isset($_COOKIE['beeuser'])) {
    echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
    exit;
}

$offset = isset($_POST['offset']) ? (int)$_POST['offset'] : 0;
$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 20;
$mode = isset($_POST['mode']) ? $_POST['mode'] : 'missing';
$kodeSoal = isset($_POST['kodesoal']) ? trim($_POST['kodesoal']) : '';

if ($offset < 0) {
    $offset = 0;
}
if ($limit < 1) {
    $limit = 20;
}
if ($limit > 100) {
    $limit = 100;
}
if ($mode !== 'force') {
    $mode = 'missing';
}

if (!function_exists('cbt_convert_webp_file')) {
    function cbt_convert_webp_file($filename, $mode)
    {
        static $pictureIndex = null;
        $filename = basename(str_replace("\\", "/", trim($filename)));
        $filenameDecoded = basename(str_replace("\\", "/", urldecode($filename)));
        if ($filename === '') {
            return array('status' => 'empty');
        }

        $srcDir = __DIR__ . "/../../pictures";
        $dstDir = __DIR__ . "/../../pictures_webp";
        $srcFile = $srcDir . "/" . $filename;
        if (!file_exists($srcFile) && $filenameDecoded !== $filename) {
            $srcFileDecoded = $srcDir . "/" . $filenameDecoded;
            if (file_exists($srcFileDecoded)) {
                $srcFile = $srcFileDecoded;
                $filename = $filenameDecoded;
            }
        }

        if (!file_exists($srcFile) && is_dir($srcDir)) {
            if ($pictureIndex === null) {
                $pictureIndex = array();
                $files = @scandir($srcDir);
                if (is_array($files)) {
                    foreach ($files as $f) {
                        if ($f === '.' || $f === '..') {
                            continue;
                        }
                        $pictureIndex[strtolower($f)] = $f;
                    }
                }
            }
            $key1 = strtolower($filename);
            $key2 = strtolower($filenameDecoded);
            if (isset($pictureIndex[$key1])) {
                $filename = $pictureIndex[$key1];
                $srcFile = $srcDir . "/" . $filename;
            } elseif (isset($pictureIndex[$key2])) {
                $filename = $pictureIndex[$key2];
                $srcFile = $srcDir . "/" . $filename;
            }
        }

        $baseName = pathinfo($filename, PATHINFO_FILENAME);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $dstFile = $dstDir . "/" . $baseName . ".webp";

        if ($mode === 'missing' && file_exists($dstFile)) {
            return array('status' => 'skip_exists');
        }

        if (!file_exists($srcFile) || !is_file($srcFile)) {
            // Bila source asli hilang tetapi webp sudah ada, anggap aman (skip)
            if (file_exists($dstFile)) {
                return array('status' => 'skip_exists');
            }
            return array('status' => 'source_missing');
        }

        if (!is_dir($dstDir)) {
            @mkdir($dstDir, 0775, true);
        }
        if (!is_dir($dstDir) || !is_writable($dstDir)) {
            return array('status' => 'dst_not_writable');
        }

        if (!function_exists('imagewebp')) {
            return array('status' => 'webp_not_supported');
        }

        $img = false;
        if ($ext === 'jpg' || $ext === 'jpeg') {
            $img = @imagecreatefromjpeg($srcFile);
        } elseif ($ext === 'png') {
            $img = @imagecreatefrompng($srcFile);
            if ($img) {
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }
        } elseif ($ext === 'gif') {
            $img = @imagecreatefromgif($srcFile);
            if ($img) {
                imagepalettetotruecolor($img);
                imagealphablending($img, true);
                imagesavealpha($img, true);
            }
        } elseif ($ext === 'webp' && function_exists('imagecreatefromwebp')) {
            $img = @imagecreatefromwebp($srcFile);
        } else {
            $raw = @file_get_contents($srcFile);
            if ($raw !== false) {
                $img = @imagecreatefromstring($raw);
            }
        }

        if (!$img) {
            return array('status' => 'decode_failed');
        }

        $ok = @imagewebp($img, $dstFile, 82);
        imagedestroy($img);

        if ($ok && file_exists($dstFile)) {
            return array('status' => 'converted');
        }
        return array('status' => 'save_failed');
    }
}

$filter = "";
if ($kodeSoal !== '') {
    $kodeSoalSafe = mysql_real_escape_string($kodeSoal);
    $filter = " WHERE XKodeSoal = '$kodeSoalSafe' ";
}

$qTotal = mysql_query("SELECT COUNT(*) AS total FROM cbt_soal $filter");
if (!$qTotal) {
    echo json_encode(array('ok' => false, 'error' => 'query_total_failed', 'mysql_error' => mysql_error()));
    exit;
}
$rTotal = mysql_fetch_array($qTotal);
$total = (int)$rTotal['total'];

if ($total < 1) {
    echo json_encode(array(
        'ok' => true,
        'done' => true,
        'total' => 0,
        'offset' => 0,
        'next_offset' => 0,
        'fetched' => 0,
        'converted' => 0,
        'skipped' => 0,
        'failed' => 0,
        'source_missing' => 0
    ));
    exit;
}

$qRows = mysql_query("SELECT Urut, XGambarTanya, XGambarJawab1, XGambarJawab2, XGambarJawab3, XGambarJawab4, XGambarJawab5 FROM cbt_soal $filter ORDER BY Urut ASC LIMIT $offset, $limit");
if (!$qRows) {
    echo json_encode(array('ok' => false, 'error' => 'query_rows_failed', 'mysql_error' => mysql_error()));
    exit;
}

$converted = 0;
$skipped = 0;
$failed = 0;
$sourceMissing = 0;
$fetched = 0;

while ($row = mysql_fetch_array($qRows)) {
    $fetched++;
    $fields = array(
        $row['XGambarTanya'],
        $row['XGambarJawab1'],
        $row['XGambarJawab2'],
        $row['XGambarJawab3'],
        $row['XGambarJawab4'],
        $row['XGambarJawab5']
    );
    for ($i = 0; $i < count($fields); $i++) {
        $f = trim($fields[$i]);
        if ($f === '') {
            continue;
        }
        $res = cbt_convert_webp_file($f, $mode);
        if ($res['status'] === 'converted') {
            $converted++;
        } elseif ($res['status'] === 'skip_exists' || $res['status'] === 'empty') {
            $skipped++;
        } elseif ($res['status'] === 'source_missing') {
            $sourceMissing++;
        } else {
            $failed++;
        }
    }
}

$nextOffset = $offset + $fetched;
if ($nextOffset > $total) {
    $nextOffset = $total;
}
$done = ($nextOffset >= $total || $fetched < 1);

echo json_encode(array(
    'ok' => true,
    'done' => $done,
    'total' => $total,
    'offset' => $offset,
    'next_offset' => $nextOffset,
    'fetched' => $fetched,
    'converted' => $converted,
    'skipped' => $skipped,
    'failed' => $failed,
    'source_missing' => $sourceMissing
));
?>
