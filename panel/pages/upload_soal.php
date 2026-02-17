<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
?>

<?php
if(isset($_REQUEST['modul'])){
	if($_REQUEST['modul']=="upl_kelas"){
	$kata = "Data Kelas"; }
	elseif($_REQUEST['modul']=="upl_mapel"){
	$kata = "Data Mata Pelajaran"; }
	elseif($_REQUEST['modul']=="upl_siswa"){
	$kata = "Data Siswa"; }
	elseif($_REQUEST['modul']=="upl_soal"){
	$kata = "Data Soal"; }
	elseif($_REQUEST['modul']=="uploadsoal"){
	$kata = "Data Soal"; }	
}
?>
 <!-- /.row -->
            <div class="row">
                <div class="col-lg-10" style="margin-top:10px;">
                    <div class="panel panel-green">
                        <div class="panel-heading">
<?php echo "<a href=?modul=daftar_soal&soal=$_REQUEST[soal]><button type='button' class='btn btn-default'><i class='fa fa-arrow-left'></i> Kembali ke Bank Soal</button></a>"; ?>	
                           Download File Excel (Template Data Soal) 	
                        </div>
                        <div class="panel-body">
<div style="width: 20%; float:left">
   <a href="../../file-excel/bee_soal_temp.xls" target="_blank"><img src="images/xls.png" style=" width:90%; max-width:100px;padding-right:10px;"/></a>
</div>

<div style="width: 80%; float:right">
   Silahkan Klik logo Excel disamping, untuk <a href="../../file-excel/bee_soal_temp.xls" target="_blank"> download </a> file excel database soal. 
   <br />Jangan ada inputan apapun setelah nomer terakhir. Karena akan dibaca dan diacak oleh sistem. <p>Setelah selesai edit, Upload kembali untuk ditransfer ke
   database melalui tool dibawah ini. 
   
</div>
                        </div>
                        <div class="panel-footer">
                            CBT SMK AL QODIRIYAH 
                        </div>
                    </div>
                    <!-- /.col-lg-4 -->
                </div>
            </div>
            <!-- /.row -->
            
            
              <div class="row">
                <div class="col-lg-10" style="margin-top:10px;">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Upload Template Excel - Soal
                        </div>
                        <div class="panel-body">
						<form method="post" enctype="multipart/form-data" action="<?php echo "?modul=uploadsoal"; ?>">
                        File Excel Daftar Soal : 
                        <table border="0" width="78%" cellpadding="20px" cellspacing="20px"><tr><td width="30%">
                        <input name="userfile" type="file" class="btn btn-default" style="width:250px">
                        <input name="txt_mapel" type="hidden" value="<?php echo $_REQUEST['mapel']; ?>">
                        <input name="soal" type="hidden" value="<?php echo $_REQUEST['soal']; ?>">
                        </td><td>

                        &nbsp;<input name="upload" type="submit" value="Import"  class="btn btn-info" style="margin-top:0px">
                        </td></tr></table>
                        </form>
                        <div style="margin-top:10px;">Persentase Proses Upload <? echo $kata; ?> </div>
<!-- Progress bar holder -->
<div id="progress" style="width:75%; border:1px solid #ccc; padding:5px; margin-top:10px; height:33px"></div>
<!-- Progress information -->
<div id="information" style="width"></div>

<?php
if($_REQUEST['modul']=="uploadsoal"){
// menggunakan class phpExcelReader
include "excel_reader2.php";
$xkodemapel = "$_REQUEST[txt_mapel]";
$xkodesoal = "$_REQUEST[soal]";
//$xkodekelas = "$_REQUEST[txt_level]";
// membaca file excel yang diupload
$data = new Spreadsheet_Excel_Reader($_FILES['userfile']['tmp_name']);

// membaca jumlah baris dari data excel
$baris = $data->rowcount($sheet_index=0);

// nilai awal counter untuk jumlah data yang sukses dan yang gagal diimport
$sukses = 0;
$gagal = 0;
$dilewati = 0;
$importErrors = array();

$urutAwal = 1;
$cekUrut = mysql_query("SELECT MAX(Urut) AS max_urut FROM cbt_soal");
if ($cekUrut) {
    $rowUrut = mysql_fetch_assoc($cekUrut);
    if ($rowUrut && isset($rowUrut['max_urut']) && $rowUrut['max_urut'] !== null) {
        $urutAwal = ((int) $rowUrut['max_urut']) + 1;
    }
}

bee_log('INFO', 'SOAL_IMPORT_REQUEST', 'Mulai import soal dari excel', array(
    'kodesoal' => $xkodesoal,
    'mapel' => $xkodemapel,
    'baris_excel' => $baris
));

// import data excel mulai baris ke-2 (karena baris pertama adalah nama kolom)
for ($i=3; $i<=$baris; $i++)
{
  // membaca data soalid (kolom ke-1 FIELD)
  $fieldz = $data->val($i, 1);
  // membaca data pertanyaan (kolom ke-2 R)
  $xnomer = $data->val($i, 1);
  $xjen 		= $data->val($i, 2);
  $xkat 		= $data->val($i, 3);
  $xacak 		= $data->val($i,4);
  $xtanya 		= $data->val($i, 5);
  $xjawab1 		= $data->val($i, 6);
  $xfilejawab1 	= $data->val($i, 7);
  $xjawab2 		= $data->val($i, 8);
  $xfilejawab2 	= $data->val($i, 9);  
  $xjawab3 		= $data->val($i, 10);
  $xfilejawab3 	= $data->val($i, 11);  
  $xjawab4 		= $data->val($i, 12);
  $xfilejawab4 	= $data->val($i, 13);  
  $xjawab5 		= $data->val($i, 14);
  $xfilejawab5 	= $data->val($i, 15);  
  $xaudio 		= $data->val($i, 16);
  $xvideo 		= $data->val($i, 17);
  $xgambar 		= $data->val($i, 18);
  $xjwban 		= $data->val($i, 19);
  $xacakopsi	= $data->val($i, 20);
  $xagama		= $data->val($i, 21);  
  
  $xtanyaTrim = trim($xtanya);
  if ($xtanyaTrim === '') {
      $dilewati++;
      continue;
  }

  $xnomer = (int) $xnomer;
  if ($xnomer < 1) {
      $xnomer = $i - 2;
  }

  $xjen = (int) $xjen;
  if ($xjen < 1) {
      $xjen = 1;
  }
  $xkat = (int) $xkat;
  if ($xkat < 1) {
      $xkat = 1;
  }

  $xacak = strtoupper(trim($xacak));
  if ($xacak !== 'A' && $xacak !== 'T') {
      $xacak = 'A';
  }

  $xacakopsi = strtoupper(trim($xacakopsi));
  if ($xacakopsi === '') {
      $xacakopsi = 'N';
  }

  $xjwban = strtoupper(trim($xjwban));
  if ($xjwban === '') {
      $xjwban = 'A';
  }

  $xtanya = mysql_real_escape_string(str_replace("'", "`", $xtanyaTrim));
  $xjawab1 = mysql_real_escape_string(str_replace("'", "`", $xjawab1));
  $xjawab2 = mysql_real_escape_string(str_replace("'", "`", $xjawab2));
  $xjawab3 = mysql_real_escape_string(str_replace("'", "`", $xjawab3));
  $xjawab4 = mysql_real_escape_string(str_replace("'", "`", $xjawab4));
  $xjawab5 = mysql_real_escape_string(str_replace("'", "`", $xjawab5));
  $xfilejawab1 = mysql_real_escape_string(trim($xfilejawab1));
  $xfilejawab2 = mysql_real_escape_string(trim($xfilejawab2));
  $xfilejawab3 = mysql_real_escape_string(trim($xfilejawab3));
  $xfilejawab4 = mysql_real_escape_string(trim($xfilejawab4));
  $xfilejawab5 = mysql_real_escape_string(trim($xfilejawab5));
  $xaudio = mysql_real_escape_string(trim($xaudio));
  $xvideo = mysql_real_escape_string(trim($xvideo));
  $xgambar = mysql_real_escape_string(trim($xgambar));
  $xagama = mysql_real_escape_string(trim($xagama));
  $xkodemapelEsc = mysql_real_escape_string($xkodemapel);
  $xkodesoalEsc = mysql_real_escape_string($xkodesoal);
  $urutInsert = (int) $urutAwal;

  $query = "INSERT INTO cbt_soal (Urut, XNomerSoal, XKodeMapel, XKodeSoal, XTanya, XJawab1, XGambarJawab1, XJawab2, XGambarJawab2, XJawab3, XGambarJawab3, 
          XJawab4, XGambarJawab4, XJawab5, XGambarJawab5, XAudioTanya, XVideoTanya, XGambarTanya, XKunciJawaban, XJenisSoal, XKategori, XAcakSoal, XAcakOpsi, XAgama) 
          VALUES ('$urutInsert', '$xnomer', '$xkodemapelEsc', '$xkodesoalEsc', '$xtanya', '$xjawab1', '$xfilejawab1', '$xjawab2', '$xfilejawab2', '$xjawab3',
          '$xfilejawab3', '$xjawab4', '$xfilejawab4', '$xjawab5', '$xfilejawab5', '$xaudio', '$xvideo', '$xgambar',
          '$xjwban', '$xjen', '$xkat', '$xacak', '$xacakopsi', '$xagama')";

  $hasil = mysql_query($query);
  if ($hasil) {
      $sukses++;
      $urutAwal++;
  } else {
      $gagal++;
      $err = mysql_error();
      $importErrors[] = "Baris $i gagal: $err";
      bee_log('ERROR', 'SOAL_IMPORT_ROW_FAILED', 'Gagal import baris soal dari excel', array(
          'baris' => $i,
          'urut_target' => $urutInsert,
          'nomor_soal' => $xnomer,
          'kodesoal' => $xkodesoal,
          'mapel' => $xkodemapel,
          'db_error' => $err
      ));
  }
		 
			  // jika proses insert data sukses, maka counter $sukses bertambah
			  // jika gagal, maka counter $gagal yang bertambah
			
}
  
	// Calculate the percentation
	$processedRows = max(0, $baris - 2);
	$doneRows = $sukses + $gagal + $dilewati;
	if ($processedRows > 0) {
	    $percent = intval(($doneRows / $processedRows) * 100);
	    if ($percent > 100) {
	        $percent = 100;
	    }
	} else {
	    $percent = 100;
	}
	$percent = $percent . "%";
    
    // Javascript for updating the progress bar and information
    echo '<script language="javascript">
    document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-image:url(images/pbar-ani1.gif);\">&nbsp;</div>";
    document.getElementById("information").innerHTML="  Proses Entri : Soal ... <b>'.$doneRows.'</b> row(s) of <b>'. $processedRows.'</b> processed.";
    </script>';
// This is for the buffer achieve the minimum size in order to flush data
    echo str_repeat(' ',1024*64);
    

// Send output to browser immediately
    flush();
// Tell user that the process is completed
   echo '<script language="javascript">document.getElementById("information").innerHTML=" Proses update database Bank Soal : Completed"</script>';

   bee_log('INFO', 'SOAL_IMPORT_SUMMARY', 'Import soal excel selesai', array(
       'kodesoal' => $xkodesoal,
       'mapel' => $xkodemapel,
       'total_baris' => $baris,
       'sukses' => $sukses,
       'gagal' => $gagal,
       'dilewati_kosong' => $dilewati
   ));
  
//  } end if jika tanya = kosong


  // jika proses insert data sukses, maka counter $sukses bertambah
  // jika gagal, maka counter $gagal yang bertambah


// tampilan status sukses dan gagal
?>
<div style="width:75%; margin-top:10px">
    <div class="alert alert-success">
    <?php
    echo "<p>Jumlah data yang sukses diimport : ".$sukses."<br>";
    echo "Jumlah data yang dilewati (pertanyaan kosong) : ".$dilewati."</p>";
    ?>
    </div>
    
    <?php
        if($gagal>0){
        ?>
        <div class="alert alert-danger">
        <?php
        echo "Jumlah data yang gagal diimport : ".$gagal."</p>";
        $maxPreview = min(5, count($importErrors));
        if ($maxPreview > 0) {
            echo "<p>Detail error (maks 5):<br>";
            for ($x = 0; $x < $maxPreview; $x++) {
                echo htmlspecialchars($importErrors[$x]) . "<br>";
            }
            echo "Lihat menu Log Aktivitas untuk detail lengkap.</p>";
        }
        ?></div>
        <?php
        }
    }
    ?>
    
	</div>
</div>

                    </div>
                    <!-- /.col-lg-4 -->
                </div>
            </div>
            <!-- /.row -->
            


<script src="../../../js/jquery.js"></script>
