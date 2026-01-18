<?php
if (!function_exists('cbt_ensure_pengawasan_table')) {
    function cbt_ensure_pengawasan_table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `cbt_pengawasan` (
            `XNomerUjian` varchar(30) NOT NULL,
            `XTokenUjian` varchar(30) NOT NULL,
            `XKodeSoal` varchar(30) NOT NULL,
            `XLastEvent` varchar(20) DEFAULT NULL,
            `XLastEventAt` datetime DEFAULT NULL,
            `XPindahTabCount` int(11) NOT NULL DEFAULT '0',
            `XPrintscreenCount` int(11) NOT NULL DEFAULT '0',
            `XIsLocked` tinyint(1) NOT NULL DEFAULT '0',
            `XLockedBy` varchar(50) DEFAULT NULL,
            `XLockedAt` datetime DEFAULT NULL,
            `XUpdatedAt` datetime DEFAULT NULL,
            PRIMARY KEY (`XNomerUjian`,`XTokenUjian`,`XKodeSoal`),
            KEY `idx_pengawasan_token` (`XTokenUjian`),
            KEY `idx_pengawasan_kodesoal` (`XKodeSoal`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
        mysql_query($sql);
    }
}

if (!function_exists('cbt_ensure_pengawasan_config_table')) {
    function cbt_ensure_pengawasan_config_table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `cbt_pengawasan_config` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `config_key` varchar(50) NOT NULL,
            `config_value` tinyint(1) NOT NULL DEFAULT '1',
            `config_label` varchar(100) NOT NULL,
            `config_description` text,
            `updated_at` datetime DEFAULT NULL,
            `updated_by` varchar(50) DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_config_key` (`config_key`)
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1";
        mysql_query($sql);

        // Insert default configurations
        $defaults = array(
            array('monitor_tab_switch', 'Deteksi Pindah Tab', 'Deteksi ketika siswa berpindah tab atau minimize browser'),
            array('monitor_printscreen', 'Deteksi Printscreen', 'Deteksi ketika siswa menekan tombol PrintScreen'),
            array('monitor_tab_close', 'Deteksi Tutup Tab', 'Deteksi ketika siswa menutup tab ujian'),
            array('monitor_rto', 'Deteksi Koneksi Terputus', 'Deteksi ketika koneksi internet siswa terputus'),
            array('auto_lock_on_violation', 'Auto-lock saat Pelanggaran', 'Otomatis mengunci ujian siswa saat terdeteksi pelanggaran')
        );

        foreach ($defaults as $def) {
            $key = mysql_real_escape_string($def[0]);
            $label = mysql_real_escape_string($def[1]);
            $desc = mysql_real_escape_string($def[2]);
            mysql_query("INSERT IGNORE INTO cbt_pengawasan_config (config_key, config_value, config_label, config_description) 
                         VALUES ('$key', 1, '$label', '$desc')");
        }
    }
}

if (!function_exists('cbt_get_pengawasan_config')) {
    function cbt_get_pengawasan_config()
    {
        $result = mysql_query("SELECT config_key, config_value FROM cbt_pengawasan_config");
        $config = array();
        if ($result) {
            while ($row = mysql_fetch_assoc($result)) {
                $config[$row['config_key']] = (int)$row['config_value'];
            }
        }
        return $config;
    }
}
?>
