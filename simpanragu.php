<?php
include "config/server.php";
include "config/pengawasan.php";
//update cbt_jawaban set XRagu = '1' where XNomerSoal='$_REQUEST[who]'
$user = isset($_COOKIE['PESERTA']) ? $_COOKIE['PESERTA'] : '';
if ($user === '') {
    exit;
}

cbt_ensure_pengawasan_table();
$sqlUjian = db_query(
    $db,
    "SELECT XTokenUjian, XKodeSoal FROM cbt_siswa_ujian WHERE XNomerUjian = :user AND XStatusUjian = '1' ORDER BY XMulaiUjian DESC LIMIT 1",
    array('user' => $user)
);
$uj = db_fetch_one($sqlUjian);
if ($uj) {
    $token = $uj['XTokenUjian'];
    $kodesoal = $uj['XKodeSoal'];
    $ceklock = db_query(
        $db,
        "SELECT count(1) as total FROM cbt_pengawasan WHERE XNomerUjian = :user AND XTokenUjian = :token AND XKodeSoal = :kodesoal AND XIsLocked = '1'",
        array('user' => $user, 'token' => $token, 'kodesoal' => $kodesoal)
    );
    $ceklock_count = (int) db_fetch_value($ceklock);
    if ($ceklock_count > 0) {
        header('HTTP/1.1 403 Forbidden');
        echo "LOCKED";
        exit;
    }
}

if (isset($_REQUEST['chk'], $_REQUEST['who'])) {
    $chk = $_REQUEST['chk'];
    $who = $_REQUEST['who'];
    db_query(
        $db,
        "update cbt_jawaban set XRagu = :chk where Urut = :urut AND XUserJawab = :user",
        array('chk' => $chk, 'urut' => $who, 'user' => $user)
    );
}

$anu = isset($_REQUEST['anu']) ? $_REQUEST['anu'] : null;
if ($anu !== null) {
    if ($anu == 0) {
        db_query(
            $db,
            "update cbt_audio set XMulai = :mulai, XPutar = '2'",
            array('mulai' => $anu)
        );
    } else {
        db_query(
            $db,
            "update cbt_audio set XMulai = :mulai",
            array('mulai' => $anu)
        );
    }
}

?>
