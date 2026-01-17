<?php
if (isset($_POST['userz'], $_POST['passz'])) {
    include "../../config/server.php";
    require("../../config/fungsi_thn.php");

    $userz = isset($_POST['userz']) ? trim($_POST['userz']) : '';
    $passz = isset($_POST['passz']) ? $_POST['passz'] : '';
    $loginz = isset($_POST['login']) ? $_POST['login'] : '';

    if ($userz === '' || $passz === '') {
        header("Location: login.php");
        exit;
    }

    if ($loginz == "admin") {
        $peran = "1";
    } elseif ($loginz == "pengawas") {
        $peran = "2";
    } else {
        $peran = "0";
    }

    $stmt = db_query(
        $db,
        "select Urut, Password from cbt_user where Username = :user and login = :login limit 1",
        array('user' => $userz, 'login' => $peran)
    );
    $user = db_fetch_one($stmt);

    $valid = false;
    if ($user && isset($user['Password'])) {
        $stored = (string) $user['Password'];
        if (password_get_info($stored)['algo'] !== 0) {
            $valid = password_verify($passz, $stored);
        } elseif (strlen($stored) === 32 && ctype_xdigit($stored)) {
            $valid = hash_equals($stored, md5($passz));
            if ($valid) {
                $len_stmt = db_query(
                    $db,
                    "select CHARACTER_MAXIMUM_LENGTH as len from information_schema.COLUMNS where TABLE_SCHEMA = DATABASE() and TABLE_NAME = 'cbt_user' and COLUMN_NAME = 'Password'",
                    array()
                );
                $len_row = db_fetch_one($len_stmt);
                $max_len = $len_row ? (int) $len_row['len'] : 0;
                if ($max_len >= 60) {
                    $new_hash = password_hash($passz, PASSWORD_DEFAULT);
                    db_query(
                        $db,
                        "update cbt_user set Password = :hash where Urut = :urut",
                        array('hash' => $new_hash, 'urut' => $user['Urut'])
                    );
                }
            }
        } else {
            $valid = hash_equals($stored, $passz);
        }
    }

    if ($valid) {
        $sqltahun = db_query($db, "select XKodeAY from cbt_setid where XStatus = '1' limit 1", array());
        $st = db_fetch_one($sqltahun);
        $tahunz = $st ? $st['XKodeAY'] : '';

        $sqlsekolah = db_query($db, "select XKodeSekolah from cbt_admin limit 1", array());
        $sk = db_fetch_one($sqlsekolah);
        $kode_sekolah = $sk ? $sk['XKodeSekolah'] : '';

        $cookie_options = array(
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => !empty($_SERVER['HTTPS']),
        );
        setcookie('beeuser', $userz, $cookie_options);
        setcookie('beelogin', $loginz, $cookie_options);
        setcookie('beetahun', $tahunz, $cookie_options);
        setcookie('beesekolah', $kode_sekolah, $cookie_options);

        header("Location: ../pages/?");
        exit;
    }

    header("Location: login.php");
    exit;
}

header("Location: login.php");
exit;
?>

