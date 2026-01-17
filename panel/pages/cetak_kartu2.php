<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
?>
<table width="470" border=0>
<tr>
<?php
//koneksi database
require_once __DIR__ . "/../../config/server.php";
$BatasAwal = 50;

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) {
	$page = 1;
}
$MulaiAwal = $BatasAwal * ($page - 1);
//tampil data
$kolom = 2;
$i = 0;
$MulaiAwal = (int) $MulaiAwal;
$BatasAwal = (int) $BatasAwal;
$query = db_query($db, "SELECT * FROM cbt_siswa LIMIT $MulaiAwal, $BatasAwal", array());
while ($record = $query->fetch()) {
	   if ($i >= $kolom) {
        echo "<tr></tr>";
        $i = 0;
    }
    $i++;
?>
    <td width="464"><table width="394">
<table style="width:9cm;border:1px solid black;" class="kartu">
					<tbody><tr>
						<td colspan="3" style="border-bottom:1px solid black">
							<table width="100%" class="kartu">
							<tbody><tr>
								<td><img src="images/1.jpg" height="40"></td>
								<td align="center" style="font-weight:bold">
									KARTU PESERTA UJIAN CBT <BR /> 
							  </td>
							</tr>
							</tbody></table>
						</td>
					</tr>
					<tr><td width="90">Nama Peserta</td><td width="8">:</td><td width="226" style="font-size:12px;font-weight:bold;"><?php echo $record['XNamaSiswa']; ?></td></tr>
					
					<tr><td>Username</td><td>:</td><td style="font-size:12px;font-weight:bold;"><?php echo $record['XNomerUjian']; ?></td></tr>
					<tr><td>Password</td><td>:</td><td style="font-size:12px;font-weight:bold;"><?php echo $record['XPassword']; ?></td></tr>
					<tr><td>&nbsp;</td><td></td>
					<td style="font-size:12px;font-weight:bold;">Ttd ,</td></tr>
					<tr><td>&nbsp;</td><td></td>
					<td>&nbsp; </td></tr>
					<tr><td>&nbsp;</td><td></td>
					<td><span style="font-size: 12px">Panitia Ujian CBT</span></td></tr>
				</tbody></table><hr size="25" color="#FFFFFF"></td>
                
<?php
}

?>
</tr>
</table>
<?php
$cekQuery = db_query($db, "SELECT COUNT(*) FROM cbt_siswa", array());
$jumlahData = (int) db_fetch_value($cekQuery);
if ($jumlahData > $BatasAwal) {
echo '<br/><center><div style="font-size:10pt;">Page : ';
$a = explode(".", $jumlahData / $BatasAwal);
$b = $a[0];
$c = $b + 1;
for ($i = 1; $i <= $c; $i++) {
echo '<a style="text-decoration:none;';
if ($page == $i) {
echo 'color:red';
}
echo '" href="?page=' . $i . '">' . $i . '</a>, ';
}
echo '</div><button onclick="window.print()">Cetak Halaman Web</button></center>';
}
?>
