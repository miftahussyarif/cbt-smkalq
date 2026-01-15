<?php
include "../../config/server.php";
include "../../config/pengawasan.php";

header('Content-Type: application/json');

$role = isset($_COOKIE['beelogin']) ? $_COOKIE['beelogin'] : '';
if (!isset($_COOKIE['beeuser']) || ($role !== '' && $role != 'admin' && $role != 'guru' && $role != 'pengawas')) {
    echo json_encode(array('ok' => false, 'error' => 'unauthorized'));
    exit;
}

cbt_ensure_pengawasan_table();

$sqlAktif = mysql_query("SELECT 1 FROM cbt_ujian WHERE XStatusUjian = '1' LIMIT 1");
if (!$sqlAktif || mysql_num_rows($sqlAktif) < 1) {
    mysql_query("DELETE FROM cbt_pengawasan");
    echo json_encode(array('ok' => true, 'data' => array()));
    exit;
}

$sql = mysql_query("SELECT
    p.XNomerUjian,
    p.XTokenUjian,
    p.XKodeSoal,
    COALESCE(u.XKodeKelas, s.XKodeKelas, j.XKodeKelas) AS XKodeKelas,
    s.XNamaSiswa,
    COALESCE(j.XKodeMapel, u.XKodeMapel) AS XKodeMapel,
    m.XNamaMapel,
    p.XLastEvent,
    p.XPindahTabCount,
    p.XPrintscreenCount,
    p.XIsLocked
FROM cbt_pengawasan p
LEFT JOIN cbt_siswa_ujian u ON (TRIM(u.XNomerUjian) = TRIM(p.XNomerUjian) AND TRIM(u.XTokenUjian) = TRIM(p.XTokenUjian) AND TRIM(u.XKodeSoal) = TRIM(p.XKodeSoal))
LEFT JOIN cbt_siswa s ON TRIM(s.XNomerUjian) = TRIM(p.XNomerUjian)
LEFT JOIN cbt_ujian j ON (TRIM(p.XKodeSoal) = TRIM(j.XKodeSoal) AND TRIM(p.XTokenUjian) = TRIM(j.XTokenUjian))
LEFT JOIN cbt_mapel m ON m.XKodeMapel = COALESCE(j.XKodeMapel, u.XKodeMapel)
ORDER BY s.XNamaSiswa");

function cbt_find_siswa($nomer)
{
    $nomerSafe = mysql_real_escape_string($nomer);
    $sql = mysql_query("SELECT XNamaSiswa, XKodeKelas FROM cbt_siswa WHERE TRIM(XNomerUjian) = TRIM('$nomerSafe') LIMIT 1");
    if ($sql && mysql_num_rows($sql) > 0) {
        return mysql_fetch_array($sql);
    }
    $sql = mysql_query("SELECT XNamaSiswa, XKodeKelas FROM cbt_siswa WHERE REPLACE(XNomerUjian,' ','') = REPLACE('$nomerSafe',' ','') LIMIT 1");
    if ($sql && mysql_num_rows($sql) > 0) {
        return mysql_fetch_array($sql);
    }
    $sql = mysql_query("SELECT XNamaSiswa, XKodeKelas FROM cbt_siswa WHERE REPLACE(XNomerUjian,' ','') LIKE CONCAT('%', REPLACE('$nomerSafe',' ',''), '%') LIMIT 1");
    if ($sql && mysql_num_rows($sql) > 0) {
        return mysql_fetch_array($sql);
    }
    $sql = mysql_query("SELECT XNamaSiswa, XKodeKelas FROM cbt_siswa WHERE XNIK = '$nomerSafe' LIMIT 1");
    if ($sql && mysql_num_rows($sql) > 0) {
        return mysql_fetch_array($sql);
    }
    return null;
}

function cbt_find_mapel_info($token, $kodesoal, $nomer)
{
    $tokenSafe = mysql_real_escape_string($token);
    $kodesoalSafe = mysql_real_escape_string($kodesoal);
    $nomerSafe = mysql_real_escape_string($nomer);

    $sql = mysql_query("SELECT XKodeMapel, XKodeKelas FROM cbt_siswa_ujian WHERE TRIM(XNomerUjian) = TRIM('$nomerSafe') AND TRIM(XTokenUjian) = TRIM('$tokenSafe') ORDER BY XMulaiUjian DESC LIMIT 1");
    if ($sql && mysql_num_rows($sql) > 0) {
        return mysql_fetch_array($sql);
    }

    $sql = mysql_query("SELECT XKodeMapel, XKodeKelas FROM cbt_ujian WHERE TRIM(XTokenUjian) = TRIM('$tokenSafe') AND TRIM(XKodeSoal) = TRIM('$kodesoalSafe') ORDER BY XTglUjian DESC, XJamUjian DESC LIMIT 1");
    if ($sql && mysql_num_rows($sql) > 0) {
        return mysql_fetch_array($sql);
    }
    return null;
}

function cbt_find_mapel_name($kodeMapel)
{
    $kodeMapelSafe = mysql_real_escape_string($kodeMapel);
    $sql = mysql_query("SELECT XNamaMapel FROM cbt_mapel WHERE XKodeMapel = '$kodeMapelSafe' LIMIT 1");
    if ($sql && mysql_num_rows($sql) > 0) {
        $row = mysql_fetch_array($sql);
        return $row['XNamaMapel'];
    }
    return '';
}

function cbt_map_pengawasan_row($row, &$no)
{
    $lastEvent = isset($row['XLastEvent']) ? $row['XLastEvent'] : '';
    $pindahCount = isset($row['XPindahTabCount']) ? (int) $row['XPindahTabCount'] : 0;
    $printCount = isset($row['XPrintscreenCount']) ? (int) $row['XPrintscreenCount'] : 0;
    $isLocked = (isset($row['XIsLocked']) && $row['XIsLocked'] == '1');
    if ($lastEvent === '' || $lastEvent === 'aman') {
        if ($pindahCount > 0) {
            $lastEvent = 'pindah_tab';
        } elseif ($printCount > 0) {
            $lastEvent = 'printscreen';
        } elseif ($isLocked) {
            $lastEvent = 'terkunci';
        } else {
            $lastEvent = 'aman';
        }
    }
    $nomerUjian = isset($row['XNomerUjian']) ? trim($row['XNomerUjian']) : '';
    $token = isset($row['XTokenUjian']) ? trim($row['XTokenUjian']) : '';
    $kodesoal = isset($row['XKodeSoal']) ? trim($row['XKodeSoal']) : '';
    $nama = isset($row['XNamaSiswa']) ? trim($row['XNamaSiswa']) : '';
    $kelas = isset($row['XKodeKelas']) ? trim($row['XKodeKelas']) : '';
    $namaMapel = isset($row['XNamaMapel']) ? trim($row['XNamaMapel']) : '';
    $kodeMapel = isset($row['XKodeMapel']) ? trim($row['XKodeMapel']) : '';

    if ($nomerUjian !== '' && ($nama === '' || $kelas === '')) {
        $siswa = cbt_find_siswa($nomerUjian);
        if ($siswa) {
            if ($nama === '' && isset($siswa['XNamaSiswa'])) {
                $nama = trim($siswa['XNamaSiswa']);
            }
            if ($kelas === '' && isset($siswa['XKodeKelas'])) {
                $kelas = trim($siswa['XKodeKelas']);
            }
        }
    }

    if (($kodeMapel === '' || $namaMapel === '' || $kelas === '') && ($token !== '' || $kodesoal !== '' || $nomerUjian !== '')) {
        $mapelInfo = cbt_find_mapel_info($token, $kodesoal, $nomerUjian);
        if ($mapelInfo) {
            if ($kodeMapel === '' && isset($mapelInfo['XKodeMapel'])) {
                $kodeMapel = trim($mapelInfo['XKodeMapel']);
            }
            if ($kelas === '' && isset($mapelInfo['XKodeKelas'])) {
                $kelas = trim($mapelInfo['XKodeKelas']);
            }
        }
    }

    if ($namaMapel === '' && $kodeMapel !== '') {
        $namaMapel = cbt_find_mapel_name($kodeMapel);
    }

    $mapel = $namaMapel !== '' ? $namaMapel : $kodeMapel;

    return array(
        'no' => $no++,
        'nomer_ujian' => $nomerUjian,
        'nama' => $nama,
        'kelas' => $kelas,
        'mapel' => trim($mapel),
        'status' => $lastEvent,
        'pindah_tab' => $pindahCount,
        'printscreen' => $printCount,
        'locked' => $isLocked,
        'token' => $token,
        'kodesoal' => $kodesoal
    );
}

$data = array();
$no = 1;
if ($sql) {
    while ($row = mysql_fetch_array($sql)) {
        $data[] = cbt_map_pengawasan_row($row, $no);
    }
}

if (count($data) === 0) {
    $sqlFallback = mysql_query("SELECT p.*, s.XNamaSiswa FROM cbt_pengawasan p LEFT JOIN cbt_siswa s ON s.XNomerUjian = p.XNomerUjian ORDER BY p.XUpdatedAt DESC");
    if ($sqlFallback) {
        while ($row = mysql_fetch_array($sqlFallback)) {
            $data[] = cbt_map_pengawasan_row($row, $no);
        }
    }
}

if (count($data) === 0) {
    $sqlRaw = mysql_query("SELECT * FROM cbt_pengawasan ORDER BY XUpdatedAt DESC");
    if ($sqlRaw) {
        while ($row = mysql_fetch_array($sqlRaw)) {
            $data[] = cbt_map_pengawasan_row($row, $no);
        }
    }
}

echo json_encode(array('ok' => true, 'data' => $data));
?>
