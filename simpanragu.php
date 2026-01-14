<?php include "config/server.php";
include "config/pengawasan.php";
 //update cbt_jawaban set XRagu = '1' where XNomerSoal='$_REQUEST[who]'
// $sql = mysql_query("update cbt_jawaban set XRagu = '$_REQUEST[chk]' where XNomerSoal='$_REQUEST[who]'");
$user = $_COOKIE['PESERTA'];
cbt_ensure_pengawasan_table();
$sqlUjian = mysql_query("SELECT XTokenUjian, XKodeSoal FROM cbt_siswa_ujian WHERE XNomerUjian = '$user' AND XStatusUjian = '1' ORDER BY XMulaiUjian DESC LIMIT 1");
if ($sqlUjian && mysql_num_rows($sqlUjian) > 0) {
    $uj = mysql_fetch_array($sqlUjian);
    $token = $uj['XTokenUjian'];
    $kodesoal = $uj['XKodeSoal'];
    $ceklock = mysql_num_rows(mysql_query("SELECT XIsLocked FROM cbt_pengawasan WHERE XNomerUjian = '$user' AND XTokenUjian = '$token' AND XKodeSoal = '$kodesoal' AND XIsLocked = '1'"));
    if ($ceklock > 0) {
        header('HTTP/1.1 403 Forbidden');
        echo "LOCKED";
        exit;
    }
}
if(isset($_REQUEST['chk'],$_REQUEST['who'])){
 $sql = mysql_query("update cbt_jawaban set XRagu = '$_REQUEST[chk]' where Urut='$_REQUEST[who]' AND XUserJawab = '$user'");
 }
 
if($_REQUEST['anu']==0){
$sql = mysql_query("update cbt_audio set XMulai = '$_REQUEST[anu]', XPutar = '2'");
} else {
$sql = mysql_query("update cbt_audio set XMulai = '$_REQUEST[anu]'");
}

?>
