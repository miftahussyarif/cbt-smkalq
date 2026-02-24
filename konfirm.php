<?php
include "config/server.php";
include "ip.php";

$sqlcekdb = mysql_query("SELECT * FROM `cbt_siswa` limit 1");
if (!$sqlcekdb) {
    header('Location:login.php?salah=2');
}

if (isset($_COOKIE['PESERTA']) && isset($_COOKIE['KUNCI'])) {
    $user = "$_COOKIE[PESERTA]";
    $pass = "$_COOKIE[KUNCI]";
    $txtuser = $user;
    $txtpass = $pass;
} else {
    $txtuser = str_replace(" ", "", $_REQUEST['UserName']);
    $txtpass = str_replace(" ", "", $_REQUEST['Password']);
    setcookie('PESERTA', $txtuser);
    setcookie('KUNCI', $txtpass);
    $user = "$txtuser";
    $pass = "$txtpass";
}

$sqllogin = mysql_query("SELECT * FROM  `cbt_siswa` WHERE XNomerUjian = '$txtuser' and XPassword = '$txtpass'");
$sis = mysql_fetch_array($sqllogin);

$val_siswa = $sis['XNamaSiswa'];
$xjeniskelamin = $sis['XJenisKelamin'];
$xkelz = $sis['XKodeKelas'];
$xjurz = $sis['XKodeJurusan'];
$xsesi = $sis['XSesi'];

if ($xjeniskelamin == "L") {
    $jekel = "LAKI-LAKI";
} else {
    $jekel = "PEREMPUAN";
}

$jmlsqllogin = mysql_num_rows($sqllogin);
if ($jmlsqllogin < 1) {
    header('Location:login.php?salah=1&jumlah=' . $jmlsqllogin);
}

$tglujian = date("Y-m-d");
$xjam1 = date("H:i:s");

$sqluser = mysql_query("
SELECT u.*,m.XNamaMapel FROM `cbt_ujian` u LEFT JOIN cbt_paketsoal p on p.XKodeKelas = u.XKodeKelas and p.XKodeMapel = u.XKodeMapel
left join cbt_mapel m on u.XKodeMapel = m.XKodeMapel
WHERE (u.XKodeKelas = '$xkelz' or u.XKodeKelas = 'ALL')
  and (u.XKodeJurusan = '$xjurz' or u.XKodeJurusan = 'ALL')
  and u.XSesi = '$xsesi'
  and u.XStatusUjian = '1'
  and NOW() between CONCAT(u.XTglUjian,' ',u.XJamUjian) and ADDTIME(CONCAT(u.XTglUjian,' ',u.XJamUjian),u.XLamaUjian)
ORDER BY CONCAT(u.XTglUjian,' ',u.XJamUjian) DESC LIMIT 1");

if (mysql_num_rows($sqluser) < 1) {
    $sqluser = mysql_query("
    SELECT u.*,m.XNamaMapel FROM `cbt_ujian` u LEFT JOIN cbt_paketsoal p on p.XKodeKelas = u.XKodeKelas and p.XKodeMapel = u.XKodeMapel
    left join cbt_mapel m on u.XKodeMapel = m.XKodeMapel
    WHERE (u.XKodeKelas = '$xkelz' or u.XKodeKelas = 'ALL')
      and (u.XKodeJurusan = '$xjurz' or u.XKodeJurusan = 'ALL')
      and u.XSesi = '$xsesi'
      and u.XStatusUjian = '1'
      and CONCAT(u.XTglUjian,' ',u.XJamUjian) > NOW()
    ORDER BY CONCAT(u.XTglUjian,' ',u.XJamUjian) ASC LIMIT 1");
}

$s = mysql_fetch_array($sqluser);
$xkodesoal = isset($s['XKodeSoal']) ? $s['XKodeSoal'] : '';
$xkodekelas = isset($s['XKodeKelas']) ? $s['XKodeKelas'] : '';
$xtglujian = isset($s['XTglUjian']) ? $s['XTglUjian'] : '';
$xkodemapel = isset($s['XKodeMapel']) ? $s['XKodeMapel'] : '';
$xjumlahsoal = isset($s['XJumSoal']) ? $s['XJumSoal'] : '';
$xtokenujian = isset($s['XTokenUjian']) ? $s['XTokenUjian'] : '';
$xlamaujian = isset($s['XLamaUjian']) ? $s['XLamaUjian'] : '';
$xjamujian = isset($s['XJamUjian']) ? $s['XJamUjian'] : '';
$xbatasmasuk = isset($s['XBatasMasuk']) ? $s['XBatasMasuk'] : '';
$xkodeujian = isset($s['XKodeUjian']) ? $s['XKodeUjian'] : '';
$xmaxlambat = isset($s['XLambat']) ? $s['XLambat'] : '';
$xnamamapel = isset($s['XNamaMapel']) ? $s['XNamaMapel'] : '';
$kelas_label = $xkodekelas !== '' ? $xkodekelas : $xkelz;

$xnow_ts = time();
$xmulai_ts = 0;
$xbatasmasuk_ts = 0;
$xbatasmasuk_efektif_ts = 0;

if ($xtglujian !== '' && $xjamujian !== '') {
    $xmulai_ts = strtotime($xtglujian . ' ' . $xjamujian);
}
if ($xtglujian !== '' && $xbatasmasuk !== '') {
    $xbatasmasuk_ts = strtotime($xtglujian . ' ' . $xbatasmasuk);
    if ($xmulai_ts > 0 && $xbatasmasuk_ts < $xmulai_ts) {
        $xbatasmasuk_ts += 86400;
    }
}
$xbatasmasuk_efektif_ts = $xbatasmasuk_ts;

if ($xmulai_ts > 0 && $xlamaujian !== '') {
    $durJam = (int) substr($xlamaujian, 0, 2);
    $durMenit = (int) substr($xlamaujian, 3, 2);
    $durDetik = (int) substr($xlamaujian, 6, 2);
    $durasiDetik = ($durJam * 3600) + ($durMenit * 60) + $durDetik;
    $batasDurasiTs = $xmulai_ts + $durasiDetik;

    if ((string) $xmaxlambat === '0' || strtoupper(trim($xkodeujian)) === 'PSAJ') {
        if ($xbatasmasuk_efektif_ts <= 0 || $batasDurasiTs > $xbatasmasuk_efektif_ts) {
            $xbatasmasuk_efektif_ts = $batasDurasiTs;
        }
    }
}
$xsedang_berlangsung = ($xmulai_ts > 0 && $xbatasmasuk_efektif_ts > 0 && $xnow_ts >= $xmulai_ts && $xnow_ts <= $xbatasmasuk_efektif_ts);

$sqlada0 = mysql_query("SELECT * FROM  `cbt_siswa_ujian` WHERE XNomerUjian = '$txtuser' and XTokenUjian = '$xtokenujian'");
$ad0 = mysql_fetch_array($sqlada0);
$savedIp = '';
if ($xkodesoal !== '' && $xtokenujian !== '' && !cbt_validate_single_ip_session($txtuser, $xtokenujian, $xkodesoal, $cbt_session_lock_value, $savedIp)) {
    if (function_exists('bee_log')) {
        bee_log('WARN', 'MULTI_IP_BLOCK', 'Konfirmasi ujian ditolak karena IP berbeda', array(
            'user' => $txtuser,
            'token' => $xtokenujian,
            'kodesoal' => $xkodesoal,
            'current_ip' => $user_ip,
            'lock_mode' => isset($cbt_session_lock_mode) ? $cbt_session_lock_mode : '',
            'lock_value' => isset($cbt_session_lock_value) ? $cbt_session_lock_value : '',
            'saved_ip' => $savedIp
        ));
    }
    header('Location:login.php?salah=3');
    exit;
}

$sql_admin = mysql_query("select * from cbt_admin");
$r = mysql_fetch_array($sql_admin);
$school_name = "SMK AL QODIRIYAH";
if (is_array($r) && isset($r['XSekolah']) && $r['XSekolah'] !== '') {
    $school_name = $r['XSekolah'];
}
$brand_name = "CBT " . $school_name;

$token_error = isset($_REQUEST['salah']) && $_REQUEST['salah'] == 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?> | Konfirmasi Ujian</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        function disableBackButton() {
            window.history.forward();
        }
        setTimeout(disableBackButton, 0);
    </script>

    <link rel="stylesheet" href="css/bootstrap2.min.css">
    <link rel="stylesheet" href="css/klien.css">

    <style>
        :root {
            --page-bg-1: #0c2f74;
            --page-bg-2: #0e57aa;
            --panel-bg: #f3f6ff;
            --card-bg: #ffffff;
            --accent: #23c0ff;
            --accent-deep: #0a52c9;
            --ink: #0d1c3f;
            --muted: #5f6f90;
            --shadow: 0 30px 80px rgba(7, 18, 50, 0.28);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background: radial-gradient(900px 600px at 10% 20%, rgba(37, 147, 255, 0.65) 0%, rgba(37, 147, 255, 0) 60%),
                radial-gradient(500px 400px at 80% 80%, rgba(23, 191, 255, 0.35) 0%, rgba(23, 191, 255, 0) 70%),
                linear-gradient(135deg, var(--page-bg-1), var(--page-bg-2));
            color: var(--ink);
            font-family: "Trebuchet MS", "Candara", sans-serif;
        }

        .login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 16px;
        }

        .login-shell {
            width: min(980px, 100%);
            display: grid;
            grid-template-columns: minmax(260px, 45%) minmax(320px, 55%);
            border-radius: 18px;
            overflow: hidden;
            background: var(--card-bg);
            box-shadow: var(--shadow);
            animation: shellIn 600ms ease;
        }

        .login-aside {
            position: relative;
            padding: 40px 36px;
            color: #f7fbff;
            background: linear-gradient(145deg, #1480ff, #0a54c6 55%, #0a2c7f);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 24px;
            overflow: hidden;
        }

        .login-aside::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 40%),
                repeating-linear-gradient(135deg, rgba(255, 255, 255, 0.18) 0 2px, rgba(255, 255, 255, 0) 2px 14px);
            opacity: 0.45;
            pointer-events: none;
        }

        .login-aside>* {
            position: relative;
            z-index: 1;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.85);
            animation: fadeUp 600ms ease 60ms both;
        }

        .brand-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #ffffff;
            box-shadow: 0 0 0 6px rgba(255, 255, 255, 0.15);
        }

        .logo-mark {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
            font-size: 13px;
            padding: 6px 10px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 999px;
        }

        .logo-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ffffff;
        }

        .welcome {
            animation: fadeUp 600ms ease 120ms both;
        }

        .welcome h1 {
            margin: 12px 0 8px;
            font-size: 34px;
            line-height: 1.05;
        }

        .welcome p {
            margin: 0;
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
        }

        .aside-footer {
            display: flex;
            flex-direction: column;
            gap: 10px;
            animation: fadeUp 600ms ease 220ms both;
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.5);
            color: #ffffff;
            background: rgba(13, 41, 100, 0.2);
            text-decoration: none;
            font-weight: 600;
            letter-spacing: 0.03em;
            width: fit-content;
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.18);
        }

        .aside-note {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.75);
        }

        .login-panel {
            background: var(--panel-bg);
            padding: 42px 46px;
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .panel-head {
            animation: fadeUp 600ms ease 160ms both;
        }

        .panel-head .panel-kicker {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--muted);
        }

        .panel-head h2 {
            margin: 6px 0 6px;
            font-size: 28px;
        }

        .panel-head p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
        }

        .alert-card {
            display: none;
            border-left: 4px solid #ff4f5a;
            background: #ffe9ed;
            color: #982b32;
            border-radius: 10px;
            padding: 12px 14px;
        }

        .alert-card.is-visible {
            display: block;
        }

        .alert-title {
            font-weight: 700;
            margin-bottom: 4px;
        }

        .alert-body {
            font-size: 13px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 14px;
            animation: fadeUp 600ms ease 220ms both;
        }

        .confirm-card {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .confirm-item {
            border: 1px solid #d8e1f2;
            background: #ffffff;
            border-radius: 12px;
            padding: 12px 14px;
            box-shadow: 0 6px 16px rgba(6, 22, 56, 0.08);
        }

        .confirm-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--muted);
            font-weight: 700;
        }

        .confirm-value {
            margin-top: 4px;
            font-size: 15px;
            font-weight: 600;
            color: var(--ink);
        }

        .confirm-item.is-status .confirm-value {
            font-weight: 500;
            color: var(--muted);
        }

        .form-field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-field label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--muted);
            font-weight: 700;
        }

        .form-field input {
            border: 1px solid #d8e1f2;
            background: #fff;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 14px;
            color: var(--ink);
            box-shadow: 0 6px 16px rgba(6, 22, 56, 0.08);
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .form-field input:focus {
            outline: none;
            border-color: #4ea1ff;
            box-shadow: 0 10px 24px rgba(4, 46, 122, 0.15);
            transform: translateY(-1px);
        }

        .form-actions {
            margin-top: 6px;
        }

        .btn-login {
            width: 100%;
            border: none;
            border-radius: 12px;
            padding: 12px 16px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #fff;
            background: linear-gradient(120deg, #0b2f86, #19a7ff);
            box-shadow: 0 12px 26px rgba(7, 36, 102, 0.25);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(7, 36, 102, 0.3);
        }

        @keyframes shellIn {
            from {
                opacity: 0;
                transform: translateY(12px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 900px) {
            .login-shell {
                grid-template-columns: 1fr;
            }

            .login-aside {
                min-height: 240px;
            }

            .login-panel {
                padding: 32px;
            }

            .panel-head h2 {
                font-size: 24px;
            }
        }

        @media (max-width: 520px) {
            .login-aside {
                padding: 28px;
            }

            .login-panel {
                padding: 28px 22px;
            }
        }
    </style>

    <script src="js/inline.js"></script>
</head>

<body class="font-medium">
    <div class="login-page">
        <div class="login-shell">
            <div class="login-aside">
                <div class="brand">
                    <span class="brand-dot"></span>
                    <span><?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?></span>
                </div>
                <div class="welcome">
                    <div class="logo-mark">
                        <span class="logo-dot"></span>
                        <span>Konfirmasi Ujian</span>
                    </div>
                    <h1>Hai, <?php echo htmlspecialchars($val_siswa, ENT_QUOTES); ?>!</h1>
                    <p>Periksa data peserta dan masukkan token bila diminta sebelum mulai ujian.</p>
                </div>
                <div class="aside-footer">
                    <a class="btn-ghost" href="logout.php">Logout</a>
                    <div class="aside-note">Kelas: <?php echo htmlspecialchars($kelas_label, ENT_QUOTES); ?> | Sesi:
                        <?php echo htmlspecialchars($xsesi, ENT_QUOTES); ?></div>
                    <div class="aside-note"><?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?> 2026 | Developed by
                        Miftahussyarif</div>
                </div>
            </div>
            <div class="login-panel">
                <div class="panel-head">
                    <div class="panel-kicker"><?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?></div>
                    <h2>Konfirmasi Data Peserta</h2>
                    <p>Pastikan data sudah benar sebelum melanjutkan ujian.</p>
                </div>
                <div id="myerror" class="alert-card<?php echo $token_error ? ' is-visible' : ''; ?>">
                    <div class="alert-title">Peringatan</div>
                    <div class="alert-body">Kode token tidak sesuai.</div>
                </div>

                <form action="mulai.php" method="post">
                    <div class="confirm-card">
                        <div class="confirm-item">
                            <div class="confirm-label">Nomor Peserta</div>
                            <div class="confirm-value"><?php echo htmlspecialchars($user, ENT_QUOTES); ?></div>
                            <input id="KodeNik" name="KodeNik" type="hidden"
                                value="<?php echo htmlspecialchars($user, ENT_QUOTES); ?>">
                        </div>
                        <div class="confirm-item">
                            <div class="confirm-label">Nama Peserta</div>
                            <div class="confirm-value">
                                <?php echo htmlspecialchars($val_siswa . " (" . $kelas_label . ")", ENT_QUOTES); ?>
                            </div>
                            <input id="NamaPeserta" name="NamaPeserta" type="hidden" value="glyphicon-warning-sign">
                        </div>
                        <div class="confirm-item">
                            <div class="confirm-label">Jenis Kelamin</div>
                            <div class="confirm-value"><?php echo htmlspecialchars($jekel, ENT_QUOTES); ?></div>
                            <input id="Gender" name="Gender" type="hidden" value="Pria">
                        </div>

                        <?php
                        $sqlada = mysql_query("SELECT * FROM  `cbt_siswa_ujian` WHERE XNomerUjian = '$txtuser' and XTokenUjian = '$xtokenujian'");
                        $ad = mysql_fetch_array($sqlada);
                        $jumsis = $ad['XStatusUjian'];

                        $ada = mysql_num_rows($sqlada);
                        ?>
                        <?php
                        if ($xkodesoal !== '') { ?>
                            <div class="confirm-item">
                                <div class="confirm-label">Mata Pelajaran</div>
                                <div class="confirm-value"><?php echo htmlspecialchars($xnamamapel, ENT_QUOTES); ?></div>
                                <input id="KodePaket" name="KodePaket" type="hidden" value="IPA - SMP">
                            </div>

                            <?php if ($xsedang_berlangsung && ($jumsis !== '9')) { ?>
                                <div class="form-field">
                                    <label for="KodeToken">Token Ujian</label>
                                    <input autocomplete="off" data-val="true" data-val-required="Kode token wajib diisi"
                                        id="KodeToken" maxlength="20" name="KodeToken" placeholder="Masukkan token"
                                        type="text" value="">
                                </div>
                                <div class="form-actions">
                                    <button type="submit" class="btn-login">Submit</button>
                                </div>
                            <?php } else { ?>
                                <div class="confirm-item is-status">
                                    <div class="confirm-label">Status Ujian</div>
                                    <div class="confirm-value">
                                        <?php if ($jumsis == '9') { ?>
                                            Status tes sudah selesai.
                                        <?php } elseif ($xmulai_ts <= 0) { ?>
                                            Tidak ada jadwal ujian.
                                        <?php } elseif ($xnow_ts < $xmulai_ts) { ?>
                                            Belum waktunya.
                                        <?php } elseif ($xnow_ts > $xbatasmasuk_efektif_ts) { ?>
                                            Terlambat untuk mengikuti ujian.
                                        <?php } else { ?>
                                            Tidak ada jadwal ujian.
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>

                        <?php } else { ?>
                            <div class="confirm-item is-status">
                                <div class="confirm-label">Status Ujian</div>
                                <div class="confirm-value">Tidak ada mata uji aktif.</div>
                            </div>
                        <?php } ?>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="panel-default">
                    <div class="panel-heading">
                        <h1 class="panel-title page-label">Konfirmasi Tes</h1>
                    </div>
                    <div class="panel-body">
                        <div class="inner-content">
                            <div class="wysiwyg-content">
                                <p>
                                    Terimakasi telah berpartisipasi dalam tes ini.<br>
                                    Silahkan klik tombol LOGOUT untuk mengakhiri test.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="row">
                            <div class="col-xs-offset-3 col-xs-6">
                                <button type="submit" class="btn btn-success btn-block"
                                    data-dismiss="modal">LOGOUT</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery.cookie.js"></script>
    <script src="js/common.js"></script>
    <script src="js/main.js"></script>
    <script src="js/cookieList.js"></script>
    <script src="js/backend.js"></script>
</body>

</html>
