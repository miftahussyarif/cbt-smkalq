<?php ini_set('max_execution_time',0); 
include "../../config/ipserver.php";
$PCSERVER = $host_name;
?>
<form method="post" enctype="multipart/form-data" action="<?php echo "?modul=sinkronsatu"; ?>">
                        <br>
                        <table border="0" width="250px" cellpadding="20px" cellspacing="20px"><tr><td>
                        &nbsp;
                        <input name="upload" type="submit" value="START SYNC"  class="btn btn-danger" style="margin-top:0px">
                        </td><td>
                        <a href="#" data-toggle='modal' data-target='#myInfoz'><button class="btn btn-info">Refresh Status</button></a>
                        
                        </td></tr></table>
                        </form>
<style>
.an ul{
    list-style-type: none;
    margin: 0;
    padding: 0;
    overflow: hidden;
}

.an li {
    float: left;
	width:50%; list-style:none;
}

</style>
		<script>
        setTimeout(myFunction, 9000)
        </script>
        
        
<!-- Sinkron Siswa-->                        
<div style="width:75%; background-color:#28b2bc; color:#FFFFFF; padding:15px; margin-top:10px; font-size:22px">Sync Progress Status</div>
<!-- Progress bar holder -->
<div style="margin-top:10px;width:77%;"><ul class="an"><li style="margin-left:-40px;">DATA 1</li><li style="text-align:right; display:none" id="statusdata1">Selesai</li></ul></div>
<br>
<div id="progress" style="width:75%; border:1px solid #ccc; padding:5px; margin-top:10px; height:33px"></div>
<!-- Progress information -->
<div id="information" style="width"></div>


<!-- Sinkron Soal -->
<!-- Progress bar holder -->
<hr style="width:75%; text-align:left; margin-left:0px; padding:0px">
<div style="margin-top:10px;width:77%;"><ul class="an"><li style="margin-left:-40px;">DATA 2</li><li style="text-align:right; display:none" id="statusdata2">Selesai</li></ul></div>
<br>
<div id="progress2" style="width:75%; border:1px solid #ccc; padding:5px; margin-top:10px; height:33px"></div>
<!-- Progress information -->
<div id="information2" style="width"></div>

<!-- Sinkron Mapel -->
<hr style="width:75%; text-align:left; margin-left:0px">
<div style="margin-top:10px;width:77%;"><ul class="an"><li style="margin-left:-40px;">DATA 3</li><li style="text-align:right; display:none" id="statusdata3">Selesai</li></ul></div>
<br>
<div id="progress3" style="width:75%; border:1px solid #ccc; padding:5px; margin-top:10px; height:33px"></div>
<!-- Progress information -->
<div id="information3" style="width"></div>

<!-- Sinkron Gambar -->
<hr style="width:75%; text-align:left; margin-left:0px">
<!-- Progress bar holder -->
<div style="margin-top:10px;width:77%;"><ul class="an"><li style="margin-left:-40px;">DATA 4</li><li style="text-align:right; display:none" id="statusdataG">Selesai</li></ul></div>
<br>
<div id="progressG" style="width:75%; border:1px solid #ccc; padding:5px; margin-top:10px; height:33px"></div>
<!-- Progress information -->
<div id="informationG" style="width"></div>

<!-- Sinkron Audio-->                        
<!-- Progress bar holder -->
<hr style="width:75%; text-align:left; margin-left:0px">

<div style="margin-top:10px;width:77%;"><ul class="an"><li style="margin-left:-40px;">DATA 5</li><li style="text-align:right; display:none" id="statusdataA">Selesai</li></ul></div>
<br>
<div id="progressA" style="width:75%; border:1px solid #ccc; padding:5px; margin-top:10px; height:33px"></div>
<!-- Progress information -->
<div id="informationA" style="width"></div>

<!-- Sinkron Video-->                        
<!-- Progress bar holder -->
<hr style="width:75%; text-align:left; margin-left:0px">

<div style="margin-top:10px;width:77%;"><ul class="an"><li style="margin-left:-40px;">DATA 6</li><li style="text-align:right; display:none" id="statusdataV">Selesai</li></ul></div>
<br>
<div id="progressV" style="width:75%; border:1px solid #ccc; padding:5px; margin-top:10px; height:33px"></div>
<!-- Progress information -->
<div id="informationV" style="width"></div>


<hr style="width:75%; text-align:left; margin-left:0px">

<?php
if(isset($_REQUEST['modul']) && $_REQUEST['modul']=="sinkronsatu"){
require_once __DIR__ . "/../../config/server.php";
require_once __DIR__ . "/../../config/server_pusat.php";

/* === PAKET SOAL DATA 1== */

$db->exec("truncate table cbt_paketsoal");
$i = 1;

//document.getElementById("information").innerHTML="  Sikronisasi : DATA 1 ... <b>'.$i.'</b> dari <b>'. $baris.'</b> Selesai.";
		$barisStmt = db_query($db_pusat, "select count(*) from cbt_paketsoal", array());
		$baris = (int) db_fetch_value($barisStmt);
		//echo "jumlah total paket data : $baris";
		
		$sqlcek = db_query($db_pusat, "select * from cbt_paketsoal order by Urut", array());
		$insertPaket = $db->prepare("insert into cbt_paketsoal 
					(XKodeMapel,XLevel,XKodeSoal,XJumPilihan,XTglBuat,XGuru,XKodeKelas,XKodeJurusan,XJumSoal,XPilGanda,XEsai,XPersenPil,XPersenEsai,XStatusSoal) values 			
					(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		while($r=$sqlcek->fetch()){
		//for ($i=1; $i<=$baris; $i++){
					$insertPaket->execute(array(
						$r['XKodeMapel'],
						$r['XLevel'],
						$r['XKodeSoal'],
						$r['XJumPilihan'],
						$r['XTglBuat'],
						$r['XGuru'],
						$r['XKodeKelas'],
						$r['XKodeJurusan'],
						$r['XJumSoal'],
						$r['XPilGanda'],
						$r['XEsai'],
						$r['XPersenPil'],
						$r['XPersenEsai'],
						$r['XStatusSoal'],
					));

		$percent = $baris > 0 ? intval($i/$baris * 100)."%" : "0%";
			// Javascript for updating the progress bar and information
			echo '<script language="javascript">
			document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-image:url(images/pbar-ani1.gif);\">&nbsp;</div>";
		    document.getElementById("information").innerHTML="  Download Berkas Paket Soal <b>'.$i.'</b> row(s) of <b>'. $baris.'</b> ... '.$percent.'  Completed.";			
			</script>';
		// This is for the buffer achieve the minimum size in order to flush data
			echo str_repeat(' ',1024*64);
		// Send output to browser immediately
			flush();
		// Tell user that the process is completed
		  
		
		$i++;
		} ?>
		<script>document.getElementById("statusdata1").style.display="block";
        setTimeout(myFunction, 9000)
        </script>
        
<?php 	

/* === CBT SOAL DATA 2== */
	
 $db->exec("truncate table cbt_soal");
$i = 1;

		$barisStmt = db_query($db_pusat, "select count(*) from cbt_soal", array());
		$baris = (int) db_fetch_value($barisStmt);
		//echo "jumlah total paket data : $baris";
		
		$sqlcek = db_query($db_pusat, "select * from cbt_soal order by Urut", array());
		$insertSoal = $db->prepare("INSERT INTO cbt_soal (XNomerSoal, XKodeMapel, XKodeSoal, XTanya, XJawab1, XGambarJawab1, XJawab2,XGambarJawab2, 
							 XJawab3,XGambarJawab3, XJawab4,XGambarJawab4, XJawab5,XGambarJawab5, XAudioTanya, XVideoTanya, XGambarTanya, XKunciJawaban,XJenisSoal,XAcakSoal,
							 XAcakOpsi,XKategori) 
							 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		while($r=$sqlcek->fetch()){
		//for ($i=1; $i<=$baris; $i++){
					
							 $insertSoal->execute(array(
								 $r['XNomerSoal'],
								 $r['XKodeMapel'],
								 $r['XKodeSoal'],
								 $r['XTanya'],
								 $r['XJawab1'],
								 $r['XGambarJawab1'],
								 $r['XJawab2'],
								 $r['XGambarJawab2'],
								 $r['XJawab3'],
								 $r['XGambarJawab3'],
								 $r['XJawab4'],
								 $r['XGambarJawab4'],
								 $r['XJawab5'],
								 $r['XGambarJawab5'],
								 $r['XAudioTanya'],
								 $r['XVideoTanya'],
								 $r['XGambarTanya'],
								 $r['XKunciJawaban'],
								 $r['XJenisSoal'],
								 $r['XAcakSoal'],
								 $r['XAcakOpsi'],
								 $r['XKategori'],
							 ));
 
			// Calculate the percentation
			$percent = $baris > 0 ? intval($i/$baris * 100)."%" : "0%";

//		$percent = intval($i/$baris * 100)."%";
//			document.getElementById("information2").innerHTML="  Sikronisasi : DATA 1 ... <b>'.$i.'</b> dari <b>'. $baris.'</b> Selesai.";			

			// Javascript for updating the progress bar and information
			echo '<script language="javascript">
			document.getElementById("progress2").innerHTML="<div style=\"width:'.$percent.';background-image:url(images/pbar-ani1.gif);\">&nbsp;</div>";
		    document.getElementById("information2").innerHTML="  Download Berkas Soal <b>'.$i.'</b> row(s) of <b>'. $baris.'</b> ... '.$percent.'  completed.";			
			</script>';
		// This is for the buffer achieve the minimum size in order to flush data
			echo str_repeat(' ',1024*64);
		// Send output to browser immediately
			flush();
		// Tell user that the process is completed

		//}
				
		$i++;
		}
		?>
		<script>document.getElementById("statusdata2").style.display="block";
        setTimeout(myFunction, 9000)
        </script>

       
<?php 		

/* === CBT MAPEL DATA 3== */

 $db->exec("truncate table cbt_mapel");
$i = 1;

		$barisStmt = db_query($db_pusat, "select count(*) from cbt_mapel", array());
		$baris = (int) db_fetch_value($barisStmt);
		//echo "jumlah total paket data : $baris";
		
		$sqlcek = db_query($db_pusat, "select * from cbt_mapel order by Urut", array());
		$insertMapel = $db->prepare("INSERT INTO cbt_mapel ( XKodeMapel, XNamaMapel,XPersenUH,XPersenUTS,XPersenUAS,XKKM,XMapelAgama,XKodeSekolah) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
		while($r=$sqlcek->fetch()){
		//for ($i=1; $i<=$baris; $i++){
					$insertMapel->execute(array(
						$r['XKodeMapel'],
						$r['XNamaMapel'],
						$r['XPersenUH'],
						$r['XPersenUTS'],
						$r['XPersenUAS'],
						$r['XKKM'],
						$r['XMapelAgama'],
						$r['XKodeSekolah'],
					));
					
			$percent = $baris > 0 ? intval($i/$baris * 100)."%" : "0%";
//			document.getElementById("information2").innerHTML="  Sikronisasi : DATA 1 ... <b>'.$i.'</b> dari <b>'. $baris.'</b> Selesai.";			

			// Javascript for updating the progress bar and information
			echo '<script language="javascript">
			document.getElementById("progress3").innerHTML="<div style=\"width:'.$percent.';background-image:url(images/pbar-ani1.gif);\">&nbsp;</div>";
		    document.getElementById("information3").innerHTML="  Download Berkas Mata Pelajaran <b>'.$i.'</b> row(s) of <b>'. $baris.'</b> ... '.$percent.'  completed.";			
			</script>';
		// This is for the buffer achieve the minimum size in order to flush data
			echo str_repeat(' ',1024*64);
		// Send output to browser immediately
			flush();
		// Tell user that the process is completed
		
		$i++;
		}	?>
		<script>document.getElementById("statusdata3").style.display="block";
        setTimeout(myFunction, 9000)
        </script>
	

<script src="../../../js/jquery.js"></script>
<?php

$sql1 = db_query($db_pusat, "select XNamaFile from cbt_upload_file where XFolder = 'pictures' group by XNamaFile", array());
$jum1Stmt = db_query($db_pusat, "select count(distinct XNamaFile) from cbt_upload_file where XFolder = 'pictures'", array());
$jum1 = (int) db_fetch_value($jum1Stmt);

$sql2 = db_query($db_pusat, "select XNamaFile from cbt_upload_file where XFolder = 'audio' group by XNamaFile", array());
$jum2Stmt = db_query($db_pusat, "select count(distinct XNamaFile) from cbt_upload_file where XFolder = 'audio'", array());
$jum2 = (int) db_fetch_value($jum2Stmt);

$sql3 = db_query($db_pusat, "select XNamaFile from cbt_upload_file where XFolder = 'video' group by XNamaFile", array());
$jum3Stmt = db_query($db_pusat, "select count(distinct XNamaFile) from cbt_upload_file where XFolder = 'video'", array());
$jum3 = (int) db_fetch_value($jum3Stmt);
//echo "Jumlah : $jum1+$jum2+$jum3 = $total<br>";


/* ############## Mulai Download ############ */

/* === GambarTanya DATA 4 == */

$i = 0;
while($r=$sql1->fetch()){
	if(!$r['XNamaFile']==''){
	$filese = "{$r['XNamaFile']}";	
	
$i++;
//echo "$i. file |$filese|<br>";

//$filese = "soal1.jpg";
//$url = 'http://SMP-BINAKARYA/beesmart2/pictures/'.$filese; // working
//$file = fopen(dirname(__FILE__) . '/images/'.$filese, 'w+');
//$url = 'http://demo.cbtbeesmart.com/pictures/'.$filese; online
//$file = fopen('../../pictures/'.$filese, 'w+');
$url = 'http://'.$PCSERVER.'/beesmart/pictures/'.$filese; // working
$file = fopen('../../pictures/'.$filese, 'w+');
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_TIMEOUT, 0); 
// Update as of PHP 5.4 array() can be written []
curl_setopt_array($curl, [
    CURLOPT_URL            => $url,
//  CURLOPT_BINARYTRANSFER => 1, --- No effect from PHP 5.1.3
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_FILE           => $file,
    CURLOPT_TIMEOUT        => 50,
    CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'
]);

$response = curl_exec($curl);

if($response === false) {
    // Update as of PHP 5.3 use of Namespaces Exception() becomes \Exception()
    throw new \Exception('Curl error: ' . curl_error($curl));
}

$response; // Do something with the response.
$percent = $jum1 > 0 ? intval($i/$jum1 * 100)."%" : "0%";
// Javascript for updating the progress bar and information
			echo '<script language="javascript">
			document.getElementById("progressG").innerHTML="<div style=\"width:'.$percent.';background-image:url(images/pbar-ani1.gif);\">&nbsp;</div>";
		    document.getElementById("informationG").innerHTML="  Download Berkas Gambar Soal <b>'.$i.'</b> row(s) of <b>'. $jum1.'</b> ... '.$percent.'  completed.";			
			</script>';
		// This is for the buffer achieve the minimum size in order to flush data
			echo str_repeat(' ',1024*64);
		// Send output to browser immediately
			flush();

	}
}

		?>
		<script>document.getElementById("statusdataG").style.display="block";
        setTimeout(myFunction, 9000)
        </script>

       
<?php 		

/* === Audio Tanya Data 6 == */
$i = 0;
while($r=$sql2->fetch()){
	if(!$r['XNamaFile']==''){
	$filese = "{$r['XNamaFile']}";		
	
$i++;
//echo "$i. file |$filese|<br>";

//$filese = "soal1.jpg";
//$url = 'http://SMP-BINAKARYA/beesmart2/pictures/'.$filese; // working
//$file = fopen(dirname(__FILE__) . '/images/'.$filese, 'w+');
$url = 'http://'.$PCSERVER.'/beesmart/audio/'.$filese; // working
$file = fopen('../../audio/'.$filese, 'w+');
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_TIMEOUT, 0); 
// Update as of PHP 5.4 array() can be written []
curl_setopt_array($curl, [
    CURLOPT_URL            => $url,
//  CURLOPT_BINARYTRANSFER => 1, --- No effect from PHP 5.1.3
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_FILE           => $file,
    CURLOPT_TIMEOUT        => 50,
    CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'
]);

$response = curl_exec($curl);

if($response === false) {
    // Update as of PHP 5.3 use of Namespaces Exception() becomes \Exception()
    throw new \Exception('Curl error: ' . curl_error($curl));
}

$response; // Do something with the response.
$percent = $jum2 > 0 ? intval($i/$jum2 * 100)."%" : "0%";
// Javascript for updating the progress bar and information
			echo '<script language="javascript">
			document.getElementById("progressA").innerHTML="<div style=\"width:'.$percent.';background-image:url(images/pbar-ani1.gif);\">&nbsp;</div>";
		    document.getElementById("informationA").innerHTML="  Download Berkas Audio Soal <b>'.$i.'</b> row(s) of <b>'. $jum2.'</b> ... '.$percent.'  completed.";			
			</script>';
		// This is for the buffer achieve the minimum size in order to flush data
			echo str_repeat(' ',1024*64);
		// Send output to browser immediately
			flush();

	}
}

		?>
		<script>document.getElementById("statusdataA").style.display="block";
        setTimeout(myFunction, 9000)
        </script>

       
<?php 		


/* === Video Tanya DATA 7 == */
$i = 0;
while($r=$sql3->fetch()){
	if(!$r['XNamaFile']==''){
	$filese = "{$r['XNamaFile']}";		
	
$i++;
//echo "$i. file |$filese|<br>";

//$filese = "soal1.jpg";
//$url = 'http://SMP-BINAKARYA/beesmart2/pictures/'.$filese; // working
//$file = fopen(dirname(__FILE__) . '/images/'.$filese, 'w+');
$url = 'http://'.$PCSERVER.'/beesmart/video/'.$filese; // working
$file = fopen('../../video/'.$filese, 'w+');
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_TIMEOUT, 0); 
// Update as of PHP 5.4 array() can be written []
curl_setopt_array($curl, [
    CURLOPT_URL            => $url,
//  CURLOPT_BINARYTRANSFER => 1, --- No effect from PHP 5.1.3
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_FILE           => $file,
    CURLOPT_TIMEOUT        => 50,
    CURLOPT_USERAGENT      => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'
]);

$response = curl_exec($curl);

if($response === false) {
    // Update as of PHP 5.3 use of Namespaces Exception() becomes \Exception()
    throw new \Exception('Curl error: ' . curl_error($curl));
}

$response; // Do something with the response.
$percent = $jum3 > 0 ? intval($i/$jum3 * 100)."%" : "0%";
// Javascript for updating the progress bar and information
			echo '<script language="javascript">
			document.getElementById("progressV").innerHTML="<div style=\"width:'.$percent.';background-image:url(images/pbar-ani1.gif);\">&nbsp;</div>";
		    document.getElementById("informationV").innerHTML="  Download Berkas Audio Soal <b>'.$i.'</b> row(s) of <b>'. $jum3.'</b> ... '.$percent.'  completed.";			
			</script>';
		// This is for the buffer achieve the minimum size in order to flush data
			echo str_repeat(' ',1024*64);
		// Send output to browser immediately
			flush();

	}
}

		?>
		<script>document.getElementById("statusdataV").style.display="block";
        setTimeout(myFunction, 9000)
        </script>

       
<?php 		








//End of Copying		
}


?>
<script src="../../../js/jquery.js"></script>


