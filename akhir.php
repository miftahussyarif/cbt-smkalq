<?php
require __DIR__ . '/config/server.php';
// ===============================
// Status Ujian XStatusUjian = 1 Aktif
// Status Ujian XStatusUjian = 0 BelumAktif
// Status Ujian XStatusUjian = 9 Selesai

$user = isset($_COOKIE['PESERTA']) ? $_COOKIE['PESERTA'] : '';
if ($user === '') {
	header('Location:login.php');
	exit;
}

$tgl = date("H:i:s");
$tgl2 = date("Y-m-d");

$xtokenujian = '';
$sqltoken = db_query(
	$db,
	"SELECT s.XTokenUjian FROM `cbt_siswa_ujian` s LEFT JOIN cbt_ujian u ON u.XKodeSoal = s.XKodeSoal WHERE s.XNomerUjian = :user AND s.XStatusUjian = '1' LIMIT 1",
	array(':user' => $user)
);
$st = db_fetch_one($sqltoken);
if ($st) {
	$xtokenujian = $st['XTokenUjian'];
}

$sqlgabung = db_query(
	$db,
	"SELECT * FROM `cbt_siswa_ujian` s LEFT JOIN cbt_jawaban j ON j.XUserJawab = s.XNomerUjian AND j.XTokenUjian = s.XTokenUjian LEFT JOIN cbt_siswa s1 ON s1.XNomerUjian = s.XNomerUjian WHERE s.XNomerUjian = :user AND s.XStatusUjian = '1' LIMIT 1",
	array(':user' => $user)
);

//=======================
$s0 = db_fetch_one($sqlgabung);
$xkodesoal = $s0 ? $s0['XKodeSoal'] : '';
$xnomerujian = $s0 ? $s0['XNomerUjian'] : '';
$xnik = $s0 ? $s0['XNIK'] : '';
$xkodeujian = $s0 ? $s0['XKodeUjian'] : '';
$xkodemapel = $s0 ? $s0['XKodeMapel'] : '';
$xkodekelas = $s0 ? $s0['XKodeKelas'] : '';
$xkodejurusan = $s0 ? $s0['XKodeJurusan'] : '';
$xsemester = $s0 ? $s0['XSemester'] : '';
if ($s0 && $s0['XTokenUjian'] !== '') {
	$xtokenujian = $s0['XTokenUjian'];
}

$sa = null;
$xjumsoal = 0;
$xjumpil = 0;
$xjumbenar = 0;
$xjumsalah = 0;
$nilai_tampil = "0,00";
$has_esai = false;

if ($xkodesoal !== '') {
	$sqlsoal = db_query(
		$db,
		"SELECT * FROM cbt_ujian WHERE XKodeSoal = :kodesoal LIMIT 1",
		array(':kodesoal' => $xkodesoal)
	);
	$sa = db_fetch_one($sqlsoal);
	if ($sa) {
		$xjumsoal = (int) $sa['XJumSoal'];
		$xjumpil = isset($sa['XPilGanda']) ? (int) $sa['XPilGanda'] : 0;
	}
}

if ($xjumsoal > 0 && $xkodesoal !== '') {
	$sqlnilai = db_query(
		$db,
		"SELECT * FROM cbt_paketsoal WHERE XKodeSoal = :kodesoal LIMIT 1",
		array(':kodesoal' => $xkodesoal)
	);
	$sqn = db_fetch_one($sqlnilai);
	$per_pil = $sqn ? $sqn['XPersenPil'] : 0;
	$per_esai = $sqn ? $sqn['XPersenEsai'] : 0;
	$xjumesai = $sqn && isset($sqn['XEsai']) ? (int) $sqn['XEsai'] : 0;
	if ($xjumesai < 1 && $sa && isset($sa['XEsai'])) {
		$xjumesai = (int) $sa['XEsai'];
	}

	$xjumbenar = (int) db_fetch_value(
		db_query(
			$db,
			"SELECT COUNT(XNilai) FROM cbt_jawaban WHERE XUserJawab = :user AND XJenisSoal = '1' AND XKodeSoal = :kodesoal AND XTokenUjian = :token AND XNilai = '1'",
			array(':user' => $user, ':kodesoal' => $xkodesoal, ':token' => $xtokenujian)
		)
	);
	$xjumsalah = $xjumpil - $xjumbenar;
	if ($xjumsalah < 0) {
		$xjumsalah = 0;
	}
	if ($xjumpil > 0) {
		$nilaix = ($xjumbenar / $xjumpil) * 100;
	} else {
		$nilaix = 0;
	}
	$nilai_tampil = number_format($nilaix, 2, ',', '.');
	$has_esai = ($xjumesai > 0);
	$setAY = isset($_COOKIE['beetahun']) ? $_COOKIE['beetahun'] : "2016/2017";

	//cek apakah nilai untuk token ini sudah ada atau tidak
	$sqlceknilai = (int) db_fetch_value(
		db_query(
			$db,
			"SELECT COUNT(*) FROM cbt_nilai WHERE XNomerUjian = :nomer AND XKodeSoal = :kodesoal AND XTokenUjian = :token AND XSemester = :semester AND XSetId = :setid AND XKodeMapel = :mapel AND XNIK = :nik",
			array(
				':nomer' => $xnomerujian,
				':kodesoal' => $xkodesoal,
				':token' => $xtokenujian,
				':semester' => $xsemester,
				':setid' => $setAY,
				':mapel' => $xkodemapel,
				':nik' => $xnik,
			)
		)
	);

	if ($sqlceknilai > 0) {
		db_query(
			$db,
			"UPDATE cbt_nilai SET XJumSoal = :jumsoal, XBenar = :benar, XSalah = :salah, XNilai = :nilai, XTotalNilai = :total WHERE XNomerUjian = :nomer AND XKodeSoal = :kodesoal AND XTokenUjian = :token AND XSemester = :semester AND XSetId = :setid AND XKodeMapel = :mapel AND XNIK = :nik",
			array(
				':jumsoal' => $xjumsoal,
				':benar' => $xjumbenar,
				':salah' => $xjumsalah,
				':nilai' => $nilaix,
				':total' => $nilaix,
				':nomer' => $xnomerujian,
				':kodesoal' => $xkodesoal,
				':token' => $xtokenujian,
				':semester' => $xsemester,
				':setid' => $setAY,
				':mapel' => $xkodemapel,
				':nik' => $xnik,
			)
		);
	} else {
		db_query(
			$db,
			"INSERT INTO cbt_nilai (XKodeUjian, XTokenUjian, XTgl, XJumSoal, XBenar, XSalah, XNilai, XKodeMapel, XKodeKelas, XKodeSoal, XNomerUjian, XNIK, XSemester, XSetId, XPersenPil, XPersenEsai, XTotalNilai)
			VALUES (:kodeujian, :token, :tgl, :jumsoal, :benar, :salah, :nilai, :kodemapel, :kodekelas, :kodesoal, :nomer, :nik, :semester, :setid, :per_pil, :per_esai, :total)",
			array(
				':kodeujian' => $xkodeujian,
				':token' => $xtokenujian,
				':tgl' => $tgl2,
				':jumsoal' => $xjumsoal,
				':benar' => $xjumbenar,
				':salah' => $xjumsalah,
				':nilai' => $nilaix,
				':kodemapel' => $xkodemapel,
				':kodekelas' => $xkodekelas,
				':kodesoal' => $xkodesoal,
				':nomer' => $xnomerujian,
				':nik' => $xnik,
				':semester' => $xsemester,
				':setid' => $setAY,
				':per_pil' => $per_pil,
				':per_esai' => $per_esai,
				':total' => $nilaix,
			)
		);
	}

	if ($xtokenujian !== '') {
		db_query(
			$db,
			"UPDATE cbt_siswa_ujian SET XStatusUjian = '9' WHERE XNomerUjian = :user AND XStatusUjian = '1' AND XTokenUjian = :token",
			array(':user' => $user, ':token' => $xtokenujian)
		);
	}
	db_query(
		$db,
		"UPDATE cbt_siswa_ujian SET XStatusUjian = '9', XLastUpdate = :tgl WHERE XNomerUjian = :user AND XStatusUjian = '1'",
		array(':tgl' => $tgl, ':user' => $user)
	);
}
?>
<style>
	.left {
		float: left;
		width: 70%;
		overflow: hidden;
	}

	.left img {
		width: 100%;
		height: auto;
		display: block;
		object-fit: cover;
	}

	.right1 {
		float: right;
		width: 30%;
		background-color: #333333;
		height: 101px;
		color: #FFFFFF;
		font-size: 13px;
		font-style: normal;
		font-weight: normal;
	}

	.user {
		color: #FFFFFF;
		font-size: 15px;
		font-style: normal;
		font-weight: bold;
		top: -20px;
	}

	.log {
		color: #3799c2;
		font-size: 11px;
		font-style: normal;
		font-weight: bold;
		top: -20px;
	}

	.group:after {
		content: "";
		display: table;
		clear: both;

	}

	.visible {
		display: block !important;
	}

	.hidden {
		display: none !important;
	}

	.foto {
		height: 80px;
	}

	.buntut {
		width: 100%;
		bottom: 0px;
		position: absolute;
	}

	@media screen and (max-width: 780px) {

		/* jika screen maks. 780 right turun */
		/*    .left, */
		.left,
		.right1 {
			float: none;
			width: auto;
			margin-top: 0px;
			height: 101px;
			color: #FFFFFF;
			display: block;
		}

		.foto {
			height: 80px;
		}

		.buntut {
			width: 100%;
			bottom: 0px;
			position: absolute;
		}
	}

	@media screen and (max-width: 400px) {

		/* jika screen maks. 780 right turun */
		/*    .left, */
		.left {
			width: auto;
			height: 91px;
		}

		.right1 {
			float: none;
			width: auto;
			margin-top: 0px;
			height: 60px;
			color: #FFFFFF;
		}

		.foto {
			height: 60px;
		}

		.buntut {
			width: 100%;
			bottom: 0px;
			position: absolute;
		}
	}
</style>

<!DOCTYPE html
	PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Untitled Document</title>
</head>
<?php
require_once __DIR__ . "/config/server.php";
$sql = db_query($db, "SELECT * FROM cbt_admin LIMIT 1", array());
$r = db_fetch_one($sql);
if (!$r) {
	$r = array('XWarna' => '', 'XBanner' => '');
}
?>

<body class="font-medium" style="background-color:#c9c9c9">
	<header style="background-color:<?php echo "{$r['XWarna']}"; ?>">
		<div class="group">
			<div class="left" style="background-color:<?php echo "{$r['XWarna']}"; ?>"><a href=" "><img
						src="images/<?php echo "{$r['XBanner']}"; ?>" style=" margin-left:0px;"></a>
			</div>
			<div class="right1">
				<table width="100%" border="0" style="margin-top:10px">
					<tr>
						<td rowspan="3" width="90px" align="center"><img src="images/avatar.gif"
								style=" margin-left:0px;" class="foto"></td>
						<td>Terima Kasih</td>
					</tr>
					<tr>
						<td><span class="user">Siswa Peserta Ujian</span></td>
					</tr>
					<tr>
						<td><span class="log"><a href="logout.php">Logout</a><span></td>
					</tr>
				</table>
			</div>
		</div>
	</header>

	<link rel="stylesheet" href="css/bootstrap2.min.css">
	<link href="css/klien.css" rel="stylesheet">
	<!--	<link href="css/main.css" rel="stylesheet">
	<link rel="stylesheet" href="css/bootstrap.min.css"> !-->
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script>


	<div class="main-content">
		<div class="page-column">

			<div class="col-md-4 col-md-offset-4 login-wrapper" style="float:inherit">
				<div class="panel panel-default" style="margin-top:0px">
					<div class="panel-heading" style="font-size:22px; font-weight:bold">
						Konfirmasi Tes
					</div>

					<div class="inner-content" style="height:280px">
						<div class="form-horizontal" style="margin-top:0px"><br>


							<div class="inner-content">
								<div class="wysiwyg-content">
									<p>
										Terimakasih telah berpartisipasi dalam tes ini.<br>
										Silahkan klik tombol LOGOUT untuk mengakhiri test.
									</p>
									<div style="margin-top:10px; padding:10px; border:1px solid #ddd; background:#f7f7f7;">
										<div style="font-size:16px; font-weight:bold; margin-bottom:5px;">Hasil Pilihan Ganda</div>
										<?php if (!$has_esai) { ?>
											<div>Nilai: <strong><?php echo $nilai_tampil; ?></strong></div>
										<?php } ?>
										<div>Benar: <strong><?php echo (int)$xjumbenar; ?></strong></div>
										<div>Salah: <strong><?php echo (int)$xjumsalah; ?></strong></div>
										<div style="margin-top:6px; font-size:12px; color:#666;">Logout otomatis dalam 30 detik.</div>
									</div>
								</div>
							</div>
							<div class="panel-footer">
								<div class="row">
									<div><br /><a href="logout.php">
											<button type="submit" class="btn btn-success btn-block"
												data-dismiss="modal">LOGOUT</button>
									</div>
								</div>
							</div>



						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		setTimeout(function() {
			window.location.href = "logout.php";
		}, 30000);
	</script>
</body>

</html>
