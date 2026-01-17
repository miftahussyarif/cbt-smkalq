<?php
if (!isset($_COOKIE['beeuser'])) {
	header("Location: login.php");
}
?>
<?php
include "../../config/server.php";
$sql0 = mysql_query("select * from cbt_user order by Urut");
?>

<table width="100%">
	<tr>
		<th>No.</th>
		<th>User</th>
		<th>Nama</th>
		<th>Level</th>
		<th>Aksi</th>
	</tr>
	<script type="text/javascript" src="../js/jquery.js"></script>
	<script type="text/javascript" src="jquery-1.4.js"></script>
	<?php
	$no = 1;
	while ($xadm = mysql_fetch_array($sql0)) {
		if ($xadm['login'] == "1") {
			$rol = "Admin";
		} elseif ($xadm['login'] == "2") {
			$rol = "Pengawas";
		} else {
			$rol = "Guru";
		}
		echo "<tr height=40 style='border=0; border-bottom:thin solid #dcddde'><td>$no</td><td>{$xadm['Username']}</td><td>{$xadm['Nama']}</td>"; ?>
		<td><?php echo "$rol"; ?></td>
		<td>

			<script>
				$(document).ready(function () {
					$('#btnDelete').on('click', function () {
						alert();
					});
				});
			</script>
			<a href="#" data-toggle="modal" data-target="#modalPassword<?php echo $xadm['Urut']; ?>"
				title="Ubah Password"><button type="button" class="btn btn-warning btn-small"><i
						class="fa fa-key"></i></button></a>
			<a href="?modul=buat_user&aksion=hapus&urut=<?php echo $xadm['Urut']; ?>" title="Hapus"
				onclick="return confirm('Yakin hapus user ini?')"><button type="button" class="btn btn-danger btn-small"><i
						class="fa fa-times"></i></button></a>

			<!-- Modal Ubah Password -->
			<div class="modal fade" id="modalPassword<?php echo $xadm['Urut']; ?>" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
							<h4 class="modal-title">Ubah Password: <?php echo $xadm['Username']; ?></h4>
						</div>
						<form action="ubah_password.php" method="POST">
							<div class="modal-body">
								<input type="hidden" name="urut" value="<?php echo $xadm['Urut']; ?>">
								<div class="form-group">
									<label>Password Baru</label>
									<input type="password" name="password_baru" class="form-control" required>
								</div>
								<div class="form-group">
									<label>Konfirmasi Password</label>
									<input type="password" name="password_konfirmasi" class="form-control" required>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
								<button type="submit" class="btn btn-primary">Simpan</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</td>

		</tr>
		<?php
		$no++;
	}
	?>

</table>

<style>
	.tombol

	/* Or better yet try giving an ID or class if possible*/
		{
		border: 0;
		background: #66bda8;
		box-shadow: none;
		color: #FFF;
		text-decoration: none;
		padding: 5px;
		width: 80px;
		border-radius: 0px;
	}
</style>
