<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>

<?php include "config/server.php";
include "config/pengawasan.php";
$db->exec("SET NAMES utf8");
$user = '';
if (isset($_COOKIE['PESERTA'])) {
    $user = $_COOKIE['PESERTA'];
}
//  setcookie('PESERTA',$user);
  $sqluser = db_query(
      $db,
      "SELECT * FROM  `cbt_siswa` s LEFT JOIN cbt_ujian u ON (s.XKodeKelas = u.XKodeKelas or u.XKodeKelas = 'ALL') 
  and (s.XKodeJurusan = u.XKodeJurusan or u.XKodeJurusan = 'ALL') WHERE s.XNomerUjian = 
  :user and u.XStatusUjian = '1' limit 1",
      array('user' => $user)
  );
  $s = db_fetch_one($sqluser);
//  $xkodesoal = "BAS1";//$s['XKodeSoal'];
//  $xtokenujian = "ZQIFG"; // $s['XTokenUjian'];
    $xkodesoal = $s ? $s['XKodeSoal'] : '';
    $xtokenujian = $s ? $s['XTokenUjian'] : '';

cbt_ensure_pengawasan_table();
$ceklock = db_query(
    $db,
    "SELECT count(1) as total FROM cbt_pengawasan WHERE XNomerUjian = :user AND XTokenUjian = :token AND XKodeSoal = :kodesoal AND XIsLocked = '1'",
    array('user' => $user, 'token' => $xtokenujian, 'kodesoal' => $xkodesoal)
);
$ceklock_count = (int) db_fetch_value($ceklock);
if ($ceklock_count > 0) {
    header('HTTP/1.1 403 Forbidden');
    echo "LOCKED";
    exit;
}

  
  
$soalnja = isset($_REQUEST['soale']) ? $_REQUEST['soale'] : '';
if ($user === '' || $soalnja === '' || $xkodesoal === '') {
    echo "failed!";
    exit;
}
 $updated = false;
 $cek = db_query(
     $db,
     "select count(1) as total from cbt_jawaban where Urut = :urut and XKodeSoal = :kodesoal and XUserJawab = :user",
     array('urut' => $soalnja, 'kodesoal' => $xkodesoal, 'user' => $user)
 );
 $cek_count = (int) db_fetch_value($cek);
 if ($cek_count > 0) {
$tgl = date("Y-m-d");
$jam = date("H:i:s");

if (isset($_REQUEST['nama'])) {
    $nomber = str_replace(" ", "", $_REQUEST['nama']);
    $jawab_esai = str_replace("  ", "", $_REQUEST['nama']);
}
$nomber = isset($nomber) ? $nomber : '';
$jawab_esai = isset($jawab_esai) ? $jawab_esai : '';
$opsi = strtoupper(trim($nomber));
$allowed_opsi = array('A', 'B', 'C', 'D', 'E');
$ambiljawaban = in_array($opsi, $allowed_opsi, true) ? 'X' . $opsi : 'XA';

$sqljwb = db_query(
    $db,
    "select *, {$ambiljawaban} as hasile from cbt_jawaban where Urut = :urut and XKodeSoal = :kodesoal and XUserJawab = :user and XTokenUjian = :token",
    array(
        'urut' => $soalnja,
        'kodesoal' => $xkodesoal,
        'user' => $user,
        'token' => $xtokenujian,
    )
);
$uj = db_fetch_one($sqljwb);
if (!$uj) {
    echo "failed!";
    exit;
}
$jwb = $uj['hasile'];
$tkn = $uj['XTokenUjian'];
$knc = $uj['XKunciJawaban'];

$sqljenis = db_query(
    $db,
    "select XJenisSoal from cbt_jawaban where Urut = :urut and XKodeSoal = :kodesoal and XUserJawab = :user and XTokenUjian = :token",
    array(
        'urut' => $soalnja,
        'kodesoal' => $xkodesoal,
        'user' => $user,
        'token' => $xtokenujian,
    )
);
$uji = db_fetch_one($sqljenis);
$jenis = $uji ? $uji['XJenisSoal'] : null;

if ($jenis == 2) {
    if ($jawab_esai !== '') {
        db_query(
            $db,
            "update cbt_jawaban set XJawabanEsai = :jawab_esai, XTglJawab = :tgl, XJamJawab = :jam, Campur = :tkn, XTemp = :urut
            where Urut = :urut and XKodeSoal = :kodesoal and XUserJawab = :user and XTokenUjian = :token",
            array(
                'jawab_esai' => $jawab_esai,
                'tgl' => $tgl,
                'jam' => $jam,
                'tkn' => $tkn,
                'urut' => $soalnja,
                'kodesoal' => $xkodesoal,
                'user' => $user,
                'token' => $xtokenujian,
            )
        );
        $updated = true;
    }
} elseif ($jenis == 1) {
    if ($jwb == $knc) {
        $nil = 1;
    } else {
        $nil = 0;
    }
    db_query(
        $db,
        "update cbt_jawaban set XJawaban = :jawaban, XKodeJawab = :kodejawab, XNilaiJawab = :nilaijawab, XNilai = :nilai, XTglJawab = :tgl, XJamJawab = :jam, Campur = :tkn
        where Urut = :urut and XKodeSoal = :kodesoal and XUserJawab = :user and XTokenUjian = :token",
        array(
            'jawaban' => $nomber,
            'kodejawab' => $ambiljawaban,
            'nilaijawab' => $jwb,
            'nilai' => $nil,
            'tgl' => $tgl,
            'jam' => $jam,
            'tkn' => $tkn,
            'urut' => $soalnja,
            'kodesoal' => $xkodesoal,
            'user' => $user,
            'token' => $xtokenujian,
        )
    );
    $updated = true;
}

if (isset($jam)) {
    db_query(
        $db,
        "Update cbt_siswa_ujian set XLastUpdate = :jam where XNomerUjian = :user and XStatusUjian = '1'",
        array('jam' => $jam, 'user' => $user)
    );
}

 
 } 

    if ($updated) {
        echo "success!";
    } else {
        echo "failed!";
    }
 
?>  
</body>
</html>
