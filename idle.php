<?php
require __DIR__ . '/config/server.php';
// ===============================
// Status Ujian XStatusUjian = 1 Aktif
// Status Ujian XStatusUjian = 0 BelumAktif
// Status Ujian XStatusUjian = 9 Selesai

$tgl = date("H:i:s");
$user = isset($_COOKIE['PESERTA']) ? $_COOKIE['PESERTA'] : '';
$s = null;
$s2 = null;

if ($user !== '') {
    db_query(
        $db,
        "UPDATE cbt_siswa_ujian SET XLastUpdate = :tgl WHERE XNomerUjian = :user AND XStatusUjian = '1'",
        array(':tgl' => $tgl, ':user' => $user)
    );

    //cek data siswa ujian
    $sqluser = db_query(
        $db,
        "SELECT * FROM `cbt_siswa` s LEFT JOIN cbt_ujian u ON s.XKodeKelas = u.XKodeKelas WHERE XNomerUjian = :user AND u.XStatusUjian = '1'",
        array(':user' => $user)
    );
    $s = db_fetch_one($sqluser);
}

$val_siswa = $s ? $s['XNamaSiswa'] : '';
$xkodesoal = $s ? $s['XKodeSoal'] : '';
$xkodemapel = $s ? $s['XKodeMapel'] : '';
$xjumlahsoal = $s ? $s['XJumSoal'] : '';
$xtokenujian = $s ? $s['XTokenUjian'] : '';

if ($user !== '' && $xkodesoal !== '' && $xtokenujian !== '') {
    $sqlceksiswa = db_query(
        $db,
        "SELECT * FROM cbt_siswa_ujian WHERE XNomerUjian = :user AND XKodeSoal = :kodesoal AND XTokenUjian = :token",
        array(':user' => $user, ':kodesoal' => $xkodesoal, ':token' => $xtokenujian)
    );
    $s2 = db_fetch_one($sqlceksiswa);
}

$xjumlahjam = $s2 ? $s2['XLamaUjian'] : '';
$xjam = $xjumlahjam !== '' ? substr($xjumlahjam, 0, 2) : '';
$xmnt = $xjumlahjam !== '' ? substr($xjumlahjam, 3, 2) : '';
$xdtk = $xjumlahjam !== '' ? substr($xjumlahjam, 6, 2) : '';
$xstatusujian = $s2 ? $s2['XStatusUjian'] : '';
// echo "$xstatusujian<br>";

$jatahjam = (int) $xjam;
$jatahmnt = (int) $xmnt;
$menit = $jatahmnt + ($jatahjam * 60);
$jamterakhirlogout = '';

if ($s2 && isset($s2['XMulaiUjian'])) {
    $timestamp = strtotime($s2['XMulaiUjian']) + $menit * 60;
    $tjam = date('H', $timestamp);
    $tmnt = date('i', $timestamp);
    $tdtk = date('s', $timestamp);
    $jamterakhirlogout = "$tjam:$tmnt:$tdtk";
}
?>
<?php

if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $user = trim($parts[0]);
        setcookie($user, '', time()-1000);
        setcookie($user, '', time()-1000, '/');
		setcookie("user", '', time()-1000);
		setcookie("apl", '', time()-1000);		
    	unset($_COOKIE['user']);
    	setcookie('user', '', time() - 3600, '/'); // empty value and old timestamp

    }
}
header('location:index.php');

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>
<body>


<div class="modal" id="myModal" role="dialog">
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
                                Silahkan klik tombol LOGOUT untuk mengakhiri test.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row"  style="background-color:#fff">
                        <div class="col-xs-offset-3 col-xs-6">
                            <button type="submit" class="btn btn-success btn-block" data-dismiss="modal">LOGOUTZ</button>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>



</body>
</html>
