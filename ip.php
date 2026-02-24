<?PHP
include "config/server.php";
if (!function_exists('getUserIP')) {
    function getUserIP()
    {
        $cfip    = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : '';
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

        if (filter_var($cfip, FILTER_VALIDATE_IP)) {
            return $cfip;
        }

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            return $client;
        }

        // Handle X-Forwarded-For format: "client, proxy1, proxy2"
        if (!empty($forward)) {
            $parts = explode(',', $forward);
            foreach ($parts as $part) {
                $candidate = trim($part);
                if (filter_var($candidate, FILTER_VALIDATE_IP)) {
                    return $candidate;
                }
            }
        }

        return $remote;
    }
}

if (!function_exists('cbt_session_lock_mode')) {
    function cbt_session_lock_mode()
    {
        $mode = 'station';
        if (defined('CBT_SESSION_LOCK_MODE')) {
            $mode = (string) CBT_SESSION_LOCK_MODE;
        } elseif (getenv('CBT_SESSION_LOCK_MODE') !== false) {
            $mode = (string) getenv('CBT_SESSION_LOCK_MODE');
        }
        $mode = strtolower(trim($mode));
        if ($mode !== 'ip' && $mode !== 'station' && $mode !== 'off') {
            $mode = 'station';
        }
        return $mode;
    }
}

if (!function_exists('cbt_is_ip_lock_enabled')) {
    function cbt_is_ip_lock_enabled()
    {
        return cbt_session_lock_mode() !== 'off';
    }
}

if (!function_exists('cbt_get_station_cookie')) {
    function cbt_get_station_cookie()
    {
        if (isset($_COOKIE['CBT_STATION_ID']) && preg_match('/^[a-f0-9]{16,40}$/', $_COOKIE['CBT_STATION_ID'])) {
            return $_COOKIE['CBT_STATION_ID'];
        }
        $seed = uniqid('', true) . '|' . mt_rand() . '|' . getUserIP() . '|' . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        $station = substr(sha1($seed), 0, 24);
        setcookie('CBT_STATION_ID', $station, time() + (86400 * 30), '/');
        $_COOKIE['CBT_STATION_ID'] = $station;
        return $station;
    }
}

if (!function_exists('cbt_get_session_lock_value')) {
    function cbt_get_session_lock_value()
    {
        $mode = cbt_session_lock_mode();
        if ($mode === 'off') {
            return '';
        }
        if ($mode === 'ip') {
            return getUserIP();
        }
        return 'st:' . cbt_get_station_cookie();
    }
}

if (!function_exists('cbt_validate_single_ip_session')) {
    function cbt_validate_single_ip_session($nomerUjian, $tokenUjian, $kodeSoal, $currentIp, &$savedIp = '')
    {
        if (!cbt_is_ip_lock_enabled()) {
            $savedIp = '';
            return true;
        }

        $nomerUjian = mysql_real_escape_string($nomerUjian);
        $tokenUjian = mysql_real_escape_string($tokenUjian);
        $kodeSoal = mysql_real_escape_string($kodeSoal);
        $currentIp = mysql_real_escape_string($currentIp);

        $savedIp = '';
        $sql = mysql_query("SELECT Urut, XGetIP FROM cbt_siswa_ujian
            WHERE XNomerUjian = '$nomerUjian'
              AND XTokenUjian = '$tokenUjian'
              AND XKodeSoal = '$kodeSoal'
              AND XStatusUjian = '1'
            ORDER BY Urut DESC
            LIMIT 1");

        if (!$sql || mysql_num_rows($sql) < 1) {
            return true;
        }

        $row = mysql_fetch_array($sql);
        $savedIp = trim(isset($row['XGetIP']) ? $row['XGetIP'] : '');

        if ($savedIp === '' && $currentIp !== '') {
            mysql_query("UPDATE cbt_siswa_ujian
                SET XGetIP = '$currentIp'
                WHERE Urut = '$row[Urut]'
                LIMIT 1");
            return true;
        }

        if ($currentIp === '') {
            return true;
        }

        // Migrasi sekali saat mode lock diganti (mis: ip -> station atau station -> ip)
        if ($savedIp !== $currentIp) {
            $mode = cbt_session_lock_mode();
            $savedLooksIp = (bool) filter_var($savedIp, FILTER_VALIDATE_IP);
            $savedLooksStation = (strpos($savedIp, 'st:') === 0);
            $currentLooksIp = (bool) filter_var($currentIp, FILTER_VALIDATE_IP);
            $currentLooksStation = (strpos($currentIp, 'st:') === 0);

            if (($mode === 'station' && $savedLooksIp && $currentLooksStation) ||
                ($mode === 'ip' && $savedLooksStation && $currentLooksIp)) {
                mysql_query("UPDATE cbt_siswa_ujian
                    SET XGetIP = '$currentIp'
                    WHERE Urut = '$row[Urut]'
                    LIMIT 1");
                return true;
            }
        }

        return ($savedIp === $currentIp);
    }
}


$user_ip = getUserIP();
$cbt_session_lock_mode = cbt_session_lock_mode();
$cbt_session_lock_value = cbt_get_session_lock_value();

//echo $user_ip; // Output IP address [Ex: 177.87.193.134]


?>
