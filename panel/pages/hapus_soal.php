<?php
require_once __DIR__ . "/../../config/server.php";
$id = isset($_POST['txt_soal']) ? $_POST['txt_soal'] : '';
$sql = db_query($db, "select XKodeSoal from cbt_soal where XKodeSoal = ?", array($id));
$s = db_fetch_one($sql);
$soal = $s ? str_replace(" ", "", $s['XKodeSoal']) : '';

if ($soal !== '') {
	db_query($db, "delete from cbt_soal where XKodeSoal = ?", array($soal));
}

db_query($db, "delete from cbt_paketsoal where XKodeSoal = ?", array($id));
db_query($db, "delete from cbt_ujian where XKodeSoal = ?", array($id));
db_query($db, "delete from cbt_jawaban where XKodeSoal = ?", array($id));
db_query($db, "delete from cbt_siswa_ujian where XKodeSoal = ?", array($id));
db_query($db, "delete from cbt_nilai where XKodeSoal = ?", array($id));

?>
