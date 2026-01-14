<?php include "../../config/server.php";

//$sql = mysql_query("insert into tes (nilai) values ('$_REQUEST[token]')");
$array =  explode(',', $_REQUEST['nama']);

foreach ($array as $item) {
	$sql0 = mysql_query("select * from cbt_siswa_ujian where Urut = '$item' and XTokenUjian = '$_REQUEST[token]'");
	$s = mysql_fetch_array($sql0);
	$nomer = $s['XNomerUjian'];
	if ($nomer == "") {
		continue;
	}
	// Reset login state so peserta bisa login kembali.
	$sql = mysql_query("update cbt_siswa_ujian set XStatusUjian = '0', XGetIP = '' where Urut = '$item' and XTokenUjian = '$_REQUEST[token]' and XNomerUjian = '$nomer'");
}

?>
