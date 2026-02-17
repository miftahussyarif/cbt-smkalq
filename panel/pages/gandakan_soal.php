<?php
		if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
?>
<?php
include "../../config/server.php";
$tgl = date("Y-m-d");
$kodeSoalBaru = isset($_REQUEST['txt_namax']) ? mysql_real_escape_string($_REQUEST['txt_namax']) : '';
$kodeSoalSumber = isset($_REQUEST['txt_ujianx']) ? mysql_real_escape_string($_REQUEST['txt_ujianx']) : '';
$kodeMapel = isset($_REQUEST['txt_mapelx']) ? mysql_real_escape_string($_REQUEST['txt_mapelx']) : '';
$level = isset($_REQUEST['txt_levelx']) ? mysql_real_escape_string($_REQUEST['txt_levelx']) : '';
$kelas = isset($_REQUEST['txt_kelasx']) ? mysql_real_escape_string($_REQUEST['txt_kelasx']) : '';
$jurusan = isset($_REQUEST['txt_jurusanx']) ? mysql_real_escape_string($_REQUEST['txt_jurusanx']) : '';
$jumPilihan = isset($_REQUEST['txt_jawabx']) ? mysql_real_escape_string($_REQUEST['txt_jawabx']) : '5';
$jumPG = isset($_REQUEST['txt_jumsoalz1']) ? intval($_REQUEST['txt_jumsoalz1']) : 0;
$jumEsai = isset($_REQUEST['txt_jumsoalz2']) ? intval($_REQUEST['txt_jumsoalz2']) : 0;
$bobotPG = isset($_REQUEST['txt_bobotsoalz1']) ? mysql_real_escape_string($_REQUEST['txt_bobotsoalz1']) : '0';
$bobotEsai = isset($_REQUEST['txt_bobotsoalz2']) ? mysql_real_escape_string($_REQUEST['txt_bobotsoalz2']) : '0';

if ($kodeSoalBaru === '' || $kodeSoalSumber === '') {
	echo "0.Gagal: Kode soal sumber/tujuan tidak valid";
	exit;
}

$sqlcek = mysql_query("select * from cbt_paketsoal where XKodeSoal = '$kodeSoalBaru'");
$jumcek = mysql_num_rows($sqlcek);
if($jumcek<1){

			$jumsoal = $jumPG + $jumEsai;
				$sql = mysql_query("insert into cbt_paketsoal 
				(XKodeMapel,XLevel,XKodeSoal,XJumPilihan,XTglBuat,XGuru,XKodeKelas,XKodeJurusan,XJumSoal,XPilGanda,XEsai,XPersenPil,XPersenEsai) values 			
				('$kodeMapel','$level','$kodeSoalBaru','$jumPilihan','$tgl',			
				'$_COOKIE[beelogin]','$kelas','$jurusan','$jumsoal',
				'$jumPG','$jumEsai','$bobotPG','$bobotEsai')");
			if (!$sql) {
				echo "0.Gagal membuat paket soal: " . mysql_error();
				exit;
			}
			
			$sqlsoal = mysql_query("select * from cbt_soal where XKodeSoal = '$kodeSoalSumber' order by Urut asc");
			$jumsql = mysql_num_rows($sqlsoal);
			if($jumsql>0){
					$qUrut = mysql_query("SELECT IFNULL(MAX(Urut),0)+1 AS next_urut FROM cbt_soal");
					$rUrut = $qUrut ? mysql_fetch_assoc($qUrut) : array('next_urut' => 1);
					$nextUrut = isset($rUrut['next_urut']) ? intval($rUrut['next_urut']) : 1;
					$ok = 0;
					$gagal = 0;
					while($r = mysql_fetch_array($sqlsoal)){
					$urutBaru = $nextUrut++;
					$str_tanya = mysql_real_escape_string($r['XTanya']);
					$jawab1 = mysql_real_escape_string($r['XJawab1']);
					$jawab2 = mysql_real_escape_string($r['XJawab2']);
					$jawab3 = mysql_real_escape_string($r['XJawab3']);
					$jawab4 = mysql_real_escape_string($r['XJawab4']);
					$jawab5 = mysql_real_escape_string($r['XJawab5']);
					$gJ1 = mysql_real_escape_string($r['XGambarJawab1']);
					$gJ2 = mysql_real_escape_string($r['XGambarJawab2']);
					$gJ3 = mysql_real_escape_string($r['XGambarJawab3']);
					$gJ4 = mysql_real_escape_string($r['XGambarJawab4']);
					$gJ5 = mysql_real_escape_string($r['XGambarJawab5']);
					$audio = mysql_real_escape_string($r['XAudioTanya']);
					$video = mysql_real_escape_string($r['XVideoTanya']);
					$gambar = mysql_real_escape_string($r['XGambarTanya']);
					$kunci = mysql_real_escape_string($r['XKunciJawaban']);
					$jenis = intval($r['XJenisSoal']);
					$acakSoal = mysql_real_escape_string($r['XAcakSoal']);
					$acakOpsi = mysql_real_escape_string($r['XAcakOpsi']);
					$kategori = intval($r['XKategori']);
					$kodeKelasSoal = mysql_real_escape_string($r['XKodeKelas']);
					$levelSoal = mysql_real_escape_string($r['XLevel']);
					$ragu = mysql_real_escape_string($r['XRagu']);
					$agama = mysql_real_escape_string($r['XAgama']);
					$kodeSekolah = mysql_real_escape_string($r['XKodeSekolah']);
					$nomorSoal = intval($r['XNomerSoal']);

					$query = mysql_query("INSERT INTO cbt_soal 
						(Urut, XKodeMapel, XKodeSoal, XJenisSoal, XKodeKelas, XLevel, XNomerSoal, XRagu, XTanya, XAudioTanya, XVideoTanya, XGambarTanya,
						XJawab1, XJawab2, XJawab3, XJawab4, XJawab5, XGambarJawab1, XGambarJawab2, XGambarJawab3, XGambarJawab4, XGambarJawab5,
						XKunciJawaban, XKategori, XAcakSoal, XAcakOpsi, XAgama, XKodeSekolah)
						VALUES
						('$urutBaru', '$kodeMapel', '$kodeSoalBaru', '$jenis', '$kodeKelasSoal', '$levelSoal', '$nomorSoal', '$ragu', '$str_tanya', '$audio', '$video', '$gambar',
						'$jawab1', '$jawab2', '$jawab3', '$jawab4', '$jawab5', '$gJ1', '$gJ2', '$gJ3', '$gJ4', '$gJ5',
						'$kunci', '$kategori', '$acakSoal', '$acakOpsi', '$agama', '$kodeSekolah')");
					if ($query) {
						$ok++;
					} else {
						$gagal++;
					}
					}
					if ($ok > 0 && $gagal == 0) {
						echo "1.Soal Sukses di Gandakan ($ok soal)";
					} elseif ($ok > 0) {
						echo "1.Soal tergandakan sebagian ($ok sukses, $gagal gagal)";
					} else {
						echo "0.Gagal menggandakan soal: " . mysql_error();
					}
			} else {
				echo "0.Soal sumber tidak ditemukan";
			}
} else {
echo "2.Duplikasi Soal TIDAK BERHASIL, Kode Bank Soal SUDAH ADA";
}
?>
