
<?php
require_once __DIR__ . "/../../config/server.php";
header('Content-type: text/html; charset=utf-8');
$sss = isset($_REQUEST['txt_tanya']) ? $_REQUEST['txt_tanya'] : '';

$file = isset($_REQUEST['txt_gbr']) ? $_REQUEST['txt_gbr'] : '';
$file = basename($file);
$file = str_replace( "\\", '/',$file);
$file = basename($file);

$filea = isset($_REQUEST['txt_aud']) ? $_REQUEST['txt_aud'] : '';
$filea = basename($filea);
$filea = str_replace( "\\", '/',$filea);
$filea = basename($filea);

$filev = isset($_REQUEST['txt_vid']) ? $_REQUEST['txt_vid'] : '';
$filev = basename($filev);
$filev = str_replace( "\\", '/',$filev);
$filev = basename($filev);

/* File Gambar Opsi */
$gbr1 = isset($_REQUEST['txt_gbr1']) ? $_REQUEST['txt_gbr1'] : '';
$gbr1 = basename($gbr1);
$gbr1 = str_replace( "\\", '/',$gbr1);
$gbr1 = basename($gbr1);

$gbr2 = isset($_REQUEST['txt_gbr2']) ? $_REQUEST['txt_gbr2'] : '';
$gbr2 = basename($gbr2);
$gbr2 = str_replace( "\\", '/',$gbr2);
$gbr2 = basename($gbr2);

$gbr3 = isset($_REQUEST['txt_gbr3']) ? $_REQUEST['txt_gbr3'] : '';
$gbr3 = basename($gbr3);
$gbr3 = str_replace( "\\", '/',$gbr3);
$gbr3 = basename($gbr3);

$gbr4 = isset($_REQUEST['txt_gbr4']) ? $_REQUEST['txt_gbr4'] : '';
$gbr4 = basename($gbr4);
$gbr4 = str_replace( "\\", '/',$gbr4);
$gbr4 = basename($gbr4);

$gbr5 = isset($_REQUEST['txt_gbr5']) ? $_REQUEST['txt_gbr5'] : '';
$gbr5 = basename($gbr5);
$gbr5 = str_replace( "\\", '/',$gbr5);
$gbr5 = basename($gbr5);


$txt_soal = isset($_REQUEST['txt_soal']) ? $_REQUEST['txt_soal'] : '';
$txt_nom = isset($_REQUEST['txt_nom']) ? $_REQUEST['txt_nom'] : '';
$r = db_fetch_one(
    db_query(
        $db,
        "SELECT XGambarTanya, XVideoTanya, XAudioTanya, XGambarJawab1, XGambarJawab2, XGambarJawab3, XGambarJawab4, XGambarJawab5
        FROM cbt_soal WHERE XKodeSoal = :soal AND Urut = :urut",
        array(':soal' => $txt_soal, ':urut' => $txt_nom)
    )
);
$gambar = $r ? $r['XGambarTanya'] : '';
$audio = $r ? $r['XAudioTanya'] : '';
$video = $r ? $r['XVideoTanya'] : '';

$gambar1 = $r ? $r['XGambarJawab1'] : '';
$gambar2 = $r ? $r['XGambarJawab2'] : '';
$gambar3 = $r ? $r['XGambarJawab3'] : '';
$gambar4 = $r ? $r['XGambarJawab4'] : '';
$gambar5 = $r ? $r['XGambarJawab5'] : '';

if($file==""){$gambar = $gambar;} else {$gambar = $file;}
if($filea==""){$audio = $audio;} else {$audio = $filea;}
if($filev==""){$video = $video;} else {$video = $filev;}

if($gbr1==""){$gambar1 = $gambar1;} else {$gambar1 = $gbr1;}
if($gbr2==""){$gambar2 = $gambar2;} else {$gambar2 = $gbr2;}
if($gbr3==""){$gambar3 = $gambar3;} else {$gambar3 = $gbr3;}
if($gbr4==""){$gambar4 = $gambar4;} else {$gambar4 = $gbr4;}
if($gbr5==""){$gambar5 = $gambar5;} else {$gambar5 = $gbr5;}


    db_query(
        $db,
        "UPDATE cbt_soal SET XTanya = :tanya,
        XGambarJawab1 = :gbr1,
        XGambarJawab2 = :gbr2,
        XGambarJawab3 = :gbr3,
        XGambarJawab4 = :gbr4,
        XGambarJawab5 = :gbr5,
        XGambarTanya = :gbr,
        XAudioTanya = :aud,
        XVideoTanya = :vid,
        XJawab1 = :jawab1,
        XJawab2 = :jawab2,
        XJawab3 = :jawab3,
        XJawab4 = :jawab4,
        XJawab5 = :jawab5,
        XKunciJawaban = :kunci,
        XJenisSoal = :jenis,
        XKategori = :kategori,
        XAcakSoal = :acak,
        XAcakOpsi = :opsi
        WHERE XKodeSoal = :soal AND Urut = :urut",
        array(
            ':tanya' => $sss,
            ':gbr1' => $gambar1,
            ':gbr2' => $gambar2,
            ':gbr3' => $gambar3,
            ':gbr4' => $gambar4,
            ':gbr5' => $gambar5,
            ':gbr' => $gambar,
            ':aud' => $audio,
            ':vid' => $video,
            ':jawab1' => isset($_REQUEST['txt_jawab1']) ? $_REQUEST['txt_jawab1'] : '',
            ':jawab2' => isset($_REQUEST['txt_jawab2']) ? $_REQUEST['txt_jawab2'] : '',
            ':jawab3' => isset($_REQUEST['txt_jawab3']) ? $_REQUEST['txt_jawab3'] : '',
            ':jawab4' => isset($_REQUEST['txt_jawab4']) ? $_REQUEST['txt_jawab4'] : '',
            ':jawab5' => isset($_REQUEST['txt_jawab5']) ? $_REQUEST['txt_jawab5'] : '',
            ':kunci' => isset($_REQUEST['txt_kunci']) ? $_REQUEST['txt_kunci'] : '',
            ':jenis' => isset($_REQUEST['txt_kate']) ? $_REQUEST['txt_kate'] : '',
            ':kategori' => isset($_REQUEST['txt_kes']) ? $_REQUEST['txt_kes'] : '',
            ':acak' => isset($_REQUEST['txt_aca']) ? $_REQUEST['txt_aca'] : '',
            ':opsi' => isset($_REQUEST['txt_ops']) ? $_REQUEST['txt_ops'] : '',
            ':soal' => $txt_soal,
            ':urut' => $txt_nom,
        )
    );
	//echo "update cbt_soal set XTanya = '$sss' where XKodeSoal = '$_REQUEST[txt_soal]' and Urut = '$_REQUEST[txt_nom]'";
	

?>
