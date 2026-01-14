<?php
include "../../config/server.php";

if (!isset($_COOKIE['beeuser'])) {
    echo "NOAUTH";
    exit;
}

$aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
$urut = isset($_POST['txt_ujian']) ? mysql_real_escape_string($_POST['txt_ujian']) : '';

if ($aksi !== 'hapus' || $urut === '') {
    echo "INVALID";
    exit;
}

$sqlujian = mysql_query("SELECT XTokenUjian, XKodeSoal, XStatusUjian FROM cbt_ujian WHERE Urut = '$urut'");
if (!$sqlujian || mysql_num_rows($sqlujian) < 1) {
    echo "NOTFOUND";
    exit;
}

$uj = mysql_fetch_array($sqlujian);
if ($uj['XStatusUjian'] !== '9') {
    echo "NOTDONE";
    exit;
}

$token = mysql_real_escape_string($uj['XTokenUjian']);
$kodesoal = mysql_real_escape_string($uj['XKodeSoal']);

mysql_query("DELETE FROM cbt_jawaban WHERE XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
mysql_query("DELETE FROM cbt_nilai WHERE XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
mysql_query("DELETE FROM cbt_siswa_ujian WHERE XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
mysql_query("DELETE FROM cbt_audio WHERE XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");

$cekPengawasan = mysql_query("SHOW TABLES LIKE 'cbt_pengawasan'");
if ($cekPengawasan && mysql_num_rows($cekPengawasan) > 0) {
    mysql_query("DELETE FROM cbt_pengawasan WHERE XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
}

echo "OK";
?>
