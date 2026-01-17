<?php
require_once __DIR__ . "/../../config/server.php";
$tahun = date("Y");
$bulan = date("m");
//echo "$bulan";
// ambil Yearstart = Tahun sekarang untuk bulan > Juni, untuk Bulan < Juni set tahun = Tahun sekarang-1
if($bulan<13){
	if($bulan>6){
	$tahun=$tahun;}
	else {
    $tahun=$tahun-1;}
}	
$tahun1 = $tahun+1;
$tahune = substr($tahun1,2,2);
$ay = "BEE$tahun/$tahune";
$nama = "Tahun Ajaran $tahun/$tahun1";
$sql = db_query($db, "select 1 from cbt_setid where XKodeAY = ? limit 1", array($ay));
$exists = db_fetch_one($sql);

if(!$exists){
	db_query($db, "update cbt_setid set XStatus = '0'", array());
	db_query($db, "insert into cbt_setid (XKodeAY,XNamaAY,XStatus) values (?, ?, '1')", array($ay, $nama));
}
?>
