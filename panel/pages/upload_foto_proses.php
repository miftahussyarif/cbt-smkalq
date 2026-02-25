<?php
error_reporting(0);
session_start();

function getExtension($str)
{
    $i = strrpos($str, ".");
    if (!$i) {
        return "";
    }
    $l = strlen($str) - $i;
    $ext = substr($str, $i + 1, $l);
    return $ext;
}

function ini_to_bytes($value)
{
    $value = trim((string) $value);
    if ($value === '') {
        return 0;
    }
    $last = strtolower($value[strlen($value) - 1]);
    $num = (int) $value;
    switch ($last) {
        case 'g':
            $num *= 1024;
        case 'm':
            $num *= 1024;
        case 'k':
            $num *= 1024;
    }
    return $num;
}

$valid_formats = array("jpg", "jpeg", "gif", "png");
$uploaddir = "../../fotosiswa/";
$maxFileSize = 2 * 1024 * 1024; // 2 MB per file
$iniUploadMax = ini_to_bytes(ini_get('upload_max_filesize'));
$iniPostMax = ini_to_bytes(ini_get('post_max_size'));

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_FILES['photos'])) {
    if (!is_dir($uploaddir)) {
        @mkdir($uploaddir, 0777, true);
    }
    if (!is_writable($uploaddir)) {
        @chmod($uploaddir, 0777);
    }
    if (!is_writable($uploaddir)) {
        echo '<span class="imgList">Folder fotosiswa tidak bisa ditulis. Jalankan install.sh sebagai root/sudo.</span>';
        exit;
    }

    foreach ($_FILES['photos']['name'] as $name => $value) {
        $filename = isset($_FILES['photos']['name'][$name]) ? basename(stripslashes($_FILES['photos']['name'][$name])) : '';
        $errorCode = isset($_FILES['photos']['error'][$name]) ? (int) $_FILES['photos']['error'][$name] : UPLOAD_ERR_NO_FILE;
        $tmpName = isset($_FILES['photos']['tmp_name'][$name]) ? $_FILES['photos']['tmp_name'][$name] : '';
        $size = isset($_FILES['photos']['size'][$name]) ? (int) $_FILES['photos']['size'][$name] : 0;

        if ($errorCode === UPLOAD_ERR_NO_FILE || $filename === '') {
            continue;
        }

        if ($errorCode !== UPLOAD_ERR_OK) {
            if ($errorCode === UPLOAD_ERR_INI_SIZE || $errorCode === UPLOAD_ERR_FORM_SIZE) {
                echo '<span class="imgList">Ukuran file terlalu besar. Maksimal server upload_max_filesize=' . htmlspecialchars(ini_get('upload_max_filesize'), ENT_QUOTES, 'UTF-8') . ', post_max_size=' . htmlspecialchars(ini_get('post_max_size'), ENT_QUOTES, 'UTF-8') . '.</span>';
            } else {
                echo '<span class="imgList">Upload gagal (kode error ' . $errorCode . ').</span>';
            }
            continue;
        }

        if ($iniUploadMax > 0 && $size > $iniUploadMax) {
            echo '<span class="imgList">Ukuran file melebihi batas upload_max_filesize server.</span>';
            continue;
        }
        if ($iniPostMax > 0 && $size > $iniPostMax) {
            echo '<span class="imgList">Ukuran file melebihi batas post_max_size server.</span>';
            continue;
        }
        if ($size > $maxFileSize) {
            echo '<span class="imgList">Ukuran file melebihi batas aplikasi (maks 2 MB).</span>';
            continue;
        }
        if (!is_uploaded_file($tmpName)) {
            echo '<span class="imgList">File upload tidak valid.</span>';
            continue;
        }

        $ext = strtolower(getExtension($filename));
        if (!in_array($ext, $valid_formats)) {
            echo '<span class="imgList">Format file tidak didukung. Gunakan JPG/JPEG/PNG/GIF.</span>';
            continue;
        }

        $image_name = preg_replace('/[^A-Za-z0-9._-]/', '_', $filename);
        $newname = $uploaddir . $image_name;

        if (file_exists($newname)) {
            @unlink($newname);
        }
        if (move_uploaded_file($tmpName, $newname)) {
            @chmod($newname, 0666);
            echo "<img src='" . $uploaddir . $image_name . "' class='imgList'>";
        } else {
            echo '<span class="imgList">Gagal memindahkan file ke folder fotosiswa. Cek permission folder.</span>';
        }
    }
}
?>
