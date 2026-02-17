<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>

<?php include "config/server.php";
include "config/pengawasan.php";
include "ip.php";
mysql_query("SET NAMES utf8");
$sql = false;
$user = '';
if (isset($_COOKIE['PESERTA'])) {
    $user = $_COOKIE['PESERTA'];
}
if ($user === '') {
    if (function_exists('bee_log')) {
        bee_log('WARN', 'SIMPAN_NO_USER', 'Simpan jawaban gagal: cookie PESERTA tidak ada');
    }
    echo "failed!";
    exit;
}
//  setcookie('PESERTA',$user);
	  $sqluser = mysql_query("SELECT * FROM  `cbt_siswa` s LEFT JOIN cbt_ujian u ON (s.XKodeKelas = u.XKodeKelas or u.XKodeKelas = 'ALL') 
	  and (s.XKodeJurusan = u.XKodeJurusan or u.XKodeJurusan = 'ALL') WHERE XNomerUjian = 
	  '$user' and u.XStatusUjian = '1'");
    if (!$sqluser || mysql_num_rows($sqluser) < 1) {
        if (function_exists('bee_log')) {
            bee_log('ERROR', 'SIMPAN_NO_ACTIVE_EXAM', 'Simpan jawaban gagal: data ujian aktif tidak ditemukan', array(
                'user' => $user,
                'mysql_error' => mysql_error()
            ));
        }
        echo "failed!";
        exit;
    }
	  $s = mysql_fetch_array($sqluser);
//  $xkodesoal = "BAS1";//$s['XKodeSoal'];
//  $xtokenujian = "ZQIFG"; // $s['XTokenUjian'];
    $xkodesoal = $s['XKodeSoal'];
    $xtokenujian = $s['XTokenUjian'];

$savedIp = '';
if (!cbt_validate_single_ip_session($user, $xtokenujian, $xkodesoal, $user_ip, $savedIp)) {
    if (function_exists('bee_log')) {
        bee_log('WARN', 'MULTI_IP_BLOCK', 'Akses simpan jawaban ditolak karena IP berbeda', array(
            'user' => $user,
            'token' => $xtokenujian,
            'kodesoal' => $xkodesoal,
            'current_ip' => $user_ip,
            'saved_ip' => $savedIp
        ));
    }
    header('HTTP/1.1 403 Forbidden');
    echo "IP_MISMATCH";
    exit;
}

cbt_ensure_pengawasan_table();
$ceklock = mysql_num_rows(mysql_query("SELECT XIsLocked FROM cbt_pengawasan WHERE XNomerUjian = '$user' AND XTokenUjian = '$xtokenujian' AND XKodeSoal = '$xkodesoal' AND XIsLocked = '1'"));
if ($ceklock > 0) {
    header('HTTP/1.1 403 Forbidden');
    echo "LOCKED";
    exit;
}

  
  
if(isset($_REQUEST['soale'])){
$soalnja = $_REQUEST['soale'];
}
if (!isset($soalnja) || $soalnja === '') {
    if (function_exists('bee_log')) {
        bee_log('WARN', 'SIMPAN_NO_SOAL', 'Simpan jawaban gagal: parameter soale kosong', array(
            'user' => $user
        ));
    }
    echo "failed!";
    exit;
}
 $cek = mysql_num_rows(mysql_query("select * from cbt_jawaban where Urut='$soalnja' and XKodeSoal = '$xkodesoal' and XUserJawab = '$user'"));
 if($cek>0){
// $sql = mysql_query("update cbt_jawaban set XJawaban = '$_REQUEST[nama]' where XNomerSoal='$_REQUEST[soale]' and XKodeSoal = '$xkodesoal' and XUserJawab = '$user'");
$tgl = date("Y-m-d");
$jam = date("H:i:s");
	
$nomber = '';
$jawab_esai = '';
if(isset($_REQUEST['nama'])){
$nomber = str_replace(" ","",$_REQUEST['nama']);
$jawab_esai = str_replace("  ","",mysql_real_escape_string($_REQUEST['nama']));
}

$sqljenis = mysql_query("select * from cbt_jawaban where Urut='$soalnja' and XKodeSoal = '$xkodesoal' and XUserJawab = '$user' and XTokenUjian = '$xtokenujian'");
if (!$sqljenis || mysql_num_rows($sqljenis) < 1) {
    if (function_exists('bee_log')) {
        bee_log('ERROR', 'SIMPAN_JENIS_QUERY_FAILED', 'Simpan jawaban gagal: data jawaban tidak ditemukan', array(
            'user' => $user,
            'kodesoal' => $xkodesoal,
            'token' => $xtokenujian,
            'soal' => $soalnja,
            'mysql_error' => mysql_error()
        ));
    }
    echo "failed!";
    exit;
}
$uji = mysql_fetch_array($sqljenis);
$jenis = $uji['XJenisSoal'];
$tkn = $uji['XTokenUjian'];
$knc = $uji['XKunciJawaban'];


if($jenis==2){
		if(!$jawab_esai==""){
		$sql = mysql_query("update cbt_jawaban set XJawabanEsai = '$jawab_esai', XTglJawab = '$tgl',XJamJawab = '$jam',Campur = '$tkn',XTemp = '$soalnja'
		where Urut='$soalnja' and XKodeSoal = '$xkodesoal' and XUserJawab = '$user'  and XTokenUjian = '$xtokenujian'");
		}
} elseif($jenis==1){
    if (!preg_match('/^[A-Za-z0-9_]{1,5}$/', $nomber)) {
        if (function_exists('bee_log')) {
            bee_log('WARN', 'SIMPAN_INVALID_OPTION', 'Simpan jawaban gagal: opsi pilihan tidak valid', array(
                'user' => $user,
                'soal' => $soalnja,
                'nama_raw' => isset($_REQUEST['nama']) ? $_REQUEST['nama'] : ''
            ));
        }
        echo "failed!";
        exit;
    }
    $ambiljawaban = "X$nomber";
    $sqljwb = mysql_query("select *,$ambiljawaban as hasile from cbt_jawaban where Urut='$soalnja' and XKodeSoal = '$xkodesoal' and XUserJawab = '$user' and XTokenUjian = '$xtokenujian'");
    if (!$sqljwb || mysql_num_rows($sqljwb) < 1) {
        if (function_exists('bee_log')) {
            bee_log('ERROR', 'SIMPAN_FETCH_JAWAB_FAILED', 'Simpan jawaban gagal: query nilai jawaban tidak valid', array(
                'user' => $user,
                'soal' => $soalnja,
                'field' => $ambiljawaban,
                'mysql_error' => mysql_error()
            ));
        }
        echo "failed!";
        exit;
    }
    $uj = mysql_fetch_array($sqljwb);
    $jwb = isset($uj['hasile']) ? $uj['hasile'] : '';
	if($jwb==$knc){$nil = 1;} else {$nil=0;}
	$sql = mysql_query("update cbt_jawaban set XJawaban = '$nomber',XKodeJawab = '$ambiljawaban',XNilaiJawab = '$jwb', XNilai='$nil', XTglJawab = '$tgl',XJamJawab = '$jam', 
	Campur = '$tkn'
	where Urut='$soalnja' and XKodeSoal = '$xkodesoal' and XUserJawab = '$user'  and XTokenUjian = '$xtokenujian'");
}

if(isset($jam)){
$sql2 = mysql_query("Update cbt_siswa_ujian set XLastUpdate = '$jam'
where XNomerUjian = '$user'
and XStatusUjian = '1'
and XTokenUjian = '$xtokenujian'
and XKodeSoal = '$xkodesoal'
and XGetIP = '$user_ip'");
}

 
	 } 

    if($sql){
     echo "success!";
   	} else {
    if (function_exists('bee_log')) {
        bee_log('ERROR', 'SIMPAN_UPDATE_FAILED', 'Simpan jawaban gagal saat update', array(
            'user' => $user,
            'kodesoal' => $xkodesoal,
            'token' => $xtokenujian,
            'soal' => isset($soalnja) ? $soalnja : '',
            'mysql_error' => mysql_error()
        ));
    }
    echo "failed!";
  	}
 
?>  
</body>
</html>
