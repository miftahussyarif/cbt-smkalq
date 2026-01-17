<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
?>
<html>
<head>
<title>CBT SMK AL QODIRIYAH | Cetak Kartu</title>
</head>
<body>
            <div class="row" style="width:106%">
						<div class="panel-heading" style="background-color:#ebeaea; margin-left:-15px; margin-right:-100px; width:100%; border-bottom:thin solid #d5d5d5">
                          <i class="fa fa-desktop"></i> &nbsp; |  &nbsp; Analisa Hasil Ujian  <button type="button" style="text-align:right;" class="btn btn-default btn-sm" onClick="frames['frame'].print()""><i class="glyphicon glyphicon-print"></i> Download 
</button>
                        </div>
                <!-- /.col-lg-12 -->
            </div>
<iframe src="<?php echo "cetakabsen.php?kelas={$_REQUEST['iki3']}&jur={$_REQUEST['jur3']}"; ?>" style="display:none;" name="frame"></iframe>
<button type="button" class="btn btn-default btn-sm" onClick="frames['frame'].print()" style="margin-top:10px; margin-bottom:5px"><i class="glyphicon glyphicon-print"></i> Cetak 
</button>

<?php

//koneksi database
require_once __DIR__ . "/../../config/server.php";
$kelas = isset($_REQUEST['iki3']) ? $_REQUEST['iki3'] : '';
$jurusan = isset($_REQUEST['jur3']) ? $_REQUEST['jur3'] : '';
$mapel = isset($_REQUEST['map3']) ? $_REQUEST['map3'] : '';
$kodeUjian = isset($_REQUEST['tes3']) ? $_REQUEST['tes3'] : '';
$setId = isset($_COOKIE['beetahun']) ? $_COOKIE['beetahun'] : '';
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
    <table border="0" width="100%">
    <tr>
    							<?php 
								$sqk = db_query($db, "select * from cbt_tes where XKodeUjian = ?", array($kodeUjian));
								$rs = db_fetch_one($sqk);
                             	$rs1 = $rs ? strtoupper("{$rs['XNamaUjian']}") : "";
								
								if($kodeUjian=='A'){$namaujian = "HASIL SEMUA UJIAN ";} else {$namaujian = "HASIL UJIAN $rs1";}
								?>                                

    <td rowspan="4" width="150"><img src="images/tut.jpg" width="100"></td>
    <td colspan="2"><font size="+2"><b><?php echo "$namaujian"; ?></b></font></td>
    </tr>
    <tr>
   								 <?php 
								$sqk = db_query($db, "select * from cbt_mapel where XKodeMapel = ?", array($mapel));
								$rs = db_fetch_one($sqk);
                             	$rs1 = $rs ? strtoupper("{$rs['XNamaMapel']}") : "";
								?>   
    <td width="20%">Mata Pelajaran</td><td>: <b><?php echo $rs1; ?></b></td>
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
  <th align="center"   width="25%" style="text-align:center" colspan="3">Semester 1</th><th width="25%" style="text-align:center" colspan="3">Semester 2</th><th align="center"   width="10%" style="text-align:center" rowspan="2">Nilai Akhir</th></tr>
  <tr>
  <td height="30" width="5%">UH</td><td width="5%">UTS</td><td width="5%">UAS</td>
  <td width="5%">UH</td><td width="5%">UTS</td><td width="5%">UAS</td>  
</tr>
<?php

$mulai = $i-1;
$batas = ($mulai*$jumlahn);
$startawal = $batas;
$batasakhir = $batas+$jumlahn;

$s = $i-1;

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
	$uh = db_query(
		$db,
		"SELECT sum(XNilai) as totUH FROM cbt_nilai where XKodeKelas = ? and XNIK = ? and XKodeUjian = 'UH' and XKodeMapel = ? and XSemester = '1' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);
	$tuh = db_fetch_one($uh);
	if(isset($tuh['totUH'])){$totUH1 = number_format($tuh['totUH'], 2, ',', '.');} else {$totUH1="";}

	$uts = db_query(
		$db,
		"SELECT sum(XNilai) as totUTS FROM cbt_nilai where XKodeKelas = ? and XNIK = ? and XKodeUjian = 'UTS' and XKodeMapel = ? and XSemester = '1' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);
	$tuts = db_fetch_one($uts);
	if(isset($tuts['totUTS'])){$totUTS1 = number_format($tuts['totUTS'], 2, ',', '.');} else {$totUTS1="";}	


	$uas = db_query(
		$db,
		"SELECT sum(XNilai) as totUAS FROM cbt_nilai where XKodeKelas = ? and XNIK = ? and XKodeUjian = 'UAS' and XKodeMapel = ? and XSemester = '1' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);
	$tuas = db_fetch_one($uas);
	if(isset($tuas['totUAS'])){$totUAS1 = number_format($tuas['totUAS'], 2, ',', '.');} else {$totUAS1="";}	

	$uh2 = db_query(
		$db,
		"SELECT sum(XNilai) as totUH2 FROM cbt_nilai where XKodeKelas = ? and XNIK = ? and XKodeUjian = 'UH' and XKodeMapel = ? and XSemester = '2' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);
	$tuh2 = db_fetch_one($uh2);
	$totUH2 = $tuh2 ? $tuh2['totUH2'] : '';

	$uts2 = db_query(
		$db,
		"SELECT sum(XNilai) as totUTS2 FROM cbt_nilai where XKodeKelas = ? and XNIK = ? and XKodeUjian = 'UTS' and XKodeMapel = ? and XSemester = '2' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);
	$tuts2 = db_fetch_one($uts2);
	$totUTS2 = $tuts2 ? $tuts2['totUTS2'] : '';

	$uas2 = db_query(
		$db,
		"SELECT sum(XNilai) as totUAS2 FROM cbt_nilai where XKodeKelas = ? and XNIK = ? and XKodeUjian = 'UAS' and XKodeMapel = ? and XSemester = '2' and XSetId = ?",
		array($kelas, $f['XNIK'], $mapel, $setId)
	);
	$tuas2 = db_fetch_one($uas2);
	$totUAS2 = $tuas2 ? $tuas2['totUAS2'] : '';	
	
	  echo "<tr height=30px><td>&nbsp;$nomz</td><td>&nbsp;{$f['XNIK']}</td><td align=left>&nbsp;{$f['XNamaSiswa']}</td>
	  <td>&nbsp;$totUH1</td><td>&nbsp;$totUTS1</td><td>&nbsp;$totUAS1</td>
	  <td>&nbsp;$totUH2</td><td>&nbsp;$totUTS2</td><td>&nbsp;$totUAS2</td>
	  
	  <td>&nbsp;</td></tr>";
  $nomz++;
?>
<?php } ?>        
        </table>
    </div>
    </div>
<?php } ?>            
</body>
</html>
