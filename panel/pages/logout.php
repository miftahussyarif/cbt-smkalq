<?php 
include "../../config/server.php";
// ===============================
// Status Ujian XStatusUjian = 1 Aktif
// Status Ujian XStatusUjian = 0 BelumAktif
// Status Ujian XStatusUjian = 9 Selesai

if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
        $parts = explode('=', $cookie);
        $cookieName = trim($parts[0]);
        if ($cookieName !== '') {
            setcookie($cookieName, '', time() - 3600, '/');
            setcookie($cookieName, '', time() - 3600);
        }
    }
}

setcookie('beeuser', '', time() - 3600, '/');
setcookie('beelogin', '', time() - 3600, '/');
setcookie('beetahun', '', time() - 3600, '/');
setcookie('beesekolah', '', time() - 3600, '/');
unset($_COOKIE['beeuser'], $_COOKIE['beelogin'], $_COOKIE['beetahun'], $_COOKIE['beesekolah']);

header('location:../pages/login.php');

?>
    <script>
        function disableBackButton() {
            window.history.forward();
        }
        setTimeout("disableBackButton()", 0);
    </script>
