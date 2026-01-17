<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
?>
<?php
// menggunakan class phpExcelReader
include "excel_reader2.php";
require_once __DIR__ . "/../../config/server.php";

// koneksi ke mysql
//include "../../cbt_con.php";
$xkodemapel = "GAL1";
$xkodesoal = "XGAL1SOAL2";

if (!isset($_FILES['userfile']['tmp_name']) || $_FILES['userfile']['tmp_name'] === '') {
	echo "<h3>File upload tidak ditemukan.</h3>";
	exit;
}

// membaca file excel yang diupload
$data = new Spreadsheet_Excel_Reader($_FILES['userfile']['tmp_name']);

// membaca jumlah baris dari data excel
$baris = (int) $data->rowcount($sheet_index=0);

// nilai awal counter untuk jumlah data yang sukses dan yang gagal diimport
$sukses = 0;
$gagal = 0;

// import data excel mulai baris ke-2 (karena baris pertama adalah nama kolom)
$stmt = $db->prepare("INSERT INTO cbt_kelas (XKodeLevel, XLevelKelas, XStatusKelas) VALUES (?, ?, '1')");
for ($i=2; $i<=$baris; $i++)
{
  // membaca data soalid (kolom ke-1 FIELD)
  $fieldz = $data->val($i, 1);
  // membaca data pertanyaan (kolom ke-2 R)
  $xlevel = trim($data->val($i, 1));
  $xkelas = trim($data->val($i, 2));
  if ($xlevel === '' && $xkelas === '') {
	  continue;
  }
 
		  // setelah data dibaca, sisipkan ke dalam tabel mhs
		  $hasil = $stmt->execute(array($xlevel, $xkelas));
  if ($hasil) {
	  $sukses++;
  } else {
	  $gagal++;
  }
  }
  // jika proses insert data sukses, maka counter $sukses bertambah
  // jika gagal, maka counter $gagal yang bertambah


// tampilan status sukses dan gagal
echo "<h3>Proses import data selesai.</h3>";
echo "<p>Jumlah data yang sukses diimport : ".$sukses."<br>";
echo "Jumlah data yang gagal diimport : ".$gagal."</p>";

?>
