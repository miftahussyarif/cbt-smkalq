<?php 
include "../../config/server.php";	

$requestIdTes = isset($_REQUEST['txt_idtes']) ? (int) $_REQUEST['txt_idtes'] : 0;
$requestKodeSoal = isset($_REQUEST['txt_kodesoal']) ? $_REQUEST['txt_kodesoal'] : '';
$requestKodeUjian = isset($_REQUEST['txt_ujian']) ? $_REQUEST['txt_ujian'] : '';
$requestSemester = isset($_REQUEST['txt_semester']) ? $_REQUEST['txt_semester'] : '';
$requestToken = isset($_REQUEST['txt_token']) ? $_REQUEST['txt_token'] : '';
$requestSesi = isset($_REQUEST['txt_sesi']) ? $_REQUEST['txt_sesi'] : '';
$requestDurasi = isset($_REQUEST['txt_durasi']) ? $_REQUEST['txt_durasi'] : '';
$requestTelat = isset($_REQUEST['txt_telat']) ? $_REQUEST['txt_telat'] : '';
$requestWaktu = isset($_REQUEST['txt_waktu']) ? $_REQUEST['txt_waktu'] : '';

bee_log('INFO', 'TEST_SCHEDULE_REQUEST', 'Permintaan rilis token/jadwal ujian', array(
    'idtes' => $requestIdTes,
    'kodesoal' => $requestKodeSoal,
    'kodeujian' => $requestKodeUjian,
    'semester' => $requestSemester,
    'token' => $requestToken,
    'sesi' => $requestSesi,
    'durasi' => $requestDurasi,
    'telat' => $requestTelat
));

$tgl = substr($requestWaktu, 0, 10);
$jam = substr($requestWaktu, 11, 5);
$jam = "$jam:00";

//=========================
// Tentukan Durasi Ujian
//=========================

$minutes = $requestDurasi;
$d = floor ($minutes / 1440);
$h = floor (($minutes - $d * 1440) / 60);
$m = $minutes - ($d * 1440) - ($h * 60);

$hi = strlen($h);
$mi = strlen($m);
if($hi<2){$hi = "0".$h;}else{$hi=$h;}
if($mi<2){$mi = "0".$m;}else{$mi=$m;}
$jame = "$hi:$mi:00";
//


//=========================
// Tentukan Batas Keterlambatan Masuk Ujian
//=========================
$xlambat = $requestTelat;
if($xlambat==""){$xlambat = 0;}
elseif($xlambat>0){$xlambat = 1;}

if($xlambat==0){
$minutest = $requestDurasi;
}else{
$minutest = $requestTelat;
}

$dt = floor ($minutest / 1440);
$ht = floor (($minutest - $dt * 1440) / 60);
$mt = $minutest - ($dt * 1440) - ($ht * 60);

$hit = strlen($ht);
$mit = strlen($mt);
if($hit<2){$hit = "0".$ht;}else{$hit=$ht;}
if($mit<2){$mit = "0".$mt;}else{$mit=$mt;}
$jamet = "$hit:$mit:00";

//$telatujian = date('H:i:s',strtotime('+$hit hour +$mit minutes +00 seconds',strtotime($jamujiane)));
  $xjumlahjam = $jamet;
  $xjam = substr($xjumlahjam,0,2);
  $xmnt = substr($xjumlahjam,3,2);
  $xdtk = substr($xjumlahjam,6,2);
  
$jatahjam = $xjam;
$jatahmnt = $xmnt;
$menit = $jatahmnt+($jatahjam*60);
$timestamp = strtotime($jam) + $menit*60;
$tjam = date('H', $timestamp);
$tmnt = date('i', $timestamp);
$tdtk = date('s', $timestamp);


$telatujian = "$tjam:$tmnt:$tdtk";


//=========================
// Ambil Paket Soal
//=========================
// Jika idtes ada, itu ujian ID (dari edit_tes.php), perlu query ujian dulu untuk ambil XKodeSoal
if ($requestIdTes > 0) {
    // Query ujian untuk ambil XKodeSoal
    $ujianQuery = mysql_query("select XKodeSoal from cbt_ujian where Urut = '$requestIdTes'");
    if ($ujianQuery && mysql_num_rows($ujianQuery) > 0) {
        $ujianRow = mysql_fetch_array($ujianQuery);
        $requestKodeSoal = $ujianRow['XKodeSoal'];
    }
    $wherePaket = "XStatusSoal ='Y' and XKodeSoal = '$requestKodeSoal'";
} else {
    $wherePaket = "XStatusSoal ='Y' and XKodeSoal = '$requestKodeSoal'";
}

$loop = mysql_query("select * from cbt_paketsoal where $wherePaket");
$msg = array();
$msgClass = "success";
$jumlahDiproses = 0;
$jumlahSukses = 0;

if (!$loop) {
    bee_log('ERROR', 'TEST_SCHEDULE_QUERY_FAILED', 'Gagal membaca data paket soal', array(
        'where' => $wherePaket,
        'db_error' => mysql_error()
    ));
    echo "<div class='alert alert-danger alert-dismissable' id='ndelik'>Gagal membaca data paket soal.</div>";
    exit;
}

while($s = mysql_fetch_array($loop)){
$jumlahDiproses++;
$val_jumsoal = $s['XJumSoal'];
$val_pilganda = $s['XPilGanda'];
$val_esai = $s['XEsai'];

	// Cek apakah ujian untuk paket+sesi+mapel+kelas+jurusan+semester ini sudah ada
	$sqlubah = mysql_num_rows(mysql_query("select * from cbt_ujian where XKodeSoal = '{$s['XKodeSoal']}' and XKodeUjian = '$requestKodeUjian' and XSemester = '$requestSemester' and XKodeKelas = '{$s['XKodeKelas']}' and XKodeJurusan = '{$s['XKodeJurusan']}' and XKodeMapel = '{$s['XKodeMapel']}' and XSetId = '$_COOKIE[beetahun]' and XSesi = '$requestSesi' "));

	$cekNilai = mysql_num_rows(mysql_query("select 1 from cbt_nilai where XKodeKelas = '{$s['XKodeKelas']}' and XKodeMapel = '{$s['XKodeMapel']}' and XKodeUjian = '$requestKodeUjian' and XSemester = '$requestSemester' and XSetId = '$_COOKIE[beetahun]' limit 1"));
	if($cekNilai>0){
        $msgClass = "danger";
        $msg[] = "Data hasil ujian lama untuk Mapel <b>{$s['XKodeMapel']}</b> Kelas <b>{$s['XKodeKelas']}</b> Jenis Ujian <b>$requestKodeUjian</b> masih ada. Hapus dulu melalui menu <b>Status Tes</b> (tab Selesai) dengan tombol <b>Hapus Data</b>.";
        bee_log('WARN', 'TEST_SCHEDULE_BLOCKED_BY_SCORE', 'Rilis token ditolak karena nilai lama masih ada', array(
            'idtes' => $s['Urut'],
            'kodesoal' => $s['XKodeSoal'],
            'kodeujian' => $requestKodeUjian,
            'semester' => $requestSemester,
            'kelas' => $s['XKodeKelas'],
            'jurusan' => $s['XKodeJurusan'],
            'mapel' => $s['XKodeMapel']
        ));
		continue;
	}
	
	/*
	if($sqlubah>0){
	$sqlubah2 = mysql_query("update cbt_ujian set XStatusUjian = '0' where XKodeSoal = '$_REQUEST[txt_kodesoal]' and  XKodeUjian = '$_REQUEST[txt_ujian]' and XSemester =  
	'$_REQUEST[txt_semester]' and XKodeKelas = '$s[XKodeKelas]' and XKodeJurusan = '$s[XKodeJurusan]' and XKodeMapel = '$s[XKodeMapel]' and XSetId = '$_COOKIE[beetahun]'");
	}
	*/
	
//=========================
// Ambil Bank Soal
//=========================

$jumsoal = mysql_num_rows(mysql_query("select * from cbt_soal where  XKodeSoal = '$requestKodeSoal'"));
$val_banksoal =  "$jumsoal"; 


if($val_jumsoal==0){$ambilsoal = $val_banksoal;} 
elseif($val_jumsoal>$val_banksoal){$ambilsoal = $val_banksoal;} 
else {$ambilsoal = $val_jumsoal;}
//  $sqlubah = mysql_query("insert into cbt_sampah (anu) values ('$_REQUEST[txt_ujian]')");
//================================
//
//=================================
$sqls = mysql_query("select u.*,m.*,u.Urut as Urutan,u.XKodeKelas as kokel from cbt_ujian u left join cbt_mapel m on m.XKodeMapel = u.XKodeMapel 
left join cbt_paketsoal p on p.XKodeSoal = u.XKodeSoal where u.XStatusUjian='1'");
								while($ss = mysql_fetch_array($sqls)){ 
$time1 = "$ss[XJamUjian]";
$time2 = "$ss[XLamaUjian]";

$secs = strtotime($time2)-strtotime("00:00:00");
$jamhabis = date("H:i:s",strtotime($time1)+$secs);	
$sekarang = date("H:i:s");	
$tglsekarang = date("Y-m-d");	
$tglujian = "$ss[XTglUjian]";	
		}
	
$sqlcek = mysql_num_rows(mysql_query("select * from cbt_ujian where XTokenUjian = '$requestToken' and XKodeSoal <> '{$s['XKodeSoal']}'"));
	if($sqlcek>0){
        $msgClass = "danger";
        $msg[] = "Simpan Data Gagal Token sudah ada di paket soal lain.";
        bee_log('WARN', 'TEST_SCHEDULE_TOKEN_EXISTS', 'Rilis token ditolak karena token sudah dipakai di paket lain', array(
            'idtes' => $s['Urut'],
            'token' => $requestToken
        ));
	} else {
        // Cek apakah ujian untuk kombinasi paket+sesi ini sudah ada
        $urutUjian = 0;
        $cekUjianExisting = mysql_num_rows(mysql_query("select 1 from cbt_ujian where XKodeSoal = '{$s['XKodeSoal']}' and XKodeUjian = '$requestKodeUjian' and XSemester = '$requestSemester' and XKodeKelas = '{$s['XKodeKelas']}' and XKodeJurusan = '{$s['XKodeJurusan']}' and XKodeMapel = '{$s['XKodeMapel']}' and XSetId = '$_COOKIE[beetahun]' and XSesi = '$requestSesi' limit 1"));
        
        if ($cekUjianExisting > 0) {
            $ambilUrut = mysql_query("select Urut from cbt_ujian where XKodeSoal = '{$s['XKodeSoal']}' and XKodeUjian = '$requestKodeUjian' and XSemester = '$requestSemester' and XKodeKelas = '{$s['XKodeKelas']}' and XKodeJurusan = '{$s['XKodeJurusan']}' and XKodeMapel = '{$s['XKodeMapel']}' and XSetId = '$_COOKIE[beetahun]' and XSesi = '$requestSesi' limit 1");
            if ($ambilUrut && mysql_num_rows($ambilUrut) > 0) {
                $rowUrut = mysql_fetch_array($ambilUrut);
                $urutUjian = (int)$rowUrut['Urut'];
            }

            // UPDATE ujian yang sudah ada
            $ujianUpdateQuery = mysql_query("update cbt_ujian set 
                XTokenUjian='$requestToken', XTglUjian='$tgl', XJamUjian='$jam',
                XLamaUjian='$jame', XBatasMasuk='$telatujian', XJumSoal='$ambilsoal',
                XStatusUjian='1', XGuru='{$s['XGuru']}', XSetId='$_COOKIE[beetahun]',
                XPilGanda='$val_pilganda', XEsai='$val_esai', XLambat='$xlambat'
                where XKodeSoal = '{$s['XKodeSoal']}' and XKodeUjian = '$requestKodeUjian' and XSemester = '$requestSemester' 
                and XKodeKelas = '{$s['XKodeKelas']}' and XKodeJurusan = '{$s['XKodeJurusan']}' 
                and XKodeMapel = '{$s['XKodeMapel']}' and XSetId = '$_COOKIE[beetahun]' and XSesi = '$requestSesi'");
            $sqlinsert = $ujianUpdateQuery;
            $aksiLog = 'TEST_EDIT_SUCCESS';
            $pesanLog = 'Edit tes/rilis token berhasil disimpan';
        } else {
            // INSERT ujian baru untuk sesi ini
            $sqlinsert = mysql_query("insert into cbt_ujian 						  
                (XKodeKelas,XKodeUjian,XSemester,XKodeJurusan,XJumPilihan,XAcakSoal,XKodeMapel,
                 XTokenUjian,XTglUjian,XJamUjian,XLamaUjian,XBatasMasuk,XJumSoal
                ,XKodeSoal,XStatusUjian,XGuru,XSetId,XSesi,XPilGanda,XEsai,XLambat) values 		
                ('{$s['XKodeKelas']}','$requestKodeUjian','$requestSemester','{$s['XKodeJurusan']}','{$s['XJumPilihan']}',
                '{$s['XAcakSoal']}','{$s['XKodeMapel']}','$requestToken','$tgl','$jam','$jame','$telatujian','$ambilsoal',
                '{$s['XKodeSoal']}','1','{$s['XGuru']}','$_COOKIE[beetahun]','$requestSesi','$val_pilganda','$val_esai','$xlambat')");
            if ($sqlinsert) {
                $urutUjian = (int) mysql_insert_id();
            }
            $aksiLog = 'TEST_CREATE_SUCCESS';
            $pesanLog = 'Buat tes/rilis token berhasil disimpan';
        }

        if ($sqlinsert) {
            $jumlahSukses++;
            bee_log('INFO', $aksiLog, $pesanLog, array(
                'idtes' => $urutUjian,
                'kodesoal' => $s['XKodeSoal'],
                'kodeujian' => $requestKodeUjian,
                'semester' => $requestSemester,
                'token' => $requestToken
            ));
        } else {
            $msgClass = "danger";
            $msg[] = "Simpan data gagal untuk ID Tes <b>$urutUjian</b>.";
            bee_log('ERROR', 'TEST_SCHEDULE_SAVE_FAILED', 'Gagal menyimpan jadwal/rilis token', array(
                'idtes' => $urutUjian,
                'kodesoal' => $s['XKodeSoal'],
                'kodeujian' => $requestKodeUjian,
                'semester' => $requestSemester,
                'token' => $requestToken,
                'db_error' => mysql_error()
            ));
        }

	}
}

$sumber = $requestIdTes > 0 ? 'edit_tes' : 'set_jadwal';
if ($jumlahDiproses < 1) {
    $msgClass = "danger";
    $msg[] = "Data paket soal tidak ditemukan atau belum aktif.";
    bee_log('WARN', 'TEST_SCHEDULE_EMPTY', 'Rilis token tidak memproses data apa pun', array(
        'sumber' => $sumber,
        'idtes' => $requestIdTes,
        'kodesoal' => $requestKodeSoal
    ));
} elseif ($jumlahSukses > 0 && empty($msg)) {
    $msg[] = "Simpan Data Sukses.";
}

if (empty($msg)) {
    $msgClass = "danger";
    $msg[] = "Simpan Data Gagal.";
}

echo "<div class='alert alert-$msgClass alert-dismissable' id='ndelik'>" . implode("<br>", $msg) . "</div>";

?>
                              
