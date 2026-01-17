<?php
require_once __DIR__ . "/../../config/server.php";
$id = isset($_POST['txt_mapel']) ? $_POST['txt_mapel'] : '';
$sql = db_query($db, "select XKodeSoal from cbt_soal where Urut = ?", array($id));
$s = db_fetch_one($sql);
$soal = $s ? $s['XKodeSoal'] : '';

if ($soal !== '') {
	db_query($db, "delete from cbt_soal where XKodeSoal = ?", array($soal));
}

db_query($db, "delete from cbt_paketsoal where Urut = ?", array($id));

?>
