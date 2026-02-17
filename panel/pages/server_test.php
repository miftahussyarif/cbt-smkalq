<?php
if (!isset($_COOKIE['beelogin']) || $_COOKIE['beelogin'] !== 'admin') {
    echo '<div class="alert alert-danger">Akses ditolak.</div>';
    return;
}

function cbt_status_label_class($ok, $warn = false)
{
    if ($ok) {
        return 'label label-success';
    }
    if ($warn) {
        return 'label label-warning';
    }
    return 'label label-danger';
}

function cbt_status_text($ok, $warn = false)
{
    if ($ok) {
        return 'PASS';
    }
    if ($warn) {
        return 'WARN';
    }
    return 'FAIL';
}

function cbt_ms($start, $end)
{
    return round(($end - $start) * 1000, 2);
}

$rootPath = realpath(__DIR__ . '/../../');
$results = array();

// Run schema/table generation only from Server Test menu (no longer on every admin page load)
$generatedTables = array();
$schemaCheckOk = false;
$schemaCheckWarn = false;
$schemaCheckInfo = '';
$schemaCheckStart = microtime(true);
if (function_exists('cbt_generate_missing_tables')) {
    $generatedTables = cbt_generate_missing_tables();
    if (is_array($generatedTables)
        && !empty($generatedTables['cbt_pengawasan'])
        && !empty($generatedTables['cbt_pengawasan_config'])) {
        $schemaCheckOk = true;
        $schemaCheckInfo = 'Tabel pengawasan tersedia';
    } else {
        $schemaCheckWarn = true;
        $schemaCheckInfo = 'Sebagian tabel pengawasan belum tersedia';
    }
} else {
    $schemaCheckWarn = true;
    $schemaCheckInfo = 'Fungsi cbt_generate_missing_tables tidak ditemukan';
}
$schemaCheckEnd = microtime(true);
$results[] = array(
    'group' => 'Database',
    'name' => 'Schema Check (Pengawasan)',
    'status_ok' => $schemaCheckOk,
    'status_warn' => (!$schemaCheckOk && $schemaCheckWarn),
    'detail' => $schemaCheckInfo,
    'time_ms' => cbt_ms($schemaCheckStart, $schemaCheckEnd)
);

// 1) Database connection + read benchmark
$dbConnStart = microtime(true);
$dbConnOk = is_resource($sqlconn) ? true : false;
$dbConnEnd = microtime(true);
$results[] = array(
    'group' => 'Database',
    'name' => 'Koneksi Database',
    'status_ok' => $dbConnOk,
    'status_warn' => false,
    'detail' => $dbConnOk ? 'Koneksi aktif' : ('Koneksi gagal: ' . mysql_error()),
    'time_ms' => cbt_ms($dbConnStart, $dbConnEnd)
);

$readBenchStart = microtime(true);
$readBenchOk = false;
$readBenchInfo = '';
if ($dbConnOk) {
    $count = 50;
    $readQuery = "SELECT SQL_NO_CACHE COUNT(*) AS total FROM cbt_siswa";
    for ($i = 0; $i < $count; $i++) {
        $q = mysql_query($readQuery);
        if (!$q) {
            $readBenchInfo = 'Query gagal: ' . mysql_error();
            break;
        }
        $row = mysql_fetch_assoc($q);
        if ($i === ($count - 1)) {
            $readBenchInfo = 'COUNT cbt_siswa=' . (isset($row['total']) ? $row['total'] : '0') . ' (loop ' . $count . 'x)';
        }
        $readBenchOk = true;
    }
} else {
    $readBenchInfo = 'Dilewati karena koneksi database gagal';
}
$readBenchEnd = microtime(true);
$results[] = array(
    'group' => 'Database',
    'name' => 'Read Processing',
    'status_ok' => $readBenchOk,
    'status_warn' => (!$readBenchOk && !$dbConnOk),
    'detail' => $readBenchInfo,
    'time_ms' => cbt_ms($readBenchStart, $readBenchEnd)
);

// 2) Database write benchmark (temporary table)
$writeBenchStart = microtime(true);
$writeBenchOk = false;
$writeBenchWarn = false;
$writeBenchInfo = '';
if ($dbConnOk) {
    $tmpName = 'cbt_server_test_tmp';
    $q1 = mysql_query("CREATE TEMPORARY TABLE $tmpName (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, note VARCHAR(50) NOT NULL)");
    if ($q1) {
        $q2 = mysql_query("INSERT INTO $tmpName (note) VALUES ('health-check')");
        $q3 = mysql_query("SELECT COUNT(*) AS total FROM $tmpName");
        if ($q2 && $q3) {
            $row = mysql_fetch_assoc($q3);
            $writeBenchOk = true;
            $writeBenchInfo = 'Insert temporary table berhasil, total=' . (isset($row['total']) ? $row['total'] : '0');
        } else {
            $writeBenchInfo = 'Insert/select gagal: ' . mysql_error();
        }
        mysql_query("DROP TEMPORARY TABLE IF EXISTS $tmpName");
    } else {
        $writeBenchWarn = true;
        $writeBenchInfo = 'Tidak bisa CREATE TEMPORARY TABLE (cek privilege): ' . mysql_error();
    }
} else {
    $writeBenchWarn = true;
    $writeBenchInfo = 'Dilewati karena koneksi database gagal';
}
$writeBenchEnd = microtime(true);
$results[] = array(
    'group' => 'Database',
    'name' => 'Write Processing',
    'status_ok' => $writeBenchOk,
    'status_warn' => (!$writeBenchOk && $writeBenchWarn),
    'detail' => $writeBenchInfo,
    'time_ms' => cbt_ms($writeBenchStart, $writeBenchEnd)
);

// 3) Folder checks
$checkDirs = array(
    array('path' => $rootPath . '/audio', 'need_write' => true),
    array('path' => $rootPath . '/video', 'need_write' => true),
    array('path' => $rootPath . '/pictures', 'need_write' => true),
    array('path' => $rootPath . '/fotosiswa', 'need_write' => true),
    array('path' => $rootPath . '/images', 'need_write' => false),
    array('path' => $rootPath . '/panel/pages', 'need_write' => false),
    array('path' => $rootPath . '/config', 'need_write' => false),
    array('path' => $rootPath . '/database', 'need_write' => false),
    array('path' => $rootPath . '/output', 'need_write' => true)
);

foreach ($checkDirs as $dirCfg) {
    $dir = $dirCfg['path'];
    $needWrite = $dirCfg['need_write'];
    $exists = is_dir($dir);
    $readable = $exists ? is_readable($dir) : false;
    $writable = $exists ? is_writable($dir) : false;
    $ok = $exists && $readable && (!$needWrite || $writable);
    $warn = false;
    if ($exists && $readable && $needWrite && !$writable) {
        $warn = true;
    }
    $detail = '';
    if (!$exists) {
        $detail = 'Folder tidak ditemukan';
    } else {
        $detail = 'read=' . ($readable ? 'yes' : 'no') . ', write=' . ($writable ? 'yes' : 'no');
    }
    $results[] = array(
        'group' => 'Filesystem',
        'name' => str_replace($rootPath . '/', '', $dir),
        'status_ok' => $ok,
        'status_warn' => (!$ok && $warn),
        'detail' => $detail,
        'time_ms' => 0
    );
}

// 4) Server handling benchmark
$cpuStart = microtime(true);
$hash = '';
for ($i = 0; $i < 15000; $i++) {
    $hash = md5($hash . $i . 'cbt');
}
$cpuEnd = microtime(true);
$results[] = array(
    'group' => 'Server',
    'name' => 'CPU Processing (MD5 loop 15000x)',
    'status_ok' => true,
    'status_warn' => false,
    'detail' => 'Hash akhir: ' . substr($hash, 0, 12),
    'time_ms' => cbt_ms($cpuStart, $cpuEnd)
);

$ioStart = microtime(true);
$ioOk = false;
$ioWarn = false;
$ioDetail = '';
$tmpFile = sys_get_temp_dir() . '/cbt_server_test_' . uniqid() . '.tmp';
$payload = str_repeat('CBTTEST', 1024); // ~7KB
$writeOk = @file_put_contents($tmpFile, $payload);
if ($writeOk !== false) {
    $readBack = @file_get_contents($tmpFile);
    $ioOk = ($readBack === $payload);
    $ioDetail = $ioOk ? 'Read/Write temporary file berhasil' : 'Mismatch data saat baca ulang';
    @unlink($tmpFile);
} else {
    $ioWarn = true;
    $ioDetail = 'Tidak bisa menulis temporary file: ' . $tmpFile;
}
$ioEnd = microtime(true);
$results[] = array(
    'group' => 'Server',
    'name' => 'I/O Processing',
    'status_ok' => $ioOk,
    'status_warn' => (!$ioOk && $ioWarn),
    'detail' => $ioDetail,
    'time_ms' => cbt_ms($ioStart, $ioEnd)
);

$okCount = 0;
$warnCount = 0;
$failCount = 0;
$totalMs = 0;
foreach ($results as $r) {
    if ($r['status_ok']) {
        $okCount++;
    } elseif ($r['status_warn']) {
        $warnCount++;
    } else {
        $failCount++;
    }
    $totalMs += (float)$r['time_ms'];
}

if (function_exists('bee_log')) {
    bee_log('INFO', 'SERVER_HEALTH_TEST', 'Admin menjalankan tes kekuatan server', array(
        'ok' => $okCount,
        'warn' => $warnCount,
        'fail' => $failCount,
        'total_ms' => round($totalMs, 2),
        'generated_tables' => $generatedTables
    ));
}
?>

<div class="row" style="margin-top:10px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-dashboard fa-fw"></i> Tes Kekuatan Server
            </div>
            <div class="panel-body">
                <div class="row" style="margin-bottom:15px;">
                    <div class="col-md-3">
                        <div class="well well-sm" style="margin-bottom:8px;">
                            <strong>PASS:</strong> <?php echo $okCount; ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="well well-sm" style="margin-bottom:8px;">
                            <strong>WARN:</strong> <?php echo $warnCount; ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="well well-sm" style="margin-bottom:8px;">
                            <strong>FAIL:</strong> <?php echo $failCount; ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="well well-sm" style="margin-bottom:8px;">
                            <strong>Total Waktu:</strong> <?php echo round($totalMs, 2); ?> ms
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th width="14%">Kategori</th>
                            <th width="28%">Tes</th>
                            <th width="12%">Status</th>
                            <th width="14%">Waktu (ms)</th>
                            <th width="32%">Keterangan</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($results as $row) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['group']); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td>
                                    <span class="<?php echo cbt_status_label_class($row['status_ok'], $row['status_warn']); ?>">
                                        <?php echo cbt_status_text($row['status_ok'], $row['status_warn']); ?>
                                    </span>
                                </td>
                                <td><?php echo number_format((float)$row['time_ms'], 2); ?></td>
                                <td><?php echo htmlspecialchars($row['detail']); ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>

                <a href="?modul=server_test" class="btn btn-primary btn-sm"><i class="fa fa-refresh"></i> Jalankan Ulang Tes</a>
            </div>
        </div>
    </div>
</div>
