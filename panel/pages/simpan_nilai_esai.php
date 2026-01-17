<?php
require_once __DIR__ . "/../../config/server.php";

$nomer = isset($_REQUEST['nomere']) ? $_REQUEST['nomere'] : '';
$siswa = isset($_REQUEST['siswae']) ? $_REQUEST['siswae'] : '';
$token = isset($_REQUEST['tokene']) ? $_REQUEST['tokene'] : '';
$soal = isset($_REQUEST['soale']) ? $_REQUEST['soale'] : '';
$tgl = date("Y-m-d");
$jam = date("H:i:s");

$nilai = 0;
if (isset($_REQUEST['jawabe'])) {
    $nilai = str_replace(" ", "", $_REQUEST['jawabe']);
    if ($nilai === "" || $nilai === "0") {
        $nilai = 0;
    }
}

$uji = db_fetch_one(
    db_query(
        $db,
        "SELECT XJenisSoal FROM cbt_jawaban WHERE XNomerSoal = :nomer AND XKodeSoal = :soal AND XUserJawab = :siswa AND XTokenUjian = :token",
        array(':nomer' => $nomer, ':soal' => $soal, ':siswa' => $siswa, ':token' => $token)
    )
);
$jenis = $uji ? $uji['XJenisSoal'] : null;
if ($jenis == 2) {
    db_query(
        $db,
        "UPDATE cbt_jawaban SET XNilaiEsai = :nilai WHERE XNomerSoal = :nomer AND XKodeSoal = :soal AND XUserJawab = :siswa AND XTokenUjian = :token",
        array(':nilai' => $nilai, ':nomer' => $nomer, ':soal' => $soal, ':siswa' => $siswa, ':token' => $token)
    );
}

$tampil = (float) db_fetch_value(
    db_query(
        $db,
        "SELECT SUM(XNilaiEsai) FROM cbt_jawaban WHERE XKodeSoal = :soal AND XUserJawab = :siswa AND XTokenUjian = :token",
        array(':soal' => $soal, ':siswa' => $siswa, ':token' => $token)
    )
);
$tampil = round($tampil, 2);

$NilP = (float) db_fetch_value(
    db_query(
        $db,
        "SELECT XNilai FROM cbt_nilai WHERE XKodeSoal = :soal AND XNomerUjian = :siswa AND XTokenUjian = :token",
        array(':soal' => $soal, ':siswa' => $siswa, ':token' => $token)
    )
);

$oj = db_fetch_one(
    db_query(
        $db,
        "SELECT XPersenPil, XPersenEsai FROM cbt_paketsoal WHERE XKodeSoal = :soal",
        array(':soal' => $soal)
    )
);
$pakP = $oj ? round($oj['XPersenPil'], 2) : 0;
$pakE = $oj ? round($oj['XPersenEsai'], 2) : 0;

$subP = ($NilP * ($pakP / 100));
$subE = ($tampil * ($pakE / 100));
$Total = $subP + $subE;

db_query(
    $db,
    "UPDATE cbt_nilai SET XEsai = :esai, XPersenPil = :pakP, XPersenEsai = :pakE, XTotalNilai = :total WHERE XKodeSoal = :soal AND XNomerUjian = :siswa AND XTokenUjian = :token",
    array(
        ':esai' => $tampil,
        ':pakP' => $pakP,
        ':pakE' => $pakE,
        ':total' => $Total,
        ':soal' => $soal,
        ':siswa' => $siswa,
        ':token' => $token,
    )
);

echo $tampil;
?>
