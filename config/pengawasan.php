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
?>
