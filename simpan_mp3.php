<?php
include "config/server.php";
$aksi = isset($_REQUEST['aksi']) ? $_REQUEST['aksi'] : '';
$waktu = isset($_REQUEST['waktu']) ? $_REQUEST['waktu'] : '';
$soal = isset($_REQUEST['soal']) ? $_REQUEST['soal'] : '';
$nomer = isset($_REQUEST['nomer']) ? $_REQUEST['nomer'] : '';
$token = isset($_REQUEST['token']) ? $_REQUEST['token'] : '';
$user = isset($_COOKIE['PESERTA']) ? $_COOKIE['PESERTA'] : '';

if ($aksi === "pause") {
    db_query(
        $db,
        "update cbt_jawaban set XMulai = :waktu where XUserJawab = :user and XKodeSoal = :kodesoal and Urut = :urut and XTokenUjian = :token",
        array(
            'waktu' => $waktu,
            'user' => $user,
            'kodesoal' => $soal,
            'urut' => $nomer,
            'token' => $token,
        )
    );
} elseif ($aksi === "habis") {
    db_query(
        $db,
        "update cbt_jawaban set XMulai = :waktu, XPutar = '1' where XUserJawab = :user and XKodeSoal = :kodesoal and Urut = :urut and XTokenUjian = :token",
        array(
            'waktu' => $waktu,
            'user' => $user,
            'kodesoal' => $soal,
            'urut' => $nomer,
            'token' => $token,
        )
    );
}

?>
