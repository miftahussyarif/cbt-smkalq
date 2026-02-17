<?php include "../../config/server.php";
header('Content-type: text/html; charset=utf-8');

$sss = isset($_REQUEST['txt_tanya']) ? $_REQUEST['txt_tanya'] : '';
$sss = str_replace("'", "\'", $sss);
$sss = str_replace("  ", "", $sss);

$file = isset($_REQUEST['txt_gbr']) ? $_REQUEST['txt_gbr'] : '';
$file = basename($file);
$file = str_replace( "\\", '/',$file);
$file = basename($file);

$filea = isset($_REQUEST['txt_audio']) ? $_REQUEST['txt_audio'] : '';
$filea = basename($filea);
$filea = str_replace( "\\", '/',$filea);
$filea = basename($filea);

$filev = isset($_REQUEST['txt_video']) ? $_REQUEST['txt_video'] : '';
$filev = basename($filev);
$filev = str_replace( "\\", '/',$filev);
$filev = basename($filev);

$gambar = $file;
$audio = $filea;
$video = $filev;

$txt_jawab1 = isset($_REQUEST['txt_jawab1']) ? $_REQUEST['txt_jawab1'] : '';
$txt_jawab2 = isset($_REQUEST['txt_jawab2']) ? $_REQUEST['txt_jawab2'] : '';
$txt_jawab3 = isset($_REQUEST['txt_jawab3']) ? $_REQUEST['txt_jawab3'] : '';
$txt_jawab4 = isset($_REQUEST['txt_jawab4']) ? $_REQUEST['txt_jawab4'] : '';
$txt_jawab5 = isset($_REQUEST['txt_jawab5']) ? $_REQUEST['txt_jawab5'] : '';
$txt_kunci = isset($_REQUEST['txt_kunci']) ? $_REQUEST['txt_kunci'] : '';
$txt_gbr1 = isset($_REQUEST['txt_gbr1']) ? $_REQUEST['txt_gbr1'] : '';
$txt_gbr2 = isset($_REQUEST['txt_gbr2']) ? $_REQUEST['txt_gbr2'] : '';
$txt_gbr3 = isset($_REQUEST['txt_gbr3']) ? $_REQUEST['txt_gbr3'] : '';
$txt_gbr4 = isset($_REQUEST['txt_gbr4']) ? $_REQUEST['txt_gbr4'] : '';
$txt_gbr5 = isset($_REQUEST['txt_gbr5']) ? $_REQUEST['txt_gbr5'] : '';
$txt_soal = isset($_REQUEST['txt_soal']) ? $_REQUEST['txt_soal'] : '';
$txt_nomax = isset($_REQUEST['txt_nomax']) ? (int)$_REQUEST['txt_nomax'] : 0;
$txt_mapel = isset($_REQUEST['txt_mapel']) ? $_REQUEST['txt_mapel'] : '';
$txt_kate = isset($_REQUEST['txt_kate']) ? $_REQUEST['txt_kate'] : '1';
$txt_kes = isset($_REQUEST['txt_kes']) ? $_REQUEST['txt_kes'] : '1';
$txt_aca = isset($_REQUEST['txt_aca']) ? $_REQUEST['txt_aca'] : 'A';
$txt_ops = isset($_REQUEST['txt_ops']) ? $_REQUEST['txt_ops'] : '';

$qurut = mysql_query("SELECT MAX(Urut) AS maks FROM cbt_soal");
$rurut = mysql_fetch_array($qurut);
$urut_baru = (int)$rurut['maks'] + 1;
if ($txt_nomax <= 0) {
    $qnomer = mysql_query("SELECT MAX(XNomerSoal) AS maks FROM cbt_soal WHERE XKodeSoal = '$txt_soal'");
    $rnomer = mysql_fetch_array($qnomer);
    $txt_nomax = (int)$rnomer['maks'] + 1;
}

if (isset($_FILES['fileUpload']) && isset($_FILES['fileUpload']['tmp_name']) && $_FILES['fileUpload']['tmp_name'] != '') {
	$img = rand(1000,100000)."-".$_FILES['fileUpload']['name'];
	$img_loc = $_FILES['fileUpload']['tmp_name'];
	$folder="upl_gambar/";
	move_uploaded_file($img_loc,$folder.$img);
}

if (isset($_FILES['fileUpload2']) && isset($_FILES['fileUpload2']['tmp_name']) && $_FILES['fileUpload2']['tmp_name'] != '') {
	$img = rand(1000,100000)."-".$_FILES['fileUpload2']['name'];
	$img_loc = $_FILES['fileUpload2']['tmp_name'];
	$folder="upl_audio/";
	move_uploaded_file($img_loc,$folder.$img);
}

if (isset($_FILES['fileUpload3']) && isset($_FILES['fileUpload3']['tmp_name']) && $_FILES['fileUpload3']['tmp_name'] != '') {
	$img = rand(1000,100000)."-".$_FILES['fileUpload3']['name'];
	$img_loc = $_FILES['fileUpload3']['tmp_name'];
	$folder="upl_video/";
	move_uploaded_file($img_loc,$folder.$img);
}

$sql0 = mysql_query("insert into cbt_soal (Urut,XTanya,XJawab1,XJawab2,XJawab3,XJawab4,XJawab5,XKunciJawaban,XGambarJawab1,XGambarJawab2,XGambarJawab3,XGambarJawab4,XGambarJawab5,XKodeSoal,XNomerSoal,XKodeMapel,XGambarTanya,XAudioTanya,XVideoTanya,XJenisSoal,XKategori,XAcakSoal,XAcakOpsi) values 
('$urut_baru','$sss','$txt_jawab1','$txt_jawab2','$txt_jawab3','$txt_jawab4','$txt_jawab5',
'$txt_kunci','$txt_gbr1','$txt_gbr2','$txt_gbr3','$txt_gbr4','$txt_gbr5','$txt_soal',
'$txt_nomax','$txt_mapel','$gambar','$audio','$video','$txt_kate','$txt_kes','$txt_aca','$txt_ops')");

if (!$sql0) {
    echo "ERROR: " . mysql_error();
} else {
    echo "OK";
}

//$sql0 = mysql_query("insert into cbt_soal (XTanya) values ('$sss')");

?>
