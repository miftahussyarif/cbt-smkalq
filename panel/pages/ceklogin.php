<?php
if (!isset($_POST['userz'], $_POST['passz'])) {
    header("Location: login.php?err=required");
    exit;
}

include "../../config/server.php";
require("../../config/fungsi_thn.php");

$userz = mysql_real_escape_string(isset($_POST['userz']) ? $_POST['userz'] : '');
$passzRaw = isset($_POST['passz']) ? $_POST['passz'] : '';
$passz = md5(mysql_real_escape_string($passzRaw));
$loginz = mysql_real_escape_string(isset($_POST['login']) ? $_POST['login'] : '');

if ($loginz === "admin") {
    $peran = "1";
} elseif ($loginz === "pengawas") {
    $peran = "2";
} else {
    $peran = "0";
}

$sqladmin = mysql_num_rows(mysql_query("select * from cbt_user where Username = '$userz' and Password = '$passz' and login = '$peran'"));
if ($sqladmin > 0) {
    $sqltahun = mysql_query("select * from cbt_setid where XStatus = '1'");
    $st = $sqltahun ? mysql_fetch_array($sqltahun) : array();
    $tahunz = (is_array($st) && isset($st['XKodeAY'])) ? $st['XKodeAY'] : '';

    $sqlsekolah = mysql_query("select * from cbt_admin");
    $sk = $sqlsekolah ? mysql_fetch_array($sqlsekolah) : array();
    $kodeSekolah = (is_array($sk) && isset($sk['XKodeSekolah'])) ? $sk['XKodeSekolah'] : '';

    setcookie('beeuser', $userz);
    setcookie('beelogin', $loginz);
    setcookie('beetahun', $tahunz);
    setcookie('beesekolah', $kodeSekolah);
    header("Location: ../pages/?");
    exit;
}

header("Location: login.php?err=invalid");
exit;
?>

