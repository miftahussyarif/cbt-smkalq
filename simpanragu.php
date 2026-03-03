<?php include "config/server.php";
include "config/pengawasan.php";
include_once "cbt_exam_context.php";
 //update cbt_jawaban set XRagu = '1' where XNomerSoal='$_REQUEST[who]'
// $sql = mysql_query("update cbt_jawaban set XRagu = '$_REQUEST[chk]' where XNomerSoal='$_REQUEST[who]'");
$user = $_COOKIE['PESERTA'];
cbt_ensure_pengawasan_table();
$token = '';
$kodesoal = '';
$preferToken = isset($_COOKIE['CBT_TOKEN']) ? $_COOKIE['CBT_TOKEN'] : '';
$preferKode = isset($_COOKIE['CBT_KODESOAL']) ? $_COOKIE['CBT_KODESOAL'] : '';
$uj = cbt_get_attempt_context_for_student($user, $preferToken, $preferKode);
if (!$uj) {
    $uj = cbt_get_attempt_context_for_student($user);
}
if (!$uj) {
    $uj = cbt_get_schedule_context_for_student($user, $preferToken);
}
if ($uj) {
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
 if ($token !== '' && $kodesoal !== '') {
     $sql = mysql_query("update cbt_jawaban set XRagu = '$_REQUEST[chk]' where Urut='$_REQUEST[who]' AND XUserJawab = '$user' AND XTokenUjian = '$token' AND XKodeSoal = '$kodesoal'");
 } else {
     $sql = mysql_query("update cbt_jawaban set XRagu = '$_REQUEST[chk]' where Urut='$_REQUEST[who]' AND XUserJawab = '$user'");
 }
 }
 
if($_REQUEST['anu']==0){
$sql = mysql_query("update cbt_audio set XMulai = '$_REQUEST[anu]', XPutar = '2'");
} else {
$sql = mysql_query("update cbt_audio set XMulai = '$_REQUEST[anu]'");
}

?>
