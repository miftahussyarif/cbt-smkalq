<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
include "../../config/server.php";
	$kodeUjian = isset($_REQUEST['tes_single']) ? trim($_REQUEST['tes_single']) : '';
	$semester = isset($_REQUEST['sem_single']) ? trim($_REQUEST['sem_single']) : '1';
	$kelas = isset($_REQUEST['iki_single']) ? trim($_REQUEST['iki_single']) : '';
	$jurusan = isset($_REQUEST['jur_single']) ? trim($_REQUEST['jur_single']) : '';
	$mapel = isset($_REQUEST['map_single']) ? trim($_REQUEST['map_single']) : '';
	$setid = isset($_COOKIE['beetahun']) ? $_COOKIE['beetahun'] : '';
	$tokenListSql = '';
	$kodeSoalListSql = '';
	$kodeSoalUtama = '';
	$pesanData = '';
	$dataReady = false;

	$sqlUjian = mysql_query("select XTokenUjian, XKodeSoal from cbt_ujian where XKodeUjian = '$kodeUjian' and XKodeMapel = '$mapel' and XSemester = '$semester' and XSetId = '$setid'
		and (XKodeKelas = '$kelas' or XKodeKelas = 'ALL') and (XKodeJurusan = '$jurusan' or XKodeJurusan = 'ALL')
		order by XTglUjian desc, XJamUjian desc");
	if ($sqlUjian && mysql_num_rows($sqlUjian) > 0) {
		$tokens = array();
		$kodeSoalArr = array();
		while ($uj = mysql_fetch_array($sqlUjian)) {
			$tokens[] = $uj['XTokenUjian'];
			$kodeSoalArr[] = $uj['XKodeSoal'];
		}
		$tokens = array_values(array_unique($tokens));
		$kodeSoalArr = array_values(array_unique($kodeSoalArr));
		$kodeSoalUtama = isset($kodeSoalArr[0]) ? $kodeSoalArr[0] : '';
		$tokensEscaped = array_map('mysql_real_escape_string', $tokens);
		$soalEscaped = array_map('mysql_real_escape_string', $kodeSoalArr);
		$tokenListSql = "'" . implode("','", $tokensEscaped) . "'";
		$kodeSoalListSql = "'" . implode("','", $soalEscaped) . "'";

		$cekJawaban = mysql_num_rows(mysql_query("select 1 from cbt_jawaban where XTokenUjian in ($tokenListSql) and XKodeSoal in ($kodeSoalListSql) limit 1"));
		if ($cekJawaban > 0) {
			$dataReady = true;
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
$sqk = mysql_query("select * from cbt_tes where XKodeUjian = '$kodeUjian'");
$rs = mysql_fetch_array($sqk);
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
$sqlad = mysql_query("select * from cbt_admin");
$ad = mysql_fetch_array($sqlad);
$namsek = strtoupper($ad['XSekolah']);
$kepsek = $ad['XKepSek'];
$logsek = $ad['XLogo'];
$BatasAwal = 50;
if ($kelas != '') {
	$cekQuery = mysql_query("SELECT 1 from cbt_siswa where XKodeKelas = '$kelas' and XKodeJurusan = '$jurusan'");
} else {
	$cekQuery = mysql_query("SELECT 1 from cbt_siswa");
}
$jumlahData = mysql_num_rows($cekQuery);
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
								$sqk = mysql_query("select * from cbt_mapel where XKodeMapel = '$mapel'");
								$rs = mysql_fetch_array($sqk);
                             	$rs1 = strtoupper("$rs[XNamaMapel]");
								$NilaiKKM2 = $rs['XKKM'];
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

$per = mysql_query("SELECT * from cbt_mapel where XKodeMapel = '$mapel'");
$p = mysql_fetch_array($per);
$NilaiKKM = $p['XKKM'];
$tampilKKM = number_format($NilaiKKM, 2, ',', '.');
?>
<?php
if ($kelas != '') {
	$cekQuery1 = mysql_query("SELECT XNomerUjian, XNIK, XNamaSiswa, XKodeKelas, XKodeJurusan from cbt_siswa
		where XKodeKelas = '$kelas' and XKodeJurusan = '$jurusan' limit $batas,$jumlahn");
} else {
	$cekQuery1 = mysql_query("SELECT XNomerUjian, XNIK, XNamaSiswa, XKodeKelas, XKodeJurusan from cbt_siswa limit $batas,$jumlahn");
}

$paket = mysql_query("select XPilGanda, XEsai, XPersenPil, XPersenEsai from cbt_paketsoal where XKodeSoal = '$kodeSoalUtama' limit 1");
$pak = mysql_fetch_array($paket);
$jumPil = isset($pak['XPilGanda']) ? (int)$pak['XPilGanda'] : 0;
$jumEsai = isset($pak['XEsai']) ? (int)$pak['XEsai'] : 0;
$persenPil = isset($pak['XPersenPil']) ? (float)$pak['XPersenPil'] : 0;
$persenEsai = isset($pak['XPersenEsai']) ? (float)$pak['XPersenEsai'] : 0;
while($f= mysql_fetch_array($cekQuery1)){
	$nilaiTampil = "";
	$cekJawabSiswa = mysql_num_rows(mysql_query("select 1 from cbt_jawaban where XKodeSoal in ($kodeSoalListSql) and XTokenUjian in ($tokenListSql) and XUserJawab = '$f[XNomerUjian]' limit 1"));
	if ($cekJawabSiswa > 0) {
		$sqlBenar = mysql_query("select count(1) as benar from cbt_jawaban where XKodeSoal in ($kodeSoalListSql) and XTokenUjian in ($tokenListSql) and XUserJawab = '$f[XNomerUjian]' and XNilai = '1'");
		$br = mysql_fetch_array($sqlBenar);
		$jumBenar = (int)$br['benar'];

		$sqlEsai = mysql_query("select sum(XNilaiEsai) as hasil from cbt_jawaban where XKodeSoal in ($kodeSoalListSql) and XTokenUjian in ($tokenListSql) and XUserJawab = '$f[XNomerUjian]'");
		$es = mysql_fetch_array($sqlEsai);
		$nilaiEsai = isset($es['hasil']) ? (float)$es['hasil'] : 0;

		$nilaiPil = ($jumPil > 0) ? (($jumBenar / $jumPil) * 100) : 0;
		$totalPil = $nilaiPil * ($persenPil / 100);
		$totalEsai = $nilaiEsai * ($persenEsai / 100);
		$totalNilai = $totalPil + $totalEsai;
		$nilaiTampil = number_format($totalNilai, 2, ',', '.');
	}

	  echo "<tr height=30px><td>&nbsp;$nomz</td><td>&nbsp;$f[XNIK]</td><td align=left>&nbsp;$f[XNamaSiswa]</td>
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
