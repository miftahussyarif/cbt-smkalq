 <script type="text/javascript" src="jquery-1.4.js"></script>
 <script>    
$(document).ready(function(){
 $("#simpan").click(function(){
 //alert("hai");
 var nompes = $("#nompes").val();
 //alert(nompes);
 $.ajax({
     type:"POST",
     url:"resetlogin.php",    
     data: "aksi=simpan&nompes=" + nompes,
	 success: function(data){
	 $("#info").html(data);
	 tampildata();
	 }
	 });
	 });

});
</script>
<br>
<table class="table table-bordered" cellpadding="30px" width="100%" border="0">
								<tr height="50px" bgcolor="#E4E6DD">
                                    <th>&nbsp;No.</th>
                                    <th>Nomer Peserta</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas - NIS</th>
                                    <th>Jawab</th>
                                    <th>Benar</th>
                                    <th>Token</th>                                    
                                    <th>Analisa</th>                                    
                                </tr> <?php
require_once __DIR__ . "/../../config/server.php";
$t = db_fetch_one(db_query($db, "SELECT XTokenUjian FROM cbt_ujian WHERE XStatusUjian = '1' LIMIT 1", array()));
$tokenujian = $t ? $t['XTokenUjian'] : '';

$sql = db_query(
    $db,
    "SELECT *, u.XStatusUjian AS ujsta, c.XTokenUjian AS tokenz, u.XNomerUjian AS noujian
    FROM cbt_siswa s
    LEFT JOIN `cbt_siswa_ujian` u ON u.XNomerUjian = s.XNomerUjian
    LEFT JOIN cbt_ujian c ON (u.XKodeSoal = c.XKodeSoal AND u.XTokenUjian = c.XTokenUjian)
    WHERE c.XStatusUjian = '1' AND u.XTokenUjian = :token AND c.XTokenUjian = :token",
    array(':token' => $tokenujian)
);
$nom = 1;								
while($s = $sql->fetch()){ 
$nama = str_replace("  ","",$s['XNamaSiswa']); 
$nouji = str_replace("  ","",$s['noujian']); 
$kodekelas = str_replace("  ","",$s['XKodeKelas']); 
$kodeNIK = str_replace("  ","",$s['XNIK']); 
$staujian = str_replace("  ","",$s['ujsta']);
$token = str_replace("  ","",$s['tokenz']);
$soaluji = str_replace("  ","",$s['XKodeSoal']); 
if($staujian =='0'){$staujian = "Belum Login";}
elseif($staujian =='1'){$staujian = "Sedang Dikerjakan";}
elseif($staujian =='9'){$staujian = "Tes SELESAI";}
	$sqldijawab = (int) db_fetch_value(
        db_query(
            $db,
            "SELECT COUNT(*) FROM `cbt_jawaban` WHERE XTokenUjian = :token AND XJawaban != '' AND XUserJawab = :user",
            array(':token' => $tokenujian, ':user' => $nouji)
        )
    );
	$jumbenar = (int) db_fetch_value(
        db_query(
            $db,
            "SELECT COUNT(XNilai) FROM `cbt_jawaban` WHERE XNilai = '1' AND XTokenUjian = :token AND XUserJawab = :user",
            array(':token' => $tokenujian, ':user' => $nouji)
        )
    );
?>
                                <tr height="40px">
                                    <td width="5%">&nbsp;<?php echo $nom ; ?></td>
                                    <td width="15%"><?php echo $nouji; ?></td>
                                    <td width="40%"><?php echo $nama; ?></td>
                                    <td width="15%"><?php echo "$kodekelas - $kodeNIK"; ?></td>
                                    <td width="5%"><?php echo $sqldijawab; ?></td>
                                    <td width="5%"><?php echo $jumbenar; ?></td>
                                    <td width="5%"><?php echo $token; ?></td>
                                    <td width="5%" align="center"><a href="?modul=jawabansiswa&nomer=<?php echo $nouji; ?>&ujian=<?php echo "$soaluji"; ?>" ><img src="images/printer.png"/></a></td>
                                    </td>
                                </tr> <?php $nom++; } ?>
                                </table>
