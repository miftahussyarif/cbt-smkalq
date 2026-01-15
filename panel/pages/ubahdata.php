<?php
include "../../config/server.php";

header('Content-Type: application/json');

$txt_nama = isset($_REQUEST['txt_nama']) ? trim($_REQUEST['txt_nama']) : '';
$txt_ting = isset($_REQUEST['txt_ting']) ? trim($_REQUEST['txt_ting']) : '';
$txt_alam = isset($_REQUEST['txt_alam']) ? trim($_REQUEST['txt_alam']) : '';
$txt_telp = isset($_REQUEST['txt_telp']) ? trim($_REQUEST['txt_telp']) : '';
$txt_facs = isset($_REQUEST['txt_facs']) ? trim($_REQUEST['txt_facs']) : '';
$txt_emai = isset($_REQUEST['txt_emai']) ? trim($_REQUEST['txt_emai']) : '';
$txt_webs = isset($_REQUEST['txt_webs']) ? trim($_REQUEST['txt_webs']) : '';
$txt_ip = isset($_REQUEST['txt_ip']) ? trim($_REQUEST['txt_ip']) : '';
$txt_adm = isset($_REQUEST['txt_adm']) ? trim($_REQUEST['txt_adm']) : '';
$txt_col = isset($_REQUEST['txt_col']) ? trim($_REQUEST['txt_col']) : '';
$txt_kode = isset($_REQUEST['txt_kode']) ? trim($_REQUEST['txt_kode']) : '';
$txt_nip1 = isset($_REQUEST['txt_nip1']) ? trim($_REQUEST['txt_nip1']) : '';
$txt_nip2 = isset($_REQUEST['txt_nip2']) ? trim($_REQUEST['txt_nip2']) : '';

$errors = array();

if ($txt_kode === '') {
    $errors['txt_kode'] = 'Kode sekolah wajib diisi.';
}
if ($txt_nama === '') {
    $errors['namaskul'] = 'Nama sekolah wajib diisi.';
}
if ($txt_ting === '') {
    $errors['tingkatskul'] = 'Level sekolah wajib dipilih.';
}
if ($txt_alam === '') {
    $errors['alamatskul'] = 'Alamat sekolah wajib diisi.';
}
if ($txt_telp === '') {
    $errors['telpskul'] = 'No. Telp wajib diisi.';
} elseif (!preg_match('/^[0-9+()\s.-]+$/', $txt_telp)) {
    $errors['telpskul'] = 'No. Telp hanya boleh angka/simbol telepon.';
}
if ($txt_facs !== '' && !preg_match('/^[0-9+()\s.-]+$/', $txt_facs)) {
    $errors['faxskul'] = 'No. Fax hanya boleh angka/simbol telepon.';
}
if ($txt_emai === '') {
    $errors['emailskul'] = 'Email sekolah wajib diisi.';
} elseif (!filter_var($txt_emai, FILTER_VALIDATE_EMAIL)) {
    $errors['emailskul'] = 'Format email tidak valid.';
}
if ($txt_webs !== '' && strpos($txt_webs, '.') === false) {
    $errors['webskul'] = 'Website tidak valid.';
}
if ($txt_ip === '') {
    $errors['kepsek'] = 'Nama kepala sekolah wajib diisi.';
}
if ($txt_nip1 === '') {
    $errors['nipkepsek'] = 'NIP KepSek wajib diisi.';
} elseif (!preg_match('/^[0-9]+$/', $txt_nip1)) {
    $errors['nipkepsek'] = 'NIP KepSek harus berupa angka.';
}
if ($txt_adm === '') {
    $errors['txt_adm'] = 'Nama admin wajib diisi.';
}
if ($txt_nip2 === '') {
    $errors['nipadmin'] = 'NIP Admin wajib diisi.';
} elseif (!preg_match('/^[0-9]+$/', $txt_nip2)) {
    $errors['nipadmin'] = 'NIP Admin harus berupa angka.';
}
if ($txt_col === '') {
    $errors['txt_col'] = 'Warna header wajib diisi.';
} else {
    $colorRaw = ltrim($txt_col, '#');
    if (!preg_match('/^[0-9a-fA-F]{6}$/', $colorRaw)) {
        $errors['txt_col'] = 'Format warna harus #RRGGBB.';
    } else {
        $txt_col = '#' . $colorRaw;
    }
}

if (count($errors) > 0) {
    echo json_encode(array('ok' => false, 'errors' => $errors));
    exit;
}

$txt_nama = mysql_real_escape_string($txt_nama);
$txt_ting = mysql_real_escape_string($txt_ting);
$txt_alam = mysql_real_escape_string($txt_alam);
$txt_telp = mysql_real_escape_string($txt_telp);
$txt_facs = mysql_real_escape_string($txt_facs);
$txt_emai = mysql_real_escape_string($txt_emai);
$txt_webs = mysql_real_escape_string($txt_webs);
$txt_ip = mysql_real_escape_string($txt_ip);
$txt_adm = mysql_real_escape_string($txt_adm);
$txt_col = mysql_real_escape_string($txt_col);
$txt_kode = mysql_real_escape_string($txt_kode);
$txt_nip1 = mysql_real_escape_string($txt_nip1);
$txt_nip2 = mysql_real_escape_string($txt_nip2);

$sql = mysql_query("update cbt_admin set 
XSekolah = '$txt_nama',
XTingkat = '$txt_ting',
XAlamat = '$txt_alam',
XTelp = '$txt_telp',
XFax = '$txt_facs',
XEmail = '$txt_emai',
XWeb = '$txt_webs',
XAdmin = '$txt_adm',
XWarna = '$txt_col',
XKodeSekolah = '$txt_kode',
XNIPKepsek = '$txt_nip1',
XNIPAdmin = '$txt_nip2',
XKepSek = '$txt_ip'");

if ($sql) {
    echo json_encode(array('ok' => true, 'message' => 'Ubah data berhasil!'));
} else {
    echo json_encode(array('ok' => false, 'errors' => array('__all__' => 'Gagal menyimpan data: ' . mysql_error())));
}
?>
