<?php
require_once __DIR__ . "/../../config/server.php";
$aksi = isset($_REQUEST['aksi']) ? $_REQUEST['aksi'] : '';
$txt_mapel = isset($_REQUEST['txt_mapel']) ? $_REQUEST['txt_mapel'] : '';
$txt_soal = isset($_REQUEST['txt_soal']) ? $_REQUEST['txt_soal'] : '';
$txt_status = isset($_REQUEST['txt_status']) ? $_REQUEST['txt_status'] : '';
$sqlcek = 0;

if ($aksi == "simpan") {
    $sta = db_fetch_one(
        db_query(
            $db,
            "SELECT * FROM cbt_paketsoal WHERE Urut = :urut",
            array(':urut' => $txt_mapel)
        )
    );

    if ($sta) {
        if ($txt_status == "AKTIF") {
            db_query(
                $db,
                "UPDATE cbt_paketsoal SET XStatusSoal = 'N' WHERE Urut = :urut",
                array(':urut' => $txt_mapel)
            );
        }

        $sqlcek = (int) db_fetch_value(
            db_query(
                $db,
                "SELECT COUNT(*) FROM cbt_paketsoal WHERE XKodeMapel = :mapel AND XKodeJurusan = :jurusan AND XKodeKelas = :kelas AND XLevel = :level AND XKodeSoal = :kodesoal AND XStatusSoal = 'Y'",
                array(
                    ':mapel' => $sta['XKodeMapel'],
                    ':jurusan' => $sta['XKodeJurusan'],
                    ':kelas' => $sta['XKodeKelas'],
                    ':level' => $sta['XLevel'],
                    ':kodesoal' => $txt_soal,
                )
            )
        );

        if ($sqlcek < 1) {
            $status = $sta['XStatusSoal'];
            if ($status == "Y") {
                $ubah = "N";
            } elseif ($status == "N") {
                $ubah = "Y";
            } else {
                $ubah = "N";
            }
            db_query(
                $db,
                "UPDATE cbt_paketsoal SET XStatusSoal = :ubah WHERE Urut = :urut",
                array(':ubah' => $ubah, ':urut' => $txt_mapel)
            );
        }
    }
    echo "$sqlcek";
} else {
    echo "$sqlcek";
}

if ($aksi == "acak") {
    $sta = db_fetch_one(
        db_query(
            $db,
            "SELECT XAcakSoal FROM cbt_paketsoal WHERE Urut = :urut",
            array(':urut' => $txt_mapel)
        )
    );
    if ($sta) {
        $status = $sta['XAcakSoal'];
        if ($status == "Y") {
            $ubah = "N";
        } elseif ($status == "N") {
            $ubah = "Y";
        } else {
            $ubah = "N";
        }
        db_query(
            $db,
            "UPDATE cbt_paketsoal SET XAcakSoal = :ubah WHERE Urut = :urut",
            array(':ubah' => $ubah, ':urut' => $txt_mapel)
        );
    }
}

if (isset($_REQUEST['putar'])) {
    if ($_REQUEST['putar'] == 0) {
        db_query(
            $db,
            "UPDATE cbt_audio SET XMulai = :mulai, XPutar = '2'",
            array(':mulai' => $_REQUEST['putar'])
        );
    } else {
        $anu = isset($_REQUEST['anu']) ? $_REQUEST['anu'] : '';
        db_query(
            $db,
            "UPDATE cbt_audio SET XMulai = :mulai",
            array(':mulai' => $anu)
        );
    }
}

	?>
