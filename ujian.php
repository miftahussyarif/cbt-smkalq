<?php
if (!isset($_COOKIE['PESERTA'])) {
    header('Location:index.php');
} ?>
<?php
include "config/server.php";
include "config/fungsi_jam.php";
include "ip.php";

$tglbuat = date("Y-m-d");
$xtgl1 = date("Y-m-d");
$xjam1 = date("H:i:s");

$user = $_COOKIE['PESERTA'];

$sqluser = mysql_query("
  SELECT * , u.XKodeKelas AS kelaz, s.XKodeKelas AS kelasx, s.XKodeJurusan AS jurusx, u.XKodeSoal AS soalz, u.XKodeUjian AS ujianz,s.XSesi as sesiz,
  s.XSetId as setidx,u.XKodeMapel as mapelx,u.XSemester as semex,s.XNIK as nik_siswa,s.XKodeSekolah as sekolah_siswa,u.XKodeSekolah as sekolah_ujian FROM cbt_siswa s 
LEFT JOIN cbt_ujian u ON (s.XKodeKelas = u.XKodeKelas or u.XKodeKelas = 'ALL') 
and (s.XKodeJurusan = u.XKodeJurusan or u.XKodeJurusan = 'ALL')
LEFT JOIN cbt_mapel m on m.XKodeMapel = u.XKodeMapel
WHERE s.XNomerUjian = '$_COOKIE[PESERTA]'
  and u.XStatusUjian = '1'
  and u.XSesi = s.XSesi
  and NOW() between CONCAT(u.XTglUjian,' ',u.XJamUjian) and ADDTIME(CONCAT(u.XTglUjian,' ',u.XJamUjian),u.XLamaUjian)
ORDER BY CONCAT(u.XTglUjian,' ',u.XJamUjian) DESC
LIMIT 1");

if (mysql_num_rows($sqluser) < 1) {
    $sqluser = mysql_query("
      SELECT * , u.XKodeKelas AS kelaz, s.XKodeKelas AS kelasx, s.XKodeJurusan AS jurusx, u.XKodeSoal AS soalz, u.XKodeUjian AS ujianz,s.XSesi as sesiz,
      s.XSetId as setidx,u.XKodeMapel as mapelx,u.XSemester as semex,s.XNIK as nik_siswa,s.XKodeSekolah as sekolah_siswa,u.XKodeSekolah as sekolah_ujian FROM cbt_siswa s 
    LEFT JOIN cbt_ujian u ON (s.XKodeKelas = u.XKodeKelas or u.XKodeKelas = 'ALL') 
    and (s.XKodeJurusan = u.XKodeJurusan or u.XKodeJurusan = 'ALL')
    LEFT JOIN cbt_mapel m on m.XKodeMapel = u.XKodeMapel
    WHERE s.XNomerUjian = '$_COOKIE[PESERTA]'
      and u.XStatusUjian = '1'
      and u.XSesi = s.XSesi
      and CONCAT(u.XTglUjian,' ',u.XJamUjian) > NOW()
    ORDER BY CONCAT(u.XTglUjian,' ',u.XJamUjian) ASC
    LIMIT 1");
}


$s = mysql_fetch_array($sqluser);
if (!$s) {
    header('Location:index.php');
    exit;
}
$val_siswa = $s['XNamaSiswa'];
$xsesi = $s['sesiz'];
$xkodesoal = $s['soalz'];
$xkodemapel = $s['mapelx'];
$xsemester = $s['semex'];
$xkodekelas = $s['kelaz'];
$xkodekelasx = $s['kelasx'];
$xkodejurusx = $s['jurusx'];
$xkodeujianx = $s['ujianz'];
$xsetidx = $s['setidx'];
$xjumlahsoal = $s['XJumSoal'];
$xtokenujian = $s['XTokenUjian'];
$xbatasmasuk = $s['XBatasMasuk'];
$xlamaujian = $s['XLamaUjian'];
$xnamamapel = $s['XNamaMapel'];
$xjamujian = $s['XJamUjian'];
$xjumpilg = $s['XPilGanda'];
$xjumesai = $s['XEsai'];
$xacaksoal = $s['XAcakSoal'];
$xjumlahpilihan = $s['XJumPilihan'];
$xtglujian = $s['XTglUjian'];
$xmaxlambat = $s['XLambat'];
$xagama = $s['XAgama'];
$xmapelagama = $s['XMapelAgama'];
$xpilih = $s['XPilihan'];
$xniksiswa = $s['nik_siswa'];
$xkodesekolah = $s['sekolah_siswa'] !== '' ? $s['sekolah_siswa'] : $s['sekolah_ujian'];

$xjumlahpilganda = $s['XPilGanda'];
$xjumlahesai = $s['XEsai'];

$xnow_ts = time();
$xmulai_ts = 0;
$xbatasmasuk_ts = 0;
$xbatasmasuk_efektif_ts = 0;

if ($xtglujian !== '' && $xjamujian !== '') {
    $xmulai_ts = strtotime($xtglujian . ' ' . $xjamujian);
}
if ($xtglujian !== '' && $xbatasmasuk !== '') {
    $xbatasmasuk_ts = strtotime($xtglujian . ' ' . $xbatasmasuk);
    if ($xmulai_ts > 0 && $xbatasmasuk_ts < $xmulai_ts) {
        $xbatasmasuk_ts += 86400;
    }
}
$xbatasmasuk_efektif_ts = $xbatasmasuk_ts;

if ($xmulai_ts > 0 && $xlamaujian !== '') {
    $durJam = (int) substr($xlamaujian, 0, 2);
    $durMenit = (int) substr($xlamaujian, 3, 2);
    $durDetik = (int) substr($xlamaujian, 6, 2);
    $durasiDetik = ($durJam * 3600) + ($durMenit * 60) + $durDetik;
    $batasDurasiTs = $xmulai_ts + $durasiDetik;

    if ((string) $xmaxlambat === '0' || strtoupper(trim($xkodeujianx)) === 'PSAJ') {
        if ($xbatasmasuk_efektif_ts <= 0 || $batasDurasiTs > $xbatasmasuk_efektif_ts) {
            $xbatasmasuk_efektif_ts = $batasDurasiTs;
        }
    }
}


$sqlIP = mysql_query("SELECT * FROM  `cbt_siswa_ujian` WHERE XNomerUjian = '$user' and XTokenUjian = '$xtokenujian'");
$ad0 = mysql_fetch_array($sqlIP);
$user_ip2 = str_replace(" ", "", $ad0['XGetIP']);
if (cbt_is_ip_lock_enabled() && $cbt_session_lock_value !== '') {
    $sqlIP1 = mysql_query("update `cbt_siswa_ujian` set XGetIP = '$cbt_session_lock_value' WHERE XNomerUjian = '$user' and XTokenUjian = '$xtokenujian' and (XGetIP = '' or XGetIP is null)");
}



if ($xtglujian <> $xtgl1) {
    header('Location:index.php');
}

//********************* JIKA TERLAMBAT MASIH DIKASIH WAKTU YANG SAMA ***************
//                      DENGAN SISWA TDK TERLAMBAT , MAKA XLAMAUJIAN = XLAMA UJIAN 

$xlamaujian = $s['XLamaUjian'];
//**********************************************************************************

//********************* JIKA SISWA TERLAMBAT WAKTU ENGERJAAN LEBIH SEDIKIT DARI
//                      SISWA TDK TERLAMBAT , MAKA XLAMAUJIAN = XLAMAUJIAN - (XMULAIUJIAN - XJAMUJIAN)
$jm1 = substr($xjam1, 0, 2);
$mn1 = substr($xjam1, 3, 2);
$dt1 = substr($xjam1, 6, 2); // pecah xmulaiujian ambil dari jamsekarang

$jm2 = substr($xjamujian, 0, 2);
$mn2 = substr($xjamujian, 3, 2);
$dt2 = substr($xjamujian, 6, 2);// pecah xjamujian 

$tg1 = substr($xtgl1, 8, 2);
$bl1 = substr($xtgl1, 5, 2);
$th1 = substr($xtgl1, 0, 4);
//mktime(hour,minute,second,month,day,year,is_dst) 
$selstart = mktime($jm1, $mn1, $dt1, $bl1, $tg1, $th1); /// jam mulai ujian
$selend = mktime($jm2, $mn2, $dt2, $bl1, $tg1, $th1); /// jam terakhir di database
$diffsec = $selstart - $selend;
$hr = (int) ($diffsec / 3600);
$mn = (int) (($diffsec % 3600) / 60);
$sc = $diffsec - ($hr * 3600 + $mn * 60); // Hasil pengurangan (XMULAIUJIAN - XJAMUJIAN)

$jm3 = substr($xlamaujian, 0, 2);
$mn3 = substr($xlamaujian, 3, 2);
$dt3 = substr($xlamaujian, 6, 2);// pecah xlamaujian 
$selstart2 = mktime($jm3, $mn3, $dt3, $bl1, $tg1, $th1); /// jam xlamaujian
$selend2 = mktime($hr, $mn, $sc, $bl1, $tg1, $th1); /// jam terakhir di database

$diffsec2 = $selstart2 - $selend2;
$hr2 = (int) ($diffsec2 / 3600);
$mn2 = (int) (($diffsec2 % 3600) / 60);
$sc2 = $diffsec2 - ($hr2 * 3600 + $mn2 * 60); // Hasil pengurangan (XMULAIUJIAN - XJAMUJIAN)

if ($hr2 == "0") {
    $hr2 = "00";
}
if ($mn2 == "0") {
    $mn2 = "00";
}
if ($sc2 == "0") {
    $sc2 = "00";
}

$hrz = strlen($hr2);
$mnz = strlen($mn2);

if ($hrz < 2) {
    $hr2 = "0" . $hr2;
} else {
    $hr2 = $hr2;
}
if ($mnz < 2) {
    $mn2 = "0" . $mn2;
} else {
    $mn2 = $mn2;
}

$sisawaktu = "$hr2:$mn2:$sc2";
//*********************************************************************************************

//cek data siswa ujian
$sqlceksiswa = mysql_query("select * from cbt_siswa_ujian where XNomerUjian = '$user' and XKodeSoal = '$xkodesoal' and XTokenUjian ='$xtokenujian' and XSesi ='$xsesi'");
$jumsqlceksiswa = mysql_num_rows($sqlceksiswa);
$s2 = mysql_fetch_array($sqlceksiswa);

//cek status ujian jika status = 9 maka sudah selesai redirect ke logout
$xstatusujian = $s2['XStatusUjian'];
if ($xstatusujian == 9) {
    header('location:logout.php');
}


//bandingkan jam sekarang dengan jam 	
//echo "";
if ($jumsqlceksiswa < 1) { // jika siswa belum pernah login 


    if ($xbatasmasuk_efektif_ts > 0 && $xnow_ts > $xbatasmasuk_efektif_ts) {
        $sqlout = mysql_query("Update cbt_siswa_ujian set XStatusUjian = '9' where XNomerUjian = '$user' and XStatusUjian = '1' and XTokenUjian ='$xtokenujian' and XSesi ='$xsesi'");
        // header('location:logout.php');
    }

    if ($xmaxlambat == 1) {
        //echo "Jam Mulai |$xjam1|";  
//******************* jika jam terlambat diperhitungkan 
        $xlamaujian = $sisawaktu;
    } elseif ($xmaxlambat == 0) {
        //******************* jika jam terlambat diperhitungkan 
        $xlamaujian = $xlamaujian;
    }

    $xjumlahjam = $xlamaujian;
    $xjam = substr($xjumlahjam, 0, 2);
    $xmnt = substr($xjumlahjam, 3, 2);
    $xdtk = substr($xjumlahjam, 6, 2);

    //  echo "$xjumlahjam  $xjam:$xmnt:$xdtk ";
    $xtglujiandb = $xtgl1 . " " . $xjam1;
    $xsisawaktu = $xlamaujian;
    $xdurasi = (((int) $xjam) * 3600) + (((int) $xmnt) * 60) + ((int) $xdtk);
    $xtargetujian = date('H:i:s', strtotime($xtgl1 . " " . $xjam1) + $xdurasi);
    $xselesaiujian = "00:00:00";


	    $sqlinputsiswa = mysql_query("insert into cbt_siswa_ujian 
			(XNomerUjian, XNISN, XKodeKelas, XKodeMapel, XKodeSoal, XPilGanda, XEsai, XJumSoal, XTglUjian, XJamUjian, XMulaiUjian, XLastUpdate, XSisaWaktu, XLamaUjian, XTargetUjian, XTokenUjian, XSelesaiUjian, XSetId, XKodeUjian, XSesi, XStatusUjian, XKodeSekolah, XGetIP) values 
			('$user','$xniksiswa','$xkodekelasx','$xkodemapel','$xkodesoal','$xjumpilg','$xjumesai','$xjumlahsoal','$xtglujiandb','$xjamujian','$xjam1',
			'$xjam1','$xsisawaktu','$xlamaujian','$xtargetujian','$xtokenujian','$xselesaiujian','$xsetidx','$xkodeujianx','$xsesi','1','$xkodesekolah','$cbt_session_lock_value')");
    if (!$sqlinputsiswa && function_exists('bee_log')) {
        bee_log('ERROR', 'INSERT_SISWA_UJIAN_FAILED', 'Gagal insert data ke cbt_siswa_ujian', array(
            'user' => $user,
            'token' => $xtokenujian,
            'kodesoal' => $xkodesoal,
            'ip' => $user_ip,
            'mysql_error' => mysql_error()
        ));
    }


} else {
    ?>


    <?php


    $tglbalik = date("H:i:s");
    if (isset($_COOKIE['PESERTA'])) {
        $user = $_COOKIE['PESERTA'];
        $sql = mysql_query("Update cbt_siswa_ujian set XLastUpdate = '$tglbalik'  where XNomerUjian = '$user' and XStatusUjian = '1' ");
    }

    $j1 = substr($s2['XMulaiUjian'], 0, 2);
    $m1 = substr($s2['XMulaiUjian'], 3, 2);
    $d1 = substr($s2['XMulaiUjian'], 6, 2);

    $j2 = substr($s2['XLastUpdate'], 0, 2);
    $m2 = substr($s2['XLastUpdate'], 3, 2);
    $d2 = substr($s2['XLastUpdate'], 6, 2);

    $sekarang = date("Y-m-d");
    $tgls = substr($sekarang, 8, 2);
    $blns = substr($sekarang, 5, 2);
    $thns = substr($sekarang, 0, 4);
    //mktime(hour,minute,second,month,day,year,is_dst) 
    $start = mktime($j1, $m1, $d1, $blns, $tgls, $thns); /// jam mulai ujian
    $end = mktime($j2, $m2, $d2, $blns, $tgls, $thns); /// jam terakhir di database

    //ambil  waktu yang sdh dipakai = jam terakhir di database - jam mulai ujian
    $diffSeconds = $end - $start;
    $hrs = (int) ($diffSeconds / 3600);
    $mins = (int) (($diffSeconds % 3600) / 60);
    $secs = $diffSeconds - ($hrs * 3600 + $mins * 60);

    //=============  waktu yang sdh dipakai
//echo "$hrs $mins $secs |<br>$j1,$m1,$d1,$blns,$tgls,$thns <br>$j2,$m2,$d2,$blns,$tgls,$thns";//11:09

    //*********************** Jam Timer = XLamaUjian - ($hrs $mins $secs)
    $awal = mktime($hrs, $mins, $secs, $blns, $tgls, $thns); /// Waktu Yang sudah dipakai

    //============= mengambil dan memecah XLamaUjian
    $j3 = substr($s2['XLamaUjian'], 0, 2);
    $m3 = substr($s2['XLamaUjian'], 3, 2);
    $d3 = substr($s2['XLamaUjian'], 6, 2);

    $akhir = mktime($j3, $m3, $d3, $blns, $tgls, $thns); /// XLamaUjian

    //ambil  waktu yang sdh dipakai = jam terakhir di database - jam mulai ujian
    $diffSeconds3 = $akhir - $awal;
    $hrs3 = (int) ($diffSeconds3 / 3600);
    $mins3 = (int) (($diffSeconds3 % 3600) / 60);
    $secs3 = $diffSeconds3 - ($hrs3 * 3600 + $mins3 * 60);
    //echo "<br>==$hrs3:$mins3:$secs3" ;

    //echo "$hrs:$mins:$secs" ;
//add time
    if (isset($xjam)) {
        $jatahjam = $xjam;
    }
    if (isset($xmnt)) {
        $jatahmnt = $xmnt;
    }
    if (isset($xjatahjam) && isset($xjatahmnt)) {
        $menit = $jatahmnt + ($jatahjam * 60);
    }
    if (isset($xmenit)) {
        $timestamp = strtotime($s2['XMulaiUjian']) + $menit * 60;
    }
    if (isset($timestamp)) {
        $tjam = date('H', $timestamp);
        $tmnt = date('i', $timestamp);
        $tdtk = date('s', $timestamp);
    }
    //echo "$jatahjam";
//Nilai Akhir yang muncul di Timer Countdown

    $xjam = $hrs3;
    $xmnt = $mins3;
    $xdtk = $secs3;

}
?>
<?php include "modal.php"; ?>

<!DOCTYPE html>
<!-- <script type="text/javascript" src="js/jquery.js"></script> !-->
<script src="js/jquery-scrolltofixed.js" type="text/javascript"></script>

<script>
    $(document).ready(function () {

        $(function () {//document ready event
            setTimeout(function () {
                $("#myModal").show();
            }, 3000);//set interval to 3 second
        });
        // Dock the header to the top of the window when scrolled past the banner.
        // This is the default behavior.

        $('.header').scrollToFixed();
        // Dock the footer to the bottom of the page, but scroll up to reveal more
        // content if the page is scrolled far enough.

        $('.footer').scrollToFixed({
            bottom: 0,
            limit: $('.footer').offset().top
        });


        // Dock each summary as it arrives just below the docked header, pushing the
        // previous summary up the page.

        var summaries = $('.summary');
        summaries.each(function (i) {
            var summary = $(summaries[i]);
            var next = summaries[i + 1];

            summary.scrollToFixed({
                marginTop: $('.header').outerHeight(true) + 10,
                limit: function () {
                    var limit = 0;
                    if (next) {
                        limit = $(next).offset().top - $(this).outerHeight(true) - 10;
                    } else {
                        limit = $('.footer').offset().top - $(this).outerHeight(true) - 10;
                    }
                    return limit;
                },
                zIndex: 999
            });
        });
    });
</script>

<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title>CBT SMK AL QODIRIYAH | UJIAN ONLINE</title>
<meta name="description" content="">
<meta name="viewport" content="width=device-width, initial-scale=1">

<script>$("input").on("click", function () {
        if ($(this).attr("type") === "radio") {
            $(this).parent().siblings().removeClass("isSelected");
        }
        $(this).parent().toggleClass("isSelected");
    });</script>

<script type="text/javascript" src="js/sidein_menu.js"></script>
<style>
    #awal {
        color: #FFF;
        font-family: Arial, Helvetica, sans-serif;
        line-height: 90%;
        margin: 0px auto;
        margin-top: 20px;
    }

    #ahir {
        color: #FFF;
        font-family: Arial, Helvetica, sans-serif;
        line-height: 120%;
        margin: 0px auto;
        margin-top: 10px;
    }


    #kaki {
        margin-top: -8px;
        margin-left: 15px;
        margin-bottom: 10px;
        margin-right: 15px;
        background-color: #000;
        color: #fff;
        height: 400px;
    }

    #koplembarsoal {
        margin-top: 15px;
        margin-left: 15px;
        margin-bottom: 15px;
        margin-right: 15px;
        background-color: #fff;
        height: 90px;
        font-size: 24px;
        font-weight: bold;
    }

    .title {
        font-size: 13pt;
        font-weight: bold;
        margin-left: 20px;
        margin-top: -33px;
        top: -33px;
    }

    .left {
        float: left;
        width: 70%;
        overflow: hidden;
    }

    .left img {
        width: 100%;
        height: auto;
        display: block;
        object-fit: cover;
    }

    .right {
        float: right;
        width: 30%;
        background-color: #333333;
        height: 101px;
        color: #FFFFFF;
        font-size: 13px;
        font-style: normal;
        font-weight: normal;
    }

    .header {
        background-color: #fff;
        padding-top: 7px;
        padding-bottom: 11px;
        margin-left: 15px;
        margin-right: 15px;
        margin-top: 10px;
        margin-bottom: 2px;
    }


    .header.scroll-to-fixed-fixed {
        color: red;
        margin-top: 0px;
        border-bottom-style: solid;
        border-color: #ccc;
        -webkit-box-shadow: 0 8px 6px -6px #ccc;
        -moz-box-shadow: 0 8px 6px -6px #ccc;
        box-shadow: 0 8px 6px -6px #ccc;

        margin-left: 0px;
    }

    .lanjut {
        background-color: #fff;
        width: 100%;
    }

    #primary {
        float: left;
        width: 480px;

    }

    #content {
        float: left;
        width: 480px;
    }

    #secondary {
        float: left;
        width: 480px;
    }

    .kotaksoal {
        width: 97%;
        padding: 20px;
        border: solid;
        top: 30px;
        border-color: #CCC;
        height: 100%;
    }

    .flex-next {
        background-color: #336898;
        width: 20px;
        height: 20px;
        margin: 10px;
        line-height: 20px;
        color: white;
        font-size: 18px;
        text-align: center;
        padding-left: 12px;
        padding-right: 12px;
        padding-top: 10px;
        padding-bottom: 10px;

    }

    .flex-ragu {
        background-color: #FC0;
        width: 20px;
        height: 20px;
        margin: 10px;
        line-height: 20px;
        color: white;
        font-size: 18px;
        text-align: center;
        padding-left: 12px;
        padding-right: 12px;
        padding-top: 10px;
        padding-bottom: 10px;
        text-decoration: none;
    }

    .flex-prev {
        background-color: #999;
        width: 25px;
        height: 25px;
        margin: 10px;
        line-height: 20px;
        color: white;
        font-size: 18px;
        text-align: center;
        padding-left: 12px;
        padding-right: 12px;
        padding-top: 10px;
        padding-bottom: 10px;
    }

    .flex-container {
        height: 100%;
        padding: 0;
        margin: 0;
        display: -webkit-box;
        display: -moz-box;
        display: -ms-flexbox;
        display: -webkit-flex;
        display: flex;
    }

    .row {
        width: auto;
        /*border: 1px solid blue;*/
        background-color: #336898;
    }

    .flex-item {
        background-color: #336898;
        width: 120px;
        height: 40px;
        margin-right: 0px;
        margin-top: -10px;
        line-height: 20px;
        color: white;
        font-size: 15px;
        font-weight: bold;
        text-align: center;
        padding-left: 12px;
        padding-right: 12px;
        padding-top: 7px;
        padding-bottom: 6px;
    }

    .flex-abu {
        background-color: #999;
        width: 120px;
        height: 40px;
        margin-right: 0px;
        margin-top: -10px;
        line-height: 20px;
        color: white;
        font-size: 15px;
        text-align: center;
        padding-left: 12px;
        padding-right: 12px;
        padding-top: 10px;
        padding-bottom: 10px;
        float: right;
    }

    .flex-biru {
        background-color: #000;
        width: 120px;
        height: 40px;
        margin-right: 0px;
        margin-top: -10px;
        line-height: 20px;
        color: white;
        font-size: 15px;
        text-align: center;
        padding-left: 5px;
        padding-right: 5px;
        padding-top: 10px;
        padding-bottom: 10px;
        float: right;
    }

    .flex-putih {
        background-color: #fff;
        width: 120px;
        height: 40px;
        margin-right: 0px;
        margin-top: -10px;
        line-height: 20px;
        color: black;
        font-size: 15px;
        font-weight: bold;
        text-align: center;
        padding-left: 12px;
        padding-right: 12px;
        padding-top: 10px;
        padding-bottom: 10px;
        float: left;
    }

    #cbt-lock-banner {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80%;
        max-width: 960px;
        z-index: 10000;
        background-color: #b30000;
        color: #fff;
        text-align: center;
        padding: 20px;
        font-size: 32px;
        font-weight: bold;
        border-radius: 12px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.35);
        display: none;
    }

    body.cbt-locked #cbt-lock-banner {
        display: block;
    }

    body.cbt-locked #picture,
    body.cbt-locked #slideMenu,
    body.cbt-locked #fontlembarsoal {
        pointer-events: none;
        opacity: 0.6;
    }

    body.cbt-locked .get_pic,
    body.cbt-locked .get1_pic {
        pointer-events: none;
    }
</style>

<script type="text/javascript" src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script>
    function showUser(str) {
        alert();
        $.ajax({
            //this was the confusing part...did not know how to pass the data to the script
            url: 'simpanragu.php',
            type: 'post',
            data: 'who=' + who + '&chk=' + chk,
            success: function (data) {
                return false;
            }
        });
        return false;
    }
</script>
<script>
    function toggle_select(id) {
        if (window.__cbtLocked) {
            return false;
        }
        var anu = document.getElementById(id);
        var X = document.getElementById(id);
        if (X.checked == true) {
            X.value = "1";
        } else {
            X.value = "0";
        }

        //var sql="update clients set calendar='" + X.value + "' where cli_ID='" + X.id + "' limit 1";
        var who = X.id;
        var chk = X.value

        //alert(who+"Ujian"+chk);

        //alert("Joe is still debugging: (function incomplete/database record was not updated)\n"+ sql);
        $.ajax({
            //this was the confusing part...did not know how to pass the data to the script
            url: 'simpanragu.php',
            type: 'post',
            data: 'who=' + who + '&chk=' + chk + '&anu=' + anu,
            success: function (data) {
                return false;
                /*
                success: function(output) 
                { alert('success, server says '+output);
                return false;
                },
                error: function()
                { alert('something went wrong, save failed');
                return false;
                }
                */
            }
        });
        return false;



    }
</script>

<script>
    function disableBackButton() {
        window.history.forward();
    }
    setTimeout("disableBackButton()", 0);


    var box = document.querySelector('#no_email');
    console.log(box);

    box.addEventListener('change', function no_email_confirm() {
        if (this.checked == false) {
            return true;
        } else {
            var confirmation = confirm("This means that the VENDOR will NOT RECEIVE ANY communication!!!!");
            if (confirmation)
                return true;
            else
                box.checked = false;
        }
    });
</script>

<style>
    .no-close .ui-dialog-titlebar-close {
        display: none;
    }

    #tampilkan {
        background-color: #336898;
        width: 150px;
        height: 50px;
        margin-right: 20px;
        margin-top: -10px;
        line-height: 20px;
        color: white;
        font-size: 22px;
        text-align: center;
        padding-left: 12px;
        padding-right: 12px;
        padding-top: 14px;
        padding-bottom: 14px;
        float: right;

    }
</style>
<!--
<link href="css/fonts.css" rel="stylesheet">
<link href="css/main.css" rel="stylesheet">!-->
<link href="css/klien.css" rel="stylesheet">

<link href="css/sikil.css" rel="stylesheet">
<link href="css/getsoal.css" rel="stylesheet">
<script src="js/inline.js"></script>
<!--<script type="text/javascript"
  src="http://cdn.mathjax.org/mathjax/latest/MathJax.js?config=AM_HTMLorMML-full"></script>
  !-->

<?php
$cek = mysql_num_rows(mysql_query("select * from cbt_jawaban where XKodeSoal = '$xkodesoal' and XUserJawab = '$user' and XTokenUjian = '$xtokenujian'"));
if ($cek < 1) {
    $hit = 1;


    //  $xjumpilg = $s['XPilGanda'];   
//  $xjumesai = $s['XEsai'];     

    //ambil soal pilihan yang status acak sebanyak xjumlahsoalpil


    if ($xmapelagama == 'Y') {
        $sqlambilsoalpilT1 = mysql_query("select * from cbt_soal where XKodeSoal = '$xkodesoal' and XJenisSoal = '1' and XAcakSoal = 'T' and XAgama = '$xpilih' order by Urut LIMIT  $xjumpilg");
    } else if ($xmapelagama == 'A') {
        $sqlambilsoalpilT1 = mysql_query("select * from cbt_soal where XKodeSoal = '$xkodesoal' and XJenisSoal = '1' and XAcakSoal = 'T' and XAgama = '$xagama' order by Urut LIMIT  $xjumpilg");
    } else {
        $sqlambilsoalpilT1 = mysql_query("select * from cbt_soal where XKodeSoal = '$xkodesoal' and XJenisSoal = '1' and XAcakSoal = 'T' order by Urut LIMIT  $xjumpilg");
    }
    while ($r2 = mysql_fetch_array($sqlambilsoalpilT1)) {
        if ($xjumlahpilihan == 4) {
            $a = array("1", "2", "3", "4");

            if ($r2['XAcakOpsi'] == 'Y') { //jika opsijawaban diacak
                shuffle($a);
            }

            $A1 = $a[0];
            $X1 = "XGambarJawab1$A1";
            $B1 = $a[1];
            $C1 = $a[2];
            $D1 = $a[3];

            $var = array_search($r2['XKunciJawaban'], $a);
            $kuncijawab1 = $var + 1;
            if ($kuncijawab1 == '1') {
                $kuncijawab = $A1;
            }
            if ($kuncijawab1 == '2') {
                $kuncijawab = $B1;
            }
            if ($kuncijawab1 == '3') {
                $kuncijawab = $C1;
            }
            if ($kuncijawab1 == '4') {
                $kuncijawab = $D1;
            }


            $sql = mysql_query("insert into cbt_jawaban (Urut,XNomerSoal,XUserJawab,XKodeSoal,XTokenUjian,XKunciJawaban,XA,XB,XC,XD,XTglJawab,XJenisSoal,XKodeKelas,XKodeJurusan,XKodeUjian,XSetId,XKodeMapel,XSemester) values 	
	('$hit','$r2[XNomerSoal]','$user','$xkodesoal','$xtokenujian','$kuncijawab]',
	'$A1','$B1','$C1','$D1','$tglbuat','1','$xkodekelasx','$xkodejurusx','$xkodeujianx',
'$xsetidx','$xkodemapel','$xsemester')");
            $hit = $hit + 1;
        } elseif ($xjumlahpilihan == 5) {
            $a = array("1", "2", "3", "4", "5");

            if ($r2['XAcakOpsi'] == 'Y') { //jika opsijawaban diacak
                shuffle($a);
            }

            $A1 = $a[0];
            $B1 = $a[1];
            $C1 = $a[2];
            $D1 = $a[3];
            $E1 = $a[4];
            $var = array_search($r2['XKunciJawaban'], $a);
            $kuncijawab1 = $var + 1;
            if ($kuncijawab1 == '1') {
                $kuncijawab = $A1;
            }
            if ($kuncijawab1 == '2') {
                $kuncijawab = $B1;
            }
            if ($kuncijawab1 == '3') {
                $kuncijawab = $C1;
            }
            if ($kuncijawab1 == '4') {
                $kuncijawab = $D1;
            }
            if ($kuncijawab1 == '5') {
                $kuncijawab = $E1;
            }



            $sql = mysql_query("insert into cbt_jawaban (Urut,XNomerSoal,XUserJawab,XKodeSoal,XTokenUjian,XKunciJawaban,XA,XB,XC,XD,XE,XTglJawab,XJenisSoal,XKodeKelas,XKodeJurusan,XKodeUjian,XSetId,XKodeMapel,XSemester) values 
	('$hit','$r2[XNomerSoal]','$user','$xkodesoal','$xtokenujian','$kuncijawab','$A1','$B1','$C1','$D1','$E1','$tglbuat','1','$xkodekelasx','$xkodejurusx','$xkodeujianx',
'$xsetidx','$xkodemapel','$xsemester')");
            $hit = $hit + 1;
        }
    }


    // jumlah soal tidak acak harus tampil semua
    $jmlpilT = mysql_num_rows($sqlambilsoalpilT1);
    // jumlah soal tersisa buat yg acak adalah $jmlpilA
    $jmlpilA = $xjumpilg - $jmlpilT;

    if ($xmapelagama == 'Y') {
        $sqlambilsoalpilA2 = mysql_query("select * from cbt_soal where XKodeSoal = '$xkodesoal' and XJenisSoal = '1' and XAcakSoal = 'A' and XAgama = '$xpilih'
   order by RAND() LIMIT  $jmlpilA");
    } elseif ($xmapelagama == 'A') {
        $sqlambilsoalpilA2 = mysql_query("select * from cbt_soal where XKodeSoal = '$xkodesoal' and XJenisSoal = '1' and XAcakSoal = 'A' and XAgama = '$xagama'
   order by RAND() LIMIT  $jmlpilA");
    } else {
        $sqlambilsoalpilA2 = mysql_query("select * from cbt_soal where XKodeSoal = '$xkodesoal' and XJenisSoal = '1' and XAcakSoal = 'A' order by RAND() LIMIT  $jmlpilA");
    }
    while ($r2 = mysql_fetch_array($sqlambilsoalpilA2)) {
        if ($xjumlahpilihan == 4) {
            $a = array("1", "2", "3", "4");

            if ($r2['XAcakOpsi'] == 'Y') { //jika opsijawaban diacak
                shuffle($a);
            }

            $A1 = $a[0];
            $B1 = $a[1];
            $C1 = $a[2];
            $D1 = $a[3];
            $sql = mysql_query("insert into cbt_jawaban (Urut,XNomerSoal,XUserJawab,XKodeSoal,XTokenUjian,XKunciJawaban,XA,XB,XC,XD,XTglJawab,XJenisSoal,XKodeKelas,XKodeJurusan,XKodeUjian,XSetId,XKodeMapel,XSemester) values 	
	('$hit','$r2[XNomerSoal]','$user','$xkodesoal','$xtokenujian','$r2[XKunciJawaban]','$A1','$B1','$C1','$D1','$tglbuat','1','$xkodekelasx','$xkodejurusx','$xkodeujianx',
'$xsetidx','$xkodemapel','$xsemester')");
            $hit = $hit + 1;
        } elseif ($xjumlahpilihan == 5) {
            $a = array("1", "2", "3", "4", "5");

            if ($r2['XAcakOpsi'] == 'Y') { //jika opsijawaban diacak
                shuffle($a);
            }

            $A1 = $a[0];
            $B1 = $a[1];
            $C1 = $a[2];
            $D1 = $a[3];
            $E1 = $a[4];
            $sql = mysql_query("insert into cbt_jawaban (Urut,XNomerSoal,XUserJawab,XKodeSoal,XTokenUjian,XKunciJawaban,XA,XB,XC,XD,XE,XTglJawab,XJenisSoal,XKodeKelas,XKodeJurusan,XKodeUjian,XSetId,XKodeMapel,XSemester) values 
	('$hit','$r2[XNomerSoal]','$user','$xkodesoal','$xtokenujian','$r2[XKunciJawaban]','$A1','$B1','$C1','$D1','$E1','$tglbuat','1','$xkodekelasx','$xkodejurusx','$xkodeujianx',
'$xsetidx','$xkodemapel','$xsemester')");
            $hit = $hit + 1;
        }
    }

    //Ambil Soal Esai 
//  $xjumlahesai = $s['XEsai'];  
    if ($xmapelagama == 'A') {
        $sqlambilsoalesai = mysql_query("select * from cbt_soal where XKodeSoal = '$xkodesoal' and XJenisSoal = '2' and XAcakSoal = 'T' and XAgama = '$xagama' order by Urut LIMIT $xjumesai");
    } elseif ($xmapelagama == 'Y') {
        $sqlambilsoalesai = mysql_query("select * from cbt_soal where XKodeSoal = '$xkodesoal' and XJenisSoal = '2' and XAcakSoal = 'T' and XAgama = '$xpilih' order by Urut LIMIT $xjumesai");
    } else {
        $sqlambilsoalesai = mysql_query("select * from cbt_soal where XKodeSoal = '$xkodesoal' and XJenisSoal = '2' and XAcakSoal = 'T' order by Urut LIMIT $xjumesai");
    }
    while ($r1 = mysql_fetch_array($sqlambilsoalesai)) {
        $sqlsimpanesai = mysql_query("insert into cbt_jawaban (Urut,XNomerSoal,XUserJawab,XKodeSoal,XTokenUjian,XTglJawab,XJenisSoal,XKodeKelas,XKodeJurusan,XKodeUjian,XSetId,XKodeMapel,XSemester) values 	
	  ('$hit','$r1[XNomerSoal]','$user','$xkodesoal','$xtokenujian','$tglbuat','2','$xkodekelasx','$xkodejurusx','$xkodeujianx','$xsetidx','$xkodemapel','$xsemester'  
)");
        $hit = $hit + 1;
    }
    //esai utama harus muncul bila acak=T
    $jmlesaiutama = mysql_num_rows($sqlambilsoalesai);
    //jika jml esai utama masih < xjumlahesai
    if ($jmlesaiutama < $xjumesai) {
        //ambil acak esai tambahan sebanyak sisa esai
        $sisaesai = $xjumesai - $jmlesaiutama;
        if ($xmapelagama == 'Y') {
            $sqlambilsoalesai = mysql_query("select * from cbt_soal where XKodeSoal = '$xkodesoal' and XJenisSoal = '2' and XAcakSoal = 'A' and XAgama = '$xpilih' order by RAND() 
	  LIMIT $sisaesai");
        } elseif ($xmapelagama == 'A') {
            $sqlambilsoalesai = mysql_query("select * from cbt_soal where XKodeSoal = '$xkodesoal' and XJenisSoal = '2' and XAcakSoal = 'A' and XAgama = '$xagama' order by RAND() 
	  LIMIT $sisaesai");
        } else {
            $sqlambilsoalesai = mysql_query("select * from cbt_soal where XKodeSoal = '$xkodesoal' and XJenisSoal = '2' and XAcakSoal = 'A' order by RAND() LIMIT $sisaesai");
        }
        while ($r2 = mysql_fetch_array($sqlambilsoalesai)) {
            $sqlsimpanesai = mysql_query("insert into cbt_jawaban (Urut,XNomerSoal,XUserJawab,XKodeSoal,XTokenUjian,XTglJawab,XJenisSoal,XKodeKelas,XKodeJurusan,XKodeUjian,XSetId,XKodeMapel,XSemester) values 	
	  ('$hit','$r2[XNomerSoal]','$user','$xkodesoal','$xtokenujian','$tglbuat','2','$xkodekelasx','$xkodejurusx','$xkodeujianx','$xsetidx','$xkodemapel','$xsemester')");
            $hit = $hit + 1;
        }

    }

    $xjumlahsoalpil = $xjumlahsoal - $xjumlahesai;
    //Ambil Soal berdasarkan Random atau Tidak
/*
  $sqlambilsoalpilT1 = mysql_query("select * from cbt_soal where XKodeSoal = '$xkodesoal' and XJenisSoal = '1' and XAcakSoal = 'T' order by Urut LIMIT  $jmlpilT");
  while($r1=mysql_fetch_array($sqlambilsoalpilT1)){
	if($xjumlahpilihan==4){
	$a=array("1","2","3","4");
	shuffle($a);

	$A1 = $a[0];
	$B1 = $a[1];
	$C1 = $a[2];
	$D1 = $a[3];
	$sql = mysql_query("insert into cbt_jawaban (Urut,XNomerSoal,XUserJawab,XKodeSoal,XTokenUjian,XKunciJawaban,XA,XB,XC,XD,XTglJawab,XJenisSoal) values 	
	('$hit','$r1[XNomerSoal]','$user','$xkodesoal','$xtokenujian','$r1[XKunciJawaban]','$A1','$B1','$C1','$D1','$tglbuat','1')"); 
	$hit = $hit+1;
	} elseif($xjumlahpilihan==5){
    $a=array("1","2","3","4","5");
	shuffle($a);

	$A1 = $a[0];
	$B1 = $a[1];
	$C1 = $a[2];
	$D1 = $a[3];
	$E1 = $a[4];	
	$sql = mysql_query("insert into cbt_jawaban (Urut,XNomerSoal,XUserJawab,XKodeSoal,XTokenUjian,XKunciJawaban,XA,XB,XC,XD,XE,XTglJawab,XJenisSoal) values 
	('$hit','$r1[XNomerSoal]','$user','$xkodesoal','$xtokenujian','$r1[XKunciJawaban]','$A1','$B1','$C1','$D1','$E1','$tglbuat','1')");
	$hit = $hit+1; 
	}  
  }
*/
}
?>
<style>
    .container {
        font-size: 0;
        /*fix white space*/
    }

    .container>div {
        font-size: 16px;
        /*reset font size*/
        display: inline-block;
        vertical-align: top;
        width: 33.33%;
        border: thin;
        border-color: #0000FF;
        box-sizing: border-box;
        text-align: left;

    }

    @media (max-width: 400px) {

        /*breakpoint*/
        .container>div {
            display: block;
            width: 100%;
            margin-left: 0px;
        }
    }
</style>
<style>
    .left1 {
        float: left;
        width: 70%;
        overflow: hidden;
    }

    .left1 img {
        width: 100%;
        height: auto;
        display: block;
        object-fit: cover;
    }

    .right1 {
        float: right;
        width: 30%;
        background-color: #333333;
        height: 101px;
        color: #FFFFFF;
        font-size: 13px;
        font-style: normal;
        font-weight: normal;
    }

    .user {
        color: #FFFFFF;
        font-size: 15px;
        font-style: normal;
        font-weight: bold;
        top: -20px;
    }

    .log {
        color: #3799c2;
        font-size: 11px;
        font-style: normal;
        font-weight: bold;
        top: -20px;
    }

    .group:after {
        content: "";
        display: table;
        clear: both;

    }

    /*
img {
    max-width: 100%;
    height: auto;
}
*/

    .visible {
        display: block !important;
    }

    .hidden {
        display: none !important;
    }

    .foto {
        height: 80px;
    }

    @media screen and (max-width: 780px) {

        /* jika screen maks. 780 right turun */
        /*    .left, */
        .left1,
        .right1 {
            float: none;
            width: auto;
            margin-top: 0px;
            height: 91px;
            color: #FFFFFF;
            display: block;
        }

        .foto {
            height: 65px;
        }

        .flex-putih,
        {
        display: none
    }

    .flex-abu {
        background-color: #999;
        width: 100px;
        height: 40px;
        margin-right: 0px;
        margin-top: -10px;
        line-height: 20px;
        color: white;
        font-size: 15px;
        text-align: center;
        padding-left: 5px;
        padding-right: 5px;
        padding-top: 10px;
        padding-bottom: 10px;
        float: right;
    }
    }

    @media screen and (max-width: 400px) {

        /* jika screen maks. 780 right turun */
        /*    .left, */
        .left1 {
            width: auto;
            height: 91px;
        }

        .right1 {
            float: none;
            width: auto;
            margin-top: 0px;
            height: 60px;
            color: #FFFFFF;
        }

        .foto {
            height: 40px;
        }

        .fontlembarsoal {
            padding-top: 50px;
        }

        .left1 {
            width: auto;
            height: 91px;
        }

        .right1 {
            float: none;
            width: auto;
            margin-top: 0px;
            height: 60px;
            color: #FFFFFF;
        }

        .flex-putih,
        .flex-abu {
            display: none
        }

        .flex-item {
            margin-left: 20px;
        }
    }
</style>

<?php
include "config/server.php";
$sql = mysql_query("select * from cbt_admin");
$r = mysql_fetch_array($sql);
$fotoSiswaFile = 'nouser.png';
if (isset($s['XFoto'])) {
    $candidateFoto = trim($s['XFoto']);
    if ($candidateFoto !== '') {
        $candidateFoto = basename($candidateFoto);
        $candidatePath = __DIR__ . '/fotosiswa/' . $candidateFoto;
        if (is_file($candidatePath)) {
            $fotoSiswaFile = $candidateFoto;
        }
    }
}
$fotoSiswaUrl = 'fotosiswa/' . rawurlencode($fotoSiswaFile);
?>

<body class="font-medium" style="background-color:#c9c9c9">
    <header style="background-color:<?php echo "$r[XWarna]"; ?>">
        <div class="group">
            <div class="left" style="background-color:<?php echo "$r[XWarna]"; ?>"><a href=" "><img
                        src="images/<?php echo "$r[XBanner]"; ?>" style=" margin-left:0px;"></a>
            </div>
            <div class="right1">
                <table width="100%" border="0" cellspacing="5px;" style="margin-top:10px">
                    <tr>
                        <td rowspan="3" width="100px" align="center"><img src="<?php echo $fotoSiswaUrl; ?>"
                                style=" margin-left:0px; margin-top:5px" class="foto"></td>
                        <td><span style=" margin-left:0px; margin-top:5px">Selamat Datang</span></td>
                    </tr>
                    <tr>
                        <td><span class="user"><?php echo "$val_siswa ($xkodekelasx)"; ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="log"><a href="logout.php">Logout</a><span></td>
                    </tr>
                </table>
            </div>

        </div>
        </div>
        </div>
    </header>

    <div id="cbt-lock-banner">User anda terkunci. Terdeteksi pelanggaran ujian! Hubungi pengawas ruang untuk melanjutkan ujian.</div>

    <li class="header">
        <div class="main">

            <span class="flex-putih">SOAL NO. </span>
            <!-- asli            <span class="flex-item" style="background-color:<?php echo $cssb; ?>"  id="soal"></span> !-->
            <span class="flex-item" style="background-color:<?php echo $cssb; ?>" id="soal"> </span>
            <span class="flex-biru">
                <div id="h_timer"></div>
            </span>
            <span class="flex-abu">Sisa Waktu</span>
        </div>
    </li>


    <div id="fontlembarsoal" class="fontlembarsoal">
        <span id="hurufsoal"> Ukuran font soal : <a id="jfontsize-m2" href="#"
                style="font-size:14px; text-decoration:none">&nbsp; A &nbsp;</a> <a id="jfontsize-d2" href="#"
                style="font-size:16px; text-decoration:none">&nbsp; A &nbsp;</a> <a id="jfontsize-p2" href="#"
                style="font-size:18px; text-decoration:none">&nbsp; A &nbsp;</a></span>
    </div>

    <script type="text/javascript" src="js/jquery-2.0.3.js"></script>
    <script type="text/javascript" src="js/jquery.countdownTimer.js"></script>
    <script>
        $(function () {
            $('#h_timer').countdowntimer({
                hours: <?php echo $xjam; ?>,
                minutes: <?php echo $xmnt; ?>,
                seconds: <?php echo $xdtk; ?>,
                size: "lg",
                timeUp: timeisUp
            });
        });
        function timeisUp() {
            alert("Waktu pengerjaan sudah habis");

            setTimeout(function () {
                window.location.href = $("a")[0].href;
            }, 2000);
            //Code to be executed when timer expires.
            window.location = "akhir.php";

        }


    </script>

    <script>
        (function () {
            var monitorUrl = 'monitor_event.php';
            var statusUrl = 'monitor_status.php';
            var configUrl = 'pengawasan_get_config.php';
            var lastEvent = '';
            var lastSentAt = 0;
            window.__cbtLocked = false;
            var hideTimer = null;
            var unloadEventSent = false;
            var lastSplitViewDetected = false;
            var monitorConfig = {
                monitor_tab_switch: true,
                monitor_app_switch: true,
                monitor_split_view: true,
                monitor_printscreen: true,
                monitor_key_violation: true,
                monitor_tab_close: true,
                monitor_rto: true,
                auto_lock_on_violation: true
            };

            // Load monitoring configuration
            $.getJSON(configUrl, function(resp) {
                if (resp && resp.ok && resp.config) {
                    monitorConfig = resp.config;
                }
            });

            function sendEvent(evt, extraData) {
                var now = Date.now();
                if (!(extraData && extraData.force) && evt === lastEvent && (now - lastSentAt) < 3000) {
                    return;
                }
                lastEvent = evt;
                lastSentAt = now;
                $.ajax({
                    url: monitorUrl,
                    type: 'POST',
                    data: $.extend({ event: evt }, extraData || {})
                });
            }

            function sendBeaconEvent(evt, extraData) {
                if (unloadEventSent) {
                    return;
                }
                unloadEventSent = true;
                if (!navigator.sendBeacon) {
                    sendEvent(evt, extraData);
                    return;
                }
                var form = new FormData();
                form.append('event', evt);
                if (extraData) {
                    Object.keys(extraData).forEach(function (key) {
                        form.append(key, extraData[key]);
                    });
                }
                // Use beacon on unload to ensure the lock event is sent.
                navigator.sendBeacon(monitorUrl, form);
            }

            function applyLockState(isLocked) {
                if (isLocked === window.__cbtLocked) {
                    return;
                }
                window.__cbtLocked = isLocked;
                if (isLocked) {
                    $('body').addClass('cbt-locked');
                    $('#picture :input').each(function () {
                        var $el = $(this);
                        if (!$el.prop('disabled')) {
                            $el.attr('data-cbt-lock-disabled', '1');
                            $el.prop('disabled', true);
                        }
                    });
                } else {
                    $('body').removeClass('cbt-locked');
                    $('#picture :input[data-cbt-lock-disabled="1"]').each(function () {
                        var $el = $(this);
                        $el.prop('disabled', false);
                        $el.removeAttr('data-cbt-lock-disabled');
                    });
                }
            }

            function checkLock() {
                $.getJSON(statusUrl, function (resp) {
                    if (!resp || resp.ok === false) {
                        return;
                    }
                    applyLockState(resp.locked);
                });
            }

            function clearHideTimer() {
                if (hideTimer) {
                    clearTimeout(hideTimer);
                    hideTimer = null;
                }
            }

            function startHideTimer() {
                clearHideTimer();
                hideTimer = setTimeout(function () {
                    if (document.hidden) {
                        var autoLock = monitorConfig.auto_lock_on_violation ? 1 : 0;
                        sendEvent('tab_hidden', { auto_lock: autoLock });
                    }
                }, 3000);
            }

            document.addEventListener('visibilitychange', function () {
                if (!monitorConfig.monitor_tab_switch) return;
                if (document.hidden) {
                    startHideTimer();
                } else {
                    clearHideTimer();
                    sendEvent('aman');
                }
            });
            window.addEventListener('blur', function () {
                if (!monitorConfig.monitor_tab_switch) return;
                startHideTimer();
            });
            window.addEventListener('focus', function () {
                if (!monitorConfig.monitor_tab_switch) return;
                clearHideTimer();
                sendEvent('aman');
            });
            window.addEventListener('pagehide', function () {
                if (!monitorConfig.monitor_tab_close) return;
                var autoLock = monitorConfig.auto_lock_on_violation ? 1 : 0;
                sendBeaconEvent('tab_close', { auto_lock: autoLock, reason: 'pagehide', force: 1 });
            });
            window.addEventListener('beforeunload', function () {
                if (!monitorConfig.monitor_tab_close) return;
                var autoLock = monitorConfig.auto_lock_on_violation ? 1 : 0;
                sendBeaconEvent('tab_close', { auto_lock: autoLock, reason: 'beforeunload', force: 1 });
            });
            document.addEventListener('keydown', function (e) {
                if (window.__cbtLocked) return;

                if (monitorConfig.monitor_printscreen && (e.key === 'PrintScreen' || e.keyCode === 44)) {
                    sendEvent('printscreen');
                }

                if (!monitorConfig.monitor_key_violation) return;
                var keyName = '';
                if (e.key === 'Control' || e.keyCode === 17 || e.ctrlKey) {
                    keyName = 'ctrl';
                } else if (e.key === 'Tab' || e.keyCode === 9) {
                    keyName = 'tab';
                } else if (e.key === 'Alt' || e.keyCode === 18 || e.altKey) {
                    keyName = 'alt';
                }

                if (keyName !== '') {
                    var autoLock = monitorConfig.auto_lock_on_violation ? 1 : 0;
                    sendEvent('key_violation', { key_pressed: keyName, auto_lock: autoLock });
                }
            });

            var rtoSince = null;
            function handleRto() {
                if (window.__cbtLocked) {
                    return;
                }
                var now = Date.now();
                if (rtoSince === null) {
                    rtoSince = now;
                }
                if (now - rtoSince >= 10000) {
                    var autoLock = monitorConfig.auto_lock_on_violation ? 1 : 0;
                    sendEvent('rto', { auto_lock: autoLock, reason: 'rto', force: 1 });
                    rtoSince = null;
                }
            }

            function clearRto() {
                rtoSince = null;
            }

            function isDesktopSplitView() {
                if (!window.screen || !window.screen.availWidth || !window.screen.availHeight) {
                    return false;
                }
                // Skip split-view check for small/mobile layouts.
                if (window.innerWidth < 900 || window.innerHeight < 500) {
                    return false;
                }
                var widthRatio = window.innerWidth / window.screen.availWidth;
                var heightRatio = window.innerHeight / window.screen.availHeight;
                return (widthRatio < 0.78 || heightRatio < 0.78);
            }

            function checkSplitView() {
                if (!monitorConfig.monitor_split_view) return;
                if (window.__cbtLocked || document.hidden) return;

                var detected = isDesktopSplitView();
                if (detected && !lastSplitViewDetected) {
                    var autoLock = monitorConfig.auto_lock_on_violation ? 1 : 0;
                    sendEvent('split_view', { auto_lock: autoLock, force: 1 });
                } else if (!detected && lastSplitViewDetected) {
                    sendEvent('aman');
                }
                lastSplitViewDetected = detected;
            }

            function pingInternet() {
                if (!monitorConfig.monitor_rto) return;
                if (window.__cbtLocked) {
                    return;
                }
                $.ajax({
                    url: 'monitor_ping.php',
                    type: 'GET',
                    dataType: 'json',
                    timeout: 4000
                }).done(function (resp) {
                    if (!resp || resp.ok === false) {
                        handleRto();
                        return;
                    }
                    if (resp.rto) {
                        handleRto();
                    } else {
                        clearRto();
                    }
                }).fail(function () {
                    handleRto();
                });
            }

            $(document).ready(function () {
                sendEvent('aman');
                checkLock();
                checkSplitView();
                setInterval(checkLock, 3000);
                setInterval(function () {
                    if (!document.hidden) {
                        sendEvent('aman');
                    }
                }, 30000);
                setInterval(pingInternet, 3000);
                setInterval(checkSplitView, 3000);
            });

            window.addEventListener('resize', function () {
                checkSplitView();
            });
        })();
    </script>

    <!-- load jquery -->
    <script type="text/javascript">
        $(document).ready(function () {
            var cbtStorageKey = 'cbt_last_soal_<?php echo $user; ?>_<?php echo $xtokenujian; ?>_<?php echo $xkodesoal; ?>';
            var initialSoal = 1;

            try {
                var savedSoal = localStorage.getItem(cbtStorageKey);
                if (savedSoal !== null) {
                    var parsed = parseInt(savedSoal, 10);
                    if (!isNaN(parsed) && parsed > 0) {
                        initialSoal = parsed;
                    }
                }
            } catch (e) {
                initialSoal = 1;
            }

            $("#soal").html(initialSoal);
            $.post("getsoal.php?kode=<?php echo $xkodesoal; ?>&assets=1", { pic: String(initialSoal) }, function (data) {
                $("#picture").html(data);
                $("#soal").html(initialSoal);
            });

            $("#picture").on("click", ".get_pic", function (e) {
                if (window.__cbtLocked) {
                    e.preventDefault();
                    return false;
                }
                var picture_id = $(this).attr('data-id');
                $("#picture").html("<div style=\"margin:50px auto;width:50px;\"><img src=\"loader.gif\" /></div>");
                $("#soal").html(picture_id);
                try {
                    localStorage.setItem(cbtStorageKey, String(picture_id));
                } catch (e) {
                }
                $.post("getsoal.php?assets=0", { pic: picture_id }, function (data) {
                    $("#picture").html(data);
                });
                return false;
            });

        });
    </script>

    <script src="js/jquery-scrolltofixed.js" type="text/javascript"></script>
    <script>
        $(document).ready(function () {

            // Dock the header to the top of the window when scrolled past the banner.
            // This is the default behavior.

            $('.header').scrollToFixed();


            // Dock the footer to the bottom of the page, but scroll up to reveal more
            // content if the page is scrolled far enough.

            $('.footer').scrollToFixed({
                bottom: 0,
                limit: $('.footer').offset().top
            });


            // Dock each summary as it arrives just below the docked header, pushing the
            // previous summary up the page.

            var summaries = $('.summary');
            summaries.each(function (i) {
                var summary = $(summaries[i]);
                var next = summaries[i + 1];

                summary.scrollToFixed({
                    marginTop: $('.header').outerHeight(true) + 10,
                    limit: function () {
                        var limit = 0;
                        if (next) {
                            limit = $(next).offset().top - $(this).outerHeight(true) - 10;
                        } else {
                            limit = $('.footer').offset().top - $(this).outerHeight(true) - 10;
                        }
                        return limit;
                    },
                    zIndex: 999
                });
            });
        });
    </script>

    <div id="picture">
        <!-- pictures will appear here -->
    </div>
</body>

<script src="js/jquery.cookie.js"></script>
<script src="js/common.js"></script>
<script src="js/main.js"></script>
<script src="js/cookieList.js"></script>
<script src="js/backend.js"></script>

<!-- Modal -->
<div class="modal fade" id="modal-form" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title page-label">Konfirmasi Tes</h1>
                </div>
                <div class="panel-body">
                    <div class="inner-content">
                        <div class="wysiwyg-content">
                            <p>
                                Terimakasih telah berpartisipasi dalam tes ini.<br>
                                <span id="waktuInfo1"></span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row" style="background-color:#fff">
                        <div class="col-xs-offset-3 col-xs-6">
                            <button type="button" class="btn btn-success" id="btnSelesai1"
                                    onclick="selesaiTes()">SELESAI</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">TIDAK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Minimum waktu pengerjaan dalam menit
    var minWaktuMenit = 30;

    // Waktu total ujian dalam detik (dari PHP)
    var totalWaktuDetik = <?php
    $totalJam = substr($s['XLamaUjian'], 0, 2);
    $totalMnt = substr($s['XLamaUjian'], 3, 2);
    $totalDtk = substr($s['XLamaUjian'], 6, 2);
    $totalDetik = ($totalJam * 3600) + ($totalMnt * 60) + $totalDtk;
    echo $totalDetik;
    ?>;

    // Sisa waktu saat ini dalam detik (dari timer PHP)
    var sisaWaktuAwal = (<?php echo $xjam; ?> * 3600) + (<?php echo $xmnt; ?> * 60) + <?php echo $xdtk; ?>;

    // Waktu yang sudah dipakai saat halaman load
    var waktuTerpakai = totalWaktuDetik - sisaWaktuAwal;
    var waktuTerpakaiAwal = waktuTerpakai;
    var waktuMulaiClient = Date.now();

    function getWaktuTerpakaiDetik() {
        var selisihDetik = Math.floor((Date.now() - waktuMulaiClient) / 1000);
        return waktuTerpakaiAwal + selisihDetik;
    }

    function getWaktuTerpakaiMenit() {
        return Math.floor(getWaktuTerpakaiDetik() / 60);
    }

    // Update waktu terpakai setiap detik
    setInterval(function () {
        waktuTerpakai = getWaktuTerpakaiDetik();
        updateModalStatus();
    }, 1000);

    function updateModalStatus() {
        var menitTerpakai = getWaktuTerpakaiMenit();
        var sisaMenit = minWaktuMenit - menitTerpakai;

        var infoText1 = document.getElementById('waktuInfo1');
        var infoText2 = document.getElementById('waktuInfo2');
        var infoTextLanjut = document.getElementById('waktuInfoLanjut');
        var infoTextR = document.getElementById('waktuInfoR');

        var checkContainer1 = document.getElementById('checkboxContainer1');
        var checkContainer2 = document.getElementById('checkboxContainer2');
        var checkContainerLanjut = document.getElementById('checkboxContainerLanjut');
        var checkContainerRagu = document.getElementById('checkboxContainerRagu');

        var btn1 = document.getElementById('btnSelesai1');
        var btn2 = document.getElementById('btnSelesai2');
        var btnLanjut = document.getElementById('btnSelesaiLanjut');
        var btnRagu = document.getElementById('btnRagu');

        if (menitTerpakai < minWaktuMenit) {
            var pesan = 'Anda belum bisa mengakhiri tes. Minimal pengerjaan ' + minWaktuMenit + ' menit.<br>Waktu pengerjaan Anda: ' + menitTerpakai + ' menit. Tunggu ' + sisaMenit + ' menit lagi.';
            if (infoText1) infoText1.innerHTML = pesan;
            if (infoText2) infoText2.innerHTML = pesan;
            if (infoTextLanjut) infoTextLanjut.innerHTML = pesan;
            if (infoTextR) infoTextR.innerHTML = pesan;

            if (checkContainer2) checkContainer2.style.display = 'none';
            if (checkContainerLanjut) checkContainerLanjut.style.display = 'none';
            if (checkContainerRagu) checkContainerRagu.style.display = 'none';
            
            if (btn1) btn1.disabled = false; // Always enabled for main modal
            if (btn2) btn2.disabled = true;
            if (btnLanjut) btnLanjut.disabled = true;
            if (btnRagu) btnRagu.disabled = true;
        } else {
            var pesan = 'Waktu pengerjaan Anda: ' + menitTerpakai + ' menit.<br>Silahkan centang kotak di bawah dan klik tombol SELESAI untuk mengakhiri test.';
            var pesanR = 'Waktu pengerjaan Anda: ' + menitTerpakai + ' menit.<br>Pastikan semua jawaban sudah terisi dan tidak ada yang RAGU-RAGU.';

            if (infoText1) infoText1.innerHTML = pesan;
            if (infoText2) infoText2.innerHTML = pesan;
            if (infoTextLanjut) infoTextLanjut.innerHTML = pesan;
            if (infoTextR) infoTextR.innerHTML = pesanR;

            if (checkContainer2) checkContainer2.style.display = 'block';
            if (checkContainerLanjut) checkContainerLanjut.style.display = 'block';
            if (checkContainerRagu) checkContainerRagu.style.display = 'block';
            
            btn1.disabled = false; 
            toggleSelesaiBtn2();
            toggleSelesaiBtnLanjut();
            toggleSelesaiBtnRagu();
        }
    }

    function toggleSelesaiBtn1() {
        var btn = document.getElementById('btnSelesai1');
        if (btn) btn.disabled = false;
    }

    function toggleSelesaiBtn2() {
        var checkbox = document.getElementById('confirmCheck2');
        var btn = document.getElementById('btnSelesai2');
        if (!checkbox || !btn) {
            return;
        }
        var menitTerpakai = getWaktuTerpakaiMenit();

        btn.disabled = !(checkbox.checked && menitTerpakai >= minWaktuMenit);
    }

    function toggleSelesaiBtnLanjut() {
        var checkbox = document.getElementById('confirmCheckLanjut');
        var btn = document.getElementById('btnSelesaiLanjut');

        if (!btn || !checkbox) return;

        if (checkbox.checked) {
            btn.disabled = false;
            btn.removeAttribute('disabled');
            btn.classList.remove('disabled'); // Ensure Bootstrap disabled class is removed
            
            // Force re-attach click event if needed (optional safety net)
            btn.style.pointerEvents = 'auto'; // Force pointer events
            
            console.log('Button SELESAI diaktifkan (enabled) & class disabled dihapus');
        } else {
            btn.disabled = true;
            btn.setAttribute('disabled', 'disabled');
            btn.classList.add('disabled');
            console.log('Button SELESAI dinonaktifkan (disabled)');
        }
    }

    function toggleSelesaiBtnRagu() {
        var checkbox = document.getElementById('confirmCheckRagu');
        var btn = document.getElementById('btnRagu');

        if (checkbox.checked) {
            btn.disabled = false;
        } else {
            btn.disabled = true;
        }
    }

    // Reset checkbox dan button saat modal dibuka
    $('#modal-form').on('show.bs.modal', function () {
        var btn1 = document.getElementById('btnSelesai1');
        if (btn1) btn1.disabled = false;
        updateModalStatus();
    });

    $('#myModal1').on('show.bs.modal', function () {
        var check2 = document.getElementById('confirmCheck2');
        var btn2 = document.getElementById('btnSelesai2');
        if (check2) check2.checked = false;
        if (btn2) btn2.disabled = true;
        updateModalStatus();
    });

    $('#myModal2').on('show.bs.modal', function () {
        var checkLanjut = document.getElementById('confirmCheckLanjut');
        var btnLanjut = document.getElementById('btnSelesaiLanjut');
        if (checkLanjut) checkLanjut.checked = false;
        if (btnLanjut) btnLanjut.disabled = true;
        
        // Tambahkan event listener untuk tombol SELESAI
        if (btnLanjut) {
            btnLanjut.onclick = function() {
                console.log('Tombol SELESAI diklik via addEventListener');
                selesaiTes();
                return false;
            };
        }
        
        updateModalStatus();
    });

    $('#myModalR').on('show.bs.modal', function () {
        var checkRagu = document.getElementById('confirmCheckRagu');
        var btnRagu = document.getElementById('btnRagu');
        if (checkRagu) checkRagu.checked = false;
        if (btnRagu) btnRagu.disabled = true;
        
        // Tambahkan event listener untuk tombol SELESAI
        if (btnRagu) {
            btnRagu.onclick = function() {
                console.log('Tombol SELESAI (Ragu) diklik via addEventListener');
                selesaiTes();
                return false;
            };
        }
        
        updateModalStatus();
    });

    // Fungsi untuk mengecek waktu minimum sebelum menampilkan modal selesai
    function cekWaktuMinimum(targetModal) {
        try {
            console.log('=== cekWaktuMinimum dipanggil ===');
            console.log('Target Modal:', targetModal);
            
            // Bypass time check for main modal
            if (targetModal === '#modal-form') {
                $(targetModal).modal('show');
                return;
            }

            // Verifikasi bahwa variabel waktu sudah didefinisikan
            if (typeof totalWaktuDetik === 'undefined') {
                console.error('ERROR: totalWaktuDetik tidak didefinisikan');
                alert('Error: Variabel waktu tidak dapat diakses. Refresh halaman.');
                return;
            }
            
            if (typeof getWaktuTerpakaiMenit === 'undefined') {
                console.error('ERROR: getWaktuTerpakaiMenit tidak didefinisikan');
                alert('Error: Fungsi waktu tidak dapat diakses. Refresh halaman.');
                return;
            }
            
            var menitTerpakai = getWaktuTerpakaiMenit();
            var sisaMenit = minWaktuMenit - menitTerpakai;

            console.log('Menit terpakai:', menitTerpakai);
            console.log('Minimum waktu:', minWaktuMenit);
            console.log('Sisa menit:', sisaMenit);
            console.log('Waktu cukup?:', menitTerpakai >= minWaktuMenit);

            if (menitTerpakai < minWaktuMenit) {
                // Tampilkan modal peringatan waktu
                console.log('>>> Menampilkan modal peringatan waktu');
                var infoText = document.getElementById('waktuInfoGetsoal');
                console.log('Element waktuInfoGetsoal ditemukan?:', infoText !== null);
                if (infoText) {
                    infoText.innerHTML = 'Waktu pengerjaan Anda: <strong>' + menitTerpakai + ' menit</strong>.<br>Tunggu <strong>' + sisaMenit + ' menit</strong> lagi.';
                }
                
                // Coba menggunakan jQuery jika tersedia
                if (typeof jQuery !== 'undefined' && typeof jQuery().modal === 'function') {
                    $('#myModalWaktu').modal('show');
                    console.log('Modal peringatan ditampilkan dengan jQuery');
                } else {
                    // Fallback: Tampilkan modal menggunakan Bootstrap 4+ atau manual show
                    var modalElement = document.getElementById('myModalWaktu');
                    if (modalElement) {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            // Bootstrap 5
                            var modal = new bootstrap.Modal(modalElement);
                            modal.show();
                            console.log('Modal peringatan ditampilkan dengan Bootstrap 5');
                        } else {
                            // Manual: gunakan class Bootstrap 3
                            modalElement.classList.add('in');
                            modalElement.style.display = 'block';
                            var backdrop = document.createElement('div');
                            backdrop.className = 'modal-backdrop fade in';
                            document.body.appendChild(backdrop);
                            console.log('Modal peringatan ditampilkan secara manual');
                        }
                    }
                }
            } else {
                // Waktu sudah cukup, tampilkan modal konfirmasi selesai
                console.log('>>> Menampilkan modal konfirmasi selesai');

                // If the caller is the final confirmation modal, finish the test instead of reopening the modal.
                // If the caller is the final confirmation modal, finish the test instead of reopening the modal.
                // NOTE: #myModal1 removed from here so it shows up for confirmation
                if (targetModal === '#modal-form') {
                    console.log('Waktu cukup, melewatkan modal dan menyelesaikan tes secara langsung');
                    selesaiTes();
                    return;
                }
                
                // Coba menggunakan jQuery jika tersedia
                if (typeof jQuery !== 'undefined' && typeof jQuery().modal === 'function') {
                    $(targetModal).modal('show');
                    console.log('Modal konfirmasi ditampilkan dengan jQuery');
                } else {
                    // Fallback: Tampilkan modal menggunakan Bootstrap 4+ atau manual show
                    var modalElement = document.querySelector(targetModal);
                    if (modalElement) {
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            // Bootstrap 5
                            var modal = new bootstrap.Modal(modalElement);
                            modal.show();
                            console.log('Modal konfirmasi ditampilkan dengan Bootstrap 5');
                        } else {
                            // Manual: gunakan class Bootstrap 3
                            modalElement.classList.add('in');
                            modalElement.style.display = 'block';
                            var backdrop = document.createElement('div');
                            backdrop.className = 'modal-backdrop fade in';
                            document.body.appendChild(backdrop);
                            console.log('Modal konfirmasi ditampilkan secara manual');
                        }
                    } else {
                        console.error('Modal element tidak ditemukan:', targetModal);
                    }
                }
            }
        } catch (e) {
            console.error('ERROR di cekWaktuMinimum:', e);
            alert('Error: ' + e.message);
        }
    }

    // Fungsi untuk menyelesaikan tes dan mengarahkan ke akhir.php
    function selesaiTes() {
        try {
            console.log('selesaiTes() dipanggil - Mengarahkan ke akhir.php');
            console.log('Current URL:', window.location.href);
            console.log('Target URL: akhir.php');
            
            // Verifikasi bahwa window.location tersedia
            if (typeof window.location === 'undefined') {
                console.error('ERROR: window.location tidak tersedia');
                alert('Error: Tidak bisa mengakses lokasi halaman');
                return;
            }
            
            // Gunakan timeout kecil untuk memastikan event terpicu sebelum navigasi
            setTimeout(function() {
                window.location.href = 'akhir.php';
            }, 100);
        } catch (e) {
            console.error('ERROR di selesaiTes():', e);
            alert('Error: ' + e.message);
        }
    }

    // Fungsi untuk menutup modal
    function tutupModal(modalId) {
        console.log('tutupModal() dipanggil dengan ID:', modalId);
        
        // Coba menggunakan jQuery jika tersedia
        if (typeof jQuery !== 'undefined' && typeof jQuery().modal === 'function') {
            $(modalId).modal('hide');
            console.log('Modal ditutup dengan jQuery');
        } else if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            // Bootstrap 5
            var modalElement = document.querySelector(modalId);
            if (modalElement) {
                var modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                    console.log('Modal ditutup dengan Bootstrap 5');
                }
            }
        } else {
            // Manual: hapus classes dan tampilan modal
            var modalElement = document.querySelector(modalId);
            if (modalElement) {
                modalElement.classList.remove('in');
                modalElement.style.display = 'none';
                // Hapus backdrop jika ada
                var backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                console.log('Modal ditutup secara manual');
            }
        }
    }
</script>


</head>
<style>
    #fontlembarsoal {
        margin-top: 3px;
        margin-left: 15px;
        margin-bottom: 0px;
        margin-right: 15px;
        background-color: #f0efef;
        font-size: 12px;
        font-weight: bold;
        height: 45px;
        left: 40px;
        padding-top: 10px;
        padding-bottom: 3px;
    }

    #tulisansoal {
        background-color: #fff;
        height: 90px;
        font-size: 18px;
        font-weight: bold;
        vertical-align: middle;
        top: 495px;
    }

    .tulisansoal {
        background-color: #fff;
        height: 90px;
        font-size: 18px;
        font-weight: bold;
        vertical-align: middle;
        top: 495px;
    }

    .nomersoal {
        top: 25px;
        width: 100px;
        background-color: #336898;
        color: #fff;
        height: 90px;
        font-size: 18px;
        font-weight: bold;
        vertical-align: middle;
    }

    #lembarsoal {
        margin-top: -8px;
        margin-left: 15px;
        margin-bottom: 2px;
        margin-right: 15px;
        background-color: #fff;
        height: 150%;
        border-radius: 30px;
        border-style: solid;
        border-color: #999;
    }

    #hurufsoal {
        padding-left: 30px;
        padding-top: 2px;
        padding-bottom: 2px;
    }

    #tampilkan {
        background-color: #336898;
        width: 150px;
        height: 50px;
        margin-right: 20px;
        margin-top: -10px;
        line-height: 20px;
        color: white;
        font-size: 22px;
        text-align: center;
        padding-left: 12px;
        padding-right: 12px;
        padding-top: 14px;
        padding-bottom: 14px;
        float: right;
    }

    #kotaksoal {
        width: 97%;
        margin: 0px auto;
        padding: 20px;
        border: solid;
        top: 30px;
        border-color: #CCC;

    }

    p {
        padding: 20px;
        font-size: 16px;
    }

    li {
        list-style: none;
        font-size: 18px;
    }

    #lembaran {
        padding: 20px;
        margin-left: 12px;
        margin-right: 12px;
        top: -30px;
        font-size: 12pt;
        background-color: #fff;
        border: solid;
        border-color: #ccc;
    }

    #lembaransoal {
        padding: 20px;
        font-size: 12pt;
        border: solid;
        border-color: #ccc;
    }

    .jawab {
        font-size: 10pt;
    }

    .jawaban {
        padding-bottom: 10px;
        font-size: 10pt;
        border: solid;
        border-color: #CCC;
    }

    .pilihanjawaban {
        font-size: 10pt;
        padding-bottom: 15px;
    }

    .noti-jawab {
        position: absolute;
        background-color: white;
        color: #999;
        padding: 4px;
        -webkit-border-radius: 30px;
        -moz-border-radius: 30px;
        border-radius: 30px;
        border-style: solid;
        border-color: #999;
        width: 30px;
        height: 30px;
        text-align: center;
    }
</style>

<style>
    .cc-selector input {
        margin-left: 0px;
        padding: 0;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        margin-top: -90px;
        top: -90px;
    }

    .A {
        background-image: url(images/A.png);
    }

    .B {
        background-image: url(images/B.png);
    }

    .C {
        background-image: url(images/C.png);
    }

    .D {
        background-image: url(images/D.png);
    }

    .E {
        background-image: url(images/E.png);
    }

    .piljwb {
        margin-left: 0;
        border-radius: 30px;
        border-style: solid;
        border-color: #999;
        list-style: none;
    }

    .cc-selector input:active+.drinkcard-cc {
        opacity: .9;
    }

    .cc-selector input:checked+.drinkcard-cc {
        background-image: url(images/pilih.png);
        -webkit-filter: none;
        -moz-filter: none;
        filter: none;
    }

    .drinkcard-cc {
        cursor: pointer;
        background-size: contain;
        background-repeat: no-repeat;
        display: inline-block;
        width: 38px;
        height: 28px;
        ;

    }

    .drinkcard-cc:hover {
        -webkit-filter: brightness(1.2) grayscale(.5) opacity(.9);
        -moz-filter: brightness(1.2) grayscale(.5) opacity(.9);
        filter: brightness(1.2) grayscale(.5) opacity(.9);
    }

    .main {
        margin-right: 15px;
        margin-top: 10px;
    }

    .content {
        padding: 20px;
        overflow: hidden;
    }

    .left {
        float: left;
        width: 680px;
    }

    .right {
        float: left;
        margin-left: 40px;
    }

    .summary {
        border: 1px solid #dddddd;
        overflow: hidden;
        margin-top: 20px;
        background-color: white;
    }

    .summary .caption {
        border-bottom: 1px solid #dddddd;
        background-color: #dddddd;
        font-size: 12pt;
        font-weight: bold;
        padding: 5px;
    }

    .summary.scroll-to-fixed-fixed {
        margin-top: 0px;
    }

    .summary.scroll-to-fixed-fixed .caption {
        color: red;
    }

    .contents {
        width: 150px;
        margin: 10px;
        font-size: 80%;
    }

    .kakisoal {
        margin-left: 15px;
        margin-bottom: 10px;
        margin-right: 15px;
        background-color: #fff;
        font-size: 12px;
        font-weight: bold;
        height: 70px;
        left: 140px;

    }

    .labelprev {
        display: block;
        padding: 10px 10px;
        font-size: 16px;
        margin: 5px auto;
        background-color: #999;
        border-radius: 2px;
        cursor: pointer;
        width: 200px;
        color: #FFF;

        &:hover {
            cursor: pointer;
        }
    }

    .labelnext {
        display: block;
        padding: 10px 10px;
        font-size: 16px;
        float: right;
        margin: 5px auto;
        background-color: #336898;
        border-radius: 2px;
        cursor: pointer;
        width: 200px;
        color: #FFF;

        &:hover {
            cursor: pointer;
        }
    }

    input[type="checkbox"] {
        position: relative;
        top: 3px;
        font-size: 18px;
        border: 2px solid black;
        width: 20px;
        height: 20px;
        margin: 0;
        padding: 0;
    }

    .flatRoundedCheckbox {
        width: 120px;
        height: 40px;
        margin: 20px 50px;
        position: relative;
    }

    .flatRoundedCheckbox div {
        width: 100%;
        height: 100%;
        background: #d3d3d3;
        border-radius: 50px;
        position: relative;
        top: -30px;
    }
</style>

<div class="modal fade" id="myModal1" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="panel-default">
                <div class="panel-heading">
                    <h1 class="panel-title page-label">Konfirmasi Tes</h1>
                </div>
                <div class="panel-body">
                    <div class="inner-content">
                        <div class="wysiwyg-content">
                            <p>
                                Terimakasih telah berpartisipasi dalam tes ini.<br>
                                <span id="waktuInfo2"></span>
                            </p>
                            <div id="checkboxContainer2" style="margin-top:10px;">
                                <label>
                                    <input type="checkbox" id="confirmCheck2" onchange="toggleSelesaiBtn2()">
                                    Saya yakin ingin mengakhiri tes ini
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row" style="background-color:#fff">
                        <div class="col-xs-offset-3 col-xs-6">
                            <button type="button" class="btn btn-success" id="btnSelesai2"
                                    >SELESAI</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">TIDAK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Peringatan Waktu Minimum -->
<div class="modal fade" id="myModalWaktu" role="dialog">
    <div class="modal-dialog">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title page-label">Warning!</h1>
            </div>
            <div class="panel-body">
                <div class="inner-content">
                    <div class="row" style="background-color:#fff">
                        <div class="col-xs-3">
                            <span><img src="images/alert.png" width="100px"></span>
                        </div>
                        <div class="col-xs-9">
                            <div class="wysiwyg-content">
                                <p>
                                    <strong>Minimum pengerjaan soal adalah 30 menit, jika anda keluar maka jawaban
                                        hilang.</strong><br><br>
                                    <span id="waktuInfoGetsoal"></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row" style="background-color:#fff">
                        <div class="col-xs-6 col-center" style="margin-left:25%">
                            <button type="button" class="btn btn-primary btn-block" data-dismiss="modal"
                                    onclick="tutupModal('#myModalWaktu');">Tutup</button>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Tes (tanpa soal ragu-ragu) -->
<div class="modal fade" id="myModal2" role="dialog">
    <div class="modal-dialog">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title page-label">Konfirmasi Tes</h1>
            </div>
            <div class="panel-body">
                <div class="inner-content">
                    <div class="row" style="background-color:#fff">
                        <div class="col-xs-3">
                            <span><img src="images/alert.png" width="150px"></span>
                        </div>
                        <div class="col-xs-9">
                            <div class="wysiwyg-content">
                                <p>
                                    Apakah anda yakin ingin mengakhiri tes?<br>
                                    Anda tidak akan bisa kembali ke soal jika sudah menekan tombol selesai.
                                </p>
                            </div>
                            <div id="checkboxContainerLanjut" style="margin-top:10px;">
                                <label class="assentcb-label">
                                    <input type="checkbox" id="confirmCheckLanjut" onchange="toggleSelesaiBtnLanjut()">
                                    Saya yakin ingin mengakhiri tes ini
                                </label>
                            </div>
                            <span id="waktuInfoLanjut"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row" style="background-color:#fff">
                    <div class="col-xs-6">
                        <button id="btnSelesaiLanjut" type="button" class="btn btn-success btn-block" disabled
                            onclick="console.log('Tombol SELESAI diklik'); selesaiTes();">SELESAI</button>
                    </div>
                    <div class="col-xs-6">
                        <button type="button" class="btn btn-danger btn-block" onclick="tutupModal('#myModal2');">TIDAK</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- Modal Peringatan Soal Ragu-Ragu -->
<div class="modal fade" id="myModalR" role="dialog">
    <div class="modal-dialog">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h1 class="panel-title page-label">Konfirmasi Tes</h1>
            </div>
            <div class="panel-body">
                <div class="inner-content">
                    <div class="row" style="background-color:#fff">
                        <div class="col-xs-3 glyphicon-left-panel">
                            <span><img src="images/alert.png" width="150px"></span>
                        </div>
                        <div class="col-xs-9">
                            <div class="wysiwyg-content">
                                <p>
                                    Terdapat soal yang bertanda RAGU-RAGU <br>
                                    Selesaikan lebih dulu Soal RAGU-RAGU.<br>
                                    <span id="waktuInfoR"></span>
                                </p>
                                <div id="checkboxContainerRagu" style="margin-top:10px;">
                                    <label class="assentcb-label">
                                        <input type="checkbox" id="confirmCheckRagu" onchange="toggleSelesaiBtnRagu()">
                                        Saya yakin ingin mengakhiri tes ini
                                    </label>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row" style="background-color:#fff">
                    <div class="col-xs-6">
                        <button id="btnRagu" type="button" class="btn btn-success btn-block" disabled
                            onclick="selesaiTes();">SELESAI</button>
                    </div>
                    <div class="col-xs-6">
                        <button type="button" class="btn btn-danger btn-block"
                            onclick="tutupModal('#myModalR');">LANJUT</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


<!-- Script untuk menonaktifkan klik kanan dan Ctrl+C/Ctrl+V -->
<script>
    // Disable right-click context menu
    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
        return false;
    });

    // Disable Ctrl+C, Ctrl+V, Ctrl+X, and other copy/paste shortcuts
    document.addEventListener('keydown', function (e) {
        // Check if Ctrl key is pressed
        if (e.ctrlKey) {
            // Block Ctrl+C (copy), Ctrl+V (paste), Ctrl+X (cut), Ctrl+A (select all)
            if (e.key === 'c' || e.key === 'C' ||
                // e.key === 'v' || e.key === 'V' || 
                e.key === 'x' || e.key === 'X' ||
                e.key === 'a' || e.key === 'A') {
                e.preventDefault();
                return false;
            }
        }
    });

    // Disable text selection on double click (optional extra protection)
    document.addEventListener('selectstart', function (e) {
        // Allow selection in input fields and textareas
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return true;
        }
        e.preventDefault();
        return false;
    });
</script>
