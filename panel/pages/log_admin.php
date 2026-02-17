<?php
if (!isset($_COOKIE['beeuser'])) {
    header("Location: login.php");
    exit;
}
if (!isset($_COOKIE['beelogin']) || $_COOKIE['beelogin'] !== 'admin') {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    return;
}

$logFile = bee_get_log_file();
$notif = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'clear_log') {
    @file_put_contents($logFile, "");
    bee_log('WARN', 'LOG_CLEAR', 'Log dibersihkan dari halaman Log', array(
        'by' => isset($_COOKIE['beeuser']) ? $_COOKIE['beeuser'] : '-'
    ));
    $notif = "<div class='alert alert-success'>Log berhasil dibersihkan.</div>";
}

$selectedLevel = isset($_GET['level']) ? strtoupper(trim($_GET['level'])) : 'ALL';
$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$maxRows = 500;
$rows = array();

if (file_exists($logFile)) {
    $lines = @file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (is_array($lines)) {
        $lines = array_reverse($lines);
        foreach ($lines as $line) {
            $item = json_decode($line, true);
            if (!is_array($item)) {
                $item = array(
                    'time' => '-',
                    'level' => 'RAW',
                    'action' => 'RAW_LOG',
                    'message' => $line,
                    'user' => '-',
                    'role' => '-',
                    'ip' => '-',
                    'uri' => '-',
                    'context' => array()
                );
            }

            $msgBlob = $item['action'] . ' ' . $item['message'] . ' ' . $item['user'] . ' ' . $item['uri'];
            if (isset($item['action']) && strtoupper($item['action']) === 'FRONTEND_JS_ERROR') {
                continue;
            }
            if ($selectedLevel !== 'ALL' && strtoupper($item['level']) !== $selectedLevel) {
                continue;
            }
            if ($keyword !== '' && stripos($msgBlob, $keyword) === false) {
                continue;
            }

            $rows[] = $item;
            if (count($rows) >= $maxRows) {
                break;
            }
        }
    }
}
?>

<div class="row">
    <div class="col-lg-12">
        <h3 class="page-header">Log Admin & Error</h3>
    </div>
</div>

<?php echo $notif; ?>

<div class="panel panel-primary">
    <div class="panel-heading">Monitoring Aktivitas dan Error Sistem</div>
    <div class="panel-body">
        <form method="get" class="form-inline" style="margin-bottom:12px;">
            <input type="hidden" name="modul" value="log_admin">
            <div class="form-group">
                <label for="level">Level </label>
                <select name="level" id="level" class="form-control">
                    <?php
                    $levels = array('ALL', 'INFO', 'WARN', 'ERROR', 'FATAL');
                    foreach ($levels as $lv) {
                        $sel = ($selectedLevel === $lv) ? "selected" : "";
                        echo "<option value='$lv' $sel>$lv</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group" style="margin-left:8px;">
                <label for="q">Cari </label>
                <input type="text" name="q" id="q" class="form-control" value="<?php echo htmlspecialchars($keyword); ?>" placeholder="kata kunci">
            </div>
            <button type="submit" class="btn btn-info" style="margin-left:8px;">Filter</button>
            <a href="?modul=log_admin" class="btn btn-default">Reset</a>
        </form>

        <form method="post" onsubmit="return confirm('Hapus seluruh log?');" style="margin-bottom:12px;">
            <input type="hidden" name="aksi" value="clear_log">
            <button type="submit" class="btn btn-danger">Hapus Semua Log</button>
            <span class="text-muted" style="margin-left:8px;">File log: <?php echo htmlspecialchars($logFile); ?></span>
        </form>

        <div class="table-responsive">
            <table class="table table-striped table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="14%">Waktu</th>
                        <th width="8%">Level</th>
                        <th width="12%">Aksi</th>
                        <th width="26%">Pesan</th>
                        <th width="8%">User</th>
                        <th width="7%">Role</th>
                        <th width="8%">IP</th>
                        <th width="17%">URI / Context</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($rows) === 0) { ?>
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data log.</td>
                        </tr>
                    <?php } else {
                        foreach ($rows as $r) {
                            $ctx = isset($r['context']) ? $r['context'] : array();
                            $ctxText = '';
                            if (is_array($ctx) && !empty($ctx)) {
                                $ctxText = json_encode($ctx);
                            }
                            $uri = isset($r['uri']) ? $r['uri'] : '-';
                            $uriContext = trim($uri . ($ctxText !== '' ? "\n" . $ctxText : ''));
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars(isset($r['time']) ? $r['time'] : '-'); ?></td>
                                <td><?php echo htmlspecialchars(isset($r['level']) ? $r['level'] : '-'); ?></td>
                                <td><?php echo htmlspecialchars(isset($r['action']) ? $r['action'] : '-'); ?></td>
                                <td><?php echo htmlspecialchars(isset($r['message']) ? $r['message'] : '-'); ?></td>
                                <td><?php echo htmlspecialchars(isset($r['user']) ? $r['user'] : '-'); ?></td>
                                <td><?php echo htmlspecialchars(isset($r['role']) ? $r['role'] : '-'); ?></td>
                                <td><?php echo htmlspecialchars(isset($r['ip']) ? $r['ip'] : '-'); ?></td>
                                <td><pre style="white-space:pre-wrap; margin:0; border:0; background:none;"><?php echo htmlspecialchars($uriContext); ?></pre></td>
                            </tr>
                        <?php }
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
