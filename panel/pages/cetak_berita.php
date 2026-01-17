<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
	include "../../config/fungsi_jam.php";
?>
<html>
<head>
<title>CBT SMK AL QODIRIYAH | Cetak Kartu</title>
<script type="text/javascript" src="jquery.gdocsviewer.min.js"></script> 

<script type="text/javascript"> 
/*<![CDATA[*/
$(document).ready(function() {
	$('a.embed').gdocsViewer({width: 600, height: 750});
	$('#embedURL').gdocsViewer();
});
/*]]>*/
</script> 
</head>
<body>
<iframe src="<?php echo "cetakberita.php?token={$_REQUEST['token']}"; ?>" style="display:none;" name="frame"></iframe>
<button type="button" class="btn btn-default btn-sm" onClick="frames['frame'].print()" style="margin-top:10px; margin-bottom:5px"><i class="glyphicon glyphicon-print"></i> Cetak 
</button>
<?php echo "Cetak Berita Acara"; ?>

<?php

//koneksi database
require_once __DIR__ . "/../../config/server.php";
$ad = db_fetch_one(db_query($db, "SELECT * FROM cbt_admin LIMIT 1", array()));
$ad = $ad ? $ad : array('XSekolah' => '', 'XKepSek' => '', 'XNIPAdmin' => '', 'XNIPKepsek' => '', 'XLogo' => '', 'XKodeSekolah' => '');
$namsek = strtoupper($ad['XSekolah']);
$kepsek = $ad['XKepSek'];
$nipadmin = $ad['XNIPAdmin'];
$nipkepsek = $ad['XNIPKepsek'];
$logsek = $ad['XLogo'];
$BatasAwal = 50;


								$token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
								$rs = db_fetch_one(
								    db_query(
								        $db,
								        "SELECT * FROM cbt_ujian WHERE XTokenUjian = :token LIMIT 1",
								        array(':token' => $token)
								    )
								);
								$rs = $rs ? $rs : array(
								    'XTglUjian' => '',
								    'XKodeKelas' => '',
								    'XKodeJurusan' => '',
								    'XPengawas' => '',
								    'XNIPPengawas' => '',
								    'XJamUjian' => '',
								    'XLamaUjian' => '',
								    'XTokenUjian' => '',
								    'XMapel' => '',
								    'XKodeMapel' => '',
								    'XSesi' => '',
								);
								$tanggal = "{$rs['XTglUjian']}";
								$kelas = "{$rs['XKodeKelas']}";
								$jurus = "{$rs['XKodeJurusan']}";
								$pengawas = "{$rs['XPengawas']}";
								$nip = "{$rs['XNIPPengawas']}";
																								
								$timestamp = strtotime($tanggal);								
								$hari = date('l', $timestamp);
								$tgl = date('d', $timestamp);
								$bln = date('F', $timestamp);
								$bln2 = date('m', $timestamp);
								$thn = date('Y', $timestamp);
								$jamawal = $rs['XJamUjian'];
								$j1 = substr($jamawal,0,2);
								$m1 = substr($jamawal,3,2);
								$d1 = substr($jamawal,6,2); // pecah xmulaiujian ambil dari jamsekarang
								$jam1 = "$j1:$m1";								
	
	
$j2 = substr($rs['XLamaUjian'],0,2);
$m2 = substr($rs['XLamaUjian'],3,2);
$d2 = substr($rs['XLamaUjian'],6,2);
$selectedTime = "$j1:$m1:$d1";

$j3 = $j2*60;
$m3 = $m2;
$jummenit = $j3+$m3;
$jum_menit = "+$j2 hour +$m2 minutes";

$minutes_to_add = $jum_menit;

//set timezone
//date_default_timezone_set('GMT');

//set an date and time to work with
$start = "$thn-$bln2-$tgl $j1:$m1:$d1";
//display the converted time
$habis = date('H:i',strtotime($jum_menit,strtotime($start)));

if(!$kelas=="ALL"&&!$jurus=="ALL"){ 
$kondisi = "1";
$jumlahSiswaSemua = (int) db_fetch_value(db_query($db, "SELECT COUNT(*) FROM cbt_siswa WHERE XKodeKelas = :kelas AND XKodeJurusan = :jurus", array(':kelas' => $kelas, ':jurus' => $jurus)));
}elseif(!$kelas=="ALL"&&$jurus=="ALL"){ 
$kondisi = "2";
$jumlahSiswaSemua = (int) db_fetch_value(db_query($db, "SELECT COUNT(*) FROM cbt_siswa WHERE XKodeKelas = :kelas", array(':kelas' => $kelas)));
}elseif($kelas=="ALL"&&!$jurus=="ALL"){ 
$kondisi = "3";
$jumlahSiswaSemua = (int) db_fetch_value(db_query($db, "SELECT COUNT(*) FROM cbt_siswa WHERE XKodeJurusan = :jurus", array(':jurus' => $jurus)));
}elseif($kelas=="ALL"&&$jurus=="ALL"){ 
$kondisi = "4";
$jumlahSiswaSemua = (int) db_fetch_value(db_query($db, "SELECT COUNT(*) FROM cbt_siswa", array()));
} else {
$kondisi = "5";
$jumlahSiswaSemua = (int) db_fetch_value(db_query($db, "SELECT COUNT(*) FROM cbt_siswa WHERE XKodeKelas = :kelas AND XKodeJurusan = :jurus", array(':kelas' => $kelas, ':jurus' => $jurus)));
}

$jumlahSiswaUjian = (int) db_fetch_value(
    db_query(
        $db,
        "SELECT COUNT(*) FROM cbt_siswa_ujian WHERE XTokenUjian = :token",
        array(':token' => $rs['XTokenUjian'])
    )
);
$jumlahSiswaAbsen = $jumlahSiswaSemua-$jumlahSiswaUjian;
?>
	<div style="background:#999; width:100%; height:1275px;" ><br>
	<div style="background:#fff; width:90%; margin:0 auto; padding:30px; height:90%;">
    <table border="0" width="100%">
    <tr>
    							<?php
							
								//Our YYYY-MM-DD date string.
								if($hari=='Sunday'){$hari = "Minggu";}
								elseif($hari=='Monday'){$hari = "Senin";}
								elseif($hari=='Tuesday'){$hari = "Selasa";}
								elseif($hari=='Wednesday'){$hari = "Rabu";}
								elseif($hari=='Thursday'){$hari = "Kamis";}
								elseif($hari=='Friday'){$hari = "Jum'at";}
								elseif($hari=='Saturday'){$hari = "Sabtu";}
								
								if($bln=='January'){$bln = "Januari";}
								elseif($bln=='February'){$bln = "Pebruari";}
								elseif($bln=='March'){$bln = "Maret";}
								elseif($bln=='April'){$bln = "April";}
								elseif($bln=='May'){$bln = "Mei";}
								elseif($bln=='June'){$bln = "Juni";}
								elseif($bln=='July'){$bln = "Juli";}
								elseif($bln=='August'){$bln = "Agustus";}
								elseif($bln=='September'){$bln = "September";}
								elseif($bln=='Octocber'){$bln = "Oktober";}
								elseif($bln=='November'){$bln = "Nopember";}
								elseif($bln=='December'){$bln = "Desember";}
								
																?>                                

    <td rowspan="7" width="150" align="right"><img src="../../images/<?php echo "$logsek"; ?>" width="100"></td>
    <td colspan="2"  align="center"><font size="+1"><b>BERITA ACARA PELAKSANAAN</b></font></td>
    <td rowspan="7" width="150" align="center"></td>
    </tr>
    <tr>
    <td colspan="2" align="center"><font size="+1"><b>UJIAN BERBASIS KOMPUTER (UBK)</b></font></td>
    </tr>
    <tr>
    <td colspan="2" align="center"><font size="+1"><b><?php echo "$namsek"; ?></b></font></td>
    </tr>    
    <tr>
    <td colspan="2" align="center"><font size="+1"><b>TAHUN PELAJARAN : <?php echo $_COOKIE['beetahun']; ?></b></font></td>
    </tr>    
  <tr>
    <td >&nbsp;</td>
  </tr>
  <tr>
    <td >&nbsp;</td>
  </tr>
   
   <!-- <tr>

   								 <?php 
								$rs1 = db_fetch_one(
								    db_query(
								        $db,
								        "SELECT * FROM cbt_mapel WHERE XKodeMapel = :mapel LIMIT 1",
								        array(':mapel' => $rs['XKodeMapel'])
								    )
								);
                                $rs1 = $rs1 ? $rs1 : array('XNamaMapel' => '', 'XKKM' => '');
                             	$rs2 = strtoupper("{$rs1['XNamaMapel']}");
								$NilaiKKM2 = $rs1['XKKM'];
								?>   
    <td width="20%">Mata Pelajaran</td><td>: <b><?php echo $rs2; ?> (Nilai KKM : <?php echo $NilaiKKM2; ?>)</b></td>
    </tr>
    <tr>
    <td>Kelas | Jurusan</td><td>: <b><?php echo $rs['XKodeKelas']; ?> | <?php echo $rs['XKodeJurusan']; ?></b></td>
    </tr>

  <tr>
    <td>Tahun Akademik </td><td>: <?php echo $_COOKIE['beetahun']; ?></td>
  </tr>
  
  !-->
  <tr>
    <td colspan="4"></td>
  </tr>
  <tr>
    <td colspan="4"></td>
  </tr>
  </table><br><br>
  
  <table border="0" width="90%" align="center" >
  <tr height="30">
  <td height="30" colspan="4">Pada hari ini <?php echo $hari; ?> tanggal <?php echo $tgl; ?> bulan <?php echo $bln; ?> tahun <?php echo $thn; ?>, di <?php echo "$namsek"; ?> 
  telah diselenggarakan Ujian Berbasis Komputer (UBK) untuk matapelajaran <?php echo $rs2; ?> dari pukul <?php echo $jam1; ?> sampai dengan pukul <?php echo $habis; ?></td>
	</tr>
  </table>
  <br>
  <table border="0" width="85%" style="margin-left:50px">
  <tr height="30">
  <td height="30" width="5%">1.</td>
  <td height="30" width="30%">Username</td>
  <td height="30" width="60%" style="border-bottom:thin solid #000000"><?php echo "{$ad['XKodeSekolah']}"; ?></td>
  </tr>
  <tr height="30">
  <td height="30" width="10px"></td>
  <td height="30">Sekolah/Madrasah</td>
  <td height="30" width="60%" style="border-bottom:thin solid #000000"><?php echo "$namsek"; ?></td>  
  </tr>
  <tr height="30">
  <td height="30" width="10px"></td>
  <td height="30">Sesi</td>
  <td height="30" width="60%" style="border-bottom:thin solid #000000"><?php echo "{$rs['XSesi']}"; ?></td>  
  </tr>
  <tr height="30">
  <td height="30" width="10px"></td>
  <td height="30">Jumlah Peserta Seharusnya</td>
  <td height="30" width="60%" style="border-bottom:thin solid #000000"><?php echo "$jumlahSiswaSemua"; ?></td>  
  </tr>
  <tr height="30">
  <td height="30" width="10px"></td>
  <td height="30">Jumlah Hadir (ikut ujian)</td>
  <td height="30" width="60%" style="border-bottom:thin solid #000000"><?php echo "$jumlahSiswaUjian"; ?></td>  
  </tr>
  <tr height="30">
  <td height="30" width="10px"></td>
  <td height="30">Jumlah Tidak Hadir</td>
  <td height="30" width="60%" style="border-bottom:thin solid #000000"><?php echo "$jumlahSiswaAbsen"; ?></td>  
  </tr>
  <tr height="30">
  <td height="30" width="10px"></td>
  <td height="30">Nomer</td>
  <td height="30" width="60%" style="border-bottom:thin solid #000000"></td>  
  </tr>
   <tr height="30">
  <td height="30" width="10px"></td></tr>    
  <tr height="30">
  <td height="30" width="5%">2.</td>
  <td colspan="2" height="30" width="30%">Catatan selama Ujian Berbasis Komputer (UBK) </td>
  </tr>
  <tr height="30">
  <td height="30" width="10px"></td>
  <td colspan="2" height="30" width="60%" style="border-bottom:thin solid #000000"></td>  
  </tr>
  <tr height="30">
  <td height="30" width="10px"></td>
  <td colspan="2" height="30" width="60%" style="border-bottom:thin solid #000000"></td>   
  </tr>
  <tr height="30">
  <td height="30" width="10px"></td>
  <td colspan="2" height="30" width="60%" style="border-bottom:thin solid #000000"></td>    
  </tr>
  <tr height="30">
  <td height="30" width="10px"></td>
  <td colspan="2" height="30" width="60%" style="border-bottom:thin solid #000000"></td>   
  </tr>
  <tr height="30">
  <td height="30" width="10px"></td>
  <td colspan="2" height="30" width="60%" style="border-bottom:thin solid #000000"></td>   
  </tr>
  <tr height="30">
  <td height="30" width="10px"></td></tr>    
  <tr height="30">
  <td height="30" colspan="2" width="5%">Yang membuat berita acara : </td></tr>
  </table>
  
  <table border="0" width="80%" style="margin-left:50px">  
  <tr><td colspan="4" ></td>
  <td height="30" width="30%">TTD</td>
  </tr>
  <tr><td width="10%">1. </td><td width="20%">Proktor</td><td width="30%" style="border-bottom:thin solid #000000"><?php echo strtoupper($ad['XAdmin']); ?></td>
  <td height="30" width="5%"></td><td height="30" width="35%"></td>
  </tr>
  <tr><td width="10%">   </td><td width="20%">NIP. </td><td width="30%" style="border-bottom:thin solid #000000"><?php echo $nipadmin; ?></td>
  <td height="30" width="5%"></td><td height="30" width="35%" style="border-bottom:thin solid #000000">  1. </td>
  </tr>

  <tr><td width="10%">2. </td><td width="20%">Pengawas</td><td width="30%" style="border-bottom:thin solid #000000"><?php echo strtoupper($pengawas); ?></td>
  <td height="30" width="5%"></td><td height="30" width="35%"></td>
  </tr>
  <tr><td width="10%">   </td><td width="20%">NIP. </td><td width="30%" style="border-bottom:thin solid #000000"><?php echo $nip; ?></td>
  <td height="30" width="5%"></td><td height="30" width="35%" style="border-bottom:thin solid #000000">  2. </td>
  </tr>

  <tr><td width="10%">3. </td><td width="20%">Kepala Sekolah</td><td width="30%" style="border-bottom:thin solid #000000"><?php echo strtoupper($ad['XKepSek']); ?></td>
  <td height="30" width="5%"></td><td height="30" width="35%"></td>
  </tr>
  <tr><td width="10%">   </td><td width="20%">NIP. </td><td width="30%" style="border-bottom:thin solid #000000"><?php echo $nipkepsek; ?></td>
  <td height="30" width="5%"></td><td height="30" width="35%" style="border-bottom:thin solid #000000">  3. </td>
  </tr>
  
  </table>  
      
    </div>

    </div>
  
            
</body>
</html>
