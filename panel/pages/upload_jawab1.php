<?php
include "../../config/server.php";

$uploaddir = '../../pictures/';
if (!is_dir($uploaddir)) {
    @mkdir($uploaddir, 0775, true);
}

$field = 'uploadfile4';
if (!isset($_FILES[$field])) {
    echo 'error: field file tidak ditemukan';
    exit;
}
if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
    echo 'error: upload gagal (code ' . intval($_FILES[$field]['error']) . ')';
    exit;
}
if (!is_writable($uploaddir)) {
    echo 'error: folder pictures tidak writable';
    exit;
}

$namaAsli = basename($_FILES[$field]['name']);
if ($namaAsli === '') {
    echo 'error: nama file kosong';
    exit;
}

$ext = strtolower(pathinfo($namaAsli, PATHINFO_EXTENSION));
$allow = array('jpg', 'jpeg', 'png', 'gif');
if (!in_array($ext, $allow, true)) {
    echo 'error: ekstensi tidak diizinkan';
    exit;
}

$target = $uploaddir . $namaAsli;
if (move_uploaded_file($_FILES[$field]['tmp_name'], $target)) {
    @chmod($target, 0644);
    echo "success";
} else {
    echo "error: gagal memindahkan file upload";
}
?>
