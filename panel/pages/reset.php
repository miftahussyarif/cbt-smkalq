<?php
require_once __DIR__ . "/../../config/server.php";

//$sql = mysql_query("insert into tes (nilai) values ('$_REQUEST[token]')");
$nama = isset($_REQUEST['nama']) ? $_REQUEST['nama'] : '';
$token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
$items = array_filter(array_map('trim', explode(',', $nama)), 'strlen');

foreach ($items as $item) {
    if (!is_numeric($item)) {
        continue;
    }
    $urut = (int) $item;
    $s = db_fetch_one(
        db_query(
            $db,
            "SELECT XNomerUjian FROM cbt_siswa_ujian WHERE Urut = :urut AND XTokenUjian = :token",
            array(':urut' => $urut, ':token' => $token)
        )
    );
    if (!$s) {
        continue;
    }
    $nomer = $s['XNomerUjian'];
    if ($nomer == "") {
        continue;
    }
    // Reset login state so peserta bisa login kembali.
    db_query(
        $db,
        "UPDATE cbt_siswa_ujian SET XStatusUjian = '0', XGetIP = '' WHERE Urut = :urut AND XTokenUjian = :token AND XNomerUjian = :nomer",
        array(':urut' => $urut, ':token' => $token, ':nomer' => $nomer)
    );
}

?>
