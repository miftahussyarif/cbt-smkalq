<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
	$kodeUjian = isset($_REQUEST['tes3']) ? $_REQUEST['tes3'] : 'A';
	$kodeUjian = trim($kodeUjian);
	$allowedUjian = array('A', 'UH', 'UTS', 'UAS');
	if (!in_array($kodeUjian, $allowedUjian, true)) {
		$kodeUjian = 'A';
	}
	require_once __DIR__ . "/../../config/server.php";
	$kelas = isset($_REQUEST['iki3']) ? $_REQUEST['iki3'] : '';
	$jurusan = isset($_REQUEST['jur3']) ? $_REQUEST['jur3'] : '';
	$mapel = isset($_REQUEST['map3']) ? $_REQUEST['map3'] : '';
	$setId = isset($_COOKIE['beetahun']) ? $_COOKIE['beetahun'] : '';
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
<iframe src="<?php echo "cetaknilai.php?kelas={$_REQUEST['iki3']}&jur={$_REQUEST['jur3']}&mapz={$_REQUEST['map3']}&tes3=$kodeUjian"; ?>" style="display:none;" name="frame"></iframe>
<button type="button" class="btn btn-default btn-sm" onClick="frames['frame'].print()" style="margin-top:10px; margin-bottom:5px"><i class="glyphicon glyphicon-print"></i> Cetak 
</button>
<?php
if ($kodeUjian == 'A') {
	echo "Cetak Hasil Semua Ujian Kelas : '{$_REQUEST['iki3']}', Jurusan : '{$_REQUEST['jur3']}'";
} else {
	$labelUjian = $kodeUjian;
	$sqk = db_query($db, "select * from cbt_tes where XKodeUjian = ?", array($kodeUjian));
	$rs = db_fetch_one($sqk);
	if ($rs && $rs['XNamaUjian'] != '') {
		$labelUjian = $rs['XNamaUjian'];
	}
	echo "Cetak Hasil Ujian $labelUjian Kelas : '{$_REQUEST['iki3']}', Jurusan : '{$_REQUEST['jur3']}'";
}
?>

<?php

//koneksi database
$sqlad = db_query($db, "select * from cbt_admin", array());
$ad = db_fetch_one($sqlad);
$namsek = strtoupper($ad['XSekolah']);
$kepsek = $ad['XKepSek'];
$logsek = $ad['XLogo'];
$BatasAwal = 50;
 if(isset($_REQUEST['iki3'])){ 
$cekQuery = db_query(
	$db,
	"SELECT COUNT(*) FROM cbt_siswa where XKodeKelas = ? and XKodeJurusan = ?",
	array($kelas, $jurusan)
);
}else{
$cekQuery = db_query($db, "SELECT COUNT(*) FROM cbt_siswa", array());
}
$jumlahData = (int) db_fetch_value($cekQuery);
$jumlahn = 20;
$n = ceil($jumlahData/$jumlahn);
$nomz = 1;
for($i=1;$i<=$n;$i++){ ?>
	<div style="background:#999; width:100%; height:1275px;" ><br>
	<div style="background:#fff; width:90%; margin:0 auto; padding:30px; height:90%;">
    <table border="0" width="100%">
    <tr>
    							<?php 
								$sqk = db_query($db, "select * from cbt_tes where XKodeUjian = ?", array($kodeUjian));
								$rs = db_fetch_one($sqk);
                             	$rs1 = $rs ? strtoupper("{$rs['XNamaUjian']}") : "";
								
								if($kodeUjian=='A'){$namaujian = "HASIL SEMUA UJIAN ";} else {$namaujian = "HASIL UJIAN $rs1";}
								?>                                

    <td rowspan="4" width="150"><img src="../../images/<?php echo "$logsek"; ?>" width="100"></td>
    <td colspan="2"><font size="+2"><b><?php echo "$namaujian"; ?></b></font></td>
    </tr>
    <tr>
   								 <?php 
								$sqk = db_query($db, "select * from cbt_mapel where XKodeMapel = ?", array($mapel));
								$rs = db_fetch_one($sqk);
                             	$rs1 = $rs ? strtoupper("{$rs['XNamaMapel']}") : "";
								$NilaiKKM2 = $rs ? $rs['XKKM'] : 0;
								?>   
    <td width="20%">Mata Pelajaran</td><td>: <b><?php echo $rs1; ?> (Nilai KKM : <?php echo $NilaiKKM2; ?>)</b></td>
    </tr>
    <tr>
    <td>Kelas | Jurusan</td><td>: <b><?php echo $_REQUEST['iki3']; ?> | <?php echo $_REQUEST['jur3']; ?></b></td>
    </tr>

  <tr>
    <td>Tahun Akademik </td><td>: <?php echo $_COOKIE['beetahun']; ?></td>
  </tr>
  <tr>
    <td></td>
  </tr>
  <tr>
    <td></td>
  </tr>
  </table><br>
  
  <table border="1" width="100%" style="text-align:center">
  <tr bgcolor="#CCCCCC" height="30"><th width="5%" style="text-align:center" rowspan="2">No.</th><th width="10%" style="text-align:center" rowspan="2">NIS</th><th width="25%" 
  rowspan="2" style="text-align:center">Nama Siswa</th>
  <th align="center"   width="25%" style="text-align:center" colspan="5">Semester 1</th><th width="25%" style="text-align:center" colspan="5">Semester 2</th>
  <th align="center"   width="5%" style="text-align:center" rowspan="2">NA</th>
  <th align="center"   width="5%" style="text-align:center" rowspan="2">KKM</th>
</tr>
  <tr>
  <td height="30" width="5%">UH</td><td height="30" width="5%">TG</td><td width="5%">UTS</td><td width="5%">UAS</td><td width="5%">NILAI1</td>
  <td width="5%">UH</td><td height="30" width="5%">TG</td><td width="5%">UTS</td><td width="5%">UAS</td><td width="5%">NILAI2</td>
</tr>
<?php

$mulai = $i-1;
$batas = ($mulai*$jumlahn);
$startawal = $batas;
$batasakhir = $batas+$jumlahn;

$s = $i-1;

$per = db_query($db, "SELECT * from cbt_mapel where XKodeMapel = ?", array($mapel));
$p = db_fetch_one($per);

$perUH = $p['XPersenUH'];
$perUTS = $p['XPersenUTS'];
$perUAS = $p['XPersenUAS'];
$NilaiKKM = $p['XKKM'];
?>
<?php
if(isset($_REQUEST['iki3'])){ 
	$batas = (int) $batas;
	$jumlahn = (int) $jumlahn;
$cekQuery1 = db_query($db, "SELECT * FROM cbt_siswa where XKodeKelas = ? and XKodeJurusan = ? limit $batas,$jumlahn", array($kelas, $jurusan));
}else{
$batas = (int) $batas;
$jumlahn = (int) $jumlahn;
$cekQuery1 = db_query($db, "SELECT * FROM cbt_siswa limit $batas,$jumlahn", array());
}
while($f= $cekQuery1->fetch()){
	$utg = db_query(
		$db,
		"SELECT sum(XNilaiTugas) as totUG, count(XNilaiTugas) as jujumG FROM cbt_tugas where XKodeKelas = ? and XNIK = ? and XKodeMapel = ? and XSemester = '1' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);

	$tug = db_fetch_one($utg);
	if(isset($tug['totUG']) && !empty($tug['jujumG'])){
		$totUG1 = number_format(($tug['totUG']/$tug['jujumG']), 2, ',', '.');
		$TUG1 = ($tug['totUG']/$tug['jujumG']);
	} else {$totUG1="";$TUG1="";}


	if($kelas=="ALL"){
	$uh = db_query(
		$db,
		"SELECT sum(XTotalNilai) as totUH, count(XNilai) as jujum FROM cbt_nilai where XNIK = ? and XKodeUjian = 'UH' and XKodeMapel = ? and XSemester = '1' and XSetId = ?",
		array($f['XNIK'], $mapel, $setId)
	);
/*$uh = mysql_query("
SELECT sum(XTotalNilai) as totUH, count(XNilai) as jujum FROM `cbt_jawaban` j left join cbt_ujian u on u.XTokenUjian = j.XTokenUjian WHERE XUserJawab = '$f[XNomerUjian]' and u.XKodeUjian = 'UH' and u.XKodeMapel = '$_REQUEST[map3]' and u.XSemester = '1' and u.XSetId='$_COOKIE[beetahun]'
");
*/

	} else {
//$uh = mysql_query("SELECT sum(XTotalNilai) as totUH, count(XNilai) as jujum FROM `cbt_jawaban` j left join `cbt_ujian` u on u.XTokenUjian = j.XTokenUjian WHERE  j.XUserJawab// = '$f[XNomerUjian]' and u.XKodeMapel = '$_REQUEST[map3]' and u.XSemester = '1' and u.XSetId='$_COOKIE[beetahun]' and u.XKodeUjian = 'UH'");

	$uh = db_query(
		$db,
		"SELECT sum(XTotalNilai) as totUH, count(XNilai) as jujum FROM cbt_nilai where XKodeKelas = ? and XNIK = ? and XKodeUjian = 'UH' and XKodeMapel = ? and XSemester = '1' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);
	}

//echo "SELECT sum(XTotalNilai) as totUH, count(XNilai) as jujum FROM `cbt_jawaban` j left join cbt_ujian u on u.XTokenUjian = j.XTokenUjian WHERE XUserJawab = '$f[XNomerUjian]' and u.XKodeUjian = 'UH' and u.XKodeMapel = '$_REQUEST[map3]' and u.XSemester = '1' and u.XSetId='$_COOKIE[beetahun]'";
		
	$tuh = db_fetch_one($uh);

//echo "$tuh[totUH]-$f[XNomerUjian]<br>";

	if(isset($tuh['totUH']) && !empty($tuh['jujum'])){
		$totUH1 = number_format(($tuh['totUH']/$tuh['jujum']), 2, ',', '.');
		$TUH1 = ($tuh['totUH']/$tuh['jujum']);
	} else {$totUH1="";$TUH1 = "";}

	$uts = db_query(
		$db,
		"SELECT sum(XNilai) as totUTS FROM cbt_nilai where XKodeKelas = ? and XNIK = ? and XKodeUjian = 'UTS' and XKodeMapel = ? and XSemester = '1' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);
	$tuts = db_fetch_one($uts);
	if(isset($tuts['totUTS'])){$totUTS1 = number_format($tuts['totUTS'], 2, ',', '.');
	$TUTS1 = $tuts['totUTS'];
	} else {$totUTS1="";$TUTS1="";}	


	$uas = db_query(
		$db,
		"SELECT sum(XNilai) as totUAS FROM cbt_nilai where XKodeKelas = ? and XNIK = ? and XKodeUjian = 'UAS' and XKodeMapel = ? and XSemester = '1' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);
	$tuas = db_fetch_one($uas);
	if(isset($tuas['totUAS'])){$totUAS1 = number_format($tuas['totUAS'], 2, ',', '.');
	$TUAS1 = $tuas['totUAS'];
	} else {$totUAS1="";$TUAS1="";}	

//nilai akhir semester1
//NR = 60% (RU&T)+ 20% (UTS)  + 20% (UAS)

if(!$totUH1==""){
	$NUH1 = $TUH1;
	$NUG1 = $TUG1;	
	if($NUG1==""){$NH1   = $NUH1;} else {$NH1   = ($NUH1+$NUG1)/2; }//Nilai Harian
	$NUT1 = $TUTS1;	
	$NUA1 = $TUAS1;	
	
	//$NA1  = ($NH1*($perUH/100))+($NUT1*($perUTS/100))+($NUA1*($perUAS/100)); // bila dihitung dari presentase
	$NA1  = ($NH1*($perUH))+($NUT1*($perUTS))+($NUA1*($perUAS)); // bila dihitung dari presentase
	//$NA1  = ( ($NH1*2)+$NUT1+$NUA1 )/4 ; //
	$totNA1 = 	number_format(($NA1/100), 2, ',', '.');

} else { $NA1 = ""; $totNA1 = "";}


	$utg2 = db_query(
		$db,
		"SELECT sum(XNilaiTugas) as totUG2, count(XNilaiTugas) as jujumG2 FROM cbt_tugas where XKodeKelas = ? and XNIK = ? and XKodeMapel = ? and XSemester = '2' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);

	$tug2 = db_fetch_one($utg2);
	if(isset($tug2['totUG2']) && !empty($tug2['jujumG2'])){
		$totUG2 = number_format(($tug2['totUG2']/$tug2['jujumG2']), 2, ',', '.');
		$TUG2 = ($tug2['totUG2']/$tug2['jujumG2']);
	} else {$totUG2="";$TUG2 ="";}

	$uh2 = db_query(
		$db,
		"SELECT sum(XNilai) as totUH2, count(XNilai) as jujum2 FROM cbt_nilai where XKodeKelas = ? and XNIK = ? and XKodeUjian = 'UH' and XKodeMapel = ? and XSemester = '2' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);

	$tuh2 = db_fetch_one($uh2);
	if(isset($tuh2['totUH2']) && !empty($tuh2['jujum2'])){
		$totUH2 = number_format(($tuh2['totUH2']/$tuh2['jujum2']), 2, ',', '.');
		$TUH2 = ($tuh2['totUH2']/$tuh2['jujum2']);
	} else {$totUH2="";$TUH2 ="";}

	$uts2 = db_query(
		$db,
		"SELECT sum(XNilai) as totUTS2 FROM cbt_nilai where XKodeKelas = ? and XNIK = ? and XKodeUjian = 'UTS' and XKodeMapel = ? and XSemester = '2' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);
	$tuts2 = db_fetch_one($uts2);
	if(isset($tuts2['totUTS2'])){$totUTS2 = number_format($tuts2['totUTS2'], 2, ',', '.');
	$TUTS2 = $tuts2['totUTS2'];
	} else {$totUTS2="";$TUTS2="";}	

	$uas2 = db_query(
		$db,
		"SELECT sum(XNilai) as totUAS2 FROM cbt_nilai where XKodeKelas = ? and XNIK = ? and XKodeUjian = 'UAS' and XKodeMapel = ? and XSemester = '2' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);
	$tuas2 = db_fetch_one($uas2);
	if(isset($tuas2['totUAS2'])){$totUAS2 = number_format($tuas2['totUAS2'], 2, ',', '.');
	$TUAS2 = $tuas2['totUAS2'];
	} else {$totUAS2="";$TUAS2="";}	

if(!$totUH2==""){
	$NUH2 = $TUH2;
	$NUG2 = $TUG2;	
	$NH2   = ($NUH2+$NUG2)/2; //Nilai Harian
	$NUT2 = $TUTS2;	
	$NUA2 = $TUAS2;	
	
	$NA2  = ($NH2*($perUH/100))+($NUT2*($perUTS/100))+($NUA2*($perUAS/100)); // bila dihitung dari presentase
	//$NA1  = ( ($NH1*2)+$NUT1+$NUA1 )/4 ; //
	$totNA2 = 	number_format($NA2, 2, ',', '.');
	
} else { $totNA2 = "";}

if(!isset($NA2)){ $NA2 = 0;}

	if($NA2==""){$TotAkhir = ($NA1+$NA2)/100;} else {$TotAkhir = (($NA1+$NA2)/2)/100;}
	
	if($NA1==""&&$NA2==""){$TotAkhire ="";} else {
	$TotAkhire = number_format($TotAkhir, 2, ',', '.');
	}
	if($totUH1==''){$TotAkhir = "";}

	if($kodeUjian != 'A'){
		if($kodeUjian != 'UH'){
			$totUH1 = "";
			$totUG1 = "";
			$totUH2 = "";
			$totUG2 = "";
		}
		if($kodeUjian != 'UTS'){
			$totUTS1 = "";
			$totUTS2 = "";
		}
		if($kodeUjian != 'UAS'){
			$totUAS1 = "";
			$totUAS2 = "";
		}
		$totNA1 = "";
		$totNA2 = "";
		$TotAkhire = "";
	}

	$tampilKKM = number_format($NilaiKKM, 2, ',', '.');
	if($TotAkhir>=$NilaiKKM2){$lulus = "LULUS";} else {$lulus = "REMIDI";}
	
	  echo "<tr height=30px><td>&nbsp;$nomz</td><td>&nbsp;{$f['XNIK']}</td><td align=left>&nbsp;{$f['XNamaSiswa']}</td>
	  <td>&nbsp;$totUH1</td><td>&nbsp;$totUG1</td><td>&nbsp;$totUTS1</td><td>&nbsp;$totUAS1</td><td>&nbsp;$totNA1</td>
	  <td>&nbsp;$totUH2</td><td>&nbsp;$totUG2</td><td>&nbsp;$totUTS2</td><td>&nbsp;$totUAS2</td><td>&nbsp;$totNA2</td>
	  <td>$TotAkhire</td>
  	  <td>$NilaiKKM2</td>	  
	  </tr>";

  $nomz++;
?>
<?php } ?>        
        </table>
    </div>
    </div>
<?php } ?>            
</body>
</html>
