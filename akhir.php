<?php include "config/server.php";
include_once "cbt_exam_context.php";
// ===============================
// Status Ujian XStatusUjian = 1 Aktif
// Status Ujian XStatusUjian = 0 BelumAktif
// Status Ujian XStatusUjian = 9 Selesai

if (isset($_COOKIE['PESERTA'])) {
	$user = $_COOKIE['PESERTA'];
} else {
	header('Location:login.php');
}

$tgl = date("H:i:s");
$tgl2 = date("Y-m-d");

$preferToken = isset($_COOKIE['CBT_TOKEN']) ? $_COOKIE['CBT_TOKEN'] : '';
$preferKode = isset($_COOKIE['CBT_KODESOAL']) ? $_COOKIE['CBT_KODESOAL'] : '';
$s0 = cbt_get_attempt_context_for_student($user, $preferToken, $preferKode);
if (!$s0) {
    $s0 = cbt_get_attempt_context_for_student($user);
}
if (!$s0) {
    $s0 = cbt_get_schedule_context_for_student($user, $preferToken);
}
if (!$s0) {
    $s0 = array();
}
$xkodesoal = isset($s0['XKodeSoal']) ? $s0['XKodeSoal'] : '';
$xtokenujian = isset($s0['XTokenUjian']) ? $s0['XTokenUjian'] : '';
$xnomerujian = isset($s0['XNomerUjian']) ? $s0['XNomerUjian'] : $user;
$xnik = isset($s0['XNIK']) ? $s0['XNIK'] : '';
$xkodeujian = isset($s0['XKodeUjian']) ? $s0['XKodeUjian'] : '';
$xkodemapel = isset($s0['XKodeMapel']) ? $s0['XKodeMapel'] : '';
$xkodekelas = isset($s0['XKodeKelas']) ? $s0['XKodeKelas'] : '';
$xkodejurusan = isset($s0['XKodeJurusan']) ? $s0['XKodeJurusan'] : '';
$xsesi = isset($s0['XSesi']) ? $s0['XSesi'] : '';
$xsemester = '';

// Update Status Ujian to 9 (Selesai) IMMEDIATELY once we confirm the user and token
// This ensures that even if scoring fails, the user is marked as finished.
if (isset($xtokenujian) && $xtokenujian != "") {
    $sql_update_status = mysql_query("Update cbt_siswa_ujian set XStatusUjian = '9', XLastUpdate = '$tgl' where XNomerUjian = '$user' and XStatusUjian = '1' and XTokenUjian = '$xtokenujian' and XKodeSoal = '$xkodesoal' and XSesi = '$xsesi'");
} else {
    // Fallback if token is missing but user has active exam
    $sql_update_status = mysql_query("Update cbt_siswa_ujian set XStatusUjian = '9', XLastUpdate = '$tgl' where XNomerUjian = '$user' and XStatusUjian = '1'");
}

$sqlsoal = mysql_query("SELECT * FROM cbt_ujian WHERE XKodeSoal = '$xkodesoal' and XTokenUjian = '$xtokenujian' and XSesi = '$xsesi' ORDER BY Urut DESC LIMIT 1");
$sa = mysql_fetch_array($sqlsoal);
if (!$sa) {
    $sqlsoal = mysql_query("SELECT * FROM cbt_ujian WHERE XKodeSoal = '$xkodesoal' and XTokenUjian = '$xtokenujian' ORDER BY Urut DESC LIMIT 1");
    $sa = mysql_fetch_array($sqlsoal);
}
$xsemester = isset($sa['XSemester']) ? $sa['XSemester'] : $xsemester;
//$xkodeujian = $sa['XKodeUjian'];
$xjumsoal = isset($sa['XJumSoal']) ? (int)$sa['XJumSoal'] : 0;
$xjumpil = isset($sa['XPilGanda']) ? (int)$sa['XPilGanda'] : 0;
$xjumbenar = 0;
$xjumsalah = 0;
$nilai_tampil = "0,00";
$has_esai = false;

if ($xjumsoal > 0) {

	$sqlnilai = mysql_query(" SELECT * FROM cbt_paketsoal WHERE XKodeSoal = '$xkodesoal'");
	$sqn = mysql_fetch_array($sqlnilai);
	$per_pil = $sqn['XPersenPil'];
	$per_esai = $sqn['XPersenEsai'];
	$xjumesai = isset($sqn['XEsai']) ? (int)$sqn['XEsai'] : 0;
	if ($xjumesai < 1 && isset($sa['XEsai'])) {
		$xjumesai = (int)$sa['XEsai'];
	}



	$xjumbenarz = mysql_query("select count(XNilai) as benar from cbt_jawaban where XUserJawab = '$user' and XJenisSoal = '1' and XKodeSoal = '$xkodesoal' and XTokenUjian = '$xtokenujian' and XNilai = '1'");
	$r = mysql_fetch_array($xjumbenarz);
	$xjumbenar = $r['benar'];
	$xjumsalah = $xjumpil - $xjumbenar;
	if ($xjumsalah < 0) {
		$xjumsalah = 0;
	}
	if ($xjumpil > 0) {
		$nilaix = ($xjumbenar / $xjumpil) * 100;
	} else {
		$nilaix = 0;
	}
	$nilai_tampil = number_format($nilaix, 2, ',', '.');
	$has_esai = ($xjumesai > 0);
	if (isset($_COOKIE['beetahun'])) {
		$setAY = $_COOKIE['beetahun'];
	} else {
		$setAY = "2016/2017";
	}

	//cek apakah nilai untuk token ini sudah ada atau tidak 
	$sqlceknilai = mysql_num_rows(mysql_query("select * from cbt_nilai where XNomerUjian = '$xnomerujian' and XKodeSoal = '$xkodesoal' and XTokenUjian = '$xtokenujian' 
		  and XSemester = '$xsemester' and XSetId = '$setAY' and XKodeMapel = '$xkodemapel' and XNIK = '$xnik'"));

	if ($sqlceknilai > 0) {
		$sqlmasuk = mysql_query("update cbt_nilai set XJumSoal='$xjumsoal',XBenar='$xjumbenar',XSalah='$xjumsalah',XNilai='$nilaix',XTotalNilai='$nilaix'
		  where XNomerUjian = '$xnomerujian' and XKodeSoal = '$xkodesoal' and XTokenUjian = '$xtokenujian' and XSemester = '$xsemester' and XSetId = '$setAY' 
		  and XKodeMapel = '$xkodemapel' and XNIK = '$xnik'");
	} else {
		$sqlmasuk = mysql_query("insert into cbt_nilai (
		  XKodeUjian,XTokenUjian,XTgl,XJumSoal,XBenar,XSalah,XNilai,XKodeMapel,XKodeKelas,XKodeSoal,XNomerUjian,XNIK,XSemester,XSetId,XPersenPil,XPersenEsai,XTotalNilai) 
		  values 
		  ('$xkodeujian','$xtokenujian','$tgl2','$xjumsoal','$xjumbenar','$xjumsalah','$nilaix','$xkodemapel','$xkodekelas','$xkodesoal','$xnomerujian','$xnik','$xsemester',
		  '$setAY','$per_pil','$per_esai','$nilaix')");
	}
}

// Get admin data for branding
$sql = mysql_query("select * from cbt_admin");
$r = mysql_fetch_array($sql);
$school_name = "SMK AL QODIRIYAH";
if (is_array($r) && isset($r['XSekolah']) && $r['XSekolah'] !== '') {
    $school_name = $r['XSekolah'];
}
$brand_name = "CBT " . $school_name;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Tes Selesai | <?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="stylesheet" href="css/bootstrap2.min.css">
    
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

        html, body {
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
            gap: 12px;
            animation: fadeUp 600ms ease 220ms both;
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
            display: block;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 16px 30px rgba(7, 36, 102, 0.3);
            text-decoration: none;
            color: #fff;
        }

        .result-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e1e5ea;
            animation: fadeUp 600ms ease 240ms both;
        }

        .score-circle {
            width: 80px;
            height: 80px;
            background: var(--page-bg-1);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
            margin: 0 auto 15px;
            box-shadow: 0 4px 10px rgba(12, 47, 116, 0.3);
        }

        .result-stats {
            display: flex;
            justify-content: space-around;
            text-align: center;
            margin-top: 15px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .stat-item h3 {
            margin: 0;
            font-size: 20px;
            color: var(--ink);
        }

        .stat-item span {
            font-size: 12px;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
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
                min-height: 200px;
            }
        }

        .avatar-img {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            border: 3px solid rgba(255,255,255,0.3);
            margin-bottom: 10px;
        }
    </style>
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
                    <img src="images/avatar.gif" class="avatar-img" alt="Student Avatar">
                    <h1>Terima Kasih</h1>
                    <p><?php echo $user; ?></p>
                    <div style="margin-top:5px; opacity:0.8; font-size:12px">Siswa Peserta Ujian</div>
                </div>
                <div class="aside-footer">
                    <div class="aside-note"><?php echo htmlspecialchars($brand_name, ENT_QUOTES); ?> 2026 | Developed by Miftahussyarif</div>
                </div>
            </div>
            
            <div class="login-panel">
                <div class="panel-head">
                    <div class="panel-kicker">Status Ujian</div>
                    <h2>Selesai</h2>
                    <p>
                        Terimakasih telah berpartisipasi dalam tes ini.<br>
                        Silahkan klik tombol LOGOUT untuk mengakhiri sesi.
                    </p>
                </div>

                <div class="result-card">
                    <div style="font-size:12px; text-transform:uppercase; color:var(--muted); font-weight:700; text-align:center; margin-bottom:15px;">Hasil Pilihan Ganda</div>
                    
                    <div class="score-circle">
                        <?php echo $nilai_tampil; ?>
                    </div>
                    <?php if ($has_esai) { ?>
                        <div style="text-align:center; margin-top:-5px; margin-bottom:8px; font-size:11px; color:#856404; background:#fff3cd; border:1px solid #ffeeba; padding:6px 10px; border-radius:8px;">
                            Nilai esai menunggu koreksi guru.
                        </div>
                    <?php } ?>

                    <div class="result-stats">
                        <div class="stat-item">
                            <h3 style="color:#28a745"><?php echo (int)$xjumbenar; ?></h3>
                            <span>Benar</span>
                        </div>
                        <div class="stat-item">
                            <h3 style="color:#dc3545"><?php echo (int)$xjumsalah; ?></h3>
                            <span>Salah</span>
                        </div>
                    </div>
                    
                    <div style="text-align:center; margin-top:20px; font-size:12px; color:var(--muted)">
                        Logout otomatis dalam 5 Menit.
                    </div>
                </div>

                <div style="margin-top:auto">
                    <a href="logout.php" class="btn-login">LOGOUT SEKARANG</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = "logout.php";
        }, 300000);
    </script>
</body>
</html>
