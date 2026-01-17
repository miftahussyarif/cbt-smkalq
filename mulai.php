<?php
require __DIR__ . '/config/server.php';
if (isset($_COOKIE['PESERTA'])) {
    //echo "WAHA ";
}

$user = isset($_COOKIE['PESERTA']) ? $_COOKIE['PESERTA'] : '';
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

if (isset($_REQUEST['KodeNik'])) {
    $txtuser = str_replace(" ", "", $_REQUEST['KodeNik']);
    $sqllogin = db_query(
        $db,
        "SELECT * FROM `cbt_siswa` WHERE XNomerUjian = :user",
        array(':user' => $txtuser)
    );
    $sis = db_fetch_one($sqllogin);
    if ($sis) {
        $val_siswa = $sis['XNamaSiswa'];
        $xjeniskelamin = $sis['XJenisKelamin'];
        $xkelz = $sis['XKodeKelas'];
        $xjurz = $sis['XKodeJurusan'];
    }
    if ($xjeniskelamin == "L") {
        $jekel = "LAKI-LAKI";
    } else {
        $jekel = "PEREMPUAN";
    }

    /*
    $jmlsqllogin = mysql_num_rows($sqllogin);
    if($jmlsqllogin<1){header('Location:login.php?salah=1');
    }
    */
    $tglujian = date("Y-m-d");
    $xjam1 = date("H:i:s");
    $user = isset($_COOKIE['PESERTA']) ? $_COOKIE['PESERTA'] : '';
    // setcookie('PESERTA',$user);

    //  $user = $_COOKIE['PESERTA'];
//  setcookie('PESERTA',$user);

    /* $sqluser = mysql_query("SELECT *,s.XKodeKelas as kelassiswa,u.XKodeSoal as kelsoal FROM  `cbt_siswa` s LEFT JOIN cbt_ujian u ON s.XKodeKelas =  
     u.XKodeKelas
     left join cbt_mapel m on  m.XKodeMapel = u.XKodeMapel
     WHERE XNomerUjian = '$user' and u.XStatusUjian = '1'");
   */

    if ($xkelz !== '' && $xjurz !== '') {
        $sqluser = db_query(
            $db,
            "SELECT u.*, m.XNamaMapel FROM `cbt_ujian` u LEFT JOIN cbt_paketsoal p ON p.XKodeKelas = u.XKodeKelas AND p.XKodeMapel = u.XKodeMapel
LEFT JOIN cbt_mapel m ON u.XKodeMapel = m.XKodeMapel WHERE (u.XKodeKelas = :kelas OR u.XKodeKelas = 'ALL') AND (u.XKodeJurusan = :jur OR u.XKodeJurusan = 'ALL') AND u.XTglUjian = :tgl AND u.XJamUjian <= :jam
AND u.XStatusUjian = '1'",
            array(':kelas' => $xkelz, ':jur' => $xjurz, ':tgl' => $tglujian, ':jam' => $xjam1)
        );

        $s = db_fetch_one($sqluser);
        if ($s) {
            $xkodesoal = $s['XKodeSoal'];
            $xkodekelas = $s['XKodeKelas'];
            $xtglujian = $s['XTglUjian'];
            $xkodemapel = $s['XKodeMapel'];
            $xjumlahsoal = $s['XJumSoal'];
            $xtokenujian = $s['XTokenUjian'];
            $xlamaujian = $s['XLamaUjian'];
            $xjamujian = $s['XJamUjian'];
            $xbatasmasuk = $s['XBatasMasuk'];
            $xnamamapel = $s['XNamaMapel'];
        }
    }

    if (isset($_REQUEST['KodeToken']) && $xtokenujian !== '' && $_REQUEST['KodeToken'] !== $xtokenujian) {
        header('Location:konfirm.php?salah=1');
        echo "Token Salah";
    }
}

if (isset($xkodesoalz)) {
    echo "SELECT *,s.XKodeKelas as kelassiswa,u.XKodeSoal as kelsoal FROM  `cbt_siswa` s LEFT JOIN cbt_ujian u ON s.XKodeKelas =  
  u.XKodeKelas
  left join cbt_mapel m on  m.XKodeMapel = u.XKodeMapel
  WHERE XNomerUjian = '$user' and u.XStatusUjian = '1'";
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
    <script src="js/inline.js"></script>
    <main>

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

            .right1 {
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
                .left,
                .right1 {
                    float: none;
                    width: auto;
                    margin-top: 0px;
                    height: 101px;
                    color: #FFFFFF;
                    display: block;
                }

                .foto {
                    height: 80px;
                }
            }

            @media screen and (max-width: 400px) {

                /* jika screen maks. 780 right turun */
                .left {
                    width: auto;
                    height: 91px;
                }

                .right1 {
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

        <style>
            .kiri {
                float: left;
                width: 59%;
            }

            .kanan {
                float: right;
                width: 40%;
                font-size: 13px;
                font-style: normal;
                font-weight: normal;
            }

            .grup:after {
                content: "";
                display: table;
                clear: both;


            }

            @media screen and (max-width: 780px) {

                /* jika screen maks. 780 right turun */
                .kiri,
                .kanan {
                    margin-top: 10px;
                    float: none;
                    width: auto;
                    display: block;
                }
            }

            @media screen and (max-width: 400px) {

                /* jika screen maks. 780 right turun */
                .kiri {
                    width: auto;
                }

                .kanan {
                    float: none;
                    margin-top: 10px;
                    width: auto;
                }
            }
        </style>


        <link href="css/klien.css" rel="stylesheet">
        <link rel="stylesheet" href="css/bootstrap2.min.css">

        <script src="js/inline.js"></script>
        <?php
        require_once __DIR__ . "/config/server.php";
        $sql = db_query($db, "SELECT * FROM cbt_admin LIMIT 1", array());
        $r = db_fetch_one($sql);
        if (!$r) {
            $r = array('XWarna' => '', 'XBanner' => '');
        }
        ?>
        <header style="background-color:<?php echo "{$r['XWarna']}"; ?>">
            <div class="group">
                <div class="left" style="background-color:<?php echo "{$r['XWarna']}"; ?>"><a href=" "><img
                            src="images/<?php echo "{$r['XBanner']}"; ?>" style=" margin-left:0px;"></a>
                </div>
                <div class="right1">
                    <table width="100%" border="0" cellspacing="5px;" style="margin-top:10px">
                        <tr>
                            <td rowspan="3" width="100px" align="center"><img src="images/avatar.gif"
                                    style=" margin-left:0px; margin-top:5px" class="foto"></td>
                            <td><span style=" margin-left:0px; margin-top:5px">Selamat Datang</span></td>
                        </tr>
                        <tr>
                            <td><span class="user"><?php echo "$val_siswa ($xkelz)"; ?></span></td>
                        </tr>
                        <tr>
                            <td><span class="log"><a href="logout.php">Logout</a><span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </header>

        <div class="grup" style="width:70%; margin:0 auto; margin-top:50px">
            <div class="kiri">
                <form action="puspendik.php" method="post">


                    <div class="list-group-item top-heading">
                        <h1 class="list-group-item-heading page-label">Konfirmasi Data Tes </h1>
                    </div>

                    <div class="list-group-item">
                        <label class="list-group-item-heading">Kode Tes</label>
                        <p class="list-group-item-text">
                            <?php if (isset($xkodesoal)) {
                                echo "$xkodesoal";
                            } ?>
                        </p>
                        <!--<input id="KodeNik" name="KodeNik" type="hidden" value="21605111610018">!-->
                        <input id="KodeNik" name="KodeNik" type="hidden" value="<?php echo "$user"; ?>">
                    </div>

                    <div class="list-group-item">
                        <label class="list-group-item-heading">Status Tes</label>
                        <p class="list-group-item-text"><?php echo "$val_siswa ($xkodekelas)"; ?></p>
                        <input id="NamaPeserta" name="NamaPeserta" type="hidden" value="">
                    </div>
                    <div class="list-group-item">
                        <label class="list-group-item-heading">Mata Uji Tes</label>
                        <p class="list-group-item-text"><?php echo "$xnamamapel"; ?>
                            <?php echo "| Token $xtokenujian"; ?></p>
                        <input id="Gender" name="Gender" type="hidden" value="Pria">
                    </div>

                    <?php
                    $sqlcekujian = 0;
                    if ($xkodekelas !== '') {
                        $sqlcekujian = (int) db_fetch_value(
                            db_query(
                                $db,
                                "SELECT COUNT(*) FROM cbt_ujian WHERE XKodeKelas = :kelas AND XStatusUjian = '1'",
                                array(':kelas' => $xkodekelas)
                            )
                        );
                    }
                    if ($sqlcekujian > 0) {
                        $xtglujian0 = strtotime($xtglujian);
                        $xtglujian1 = date('d/m/Y', $xtglujian0);
                        $xtglujian2 = date('d/M/Y', $xtglujian0);
                        $j1 = substr($xlamaujian, 0, 2) * 60;
                        $m1 = substr($xlamaujian, 3, 2);
                        $jumtotwaktu = $j1 + $m1;
                        ?>

                        <div class="list-group-item">
                            <label class="list-group-item-heading">Tanggal Tes </label>
                            <p class="list-group-item-text"><?php echo "$xtglujian2"; ?></p>
                            <input id="KodePaket" name="KodePaket" type="hidden" value="IPA - SMP">
                        </div>
                        <div class="list-group-item">
                            <label class="list-group-item-heading">Waktu Tes </label>
                            <p class="list-group-item-text"><?php echo "$xtglujian1 $xjamujian"; ?></p>
                        </div>
                        <div class="list-group-item">
                            <label class="list-group-item-heading">Alokasi Waktu Tes </label>
                            <p class="list-group-item-text"><?php echo "$jumtotwaktu menit"; ?></p>
                        </div>
                    <?php } ?>

                </form>
            </div>

            <div class="kanan">

                <div id="myerror" class="alert alert-warning" role="alert"
                    style="font-size: 13px; font-style:normal; font-weight:normal">
                    <i class="glyphicon glyphicon-warning-sign"></i>
                    <font size="3px">Tombol MULAI hanya akan aktif apabila waktu sekarang sudah melewati waktu mulai
                        tes. Tekan tombol F5 untuk merefresh halaman</font>
                </div><a href=ujian.php><button type="submit"
                        class="btn btn-danger btn-block doblockui">MULAI</button></a>

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
        <br>
        <footer style=" width:100%;bottom:0px; position:absolute; margin-top:50px">
            <div class="container" style=" font-size:12px">
                <p>CBT SMK AL QODIRIYAH 2026 | Developed by. Miftahussyarif</p>
            </div>
        </footer>
        <script src="js/jquery.cookie.js"></script>
        <script src="js/common.js"></script>
        <script src="js/main.js"></script>
        <script src="js/cookieList.js"></script>
        <script src="js/backend.js"></script>
