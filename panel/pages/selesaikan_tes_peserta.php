<?php
include "../../config/server.php";

if (!isset($_COOKIE['beeuser'])) {
    echo "NOAUTH";
    exit;
}

$aksi = isset($_POST['aksi']) ? $_POST['aksi'] : '';
if ($aksi !== 'selesaikan_semua') {
    echo "INVALID";
    exit;
}

$sqlAktif = mysql_query("SELECT COUNT(*) AS jml FROM cbt_ujian u
WHERE u.XStatusUjian='1'
AND (
ADDTIME(CONCAT(u.XTglUjian,' ',u.XJamUjian),u.XLamaUjian) > NOW()
OR EXISTS (
    select 1
    from cbt_siswa_ujian su
    where su.XStatusUjian='1'
    and su.XKodeSoal = u.XKodeSoal
    and su.XTokenUjian = u.XTokenUjian
)
)");
$rowAktif = $sqlAktif ? mysql_fetch_array($sqlAktif) : array('jml' => 0);
$jmlTesAktif = isset($rowAktif['jml']) ? (int)$rowAktif['jml'] : 0;

if ($jmlTesAktif > 0) {
    echo "HAS_ACTIVE_TEST";
    exit;
}

$sqlCek = mysql_query("SELECT COUNT(*) AS jml FROM cbt_siswa_ujian WHERE XStatusUjian <> '9'");
$rowCek = $sqlCek ? mysql_fetch_array($sqlCek) : array('jml' => 0);
$jmlBelumSelesai = isset($rowCek['jml']) ? (int)$rowCek['jml'] : 0;

if ($jmlBelumSelesai < 1) {
    echo "NOTHING";
    exit;
}

$sqlUpdate = mysql_query("UPDATE cbt_siswa_ujian SET XStatusUjian='9' WHERE XStatusUjian <> '9'");
if ($sqlUpdate) {
    echo "OK";
    exit;
}

echo "ERROR";
?>
