<?PHP
include "config/server.php";
if (!function_exists('getUserIP')) {
    function getUserIP()
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';

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

if (!function_exists('cbt_validate_single_ip_session')) {
    function cbt_validate_single_ip_session($nomerUjian, $tokenUjian, $kodeSoal, $currentIp, &$savedIp = '')
    {
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

        if ($savedIp === '') {
            mysql_query("UPDATE cbt_siswa_ujian
                SET XGetIP = '$currentIp'
                WHERE Urut = '$row[Urut]'
                LIMIT 1");
            return true;
        }

        return ($savedIp === $currentIp);
    }
}


$user_ip = getUserIP();

//echo $user_ip; // Output IP address [Ex: 177.87.193.134]


?>
