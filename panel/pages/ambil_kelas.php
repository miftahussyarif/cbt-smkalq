<?php
require_once __DIR__ . "/../../config/server.php";
$jur = isset($_REQUEST['txt_jur']) ? $_REQUEST['txt_jur'] : '';
$kelas = isset($_GET['txt_kelas']) ? $_GET['txt_kelas'] : '';

$sql = db_query($db, "select * from cbt_admin", array());
$xadm = db_fetch_one($sql);
$skull= $xadm['XSekolah'];
$skul_pic= $xadm['XLogo']; 
$skul_tkt= $xadm['XTingkat']; 

//echo "<option selected>-- Pilih Jurusan --</option>\n";
//$soal = mysql_query("select * from cbt_ujian where  XKodeKelas = '$level' and XKodeMapel = '$mapel' order by XKodeSoal");
$soal = db_query($db, "select XKodeKelas from cbt_kelas where XKodeJurusan = ? and XLevelKelas = ? order by XKodeKelas", array($jur, $skul_tkt));
while($k = $soal->fetch()){
echo "<option value=\"".$k['XKodeKelas']."\">".$k['XKodeKelas']."</option>\n";
}

?>
