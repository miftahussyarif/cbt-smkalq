<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
?>
<?php
require('fpdf/fpdf.php');
include "../../config/server.php";

$kodeUjian = isset($_REQUEST['tes']) ? trim($_REQUEST['tes']) : '';
$semester = isset($_REQUEST['sem']) ? trim($_REQUEST['sem']) : '';
if ($semester == '') {
	$semester = '1';
}
$kelas = isset($_REQUEST['kelas']) ? trim($_REQUEST['kelas']) : '';
$jurusan = isset($_REQUEST['jur']) ? trim($_REQUEST['jur']) : '';
$mapel = isset($_REQUEST['mapz']) ? trim($_REQUEST['mapz']) : '';
$setid = isset($_COOKIE['beetahun']) ? $_COOKIE['beetahun'] : '';

$tokenListSql = '';
$kodeSoalListSql = '';
$kodeSoalUtama = '';
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
	}
}

class PDF extends FPDF{

function Header(){

$sqlad = mysql_query("select * from cbt_admin");
$ad = mysql_fetch_array($sqlad);
$namsek = strtoupper($ad['XSekolah']);
$kepsek = $ad['XKepSek'];
$logsek = $ad['XLogo'];

$kodeUjian = isset($_REQUEST['tes']) ? trim($_REQUEST['tes']) : '';
$namaUjian = $kodeUjian;
$sqk = mysql_query("select * from cbt_tes where XKodeUjian = '$kodeUjian'");
$rs = mysql_fetch_array($sqk);
if ($rs && $rs['XNamaUjian'] != '') {
	$namaUjian = strtoupper($rs['XNamaUjian']);
} else {
	$namaUjian = strtoupper($namaUjian);
}

$sqk = mysql_query("select * from cbt_mapel where XKodeMapel = '$_REQUEST[mapz]'");
$rs = mysql_fetch_array($sqk);
$rs1 = strtoupper("$rs[XNamaMapel]");
$NilaiKKMe = $rs['XKKM'];

   $this->Image('../../images/'.$logsek,1,1,2.0);
   $this->SetTextColor(0,0,0);
   $this->SetFont('Arial','B','12');
   $this->Cell(3,1,'');
   $this->Cell(13,1,'DAFTAR NILAI '. $namaUjian . ' ' . $namsek,0,0,'L',0);
   $this->SetFont('Arial','','10');
   $this->Cell(0,1,'BSMART - Hal. : '. $this->PageNo(),0,0,'R');

   $this->Ln(0.6);
   $this->SetFont('Arial','','10');
   $this->Cell(3,1,'');
   $this->Cell(4,1,"Mata Pelajaran ",0,0,'L');
   $this->Cell(3,1,": ".$rs1." (KKM : ".$NilaiKKMe.")",0,0,'L');
   $this->Ln(0.5);
   $this->Cell(3,1,'');
   $this->Cell(4,1,"Kelas | Jurusan ",0,0,'L');
   $this->Cell(3,1,": ".$_REQUEST['kelas']." | ".$_REQUEST['jur'],0,0,'L');
   $this->Ln(0.5);
   $this->Cell(3,1,'');
   $this->Cell(4,1,"Tahun Akademik ",0,0,'L');
   $this->Cell(3,1,": ".$_COOKIE['beetahun']." Semester : ".$_REQUEST['sem'],0,0,'L');
   $this->Ln(0.5);
   $this->Ln(1);
   $this->SetFont('Arial','B','9');
   $this->SetFillColor(192,192,192);

   $this->SetTextColor(0,0,0);
   $this->Cell(0.8,1,'No','LT',0,'C',1);
   $this->Cell(1.7,1,'NIS','LT',0,'C',1);
   $this->Cell(7.5,1,'Nama Siswa','LT',0,'C',1);
   $this->Cell(2.5,1,'Nilai','LT',0,'C',1);
   $this->Cell(1.5,1,'KKM','LTR',0,'C',1);

   $this->Ln();
}

function Footer(){
$sqlad = mysql_query("select * from cbt_admin");
$ad = mysql_fetch_array($sqlad);
$namsek = strtoupper($ad['XSekolah']);
$kepsek = $ad['XKepSek'];
$this->SetY(26.5);
   $this->Cell(2,1,'');
   $this->Cell(0,1,'Kepala Sekolah : ',0,0,'L');
   $this->Cell(0,1,'Guru  :                            ',0,0,'R');

	$this->SetY(28);
   	$this->Cell(2,1,'');
	$this->Cell(0,1, '('.$kepsek.')',0,0,'L');
	$this->Cell(0,1,'( ____________________ )              ',0,0,'R');
  }
}

$i = 0;
$nomz = 1;
if ($kelas != '') {
	$cekQuery1 = mysql_query("SELECT XNomerUjian, XNIK, XNamaSiswa from cbt_siswa where XKodeKelas = '$kelas' and XKodeJurusan = '$jurusan'");
} else {
	$cekQuery1 = mysql_query("SELECT XNomerUjian, XNIK, XNamaSiswa from cbt_siswa");
}

$per = mysql_query("SELECT * from cbt_mapel where XKodeMapel = '$mapel'");
$p = mysql_fetch_array($per);
$NilaiKKM = $p['XKKM'];
$tampilKKM = number_format($NilaiKKM, 2, ',', '.');

$pdf = new PDF('P','cm','A4');
$pdf->Open();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak('true',3);
$pdf->SetFont('Arial','','8');

if (!$dataReady) {
	$pdf->Ln(1);
	$pdf->Cell(0,1,'Data hasil ujian belum tersedia di Analisa Soal.',0,1,'L');
} else {
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

	  $pdf->Cell(0.8,1,$nomz,'LB',0,'C');
	  $pdf->Cell(1.7,1,$f['XNIK'],'LB',0,'C');
	  $pdf->Cell(7.5,1,$f['XNamaSiswa'],'LB',0,'L');
	  $pdf->Cell(2.5,1,$nilaiTampil,'LB',0,'C');
	  $pdf->Cell(1.5,1,$tampilKKM,'LBR',0,'C');
	  $pdf->Ln();
	  $nomz++;
	}
}

$pdf->Output();
?>
