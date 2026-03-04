<?php
if (!isset($_COOKIE['beeuser'])) {
    header("Location: login.php");
}
include "../../config/server.php";

if (!function_exists('cbt_resolve_panel_media_url')) {
    function cbt_resolve_panel_media_url($filename)
    {
        $clean = trim((string) $filename);
        if ($clean === '') {
            return '';
        }

        $clean = str_replace('\\', '/', $clean);
        $clean = basename($clean);
        $base = pathinfo($clean, PATHINFO_FILENAME);
        $webp = $base . '.webp';

        if (file_exists(__DIR__ . '/../../pictures_webp/' . $webp)) {
            return '../../pictures_webp/' . rawurlencode($webp);
        }
        if (file_exists(__DIR__ . '/../../pictures/' . $clean)) {
            return '../../pictures/' . rawurlencode($clean);
        }
        return '../../pictures/' . rawurlencode($clean);
    }
}

$req_soal = isset($_REQUEST['soal']) ? trim($_REQUEST['soal']) : '';
$req_siswa = isset($_REQUEST['siswa']) ? trim($_REQUEST['siswa']) : '';
$req_token = isset($_REQUEST['token']) ? trim($_REQUEST['token']) : '';

if ($req_soal === '' || $req_siswa === '') {
    echo "<div class='alert alert-warning'>Parameter tidak lengkap. Gunakan link dari menu Analisa Jawaban (soal dan siswa wajib ada).</div>";
    exit;
}

$soalSafe = mysql_real_escape_string($req_soal);
$siswaSafe = mysql_real_escape_string($req_siswa);
$tokenSafe = mysql_real_escape_string($req_token);

if ($req_token === '') {
    $sqlToken = mysql_query("SELECT XTokenUjian FROM cbt_siswa_ujian WHERE XKodeSoal = '$soalSafe' AND XNomerUjian = '$siswaSafe' ORDER BY Urut DESC LIMIT 1");
    if ($sqlToken && mysql_num_rows($sqlToken) > 0) {
        $tok = mysql_fetch_array($sqlToken);
        $req_token = $tok['XTokenUjian'];
        $tokenSafe = mysql_real_escape_string($req_token);
    } else {
        $sqlToken = mysql_query("SELECT XTokenUjian FROM cbt_jawaban WHERE XKodeSoal = '$soalSafe' AND XUserJawab = '$siswaSafe' ORDER BY Urut DESC LIMIT 1");
        if ($sqlToken && mysql_num_rows($sqlToken) > 0) {
            $tok = mysql_fetch_array($sqlToken);
            $req_token = $tok['XTokenUjian'];
            $tokenSafe = mysql_real_escape_string($req_token);
        }
    }
}

$var_token = $req_token;
$var_soal = $req_soal;
$var_siswa = $req_siswa;
$var_pil = 0;
$var_esai = 0;
$per_pil = 0;
$per_esai = 0;
$tglujian = '-';

$qHasil = "SELECT *,u.XStatusUjian as ujsta
FROM cbt_siswa s
LEFT JOIN cbt_siswa_ujian u ON u.XNomerUjian = s.XNomerUjian
LEFT JOIN cbt_ujian c ON (u.XKodeSoal = c.XKodeSoal and u.XTokenUjian = c.XTokenUjian)
WHERE c.XKodeSoal = '$soalSafe' and u.XNomerUjian = '$siswaSafe'
and c.XTokenUjian = '$tokenSafe' ORDER BY u.Urut DESC LIMIT 1";

$hasil = mysql_query($qHasil);
if ($hasil && mysql_num_rows($hasil) > 0) {
    $p = mysql_fetch_array($hasil);
    $var_token = $p['XTokenUjian'];
    $var_soal = $p['XKodeSoal'];
    $var_pil = (int) $p['XPilGanda'];
    $var_esai = (int) $p['XEsai'];
    $per_pil = isset($p['XPersenPil']) ? (float) $p['XPersenPil'] : 0;
    $per_esai = isset($p['XPersenEsai']) ? (float) $p['XPersenEsai'] : 0;
    $tglujian = $p['XTglUjian'];
}

$sqlPaket = mysql_query("SELECT XPilGanda, XEsai, XPersenPil, XPersenEsai FROM cbt_paketsoal WHERE XKodeSoal = '$soalSafe' LIMIT 1");
if ($sqlPaket && mysql_num_rows($sqlPaket) > 0) {
    $paket = mysql_fetch_array($sqlPaket);
    $var_pil = (int) $paket['XPilGanda'];
    $var_esai = (int) $paket['XEsai'];
    $per_pil = (float) $paket['XPersenPil'];
    $per_esai = (float) $paket['XPersenEsai'];
}

$tokenSafe = mysql_real_escape_string($var_token);
$tokenFilterJ = " and j.XTokenUjian = '$tokenSafe'";
$tokenFilterC = " and c.XTokenUjian = '$tokenSafe'";

$sqlmapel = mysql_query("select * from cbt_ujian c left join cbt_mapel m on m.XKodeMapel = c.XKodeMapel where c.XKodeSoal = '$var_soal' $tokenFilterC order by c.Urut desc limit 1");
$u = $sqlmapel ? mysql_fetch_array($sqlmapel) : false;
$namamapel = $u ? $u['XNamaMapel'] : '';
$xtokenujian = $u ? $u['XTokenUjian'] : $var_token;
$kodeujian = $u ? $u['XKodeUjian'] : '';

$namaUjianTampil = $kodeujian;
if ($kodeujian !== '') {
    $kodeUjianSafe = mysql_real_escape_string($kodeujian);
    $sqltes = mysql_query("select XNamaUjian from cbt_tes where XKodeUjian = '$kodeUjianSafe' limit 1");
    if ($sqltes && mysql_num_rows($sqltes) > 0) {
        $rtes = mysql_fetch_array($sqltes);
        if (trim((string) $rtes['XNamaUjian']) !== '') {
            $namaUjianTampil = $rtes['XNamaUjian'];
        }
    }
}
if ($namaUjianTampil === '') {
    $namaUjianTampil = ($var_soal !== '') ? $var_soal : 'TRY OUT';
}

$sqlsiswa = mysql_query("SELECT * FROM cbt_siswa s left join cbt_kelas k on k.XKodeKelas = s.XKodeKelas WHERE s.XNomerUjian= '$var_siswa'");
$s = $sqlsiswa ? mysql_fetch_array($sqlsiswa) : false;
$namsis = $s ? $s['XNamaSiswa'] : '';
$namkel = $s ? $s['XNamaKelas'] : '';
$nomsis = $s ? $s['XNIK'] : '';
$namjur = $s ? $s['XKodeJurusan'] : '';

$sqlad = mysql_query("select * from cbt_admin");
$ad = $sqlad ? mysql_fetch_array($sqlad) : false;
$logsek = $ad ? $ad['XLogo'] : '';
$logoUrl = ($logsek !== '') ? '../../images/' . rawurlencode($logsek) : '';

$sqljawaban = mysql_query("SELECT count(XNilai) AS HasilUjian FROM cbt_jawaban j WHERE j.XKodeSoal = '$var_soal' and j.XUserJawab = '$var_siswa' and j.XNilai = '1' $tokenFilterJ");
$sqj = $sqljawaban ? mysql_fetch_array($sqljawaban) : array('HasilUjian' => 0);
$jumbenar = (int) $sqj['HasilUjian'];
$nilai_pil = ($var_pil > 0) ? round((($jumbenar / $var_pil) * $per_pil), 2) : 0;
$total_pil = $nilai_pil;

$sqljawaban = mysql_query("SELECT sum(XNilaiEsai) AS HasilEsai FROM cbt_jawaban j WHERE j.XKodeSoal = '$var_soal' and j.XUserJawab = '$var_siswa' and j.XJenisSoal = '2' $tokenFilterJ");
$sqj = $sqljawaban ? mysql_fetch_array($sqljawaban) : array('HasilEsai' => 0);
if ($var_esai < 1) {
    $total_esai = 0;
} else {
    $hasil_esai = (float) $sqj['HasilEsai'];
    $total_esai = round(($hasil_esai * ($per_esai / 100)), 2);
}
$total_nilai = number_format(($total_pil + $total_esai), 2, ',', '.');
?>
<html class="home-bg">
<head>
<title>CBT SMK AL QODIRIYAH | Cetak Hasil Ujian</title>
<script type="text/javascript" src="../../MathJax/MathJax.js?config=AM_HTMLorMML-full"></script>
<script>
MathJax.Hub.Queue(["Typeset",MathJax.Hub]);
</script>
<link href="css/nedna.css" rel="stylesheet">
<style>
@media print {
    footer {page-break-after: always; top:20px}
    @page { size: A4; margin-bottom: 50px; }
}
.pageNumber { content: counter(page) }
#print-footer { display: none; }
@media print {
    #print-footer {
        display: block;
        position: fixed;
        bottom: 0;
        right:0;
        font:Arial, Helvetica, sans-serif;
        font-size:13px;
        color:#ccc;
    }
}
.semua { float: left; width: 100%; }
.left { float: left; width: 79%; }
.right { float: right; width: 20%; }
.group:after { content:""; display: table; clear: both; }
img { max-width: 100%; height: auto; }
.kop-logo { max-width: 70%; height: auto; }
html {
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;
}
.home-bg { background: url(images/bsmart1.jpg) no-repeat center center fixed; }
</style>
</head>
<body>

<div class="group">
    <div class="left">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Hasil Ujian CBT :</h3>
            </div>
            <div class="panel-body">
                <table border="0" width="100%">
                    <tr>
                        <td rowspan="7" width="150">
                            <?php if ($logoUrl !== '') { ?>
                                <img class="kop-logo" src="<?php echo $logoUrl; ?>" width="70%" />
                            <?php } ?>
                        </td>
                        <td width="30%">Nomer Ujian</td><td width="50%">: <?php echo "$var_siswa [$xtokenujian]"; ?></td>
                    </tr>
                    <tr><td>Nomer Induk (NIS)</td><td>: <?php echo $nomsis; ?></td></tr>
                    <tr><td>Nama Lengkap</td><td>: <?php echo $namsis; ?></td></tr>
                    <tr><td>Kelas | Jurusan</td><td>: <?php echo "$namkel | $namjur "; ?></td></tr>
                    <tr><td>Mata Pelajaran</td><td>: <?php echo $namamapel; ?></td></tr>
                    <tr><td>Jenis Ujian</td><td>: <?php echo $namaUjianTampil; ?></td></tr>
                    <tr><td>Tgl Pelaksanaan</td><td>: <?php echo $tglujian; ?></td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="right">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Nilai Ujian :</h3>
            </div>
            <div class="panel-body">
                <table border="0" width="100%" bgcolor="#00CCCC">
                    <tr><td valign="top" align="center"><div style="font-size:42px" id="nilaiskor"><?php echo $total_nilai; ?></div></td></tr>
                </table>
            </div>
        </div>
        <div class="panel panel-default" style="margin-top:-10px;">
            <div class="panel-body">
                <h3 class="panel-title"><?php echo "Ujian : $namaUjianTampil"; ?></h3>
            </div>
        </div>
    </div>
</div>

<?php
$nomer = 1;
$betul = 0;
$sql = mysql_query("SELECT * FROM cbt_jawaban j left join cbt_soal s on s.XNomerSoal = j.XNomerSoal
left join cbt_ujian u on (u.XKodeSoal = s.XKodeSoal and u.XTokenUjian = j.XTokenUjian)
WHERE j.XKodeSoal = '$var_soal' and s.XKodeSoal = '$var_soal' and j.XUserJawab = '$var_siswa'
and j.XJenisSoal = '1' $tokenFilterJ order by j.Urut");
while($r = mysql_fetch_array($sql)){
    $jumpil = $r['XJumPilihan'];
    $audiofile = trim((string)$r['XAudioTanya']);
    $vidfile = trim((string)$r['XVideoTanya']);

    echo "<table width=100% border=0><tr><td width=50px>$nomer.</td><td colspan=2>$r[XTanya] </td></tr>
    <tr><td width=50px colspan=3>&nbsp;</td></tr>";

    if($audiofile !== ''){
        echo "<tr><td width=50px colspan=3>File Listening : $audiofile</td></tr>";
    }
    if($vidfile !== ''){
        echo "<tr><td width=50px colspan=3>File Video : $vidfile</td></tr>";
    }

    if(trim((string)$r['XGambarTanya']) !== ''){
        $imgTanya = cbt_resolve_panel_media_url($r['XGambarTanya']);
        echo "<tr><td width=50px colspan=3>&nbsp;</td></tr>
        <tr><td colspan=3><img src='$imgTanya' width='50%'></td></tr>";
    }
    echo "<tr><td width=50px colspan=3>&nbsp;</td></tr>";

    $PilA = $r['XA'];
    $PilJwb = "XJawab$PilA";
    $FileGbr = "XGambarJawab$PilA";
    if(trim((string)$r[$FileGbr])==''){$GbrJwb=""; $lebar = "width=0px";}else{$GbrJwb = "<img src='" . cbt_resolve_panel_media_url($r[$FileGbr]) . "' width=80px>"; $lebar = "width=90px";}
    echo "<tr><td width=50px align=center> A. </td>";
    $sqlpil = mysql_query("SELECT $PilJwb as pilsoal FROM cbt_soal WHERE XKodeSoal = '$var_soal' and XNomerSoal = '$r[XNomerSoal]'");
    $jwb = mysql_fetch_array($sqlpil);
    $jawab = $jwb['pilsoal'];
    echo "<td $lebar>$GbrJwb</td><td>$jawab</td></tr>";

    $PilB = $r['XB'];
    $PilJwb = "XJawab$PilB";
    $FileGbr = "XGambarJawab$PilB";
    if(trim((string)$r[$FileGbr])==''){$GbrJwb=""; $lebar = "width=0px";}else{$GbrJwb = "<img src='" . cbt_resolve_panel_media_url($r[$FileGbr]) . "' width=80px>"; $lebar = "width=90px";}
    echo "<tr><td width=50px align=center> B. </td>";
    $sqlpil = mysql_query("SELECT $PilJwb as pilsoal FROM cbt_soal WHERE XKodeSoal = '$var_soal' and XNomerSoal = '$r[XNomerSoal]'");
    $jwb = mysql_fetch_array($sqlpil);
    $jawab = $jwb['pilsoal'];
    echo "<td $lebar>$GbrJwb</td><td>$jawab</td></tr>";

    $PilC = $r['XC'];
    $PilJwb = "XJawab$PilC";
    $FileGbr = "XGambarJawab$PilC";
    if(trim((string)$r[$FileGbr])==''){$GbrJwb=""; $lebar = "width=0px";}else{$GbrJwb = "<img src='" . cbt_resolve_panel_media_url($r[$FileGbr]) . "' width=80px>"; $lebar = "width=90px";}
    echo "<tr><td width=50px align=center> C. </td>";
    $sqlpil = mysql_query("SELECT $PilJwb as pilsoal FROM cbt_soal WHERE XKodeSoal = '$var_soal' and XNomerSoal = '$r[XNomerSoal]'");
    $jwb = mysql_fetch_array($sqlpil);
    $jawab = $jwb['pilsoal'];
    echo "<td $lebar>$GbrJwb</td><td>$jawab</td></tr>";

    $PilD = $r['XD'];
    $PilJwb = "XJawab$PilD";
    $FileGbr = "XGambarJawab$PilD";
    if(trim((string)$r[$FileGbr])==''){$GbrJwb=""; $lebar = "width=0px";}else{$GbrJwb = "<img src='" . cbt_resolve_panel_media_url($r[$FileGbr]) . "' width=80px>"; $lebar = "width=90px";}
    echo "<tr><td width=50px align=center> D. </td>";
    $sqlpil = mysql_query("SELECT $PilJwb as pilsoal FROM cbt_soal WHERE XKodeSoal = '$var_soal' and XNomerSoal = '$r[XNomerSoal]'");
    $jwb = mysql_fetch_array($sqlpil);
    $jawab = $jwb['pilsoal'];
    echo "<td $lebar>$GbrJwb</td><td>$jawab</td></tr>";

    if($jumpil == 5){
        $PilE = $r['XE'];
        $PilJwb = "XJawab$PilE";
        $FileGbr = "XGambarJawab$PilE";
        if(trim((string)$r[$FileGbr])==''){$GbrJwb=""; $lebar = "width=0px";}else{$GbrJwb = "<img src='" . cbt_resolve_panel_media_url($r[$FileGbr]) . "' width=80px>"; $lebar = "width=90px";}
        echo "<tr><td width=50px align=center> E. </td>";
        $sqlpil = mysql_query("SELECT $PilJwb as pilsoal FROM cbt_soal WHERE XKodeSoal = '$var_soal' and XNomerSoal = '$r[XNomerSoal]'");
        $jwb = mysql_fetch_array($sqlpil);
        $jawab = $jwb['pilsoal'];
        echo "<td $lebar>$GbrJwb</td><td>$jawab</td></tr>";
    }

    if($r['XKunciJawaban']==$r['XA']){$jwbsiswa = "A";}
    elseif($r['XKunciJawaban']==$r['XB']){$jwbsiswa = "B";}
    elseif($r['XKunciJawaban']==$r['XC']){$jwbsiswa = "C";}
    elseif($r['XKunciJawaban']==$r['XD']){$jwbsiswa = "D";}
    elseif($r['XKunciJawaban']==$r['XE']){$jwbsiswa = "E";}
    else{$jwbsiswa = "S";}

    if($jwbsiswa==$r['XJawaban']){$ikon = "images/benar.gif"; $betul++;}else{$ikon = "images/salah.gif";}
    echo "<tr><td colspan=3><br>Kunci Jawaban : $jwbsiswa, Jawaban Siswa : $r[XJawaban]&nbsp; &nbsp;  <img src=$ikon width=30px></td></tr>";
    echo "<tr><td colspan=3><hr></td></tr>";

    $nomer++;
    echo "</table>";
}
?>

<table width="100%" border="0">
<?php
$sql = mysql_query("SELECT * FROM cbt_jawaban j left join cbt_soal s on s.XNomerSoal = j.XNomerSoal
left join cbt_ujian u on (u.XKodeSoal = s.XKodeSoal and u.XTokenUjian = j.XTokenUjian)
WHERE j.XKodeSoal = '$var_soal' and s.XKodeSoal = '$var_soal' and j.XUserJawab = '$var_siswa'
and j.XJenisSoal = '2' $tokenFilterJ order by j.Urut");
while($r = mysql_fetch_array($sql)){
    $nil = $r['XNilaiEsai'];
    echo "<tr><td width=50px>$nomer.</td><td>$r[XTanya] </td></tr>
    <tr><td width=50px colspan=2>&nbsp;</td></tr>";

    if(trim((string)$r['XGambarTanya']) !== ''){
        $imgTanya = cbt_resolve_panel_media_url($r['XGambarTanya']);
        echo "<tr><td width=30px colspan=2>&nbsp; </td></tr>
        <tr><td colspan=2><img src='$imgTanya' width=150px></td></tr>";
    }
    echo "<tr><td width=50px colspan=2>&nbsp;</td></tr>";

    $jawab = $r['XJawabanEsai'];
    echo "<tr><td width=30px colspan=2><b>Jawaban : </b></td></tr>
    <tr><td colspan=2>$jawab</td></tr>
    <tr><td width=50px colspan=2>&nbsp;</td></tr>
    <tr><td colspan=1><b>Nilai : </b></td><td><span style='height:50px; width:60px; font-size:36px; padding-left:5px;color:#32689a'>$nil</span></td></tr>
    <tr><td colspan=2><hr></td></tr>";

    $nomer++;
}
?>
</table>

<div id="print-footer"><div>Hasil Ujian <?php echo $nomsis; ?> : <?php echo $namsis; ?> (<?php echo $namkel; ?> | <?php echo $namjur; ?>)</div></div>
</body>
</html>
