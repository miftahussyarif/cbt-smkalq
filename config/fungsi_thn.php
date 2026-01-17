<?php
include "server.php";

$tahun = date("Y");
$bulan = date("m");
// ambil Yearstart = Tahun sekarang untuk bulan > Juni, untuk Bulan < Juni set tahun = Tahun sekarang-1
if ($bulan < 13) {
    if ($bulan > 6) {
        $tahun = $tahun;
    } else {
        $tahun = $tahun - 1;
    }
}
$tahun1 = $tahun + 1;
$tahune = substr($tahun1, 2, 2);
//$ay = "BEE$tahun/$tahune";
$ay = "$tahun/$tahun1";
$nama = "Tahun Ajaran $tahun/$tahun1";

$stmt = db_query($db, "select 1 from cbt_setid where XKodeAY = :ay", array('ay' => $ay));
$exists = db_fetch_value($stmt);

if ($exists === null) {
    db_query($db, "update cbt_setid set XStatus = '0'", array());
    db_query(
        $db,
        "insert into cbt_setid (XKodeAY,XNamaAY,XStatus) values (:ay, :nama, '1')",
        array('ay' => $ay, 'nama' => $nama)
    );
}
?>
