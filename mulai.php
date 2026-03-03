<?php
include "config/server.php";
include_once "cbt_exam_context.php";

$txtuser = '';
$user = '';
$val_siswa = '';
$xjeniskelamin = '';
$xkelz = '';
$xjurz = '';
$jekel = '';
$xkodesoal = '';
$xkodekelas = '';
$xtglujian = '';
$xkodemapel = '';
$xjumlahsoal = '';
$xtokenujian = '';
$xlamaujian = '';
$xjamujian = '';
$xbatasmasuk = '';
$xnamamapel = '';

if (isset($_COOKIE['PESERTA'])) {
    // Keep cookie value for later use.
}

if (isset($_REQUEST['KodeNik'])) {
    $txtuser = str_replace(" ", "", $_REQUEST['KodeNik']);

    $sqllogin = mysql_query("SELECT * FROM  `cbt_siswa` WHERE XNomerUjian = '$txtuser'");
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

    $tglujian = date("Y-m-d");
    $xjam1 = date("H:i:s");
    $user = isset($_COOKIE['PESERTA']) ? $_COOKIE['PESERTA'] : $txtuser;

    $request_token = isset($_REQUEST['KodeToken']) ? trim((string) $_REQUEST['KodeToken']) : '';
    if ($request_token === '' && isset($_COOKIE['CBT_TOKEN'])) {
        $request_token = trim((string) $_COOKIE['CBT_TOKEN']);
    }
    $s = cbt_get_schedule_context_for_student($txtuser, $request_token);
    if (!$s && $request_token !== '') {
        header('Location:konfirm.php?salah=1');
        exit;
    }
    if (!$s) {
        $s = cbt_get_schedule_context_for_student($txtuser, '');
    }
    $xkodesoal = isset($s['XKodeSoal']) ? $s['XKodeSoal'] : '';
    $xkodekelas = isset($s['XKodeKelas']) ? $s['XKodeKelas'] : '';
    $xtglujian = isset($s['XTglUjian']) ? $s['XTglUjian'] : '';
    $xkodemapel = isset($s['XKodeMapel']) ? $s['XKodeMapel'] : '';
    $xjumlahsoal = isset($s['XJumSoal']) ? $s['XJumSoal'] : '';
    $xtokenujian = isset($s['XTokenUjian']) ? $s['XTokenUjian'] : '';
    $xlamaujian = isset($s['XLamaUjian']) ? $s['XLamaUjian'] : '';
    $xjamujian = isset($s['XJamUjian']) ? $s['XJamUjian'] : '';
    $xbatasmasuk = isset($s['XBatasMasuk']) ? $s['XBatasMasuk'] : '';
    $xnamamapel = isset($s['XNamaMapel']) ? $s['XNamaMapel'] : '';
    $xsesi = isset($s['XSesi']) ? $s['XSesi'] : $xsesi;

    if ($request_token === '' || $request_token !== $xtokenujian) {
        header('Location:konfirm.php?salah=1');
        exit;
    }
    if ($xkodesoal !== '' && $xtokenujian !== '') {
        cbt_set_exam_context_cookies($xtokenujian, $xkodesoal, $xsesi);
    }
}

if (isset($xkodesoalz)) {
    echo "SELECT *,s.XKodeKelas as kelassiswa,u.XKodeSoal as kelsoal FROM  `cbt_siswa` s LEFT JOIN cbt_ujian u ON s.XKodeKelas =  
  u.XKodeKelas
  left join cbt_mapel m on  m.XKodeMapel = u.XKodeMapel
  WHERE XNomerUjian = '$user' and u.XStatusUjian = '1'";
}

$sql_admin = mysql_query("select * from cbt_admin");
$r = mysql_fetch_array($sql_admin);
$school_name = "SMK AL QODIRIYAH";
if (is_array($r) && isset($r['XSekolah']) && $r['XSekolah'] !== '') {
    $school_name = $r['XSekolah'];
}
$brand_name = "CBT " . $school_name;

$kelas_label = $xkodekelas !== '' ? $xkodekelas : $xkelz;
$mapel_label = $xnamamapel !== '' ? $xnamamapel : '-';
$token_label = $xtokenujian !== '' ? $xtokenujian : '-';
$student_label = $val_siswa !== '' ? $val_siswa : 'Peserta';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?> | Mulai Ujian</title>
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
            width: min(1040px, 100%);
            display: grid;
            grid-template-columns: minmax(260px, 42%) minmax(320px, 58%);
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

        .info-card {
            border-left: 4px solid #ffb35a;
            background: #fff2da;
            color: #7a4a17;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .info-card .glyphicon {
            font-size: 16px;
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
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            justify-content: center;
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
                        <span>Mulai Ujian</span>
                    </div>
                    <h1>Siap, <?php echo htmlspecialchars($student_label, ENT_QUOTES); ?>?</h1>
                    <p>Periksa ringkasan tes sebelum menekan tombol mulai.</p>
                </div>
                <div class="aside-footer">
                    <div class="aside-note">Kelas: <?php echo htmlspecialchars($kelas_label, ENT_QUOTES); ?> | Mapel:
                        <?php echo htmlspecialchars($mapel_label, ENT_QUOTES); ?></div>
                    <div class="aside-note">Token: <?php echo htmlspecialchars($token_label, ENT_QUOTES); ?></div>
                    <a class="btn-ghost" href="logout.php">Logout</a>
                </div>
            </div>
            <div class="login-panel">
                <div class="panel-head">
                    <div class="panel-kicker"><?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?></div>
                    <h2>Konfirmasi Data Tes</h2>
                    <p>Pastikan data tes sudah benar sebelum melanjutkan.</p>
                </div>

                <form action="puspendik.php" method="post">
                    <div class="confirm-card">
                        <div class="confirm-item">
                            <div class="confirm-label">Kode Tes</div>
                            <div class="confirm-value"><?php echo htmlspecialchars($xkodesoal, ENT_QUOTES); ?></div>
                            <input id="KodeNik" name="KodeNik" type="hidden"
                                value="<?php echo htmlspecialchars($user, ENT_QUOTES); ?>">
                        </div>

                        <div class="confirm-item">
                            <div class="confirm-label">Status Tes</div>
                            <div class="confirm-value">
                                <?php echo htmlspecialchars($val_siswa . " (" . $kelas_label . ")", ENT_QUOTES); ?>
                            </div>
                            <input id="NamaPeserta" name="NamaPeserta" type="hidden" value="">
                        </div>

                        <div class="confirm-item">
                            <div class="confirm-label">Mata Uji Tes</div>
                            <div class="confirm-value">
                                <?php echo htmlspecialchars($xnamamapel, ENT_QUOTES); ?> | Token
                                <?php echo htmlspecialchars($token_label, ENT_QUOTES); ?>
                            </div>
                            <input id="Gender" name="Gender" type="hidden" value="Pria">
                        </div>

                        <?php
                        $sqlcekujian = mysql_num_rows(mysql_query("SELECT * FROM cbt_ujian where XKodeKelas = '$xkodekelas' and XStatusUjian = '1'"));
                        if ($sqlcekujian > 0) {
                            $xtglujian0 = strtotime($xtglujian);
                            $xtglujian1 = date('d/m/Y', $xtglujian0);
                            $xtglujian2 = date('d/M/Y', $xtglujian0);
                            $j1 = substr($xlamaujian, 0, 2) * 60;
                            $m1 = substr($xlamaujian, 3, 2);
                            $jumtotwaktu = $j1 + $m1;
                            ?>

                            <div class="confirm-item">
                                <div class="confirm-label">Tanggal Tes</div>
                                <div class="confirm-value"><?php echo htmlspecialchars($xtglujian2, ENT_QUOTES); ?></div>
                                <input id="KodePaket" name="KodePaket" type="hidden" value="IPA - SMP">
                            </div>
                            <div class="confirm-item">
                                <div class="confirm-label">Waktu Tes</div>
                                <div class="confirm-value">
                                    <?php echo htmlspecialchars($xtglujian1 . " " . $xjamujian, ENT_QUOTES); ?>
                                </div>
                            </div>
                            <div class="confirm-item">
                                <div class="confirm-label">Alokasi Waktu Tes</div>
                                <div class="confirm-value"><?php echo htmlspecialchars($jumtotwaktu . " menit", ENT_QUOTES); ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </form>

                <div class="info-card">
                    <span class="glyphicon glyphicon-warning-sign" aria-hidden="true"></span>
                    <span>Tombol mulai hanya akan aktif apabila waktu sekarang sudah melewati waktu mulai tes. Tekan
                        tombol F5 untuk merefresh halaman.</span>
                </div>

                <div class="form-actions">
                    <a href="ujian.php" class="btn-login">Mulai</a>
                </div>
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
