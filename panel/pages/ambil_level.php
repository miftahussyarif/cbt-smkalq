<?php
require_once __DIR__ . "/../../config/server.php";
$level = isset($_GET['txt_level']) ? $_GET['txt_level'] : '';
$kelas = isset($_GET['txt_kelas']) ? $_GET['txt_kelas'] : '';

echo "<option selected>-- Pilih Soal --</option>\n";
//$soal = mysql_query("select * from cbt_ujian where  XKodeKelas = '$level' and XKodeMapel = '$mapel' order by XKodeSoal");
$soal = db_query($db, "select XKodeJurusan from cbt_soal where XKodeKelas = ? and XLevelKelas = ? order by XKodeJurusan", array($kelas, $level));
while($k = $soal->fetch()){
echo "<option value=\"".$k['XKodeJurusan']."\">".$k['XKodeJurusan']."</option>\n";
}

?>
