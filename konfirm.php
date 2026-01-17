<?php
include "config/server.php";
include "ip.php";

try {
    db_query($db, "SELECT 1 FROM `cbt_siswa` limit 1", array());
} catch (PDOException $e) {
    header('Location:login.php?salah=2');
    exit;
}

$txtuser = '';
$txtpass = '';

if (isset($_COOKIE['PESERTA'], $_COOKIE['KUNCI'])) {
    $txtuser = trim($_COOKIE['PESERTA']);
    $txtpass = trim($_COOKIE['KUNCI']);
} else {
    $txtuser = isset($_REQUEST['UserName']) ? str_replace(' ', '', $_REQUEST['UserName']) : '';
    $txtpass = isset($_REQUEST['Password']) ? str_replace(' ', '', $_REQUEST['Password']) : '';

    $cookie_options = array(
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => !empty($_SERVER['HTTPS']),
    );
    setcookie('PESERTA', $txtuser, $cookie_options);
    setcookie('KUNCI', $txtpass, $cookie_options);
}

$stmt = db_query(
    $db,
    "SELECT * FROM `cbt_siswa` WHERE XNomerUjian = :user LIMIT 1",
    array('user' => $txtuser)
);
$sis = db_fetch_one($stmt);

if (!$sis || !isset($sis['XPassword'])) {
    header('Location:login.php?salah=1&jumlah=0');
    exit;
}

$stored = (string) $sis['XPassword'];
$valid = false;
if (password_get_info($stored)['algo'] !== 0) {
    $valid = password_verify($txtpass, $stored);
} elseif (strlen($stored) === 32 && ctype_xdigit($stored)) {
    $valid = hash_equals($stored, md5($txtpass));
    if ($valid) {
        $new_hash = password_hash($txtpass, PASSWORD_DEFAULT);
        db_query(
            $db,
            "update cbt_siswa set XPassword = :hash where Urut = :urut",
            array('hash' => $new_hash, 'urut' => $sis['Urut'])
        );
    }
} else {
    $valid = hash_equals($stored, $txtpass);
    if ($valid) {
        $new_hash = password_hash($txtpass, PASSWORD_DEFAULT);
        db_query(
            $db,
            "update cbt_siswa set XPassword = :hash where Urut = :urut",
            array('hash' => $new_hash, 'urut' => $sis['Urut'])
        );
    }
}

if (!$valid) {
    header('Location:login.php?salah=1&jumlah=0');
    exit;
}

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
//echo "SELECT * FROM  `cbt_siswa` WHERE XNomerUjian = '$txtuser' and XPassword = '$txtpass'";
$tglujian = date("Y-m-d");
$xjam1 = date("H:i:s");

//  $user = $_COOKIE['PESERTA'];
//  setcookie('PESERTA',$user);


$sqluser = db_query(
    $db,
    "
SELECT u.*,m.XNamaMapel FROM `cbt_ujian` u LEFT JOIN cbt_paketsoal p on p.XKodeKelas = u.XKodeKelas and p.XKodeMapel = u.XKodeMapel
left join cbt_mapel m on u.XKodeMapel = m.XKodeMapel 
WHERE (u.XKodeKelas = :xkelz or u.XKodeKelas = 'ALL') and (u.XKodeJurusan = :xjurz or u.XKodeJurusan = 'ALL') and u.XSesi = :xsesi and u.XTglUjian = :tglujian and u.XJamUjian <= :xjam1
and u.XStatusUjian = '1' ORDER BY u.XJamUjian DESC LIMIT 1",
    array(
        'xkelz' => $xkelz,
        'xjurz' => $xjurz,
        'xsesi' => $xsesi,
        'tglujian' => $tglujian,
        'xjam1' => $xjam1,
    )
);

$s = db_fetch_one($sqluser);
if (!$s) {
    $sqluser = db_query(
        $db,
        "
    SELECT u.*,m.XNamaMapel FROM `cbt_ujian` u LEFT JOIN cbt_paketsoal p on p.XKodeKelas = u.XKodeKelas and p.XKodeMapel = u.XKodeMapel
    left join cbt_mapel m on u.XKodeMapel = m.XKodeMapel 
    WHERE (u.XKodeKelas = :xkelz or u.XKodeKelas = 'ALL') and (u.XKodeJurusan = :xjurz or u.XKodeJurusan = 'ALL') and u.XSesi = :xsesi and u.XTglUjian = :tglujian and u.XJamUjian > :xjam1
    and u.XStatusUjian = '1' ORDER BY u.XJamUjian ASC LIMIT 1",
        array(
            'xkelz' => $xkelz,
            'xjurz' => $xjurz,
            'xsesi' => $xsesi,
            'tglujian' => $tglujian,
            'xjam1' => $xjam1,
        )
    );
    $s = db_fetch_one($sqluser);
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

$sqlada0 = db_query(
    $db,
    "SELECT XGetIP FROM `cbt_siswa_ujian` WHERE XNomerUjian = :user and XTokenUjian = :token limit 1",
    array('user' => $txtuser, 'token' => $xtokenujian)
);
$ad0 = db_fetch_one($sqlada0);
$user_ip2 = $ad0 ? str_replace(" ", "", $ad0['XGetIP']) : '';
$user_ip1 = $user_ip;
//echo " $user_ip1 = $user_ip2 | $user_ip";
if ($user_ip1 <> $user_ip2 && !$user_ip2 == "") {
    header('Location:login.php?salah=3');
    //echo " Beda";
}


?>

<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>CBT SMK AL QODIRIYAH | UJIAN ONLINE</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        function disableBackButton() {
            window.history.forward();
        }
        setTimeout("disableBackButton()", 0);
    </script>

    <style>
        .no-close .ui-dialog-titlebar-close {
            display: none;
        }
    </style>
    <style>
        .left {
            float: left;
            width: 70%;
            overflow: hidden;
        }

        .left img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: cover;
        }

        .right {
            float: right;
            width: 30%;
            background-color: #333333;
            height: 101px;
            color: #FFFFFF;
            font-size: 13px;
            font-style: normal;
            font-weight: normal;
        }

        .user {
            color: #FFFFFF;
            font-size: 15px;
            font-style: normal;
            font-weight: bold;
            top: -20px;
        }

        .log {
            color: #3799c2;
            font-size: 11px;
            font-style: normal;
            font-weight: bold;
            top: -20px;
        }

        .group:after {
            content: "";
            display: table;
            clear: both;

        }

        /*
img {
    max-width: 100%;
    height: auto;
}
*/

        .visible {
            display: block !important;
        }

        .hidden {
            display: none !important;
        }

        .foto {
            height: 80px;
        }

        @media screen and (max-width: 780px) {

            /* jika screen maks. 780 right turun */
            /*    .left, */
            .left {
                float: none;
                width: 100%;
                height: auto;
                display: block;
            }
            .left img {
                width: 100%;
                height: auto;
            }
            .right {
                float: none;
                width: 100%;
                margin-top: 0px;
                height: auto;
                min-height: 80px;
                padding: 10px;
                color: #FFFFFF;
                display: block;
            }

            .foto {
                height: 80px;
            }
        }

        @media screen and (max-width: 400px) {

            /* jika screen maks. 780 right turun */
            /*    .left, */
            .left {
                width: auto;
                height: 91px;
            }

            .right {
                float: none;
                width: auto;
                margin-top: 0px;
                height: 60px;
                color: #FFFFFF;
            }

            .foto {
                height: 40px;
            }
        }
    </style>
    <link href="css/klien.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap2.min.css">

    <script src="js/inline.js"></script>
    <?php
    $sql = db_query($db, "select * from cbt_admin limit 1", array());
    $r = db_fetch_one($sql);
    ?>

<body class="font-medium" style="background-color:#c9c9c9">
    <header style="background-color:<?php echo isset($r['XWarna']) ? $r['XWarna'] : ''; ?>">
        <div class="group">
            <div class="left" style="background-color:<?php echo isset($r['XWarna']) ? $r['XWarna'] : ''; ?>"><a
                    href=" "><img src="images/<?php echo isset($r['XBanner']) ? $r['XBanner'] : ''; ?>"
                        style=" margin-left:0px;"></a>
            </div>
            <div class="right">
                <table width="100%" border="0" cellspacing="5px;" style="margin-top:10px">
                    <tr>
                        <td rowspan="3" width="100px" align="center"><img src="images/avatar.gif"
                                style=" margin-left:0px; margin-top:5px" class="foto"></td>
                        <td><span style=" margin-left:0px; margin-top:5px">Selamat Datang</span></td>
                    </tr>
                    <tr>
                        <td><span class="user"><?php echo "$val_siswa ($xkodekelas)"; ?></span></td>
                    </tr>
                    <tr>
                        <td><span class="log"><a href="logout.php">Logout</a><span></td>
                    </tr>
                </table>
            </div>

        </div>
        </div>
        </div>
    </header>
    <ul>
        <div id="myerror" class="alert alert-danger" role="alert"
            style="font-size: 13px; font-style:normal; font-weight:normal; margin-left:-45px; padding-left:90px;">
            <?php
            if (isset($_REQUEST['salah'])) {
                if ($_REQUEST['salah'] == 1) {
                    echo "<b><ul><li>Kode TOKEN Tidak sesuai</li></ul></b>";
                }
            }
            ?>
        </div>
    </ul>

    <div class="col-md-6 col-md-offset-3 login-wrapper" style="float:inherit">
        <div class="panel panel-default">

            <form action="mulai.php" method="post">

                <div class="list-group-item top-heading">
                    <h1 class="list-group-item-heading page-label">Konfirmasi Data Peserta</h1>
                </div>
                <div class="list-group-item">
                    <label class="list-group-item-heading">Kode NIS</label>
                    <p class="list-group-item-text"><?php echo "$user"; ?></p>
                    <!--<input id="KodeNik" name="KodeNik" type="hidden" value="<?php echo "$user"; ?>">!-->
                    <input id="KodeNik" name="KodeNik" type="hidden" value="<?php echo "$user"; ?>">
                </div>
                <div class="list-group-item">
                    <label class="list-group-item-heading">Nama Peserta</label>
                    <p class="list-group-item-text"><?php echo "$val_siswa ($xkodekelas)"; ?></p>
                    <input id="NamaPeserta" name="NamaPeserta" type="hidden" value="glyphicon-warning-sign">
                </div>
                <div class="list-group-item">
                    <label class="list-group-item-heading">Jenis Kelamin</label>
                    <p class="list-group-item-text"><?php echo "$jekel"; ?></p>
                    <input id="Gender" name="Gender" type="hidden" value="Pria">
                </div>

                <?php
                $sqlada = db_query(
                    $db,
                    "SELECT XStatusUjian FROM `cbt_siswa_ujian` WHERE XNomerUjian = :user and XTokenUjian = :token ORDER BY XMulaiUjian DESC LIMIT 1",
                    array('user' => $txtuser, 'token' => $xtokenujian)
                );
                $ad = db_fetch_one($sqlada);
                $jumsis = $ad ? $ad['XStatusUjian'] : '';

                $sqlada_count = db_query(
                    $db,
                    "SELECT count(1) as total FROM `cbt_siswa_ujian` WHERE XNomerUjian = :user and XTokenUjian = :token",
                    array('user' => $txtuser, 'token' => $xtokenujian)
                );
                $ada = (int) db_fetch_value($sqlada_count);

                $sqlcekujian = db_query(
                    $db,
                    "SELECT count(1) as total FROM cbt_ujian where (XKodeKelas = :xkelz or XKodeKelas = 'ALL') and (XKodeJurusan = :xjurz or XKodeJurusan = 'ALL') and XStatusUjian = '1' and XSesi = :xsesi and XTglUjian = :tglujian and XJamUjian <= :xjam1",
                    array(
                        'xkelz' => $xkelz,
                        'xjurz' => $xjurz,
                        'xsesi' => $xsesi,
                        'tglujian' => $tglujian,
                        'xjam1' => $xjam1,
                    )
                );
                $sqlcekujian_future = db_query(
                    $db,
                    "SELECT count(1) as total FROM cbt_ujian where (XKodeKelas = :xkelz or XKodeKelas = 'ALL') and (XKodeJurusan = :xjurz or XKodeJurusan = 'ALL') and XStatusUjian = '1' and XSesi = :xsesi and XTglUjian = :tglujian and XJamUjian > :xjam1",
                    array(
                        'xkelz' => $xkelz,
                        'xjurz' => $xjurz,
                        'xsesi' => $xsesi,
                        'tglujian' => $tglujian,
                        'xjam1' => $xjam1,
                    )
                );
                $sqlcekujian_count = (int) db_fetch_value($sqlcekujian);
                $sqlcekujian_future_count = (int) db_fetch_value($sqlcekujian_future);
                if ($sqlcekujian_count > 0 || $sqlcekujian_future_count > 0) { ?>

                    <div class="list-group-item">
                        <label class="list-group-item-heading">Mata Pelajaran </label>
                        <p class="list-group-item-text"><?php echo "$xnamamapel"; ?></p>
                        <input id="KodePaket" name="KodePaket" type="hidden" value="IPA - SMP">
                    </div>

                    <?php if (($xjam1 <= $xbatasmasuk && $xjam1 >= $xjamujian) && ($tglujian == $xtglujian) && ($jumsis !== '9')) {
                        //$sqlout = mysql_query("Update cbt_siswa_ujian set XStatusUjian = '9' where XNomerUjian = '$user' and XStatusUjian = '1'");
                        // header('location:logout.php');
                        ?>
                        <div class="list-group-item">
                            <label class="list-group-item-heading">Token <?php // echo "$jumsis = $ada"; ?></label>
                            <div class="list-group-item-text">
                                <input autocomplete="off" class="input-token form-control field-xs" data-val="true"
                                    data-val-required="Kode 	
                    token wajib diisi" id="KodeToken" maxlength="20" name="KodeToken" placeholder="masukan token"
                                    type="text" value="">
                            </div>
                        </div>
                        <div class="list-group-item">
                            <div class="row">
                                <div class="col-xs-push-9 col-xs-3"><br>
                                    <button type="submit" class="btn btn-success btn-block doblockui">SUBMIT</button>
                                </div>
                            </div>
                        </div>

                    <?php } else {
                        ?>
                        <div class="list-group-item">
                            <label class="list-group-item-heading">Status Ujian <?php // echo "$jumsis = $ada"; ?></label>
                            <div class="list-group-item-text">
                                <?php if ($jumsis == '9') { ?>
                                    <p class="list-group-item-text">Status Tes sudah SELESAI</p>
                                <?php } elseif ($tglujian !== $xtglujian) { ?>
                                    <p class="list-group-item-text">Tidak Ada Jadwal Ujian</p>
                                <?php } elseif ($xjam1 < $xjamujian) { ?>
                                    <p class="list-group-item-text">Belum Waktunya</p>
                                <?php } elseif ($xjam1 > $xbatasmasuk) { ?>
                                    <p class="list-group-item-text">Terlambat Untuk Mengikuti Ujian</p>
                                <?php } else { ?>
                                    <p class="list-group-item-text">Tidak Ada Jadwal Ujian</p>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>

                <?php } else { ?>
                    <div class="list-group-item">
                        <label class="list-group-item-heading">Status Ujian<?php // echo "$jumsis / $ada"; ?> </label>
                        <div class="list-group-item-text">
                            <p class="list-group-item-text">Tidak ada Mata Uji AKTIF</p>
                        </div>
                    </div>

                <?php } ?>

        </div>
        </form>
    </div>
    </div>

    <div style="margin-top:160px; bottom:50px; background-color:#dcdcdc; padding:7px; font-size:12px">
        <div class="content">
            CBT.SMKALQ.Web:<strong>2.2</strong><br>
            CBT.Baseclass:<strong>1.0</strong><br>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
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
                                    Silahkan klik tombol LOGOUT untuk mengakhiri test. </p>
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

    <footer>
        <div class="container" style=" font-size:12px">
            <p>CBT SMK AL QODIRIYAH 2026 | Developed by. Miftahussyarif</p>
        </div>
    </footer>
    <script src="js/jquery.cookie.js"></script>
    <script src="js/common.js"></script>
    <script src="js/main.js"></script>
    <script src="js/cookieList.js"></script>
    <script src="js/backend.js"></script>
