<?php
if (!function_exists('cbt_exam_escape')) {
    function cbt_exam_escape($value)
    {
        return mysql_real_escape_string((string) $value);
    }
}

if (!function_exists('cbt_set_exam_context_cookies')) {
    function cbt_set_exam_context_cookies($token, $kodesoal, $sesi = '')
    {
        $expire = time() + (8 * 3600);
        setcookie('CBT_TOKEN', (string) $token, $expire, '/');
        setcookie('CBT_KODESOAL', (string) $kodesoal, $expire, '/');
        setcookie('CBT_SESI', (string) $sesi, $expire, '/');
    }
}

if (!function_exists('cbt_get_schedule_context_for_student')) {
    function cbt_get_schedule_context_for_student($nomerUjian, $preferToken = '')
    {
        $user = cbt_exam_escape($nomerUjian);
        $siswaQ = mysql_query("SELECT XKodeKelas, XKodeJurusan, XSesi FROM cbt_siswa WHERE XNomerUjian = '$user' LIMIT 1");
        if (!$siswaQ || mysql_num_rows($siswaQ) < 1) {
            return null;
        }
        $siswa = mysql_fetch_array($siswaQ);
        $kelas = cbt_exam_escape($siswa['XKodeKelas']);
        $jurusan = cbt_exam_escape($siswa['XKodeJurusan']);
        $sesi = cbt_exam_escape($siswa['XSesi']);
        $tokenSql = '';
        if ((string) $preferToken !== '') {
            $tokenSafe = cbt_exam_escape($preferToken);
            $tokenSql = " AND TRIM(u.XTokenUjian) = TRIM('$tokenSafe')";
        }

        $base = "FROM cbt_ujian u
LEFT JOIN cbt_mapel m ON m.XKodeMapel = u.XKodeMapel
WHERE (u.XKodeKelas = '$kelas' OR u.XKodeKelas = 'ALL')
  AND (u.XKodeJurusan = '$jurusan' OR u.XKodeJurusan = 'ALL')
  AND u.XSesi = '$sesi'
  AND u.XStatusUjian = '1' $tokenSql";

        $aktifQ = mysql_query("SELECT u.*, m.XNamaMapel $base
  AND NOW() BETWEEN CONCAT(u.XTglUjian,' ',u.XJamUjian) AND ADDTIME(CONCAT(u.XTglUjian,' ',u.XJamUjian),u.XLamaUjian)
ORDER BY CONCAT(u.XTglUjian,' ',u.XJamUjian) ASC
LIMIT 1");
        if ($aktifQ && mysql_num_rows($aktifQ) > 0) {
            return mysql_fetch_array($aktifQ);
        }

        $nextQ = mysql_query("SELECT u.*, m.XNamaMapel $base
  AND CONCAT(u.XTglUjian,' ',u.XJamUjian) > NOW()
ORDER BY CONCAT(u.XTglUjian,' ',u.XJamUjian) ASC
LIMIT 1");
        if ($nextQ && mysql_num_rows($nextQ) > 0) {
            return mysql_fetch_array($nextQ);
        }
        return null;
    }
}

if (!function_exists('cbt_get_attempt_context_for_student')) {
    function cbt_get_attempt_context_for_student($nomerUjian, $preferToken = '', $preferKodeSoal = '')
    {
        $user = cbt_exam_escape($nomerUjian);
        $siswaQ = mysql_query("SELECT XSesi FROM cbt_siswa WHERE XNomerUjian = '$user' LIMIT 1");
        if (!$siswaQ || mysql_num_rows($siswaQ) < 1) {
            return null;
        }
        $siswa = mysql_fetch_array($siswaQ);
        $sesi = cbt_exam_escape($siswa['XSesi']);

        $extra = '';
        if ((string) $preferToken !== '') {
            $extra .= " AND TRIM(XTokenUjian) = TRIM('" . cbt_exam_escape($preferToken) . "')";
        }
        if ((string) $preferKodeSoal !== '') {
            $extra .= " AND TRIM(XKodeSoal) = TRIM('" . cbt_exam_escape($preferKodeSoal) . "')";
        }

        if ($extra !== '') {
            $q = mysql_query("SELECT * FROM cbt_siswa_ujian WHERE XNomerUjian = '$user' AND XSesi = '$sesi' $extra ORDER BY (XStatusUjian='1') DESC, Urut DESC LIMIT 1");
            if ($q && mysql_num_rows($q) > 0) {
                return mysql_fetch_array($q);
            }
        }

        $q = mysql_query("SELECT * FROM cbt_siswa_ujian WHERE XNomerUjian = '$user' AND XSesi = '$sesi' AND XStatusUjian = '1' ORDER BY Urut DESC LIMIT 1");
        if ($q && mysql_num_rows($q) > 0) {
            return mysql_fetch_array($q);
        }

        $q = mysql_query("SELECT * FROM cbt_siswa_ujian WHERE XNomerUjian = '$user' AND XSesi = '$sesi' ORDER BY (XStatusUjian='1') DESC, Urut DESC LIMIT 1");
        if ($q && mysql_num_rows($q) > 0) {
            return mysql_fetch_array($q);
        }
        return null;
    }
}
