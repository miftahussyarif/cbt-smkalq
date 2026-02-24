<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
?>
 <script type="text/javascript" src="jquery-1.4.js"></script>
 <script>    
function selesaikanTesPeserta(){
 if(!confirm("Selesaikan semua status peserta yang belum selesai?")){
 return false;
 }
 $.ajax({
     type:"POST",
     url:"selesaikan_tes_peserta.php",
     data: "aksi=selesaikan_semua",
	 success: function(data){
	 var res = $.trim(data);
	 if(res == "OK"){
		 alert("Status peserta yang belum selesai berhasil diubah menjadi selesai.");
		 $("#load_comment").load("daftarpeserta.php");
	 } else if(res == "HAS_ACTIVE_TEST"){
		 alert("Masih ada tes aktif. Selesaikan tes aktif terlebih dahulu.");
	 } else if(res == "NOTHING"){
		 alert("Tidak ada data peserta yang perlu diselesaikan.");
	 } else {
		 alert("Gagal menyelesaikan tes peserta: " + data);
	 }
	 }
 });
 return false;
}

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
<?php                    
include "../../config/server.php";
$sqlAktif = mysql_query("SELECT COUNT(*) AS jml FROM cbt_ujian u
WHERE u.XStatusUjian='1'
AND (
ADDTIME(CONCAT(u.XTglUjian,' ',u.XJamUjian),u.XLamaUjian) > NOW()
OR EXISTS (
	select 1
	from cbt_siswa_ujian su
	where su.XStatusUjian='1'
	and su.XKodeSoal = u.XKodeSoal
	and su.XTokenUjian = u.XTokenUjian
)
)");
$rowAktif = $sqlAktif ? mysql_fetch_array($sqlAktif) : array('jml' => 0);
$jmlTesAktif = isset($rowAktif['jml']) ? (int)$rowAktif['jml'] : 0;

$sqlBelumSelesai = mysql_query("SELECT COUNT(*) AS jml FROM cbt_siswa_ujian WHERE XStatusUjian <> '9'");
$rowBelumSelesai = $sqlBelumSelesai ? mysql_fetch_array($sqlBelumSelesai) : array('jml' => 0);
$jmlBelumSelesai = isset($rowBelumSelesai['jml']) ? (int)$rowBelumSelesai['jml'] : 0;
?>
<?php if($jmlTesAktif < 1 && $jmlBelumSelesai > 0){ ?>
<div style="margin-bottom:10px;">
	<button type="button" class="btn btn-warning" id="btn-selesaikan-tes" onclick="return selesaikanTesPeserta();"><i class="fa fa-check"></i> Selesaikan Tes</button>
	<span style="margin-left:8px;">Peserta belum selesai: <?php echo $jmlBelumSelesai; ?></span>
</div>
<?php } ?>
<table class="table table-bordered" cellpadding="30px" width="100%" border="0">
<tr  style="color:#FFFFFF; font-style:normal; font-weight:normal; text-align:center" height="40px" bgcolor="#000">
									<th style="color:#FFFFFF; font-style:normal; font-weight:normal; text-align:center">&nbsp;No.</th>
                                    <th style="color:#FFFFFF; font-style:normal; font-weight:normal; text-align:center">Nomer Peserta</th>
                                    <th style="color:#FFFFFF; font-style:normal; font-weight:normal; text-align:center">Nama Siswa</th>
                                    <th style="color:#FFFFFF; font-style:normal; font-weight:normal; text-align:center">Kelas</th>
                                    <th style="color:#FFFFFF; font-style:normal; font-weight:normal; text-align:center">Jurusan</th>
                                    <th style="color:#FFFFFF; font-style:normal; font-weight:normal; text-align:center">NIS</th>
                                    <th style="color:#FFFFFF; font-style:normal; font-weight:normal; text-align:center">Status Tes Peserta</th>
</tr>
<?php                    
$sql = mysql_query("SELECT
u.*,
u.XStatusUjian as ujsta,
s.XNamaSiswa,
s.XKodeJurusan,
s.XKodeKelas as XKelasSiswa,
s.XNIK
FROM cbt_siswa_ujian u
LEFT JOIN cbt_siswa s ON TRIM(s.XNomerUjian) = TRIM(u.XNomerUjian)
ORDER BY u.Urut DESC"); 
$nom = 1;								
while($s= mysql_fetch_array($sql)){ 
$nama = str_replace("  ","",$s['XNamaSiswa']); 
$nouji = str_replace("  ","",$s['XNomerUjian']); 
$kodekelas = str_replace("  ","",($s['XKelasSiswa'] <> '' ? $s['XKelasSiswa'] : $s['XKodeKelas'])); 
$kodeNIK = str_replace("  ","",$s['XNIK']); 
$kodeJUR = str_replace("  ","",$s['XKodeJurusan']); 
$staujian = str_replace("  ","",$s['ujsta']); 
if($staujian =='0'){$staujian = "Belum Login";}
elseif($staujian =='1'){$staujian = "<font color='#629ad8'> Masih Dikerjakan </font>";}
elseif($staujian =='9'){$staujian = "<font color='#be425f'> Tes SELESAI </font>";}
?>
                                <tr height="40px">
                                    <td width="5%">&nbsp;<?php echo $nom ; ?></td>
                                    <td width="15%"><?php echo $nouji; ?></td>
                                    <td width="40%"><?php echo $nama; ?></td>
                                    <td width="5%"><?php echo $kodekelas; ?></td>
                                    <td width="5%"><?php echo $kodeJUR; ?></td>
                                    <td width="5%"><?php echo $kodeNIK; ?></td>
                                    <td width="20%"><?php echo "$staujian"; ?></td>
                                    </td>
                                </tr>
                                
                                <?php $nom++; } ?>
                                </table>
