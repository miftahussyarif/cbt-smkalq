<?php include "../../config/server.php";
header('Content-type: text/html; charset=utf-8');
$sss = mysql_real_escape_string($_REQUEST['txt_tanya']);

if (!function_exists('cbt_convert_image_to_webp')) {
	function cbt_convert_image_to_webp($filename)
	{
		$filename = basename(str_replace("\\", "/", trim($filename)));
		if ($filename === '') {
			return false;
		}

		$srcDir = __DIR__ . "/../../pictures";
		$dstDir = __DIR__ . "/../../pictures_webp";
		$srcFile = $srcDir . "/" . $filename;

		if (!file_exists($srcFile) || !is_file($srcFile)) {
			return false;
		}

		if (!is_dir($dstDir)) {
			@mkdir($dstDir, 0775, true);
		}
		if (!is_dir($dstDir) || !is_writable($dstDir)) {
			return false;
		}

		$baseName = pathinfo($filename, PATHINFO_FILENAME);
		$ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		$dstFile = $dstDir . "/" . $baseName . ".webp";

		if (!function_exists('imagewebp')) {
			return false;
		}

		$img = false;
		if ($ext === 'jpg' || $ext === 'jpeg') {
			$img = @imagecreatefromjpeg($srcFile);
		}
		elseif ($ext === 'png') {
			$img = @imagecreatefrompng($srcFile);
			if ($img) {
				imagepalettetotruecolor($img);
				imagealphablending($img, true);
				imagesavealpha($img, true);
			}
		}
		elseif ($ext === 'gif') {
			$img = @imagecreatefromgif($srcFile);
			if ($img) {
				imagepalettetotruecolor($img);
				imagealphablending($img, true);
				imagesavealpha($img, true);
			}
		}
		elseif ($ext === 'webp' && function_exists('imagecreatefromwebp')) {
			$img = @imagecreatefromwebp($srcFile);
		}
		else {
			$raw = @file_get_contents($srcFile);
			if ($raw !== false) {
				$img = @imagecreatefromstring($raw);
			}
		}

		if (!$img) {
			return false;
		}

		$ok = @imagewebp($img, $dstFile, 82);
		imagedestroy($img);

		return $ok && file_exists($dstFile);
	}
}

$file = $_REQUEST['txt_gbr'];
$file = basename($file);
$file = str_replace("\\", '/', $file);
$file = basename($file);

$filea = $_REQUEST['txt_aud'];
$filea = basename($filea);
$filea = str_replace("\\", '/', $filea);
$filea = basename($filea);

$filev = $_REQUEST['txt_vid'];
$filev = basename($filev);
$filev = str_replace("\\", '/', $filev);
$filev = basename($filev);

/* File Gambar Opsi */
$gbr1 = $_REQUEST['txt_gbr1'];
$gbr1 = basename($gbr1);
$gbr1 = str_replace("\\", '/', $gbr1);
$gbr1 = basename($gbr1);

$gbr2 = $_REQUEST['txt_gbr2'];
$gbr2 = basename($gbr2);
$gbr2 = str_replace("\\", '/', $gbr2);
$gbr2 = basename($gbr2);

$gbr3 = $_REQUEST['txt_gbr3'];
$gbr3 = basename($gbr3);
$gbr3 = str_replace("\\", '/', $gbr3);
$gbr3 = basename($gbr3);

$gbr4 = $_REQUEST['txt_gbr4'];
$gbr4 = basename($gbr4);
$gbr4 = str_replace("\\", '/', $gbr4);
$gbr4 = basename($gbr4);

$gbr5 = $_REQUEST['txt_gbr5'];
$gbr5 = basename($gbr5);
$gbr5 = str_replace("\\", '/', $gbr5);
$gbr5 = basename($gbr5);


$sqlcek = mysql_query("select XGambarTanya,XVideoTanya,XAudioTanya,
 XGambarJawab1,XGambarJawab2,XGambarJawab3,XGambarJawab4,XGambarJawab5
 from cbt_soal where  XKodeSoal = '$_REQUEST[txt_soal]' and Urut = '$_REQUEST[txt_nom]'");
$r = mysql_fetch_array($sqlcek);
$gambar = $r['XGambarTanya'];
$audio = $r['XAudioTanya'];
$video = $r['XVideoTanya'];

$gambar1 = $r['XGambarJawab1'];
$gambar2 = $r['XGambarJawab2'];
$gambar3 = $r['XGambarJawab3'];
$gambar4 = $r['XGambarJawab4'];
$gambar5 = $r['XGambarJawab5'];

if ($file == "") {
	$gambar = $gambar;
}
else {
	$gambar = $file;
}
if ($filea == "") {
	$audio = $audio;
}
else {
	$audio = $filea;
}
if ($filev == "") {
	$video = $video;
}
else {
	$video = $filev;
}

if ($gbr1 == "") {
	$gambar1 = $gambar1;
}
else {
	$gambar1 = $gbr1;
}
if ($gbr2 == "") {
	$gambar2 = $gambar2;
}
else {
	$gambar2 = $gbr2;
}
if ($gbr3 == "") {
	$gambar3 = $gambar3;
}
else {
	$gambar3 = $gbr3;
}
if ($gbr4 == "") {
	$gambar4 = $gambar4;
}
else {
	$gambar4 = $gbr4;
}
if ($gbr5 == "") {
	$gambar5 = $gambar5;
}
else {
	$gambar5 = $gbr5;
}

// Konversi gambar ke WebP untuk kebutuhan render ujian (folder khusus pictures_webp)
cbt_convert_image_to_webp($gambar);
cbt_convert_image_to_webp($gambar1);
cbt_convert_image_to_webp($gambar2);
cbt_convert_image_to_webp($gambar3);
cbt_convert_image_to_webp($gambar4);
cbt_convert_image_to_webp($gambar5);


$jawab1 = mysql_real_escape_string(isset($_REQUEST["txt_jawab1"]) ? $_REQUEST["txt_jawab1"] : "");
$jawab2 = mysql_real_escape_string(isset($_REQUEST["txt_jawab2"]) ? $_REQUEST["txt_jawab2"] : "");
$jawab3 = mysql_real_escape_string(isset($_REQUEST["txt_jawab3"]) ? $_REQUEST["txt_jawab3"] : "");
$jawab4 = mysql_real_escape_string(isset($_REQUEST["txt_jawab4"]) ? $_REQUEST["txt_jawab4"] : "");
$jawab5 = mysql_real_escape_string(isset($_REQUEST["txt_jawab5"]) ? $_REQUEST["txt_jawab5"] : "");

$sql0 = mysql_query("update cbt_soal set XTanya = '$sss', 
	XGambarJawab1='$gambar1', 
	XGambarJawab2='$gambar2', 
	XGambarJawab3='$gambar3',
	XGambarJawab4='$gambar4',
	XGambarJawab5='$gambar5',
	XGambarTanya='$gambar',
	XAudioTanya='$audio',
	XVideoTanya='$video',	
	XJawab1='$jawab1', 
	XJawab2='$jawab2', 
	XJawab3='$jawab3',
	XJawab4='$jawab4',
	XJawab5='$jawab5',	
	XKunciJawaban='$_REQUEST[txt_kunci]',
	XJenisSoal='$_REQUEST[txt_kate]',
	XKategori='$_REQUEST[txt_kes]',
	XAcakSoal='$_REQUEST[txt_aca]',	
	XAcakOpsi='$_REQUEST[txt_ops]'	
	where XKodeSoal = '$_REQUEST[txt_soal]' and Urut = '$_REQUEST[txt_nom]'");
//echo "update cbt_soal";

?>
