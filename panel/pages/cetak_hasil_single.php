<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
require_once __DIR__ . "/../../config/server.php";
	$kodeUjian = isset($_REQUEST['tes_single']) ? trim($_REQUEST['tes_single']) : '';
	$semester = isset($_REQUEST['sem_single']) ? trim($_REQUEST['sem_single']) : '1';
	$kelas = isset($_REQUEST['iki_single']) ? trim($_REQUEST['iki_single']) : '';
	$jurusan = isset($_REQUEST['jur_single']) ? trim($_REQUEST['jur_single']) : '';
	$mapel = isset($_REQUEST['map_single']) ? trim($_REQUEST['map_single']) : '';
	$setid = isset($_COOKIE['beetahun']) ? $_COOKIE['beetahun'] : '';
	$tokenListSql = '';
	$kodeSoalListSql = '';
	$tokenParams = array();
	$kodeSoalParams = array();
	$kodeSoalUtama = '';
	$pesanData = '';
	$dataReady = false;

	$sqlUjian = db_query(
		$db,
		"select XTokenUjian, XKodeSoal from cbt_ujian where XKodeUjian = ? and XKodeMapel = ? and XSemester = ? and XSetId = ?
		and (XKodeKelas = ? or XKodeKelas = 'ALL') and (XKodeJurusan = ? or XKodeJurusan = 'ALL')
		order by XTglUjian desc, XJamUjian desc",
		array($kodeUjian, $mapel, $semester, $setid, $kelas, $jurusan)
	);
	$ujianRows = $sqlUjian->fetchAll();
	if (!empty($ujianRows)) {
		$tokens = array();
		$kodeSoalArr = array();
		foreach ($ujianRows as $uj) {
			$tokens[] = $uj['XTokenUjian'];
			$kodeSoalArr[] = $uj['XKodeSoal'];
		}
		$tokens = array_values(array_unique($tokens));
		$kodeSoalArr = array_values(array_unique($kodeSoalArr));
		$kodeSoalUtama = isset($kodeSoalArr[0]) ? $kodeSoalArr[0] : '';
		if (!empty($tokens) && !empty($kodeSoalArr)) {
			$tokenListSql = implode(',', array_fill(0, count($tokens), '?'));
			$kodeSoalListSql = implode(',', array_fill(0, count($kodeSoalArr), '?'));
			$tokenParams = $tokens;
			$kodeSoalParams = $kodeSoalArr;

			$cekJawaban = db_query(
				$db,
				"select 1 from cbt_jawaban where XKodeSoal in ($kodeSoalListSql) and XTokenUjian in ($tokenListSql) limit 1",
				array_merge($kodeSoalParams, $tokenParams)
			);
			if (db_fetch_one($cekJawaban)) {
				$dataReady = true;
			} else {
				$pesanData = "Belum ada hasil ujian untuk kombinasi ini di Analisa Soal.";
			}
		} else {
			$pesanData = "Belum ada hasil ujian untuk kombinasi ini di Analisa Soal.";
		}
	} else {
		$pesanData = "Data ujian tidak ditemukan untuk kombinasi yang dipilih.";
	}
?>
<html>
<head>
<title>CBT SMK AL QODIRIYAH | Cetak Nilai</title>
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
<?php if ($dataReady) { ?>
<iframe src="<?php echo "cetaknilai_single.php?kelas=$kelas&jur=$jurusan&mapz=$mapel&sem=$semester&tes=$kodeUjian"; ?>" style="display:none;" name="frame"></iframe>
<button type="button" class="btn btn-default btn-sm" onClick="frames['frame'].print()" style="margin-top:10px; margin-bottom:5px"><i class="glyphicon glyphicon-print"></i> Cetak
</button>
<?php } ?>
<?php
$labelUjian = $kodeUjian;
$sqk = db_query($db, "select * from cbt_tes where XKodeUjian = ?", array($kodeUjian));
$rs = db_fetch_one($sqk);
if ($rs && $rs['XNamaUjian'] != '') {
	$labelUjian = $rs['XNamaUjian'];
}
echo "Cetak Hasil Ujian $labelUjian Kelas : '$kelas', Jurusan : '$jurusan'";
?>

<?php
if ($pesanData != '') {
	echo "<div class='alert alert-warning' style='margin-top:10px;'>$pesanData</div>";
}

if (!$dataReady) {
	echo "</body></html>";
	exit;
}

//koneksi database
$sqlad = db_query($db, "select * from cbt_admin", array());
$ad = db_fetch_one($sqlad);
$namsek = strtoupper($ad['XSekolah']);
$kepsek = $ad['XKepSek'];
$logsek = $ad['XLogo'];
$BatasAwal = 50;
if ($kelas != '') {
	$cekQuery = db_query($db, "SELECT COUNT(*) from cbt_siswa where XKodeKelas = ? and XKodeJurusan = ?", array($kelas, $jurusan));
} else {
	$cekQuery = db_query($db, "SELECT COUNT(*) from cbt_siswa", array());
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
								$namaujian = strtoupper($labelUjian);
								?>

    <td rowspan="4" width="150"><img src="../../images/<?php echo "$logsek"; ?>" width="100"></td>
    <td colspan="2"><font size="+2"><b><?php echo "HASIL UJIAN $namaujian"; ?></b></font></td>
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
    <td>Kelas | Jurusan</td><td>: <b><?php echo $kelas; ?> | <?php echo $jurusan; ?></b></td>
    </tr>

  <tr>
    <td>Tahun Akademik </td><td>: <?php echo $_COOKIE['beetahun']; ?> | Semester : <?php echo $semester; ?></td>
  </tr>
  <tr>
    <td></td>
  </tr>
  <tr>
    <td></td>
  </tr>
  </table><br>

  <table border="1" width="100%" style="text-align:center">
  <tr bgcolor="#CCCCCC" height="30"><th width="5%" style="text-align:center">No.</th><th width="10%" style="text-align:center">NIS</th><th width="55%"
  style="text-align:center">Nama Siswa</th>
  <th align="center" width="15%" style="text-align:center">Nilai</th>
  <th align="center" width="15%" style="text-align:center">KKM</th>
</tr>
<?php

$mulai = $i-1;
$batas = ($mulai*$jumlahn);
$startawal = $batas;
$batasakhir = $batas+$jumlahn;

$s = $i-1;

$per = db_query($db, "SELECT * from cbt_mapel where XKodeMapel = ?", array($mapel));
$p = db_fetch_one($per);
$NilaiKKM = $p['XKKM'];
$tampilKKM = number_format($NilaiKKM, 2, ',', '.');
?>
<?php
if ($kelas != '') {
	$batas = (int) $batas;
	$jumlahn = (int) $jumlahn;
	$cekQuery1 = db_query(
		$db,
		"SELECT XNomerUjian, XNIK, XNamaSiswa, XKodeKelas, XKodeJurusan from cbt_siswa where XKodeKelas = ? and XKodeJurusan = ? limit $batas,$jumlahn",
		array($kelas, $jurusan)
	);
} else {
	$batas = (int) $batas;
	$jumlahn = (int) $jumlahn;
	$cekQuery1 = db_query($db, "SELECT XNomerUjian, XNIK, XNamaSiswa, XKodeKelas, XKodeJurusan from cbt_siswa limit $batas,$jumlahn", array());
}

$paket = db_query($db, "select XPilGanda, XEsai, XPersenPil, XPersenEsai from cbt_paketsoal where XKodeSoal = ? limit 1", array($kodeSoalUtama));
$pak = db_fetch_one($paket);
$jumPil = isset($pak['XPilGanda']) ? (int)$pak['XPilGanda'] : 0;
$jumEsai = isset($pak['XEsai']) ? (int)$pak['XEsai'] : 0;
$persenPil = isset($pak['XPersenPil']) ? (float)$pak['XPersenPil'] : 0;
$persenEsai = isset($pak['XPersenEsai']) ? (float)$pak['XPersenEsai'] : 0;
while($f= $cekQuery1->fetch()){
	$nilaiTampil = "";
	$jawabParams = array_merge($kodeSoalParams, $tokenParams, array($f['XNomerUjian']));
	$cekJawabSiswa = db_query(
		$db,
		"select 1 from cbt_jawaban where XKodeSoal in ($kodeSoalListSql) and XTokenUjian in ($tokenListSql) and XUserJawab = ? limit 1",
		$jawabParams
	);
	if (db_fetch_one($cekJawabSiswa)) {
		$sqlBenar = db_query(
			$db,
			"select count(1) as benar from cbt_jawaban where XKodeSoal in ($kodeSoalListSql) and XTokenUjian in ($tokenListSql) and XUserJawab = ? and XNilai = '1'",
			$jawabParams
		);
		$br = db_fetch_one($sqlBenar);
		$jumBenar = (int) ($br ? $br['benar'] : 0);

		$sqlEsai = db_query(
			$db,
			"select sum(XNilaiEsai) as hasil from cbt_jawaban where XKodeSoal in ($kodeSoalListSql) and XTokenUjian in ($tokenListSql) and XUserJawab = ?",
			$jawabParams
		);
		$es = db_fetch_one($sqlEsai);
		$nilaiEsai = isset($es['hasil']) ? (float)$es['hasil'] : 0;

		$nilaiPil = ($jumPil > 0) ? (($jumBenar / $jumPil) * 100) : 0;
		$totalPil = $nilaiPil * ($persenPil / 100);
		$totalEsai = $nilaiEsai * ($persenEsai / 100);
		$totalNilai = $totalPil + $totalEsai;
		$nilaiTampil = number_format($totalNilai, 2, ',', '.');
	}

	  echo "<tr height=30px><td>&nbsp;$nomz</td><td>&nbsp;{$f['XNIK']}</td><td align=left>&nbsp;{$f['XNamaSiswa']}</td>
	  <td>&nbsp;$nilaiTampil</td>
  	  <td>$tampilKKM</td>
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
