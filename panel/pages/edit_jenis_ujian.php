<?php
if (!isset($_COOKIE['beeuser'])) {
    header("Location: login.php");
}
include "../../config/server.php";
if ($_REQUEST['urut']) {
    $id = $_POST['urut'];
    $sql = mysql_query("SELECT * FROM cbt_tes WHERE Urut = '$id'");
    $r = mysql_fetch_array($sql);
    ?>

    <form action="?modul=jenis_ujian&simpan=yes" method="post">
        <input type="hidden" name="id" value="<?php echo $r['Urut']; ?>">
        <div class="form-group">
            <label>Kode Ujian</label>
            <input type="text" class="form-control" value="<?php echo $r['XKodeUjian']; ?>" readonly>
        </div>
        <div class="form-group">
            <label>Nama Ujian</label>
            <input type="text" class="form-control" name="txt_nama" value="<?php echo $r['XNamaUjian']; ?>">
        </div>
        <button class="btn btn-primary" type="submit">Update</button>
    </form>

<?php } ?>
