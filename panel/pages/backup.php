<?php
if (!isset($_COOKIE['beeuser'])) {
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SB Admin 2 - Bootstrap Admin Theme</title>

    <!-- Bootstrap Core CSS -->
    <link href="../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link href="../vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="../vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="jquery-1.4.js"></script>
<script>
    $(document).ready(function () {

        var loading = $("#loading");
        var tampilkan = $("#tampilkan1");
        var idstu = $("#idstu").val();
        function tampildata() {
            tampilkan.hide();
            loading.fadeIn();

            $.ajax({
                type: "POST",
                url: "database_soal_tampil.php",
                data: "aksi=tampil&idstu=" + idstu,
                success: function (data) {
                    loading.fadeOut();
                    tampilkan.html(data);
                    tampilkan.fadeIn(100);
                }
            });
        }// akhir fungsi tampildata
        tampildata();

    });
</script>

<body>
    <?php
    if (!empty($_REQUEST['datax']) && $_REQUEST['datax'] == "ujian") {
        include "../../database/cbt_ujian.php";
    }
    if (!empty($_REQUEST['datax']) && $_REQUEST['datax'] == "siswa") {
        include "../../database/cbt_siswa.php";
    }
    if (!empty($_REQUEST['datax']) && $_REQUEST['datax'] == "semua") {
        include "../../database/cbt_semua.php";
    }
    ?>
    <?php include "../../config/server.php"; ?>
    <?php
    $backupDir = dirname(__FILE__) . '/../../backup';
    $backupSiswa = array();
    $backupUjian = array();
    $backupSemua = array();
    $backupFiles = array();
    $fileBackupMessage = '';
    $fileBackupDirs = array('pictures', 'audio', 'video', 'fotosiswa');
    $baseDir = realpath(__DIR__ . '/../..');

    function list_backup_files($pattern, $limit = 2)
    {
        $files = glob($pattern);
        if (!$files) {
            return array();
        }
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        return array_slice($files, 0, $limit);
    }

    function ensure_dir_writable($dir, $mode = 0777)
    {
        if (!is_dir($dir)) {
            if (!@mkdir($dir, $mode, true)) {
                return false;
            }
        }
        if (!is_writable($dir)) {
            @chmod($dir, $mode);
        }
        return is_writable($dir);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exam_reset_action']) && $_POST['exam_reset_action'] === 'reset_exam_data') {
        $role = isset($_COOKIE['beelogin']) ? $_COOKIE['beelogin'] : '';
        if ($role !== 'admin') {
            $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Akses ditolak. Reset data tes hanya untuk admin.</div>";
        } else {
            $deleted = array(
                'cbt_jawaban' => 0,
                'cbt_nilai' => 0,
                'cbt_siswa_ujian' => 0,
                'cbt_audio' => 0,
                'cbt_pengawasan' => 0,
                'cbt_ujian' => 0
            );

            mysql_query("DELETE FROM cbt_jawaban");
            $deleted['cbt_jawaban'] = mysql_affected_rows();
            mysql_query("DELETE FROM cbt_nilai");
            $deleted['cbt_nilai'] = mysql_affected_rows();
            mysql_query("DELETE FROM cbt_siswa_ujian");
            $deleted['cbt_siswa_ujian'] = mysql_affected_rows();
            mysql_query("DELETE FROM cbt_audio");
            $deleted['cbt_audio'] = mysql_affected_rows();

            $cekPengawasan = mysql_query("SHOW TABLES LIKE 'cbt_pengawasan'");
            if ($cekPengawasan && mysql_num_rows($cekPengawasan) > 0) {
                mysql_query("DELETE FROM cbt_pengawasan");
                $deleted['cbt_pengawasan'] = mysql_affected_rows();
            }

            mysql_query("DELETE FROM cbt_ujian");
            $deleted['cbt_ujian'] = mysql_affected_rows();

            if (function_exists('bee_log')) {
                bee_log('WARN', 'RESET_EXAM_DATA_ALL', 'Reset total data tes/ujian dari menu database', $deleted);
            }

            $fileBackupMessage = "<div class=\"alert alert-warning alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Reset data tes/ujian selesai. Terhapus: Jawaban <strong>" . intval($deleted['cbt_jawaban']) . "</strong>, Nilai <strong>" . intval($deleted['cbt_nilai']) . "</strong>, Peserta Ujian <strong>" . intval($deleted['cbt_siswa_ujian']) . "</strong>, Audio <strong>" . intval($deleted['cbt_audio']) . "</strong>, Pengawasan <strong>" . intval($deleted['cbt_pengawasan']) . "</strong>, Jadwal Ujian <strong>" . intval($deleted['cbt_ujian']) . "</strong>.</div>";
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exam_sync_action']) && $_POST['exam_sync_action'] === 'recalc_exam_scores') {
        $role = isset($_COOKIE['beelogin']) ? $_COOKIE['beelogin'] : '';
        if ($role !== 'admin') {
            $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Akses ditolak. Sinkronisasi nilai hanya untuk admin.</div>";
        } else {
            @set_time_limit(0);

            $sqlUpdateJawaban = "
                UPDATE cbt_jawaban j
                INNER JOIN cbt_soal s
                    ON s.XKodeSoal = j.XKodeSoal
                    AND s.XNomerSoal = j.XNomerSoal
                SET
                    j.XKunciJawaban = CASE
                        WHEN s.XKunciJawaban = 'A' OR s.XKunciJawaban = j.XA THEN 'A'
                        WHEN s.XKunciJawaban = 'B' OR s.XKunciJawaban = j.XB THEN 'B'
                        WHEN s.XKunciJawaban = 'C' OR s.XKunciJawaban = j.XC THEN 'C'
                        WHEN s.XKunciJawaban = 'D' OR s.XKunciJawaban = j.XD THEN 'D'
                        WHEN s.XKunciJawaban = 'E' OR s.XKunciJawaban = j.XE THEN 'E'
                        ELSE ''
                    END,
                    j.XNilai = CASE
                        WHEN j.XJenisSoal = 1 THEN
                            CASE
                                WHEN j.XJawaban = '' THEN 0
                                WHEN ((s.XKunciJawaban = 'A' OR s.XKunciJawaban = j.XA) AND j.XJawaban = 'A')
                                  OR ((s.XKunciJawaban = 'B' OR s.XKunciJawaban = j.XB) AND j.XJawaban = 'B')
                                  OR ((s.XKunciJawaban = 'C' OR s.XKunciJawaban = j.XC) AND j.XJawaban = 'C')
                                  OR ((s.XKunciJawaban = 'D' OR s.XKunciJawaban = j.XD) AND j.XJawaban = 'D')
                                  OR ((s.XKunciJawaban = 'E' OR s.XKunciJawaban = j.XE) AND j.XJawaban = 'E')
                                THEN 1
                                ELSE 0
                            END
                        ELSE j.XNilai
                    END
                WHERE j.XJenisSoal = 1
            ";

            $okUpdate = mysql_query($sqlUpdateJawaban);
            if (!$okUpdate) {
                $safeErr = htmlspecialchars(mysql_error(), ENT_QUOTES, 'UTF-8');
                $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Sinkronisasi gagal saat update nilai jawaban: <strong>$safeErr</strong></div>";
            } else {
                $updatedJawaban = mysql_affected_rows();

                $okDeleteNilai = mysql_query("DELETE FROM cbt_nilai");
                if (!$okDeleteNilai) {
                    $safeErr = htmlspecialchars(mysql_error(), ENT_QUOTES, 'UTF-8');
                    $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Sinkronisasi gagal saat reset rekap nilai: <strong>$safeErr</strong></div>";
                } else {
                    $sqlInsertNilai = "
                        INSERT INTO cbt_nilai
                        (
                            XNomerUjian, XNIK, XKodeUjian, XTokenUjian, XTgl, XJumSoal, XBenar, XSalah, XNilai,
                            XPersenPil, XPersenEsai, XEsai, XTotalNilai, XKodeMapel, XKodeKelas, XKodeSoal, XSetId, XSemester
                        )
                        SELECT
                            j.XUserJawab AS XNomerUjian,
                            COALESCE(sis.XNIK, '') AS XNIK,
                            COALESCE(u.XKodeUjian, '') AS XKodeUjian,
                            j.XTokenUjian AS XTokenUjian,
                            CURDATE() AS XTgl,
                            (COALESCE(p.XPilGanda, 0) + COALESCE(p.XEsai, 0)) AS XJumSoal,
                            SUM(CASE WHEN j.XJenisSoal = 1 AND j.XNilai = 1 THEN 1 ELSE 0 END) AS XBenar,
                            GREATEST(
                                COALESCE(p.XPilGanda, 0) - SUM(CASE WHEN j.XJenisSoal = 1 AND j.XNilai = 1 THEN 1 ELSE 0 END),
                                0
                            ) AS XSalah,
                            SUM(CASE WHEN j.XJenisSoal = 1 AND j.XNilai = 1 THEN 1 ELSE 0 END) AS XNilai,
                            COALESCE(p.XPersenPil, 0) AS XPersenPil,
                            COALESCE(p.XPersenEsai, 0) AS XPersenEsai,
                            SUM(CASE WHEN j.XJenisSoal = 2 THEN IFNULL(j.XNilaiEsai, 0) ELSE 0 END) AS XEsai,
                            (
                                CASE
                                    WHEN COALESCE(p.XPilGanda, 0) > 0 THEN
                                        (SUM(CASE WHEN j.XJenisSoal = 1 AND j.XNilai = 1 THEN 1 ELSE 0 END) / COALESCE(p.XPilGanda, 1)) * COALESCE(p.XPersenPil, 0)
                                    ELSE 0
                                END
                                +
                                SUM(CASE WHEN j.XJenisSoal = 2 THEN IFNULL(j.XNilaiEsai, 0) ELSE 0 END) * (COALESCE(p.XPersenEsai, 0) / 100)
                            ) AS XTotalNilai,
                            COALESCE(u.XKodeMapel, '') AS XKodeMapel,
                            COALESCE(sis.XKodeKelas, u.XKodeKelas, '') AS XKodeKelas,
                            j.XKodeSoal AS XKodeSoal,
                            COALESCE(u.XSetId, j.XSetId, '') AS XSetId,
                            COALESCE(u.XSemester, j.XSemester, 1) AS XSemester
                        FROM cbt_jawaban j
                        LEFT JOIN cbt_ujian u
                            ON u.XKodeSoal = j.XKodeSoal
                            AND u.XTokenUjian = j.XTokenUjian
                        LEFT JOIN cbt_paketsoal p
                            ON p.XKodeSoal = j.XKodeSoal
                        LEFT JOIN cbt_siswa sis
                            ON sis.XNomerUjian = j.XUserJawab
                        WHERE j.XUserJawab <> ''
                        GROUP BY j.XUserJawab, j.XKodeSoal, j.XTokenUjian
                    ";

                    $okInsertNilai = mysql_query($sqlInsertNilai);
                    if (!$okInsertNilai) {
                        $safeErr = htmlspecialchars(mysql_error(), ENT_QUOTES, 'UTF-8');
                        $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Sinkronisasi gagal saat membangun ulang rekap nilai: <strong>$safeErr</strong></div>";
                    } else {
                        $insertedNilai = mysql_affected_rows();
                        if (function_exists('bee_log')) {
                            bee_log('INFO', 'RECALC_EXAM_SCORE', 'Sinkronisasi hitung ulang hasil ujian dari menu backup', array(
                                'updated_jawaban' => (int) $updatedJawaban,
                                'inserted_nilai' => (int) $insertedNilai
                            ));
                        }
                        $fileBackupMessage = "<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Sinkronisasi selesai. Jawaban diperbarui: <strong>" . intval($updatedJawaban) . "</strong> baris. Rekap nilai dibangun ulang: <strong>" . intval($insertedNilai) . "</strong> baris.</div>";
                    }
                }
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_action'])) {
        $action = $_POST['file_action'];
        if (!class_exists('ZipArchive')) {
            $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>ZipArchive tidak tersedia pada server.</div>";
        } elseif ($baseDir === false) {
            $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Base directory tidak ditemukan.</div>";
        } elseif ($action === 'backup_files') {
            if (!ensure_dir_writable($backupDir, 0777)) {
                $safeDir = htmlspecialchars($backupDir, ENT_QUOTES, 'UTF-8');
                $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Folder backup tidak bisa ditulis: <strong>$safeDir</strong>.</div>";
            } else {
                $zipName = 'dbee-files_' . time() . '.zip';
                $zipPath = $backupDir . '/' . $zipName;
                $zip = new ZipArchive();
                if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
                $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Gagal membuat file backup.</div>";
                } else {
                    $added = 0;
                    $perDirAdded = array();
                    foreach ($fileBackupDirs as $dirName) {
                        $dirPath = $baseDir . '/' . $dirName;
                        $perDirAdded[$dirName] = 0;
                        if (!is_dir($dirPath)) {
                            continue;
                        }
                        $zip->addEmptyDir($dirName);
                        $iterator = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS)
                        );
                        foreach ($iterator as $fileInfo) {
                            if (!$fileInfo->isFile()) {
                                continue;
                            }
                            $filePath = $fileInfo->getPathname();
                            $relativePath = $dirName . '/' . substr($filePath, strlen($dirPath) + 1);
                            if ($zip->addFile($filePath, $relativePath)) {
                                $added++;
                                $perDirAdded[$dirName]++;
                            }
                        }
                    }
                    $zip->close();
                    $safeZip = htmlspecialchars($zipName, ENT_QUOTES, 'UTF-8');
                    $fotosiswaCount = isset($perDirAdded['fotosiswa']) ? (int) $perDirAdded['fotosiswa'] : 0;
                    $fileBackupMessage = "<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Backup file berhasil dibuat: <strong>$safeZip</strong> ($added file). Foto siswa dibackup: <strong>$fotosiswaCount</strong> file.</div>";
                }
            }
        } elseif ($action === 'restore_files') {
            $zipPath = '';
            $zipLabel = '';

            $hasUpload = isset($_FILES['backup_zip']) && isset($_FILES['backup_zip']['error']) && $_FILES['backup_zip']['error'] !== UPLOAD_ERR_NO_FILE;
            if ($hasUpload) {
                if ($_FILES['backup_zip']['error'] !== UPLOAD_ERR_OK) {
                    $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Upload file backup gagal (code " . intval($_FILES['backup_zip']['error']) . ").</div>";
                } else {
                    $uploadName = basename($_FILES['backup_zip']['name']);
                    $ext = strtolower(pathinfo($uploadName, PATHINFO_EXTENSION));
                    if ($ext !== 'zip') {
                        $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>File upload harus berformat .zip.</div>";
                    } else {
                        $tmpUploadPath = isset($_FILES['backup_zip']['tmp_name']) ? $_FILES['backup_zip']['tmp_name'] : '';
                        if ($tmpUploadPath === '' || !is_uploaded_file($tmpUploadPath)) {
                            $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>File upload tidak valid.</div>";
                        } else {
                            $zipPath = $tmpUploadPath;
                            $zipLabel = $uploadName;
                        }
                    }
                }
            } else {
                $backupFile = isset($_POST['backup_file']) ? basename($_POST['backup_file']) : '';
                if ($backupFile === '' || strpos($backupFile, 'dbee-files_') !== 0 || substr($backupFile, -4) !== '.zip') {
                    $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>File restore tidak valid.</div>";
                } else {
                    $zipPath = $backupDir . '/' . $backupFile;
                    $zipLabel = $backupFile;
                    if (!is_file($zipPath)) {
                        $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>File backup tidak ditemukan.</div>";
                    }
                }
            }

            if ($zipPath !== '' && $fileBackupMessage === '') {
                $zip = new ZipArchive();
                if ($zip->open($zipPath) !== true) {
                    $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Gagal membuka file backup.</div>";
                } else {
                    $extracted = 0;
                    $skipped = 0;
                    for ($i = 0; $i < $zip->numFiles; $i++) {
                        $entryName = $zip->getNameIndex($i);
                        if ($entryName === false) {
                            $skipped++;
                            continue;
                        }
                        $entryName = str_replace('\\', '/', $entryName);
                        if ($entryName === '' || strpos($entryName, "\0") !== false) {
                            $skipped++;
                            continue;
                        }
                        if ($entryName[0] === '/' || preg_match('/^[A-Za-z]:/', $entryName) || strpos($entryName, '../') !== false) {
                            $skipped++;
                            continue;
                        }
                        $parts = explode('/', $entryName, 2);
                        $topDir = $parts[0];
                        if (!in_array($topDir, $fileBackupDirs, true)) {
                            $skipped++;
                            continue;
                        }
                        $destPath = $baseDir . '/' . $entryName;
                        if (substr($entryName, -1) === '/') {
                            if (!is_dir($destPath)) {
                                ensure_dir_writable($destPath, 0777);
                            }
                            continue;
                        }
                        $destDir = dirname($destPath);
                        if (!ensure_dir_writable($destDir, 0777)) {
                            $skipped++;
                            continue;
                        }
                        if (file_exists($destPath) && !is_writable($destPath)) {
                            @unlink($destPath);
                        }
                        $in = $zip->getStream($entryName);
                        if ($in === false) {
                            $skipped++;
                            continue;
                        }
                        $out = fopen($destPath, 'w');
                        if ($out === false) {
                            fclose($in);
                            $skipped++;
                            continue;
                        }
                        stream_copy_to_stream($in, $out);
                        fclose($in);
                        fclose($out);
                        $extracted++;
                    }
                    $zip->close();
                    $safeZip = htmlspecialchars($zipLabel, ENT_QUOTES, 'UTF-8');
                    $fileBackupMessage = "<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Restore file selesai dari <strong>$safeZip</strong> ($extracted file, $skipped dilewati).</div>";
                }
            }
        }
    }

    if (is_dir($backupDir)) {
        $backupSiswa = list_backup_files($backupDir . '/dbee-siswa_*.sql');
        $backupUjian = list_backup_files($backupDir . '/dbee-ujian_*.sql');
        $backupSemua = list_backup_files($backupDir . '/dbee_*.sql');
        $backupFiles = list_backup_files($backupDir . '/dbee-files_*.zip');
    }

    function render_backup_list($files)
    {
        if (!$files || count($files) === 0) {
            return "<div><em>Belum ada backup.</em></div>";
        }
        $out = "";
        foreach ($files as $file) {
            $basename = basename($file);
            $waktu = date('Y-m-d H:i', filemtime($file));
            $out .= "<div style='margin-bottom:6px;'>$basename<br><small>$waktu</small> ";
            $safeFile = rawurlencode($basename);
            $out .= "<a class='btn btn-info btn-xs' style='margin-left:6px;' href='download_backup.php?file=$safeFile'>Download</a></div>";
        }
        return $out;
    }
    ?>
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header">Backup Database</h3>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <table width="100%">
                        <tr>
                            <td>Daftar Tabel</td>
                            <td align="right">
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <br />
                    <div class="alert alert-info">

                        Tombol &nbsp; <button type='button' class='btn btn-danger'><i class='fa fa-times'></i></button>
                        &nbsp; : selain Backup, juga akan menghapus semua table yang berkaitan dengan Pilihan Jenis
                        Data<br>
                        Lokasi file Backup, Silahkan lihat folder /cbt-smkalq/backup/
                    </div>
                    <?php
                    if ($fileBackupMessage !== '') {
                        echo $fileBackupMessage;
                    }
                    ?>


                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                            <tr>
                                <th width="10%">No.</th>
                                <th width="35%">Jenis Data</th>
                                <th width="30%">Backup Terakhir</th>
                                <th width="15%">Backup </th>
                                <th width="15%">Hapus </th>

                            </tr>
                        </thead>
                        <tbody>

                            <tr class="odd gradeX">
                                <td>1<input type="hidden" value="<?php echo $s['Urutan']; ?>"
                                        id="txt_mapel<?php echo $s['Urutan']; ?>"></td>
                                <td>Backup Mapel, Kelas, Siswa </td>
                                <td><?php echo render_backup_list($backupSiswa); ?></td>
                                <td align="center"><a href="?modul=backup&datax=siswa&aksi=1">
                                        <button type="button" class="btn btn-success btn-sm"><i
                                                class="fa fa-edit"></i></button></a></td>
                                <td align="center"><a href="?modul=backup&datax=siswa&aksi=2">
                                        <button type='button' class='btn btn-danger'><i
                                                class='fa fa-times'></i></button></a></td>
                            </tr>
                            <tr class="odd gradeX">
                                <td>2<input type="hidden" value="<?php echo $s['Urutan']; ?>"
                                        id="txt_mapel<?php echo $s['Urutan']; ?>"></td>
                                <td>Backup Soal dan Jawaban</td>
                                <td><?php echo render_backup_list($backupUjian); ?></td>
                                <td align="center"><a href="?modul=backup&datax=ujian&aksi=1">
                                        <button type="button" class="btn btn-success btn-sm"><i
                                                class="fa fa-edit"></i></button></a></td>
                                <td align="center"><a href="?modul=backup&datax=ujian&aksi=2">
                                        <button type='button' class='btn btn-danger'><i
                                                class='fa fa-times'></i></button></a></td>
                            </tr>

                            <tr class="odd gradeX">
                                <td>3<input type="hidden" value="<?php echo $s['Urutan']; ?>"
                                        id="txt_mapel<?php echo $s['Urutan']; ?>"></td>
                                <td>Backup Database</td>
                                <td><?php echo render_backup_list($backupSemua); ?></td>
                                <td align="center"><a href="?modul=backup&datax=semua&aksi=1">
                                        <button type="button" class="btn btn-success btn-sm"><i
                                                class="fa fa-edit"></i></button></a></td>
                                <td align="center"><a href="?modul=backup&datax=semua&aksi=2">
                                        <button type='button' class='btn btn-danger'><i
                                                class='fa fa-times'></i></button></a></td>
                            </tr>
                            <tr class="odd gradeX">
                                <td>4<input type="hidden" value="<?php echo $s['Urutan']; ?>"
                                        id="txt_mapel<?php echo $s['Urutan']; ?>"></td>
                                <td>Backup File Upload (Soal, Lampiran, Foto Siswa)</td>
                                <td><?php echo render_backup_list($backupFiles); ?></td>
                                <td align="center">
                                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal"
                                        data-target="#fileBackupModal"><i class="fa fa-archive"></i></button>
                                </td>
                                <td align="center">-</td>
                            </tr>
                            <tr class="odd gradeX">
                                <td>5</td>
                                <td><strong>Reset Data Tes &amp; Ujian</strong><br><small>Menghapus seluruh jawaban siswa, nilai, peserta ujian, audio, pengawasan, dan jadwal ujian. Tidak menghapus bank soal, data siswa, mapel, kelas, maupun konfigurasi sistem.</small></td>
                                <td>-</td>
                                <td align="center">-</td>
                                <td align="center">
                                    <form method="post" onsubmit="return confirm('Yakin reset SEMUA data tes dan ujian?\\n\\nData yang dihapus: jawaban, nilai, peserta ujian, audio, pengawasan, dan jadwal ujian.\\n\\nData yang TIDAK dihapus: bank soal, data siswa, mapel/kelas, dan konfigurasi sistem.');" style="margin:0;">
                                        <input type="hidden" name="exam_reset_action" value="reset_exam_data">
                                        <button type="submit" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i> Reset Data Tes</button>
                                    </form>
                                </td>
                            </tr>
                            <tr class="odd gradeX">
                                <td>6</td>
                                <td><strong>Sinkronisasi Hitung Ulang Hasil Ujian</strong><br><small>Memperbarui nilai benar/salah berdasarkan kunci jawaban terbaru di bank soal, lalu membangun ulang rekap nilai siswa untuk analisa.</small></td>
                                <td>-</td>
                                <td align="center">
                                    <form method="post" onsubmit="return confirm('Yakin sinkronisasi hitung ulang hasil ujian?\\n\\nProses ini akan memperbarui nilai jawaban pilihan ganda dan membangun ulang tabel rekap nilai siswa.');" style="margin:0;">
                                        <input type="hidden" name="exam_sync_action" value="recalc_exam_scores">
                                        <button type="submit" class="btn btn-warning btn-sm"><i class="fa fa-refresh"></i> Sinkronisasi Nilai</button>
                                    </form>
                                </td>
                                <td align="center">-</td>
                            </tr>


                            <!-- Button trigger modal -->
                            <!-- Modal -->
                            <div class="modal fade" id="myModal<?php echo $s['XNomerUjian']; ?>" tabindex="-1"
                                role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-hidden="true">&times;</button>
                                            <h4 class="modal-title" id="myModalLabel">
                                                <?php echo "Peserta Ujian : $s[XNomerUjian]"; ?></h4>
                                        </div>
                                        <div class="modal-body" style="text-align:center">

                                            <?php
                                            if (file_exists("../../fotosiswa/$s[XFoto]") && !$gbr == '') { ?>
                                                <img src="../../fotosiswa/<?php echo $s['XFoto']; ?>" width="400px">
                                            <?php
                                            } else {
                                                echo "<img src=../../fotosiswa/nouser.png>";
                                            }
                                            ?>


                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default"
                                                data-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-primary">Save changes</button>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- /.modal -->



                        </tbody>
                    </table>
                    <!-- /.table-responsive -->
                    <div class="well">
                        <h4>Restore Database</h4>
                        <br>
                        <form action="?modul=restore" method="post" enctype="multipart/form-data">
                            <table>
                                <tr>
                                    <td><input type="file" id="anu" name="anu" accept=".sql"></td>
                                    <td>
                                        <button type="submit" class="btn btn-info btn-small"><i
                                                class="fa fa-plus-circle"></i> Restore</button>
                                    </td>
                                </tr>
                            </table>
                        </form>

                    </div>
                    <div class="modal fade" id="fileBackupModal" tabindex="-1" role="dialog"
                        aria-labelledby="fileBackupModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal"
                                        aria-hidden="true">&times;</button>
                                    <h4 class="modal-title" id="fileBackupModalLabel">Backup &amp; Restore File</h4>
                                </div>
                                <div class="modal-body">
                                    <p>Backup file akan mengompres folder: pictures, audio, video, fotosiswa.</p>
                                    <form method="post" style="margin-bottom:15px;">
                                        <input type="hidden" name="file_action" value="backup_files">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fa fa-archive"></i> Backup File
                                        </button>
                                    </form>
                                    <hr />
                                    <form method="post" enctype="multipart/form-data">
                                        <input type="hidden" name="file_action" value="restore_files">
                                        <label for="backup_zip">Upload file backup ZIP (opsional)</label>
                                        <input type="file" name="backup_zip" id="backup_zip" class="form-control"
                                            accept=".zip">
                                        <p style="margin:6px 0 10px 0;color:#777;">Jika diisi, file upload akan dipakai
                                            langsung untuk restore.</p>
                                        <label for="backup_file">Pilih file backup</label>
                                        <select name="backup_file" id="backup_file" class="form-control">
                                            <?php
                                            if ($backupFiles && count($backupFiles) > 0) {
                                                foreach ($backupFiles as $file) {
                                                    $basename = htmlspecialchars(basename($file), ENT_QUOTES, 'UTF-8');
                                                    echo "<option value=\"$basename\">$basename</option>";
                                                }
                                            } else {
                                                echo "<option value=\"\">Belum ada backup file</option>";
                                            }
                                            ?>
                                        </select>
                                        <br>
                                        <button type="submit" class="btn btn-info" <?php echo ($backupFiles && count($backupFiles) > 0) ? '' : 'disabled'; ?>>
                                            <i class="fa fa-plus-circle"></i> Restore File
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->



    <script src="../vendor/jquery/jquery-1.12.3.js"></script>
    <script src="../vendor/jquery/jquery.dataTables.min.js"></script>
    <!-- jQuery -->
    <script src="../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- DataTables JavaScript -->
    <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../dist/js/sb-admin-2.js"></script>

    <!-- Page-Level Demo Scripts - Tables - Use for reference -->
    <script>
        $(document).ready(function () {
            $('#dataTables-example').DataTable({
                responsive: true
            });



        });
    </script>
    <script>$(document).ready(function () {
            var table = $('#example').DataTable();

            $('#example tbody').on('click', 'tr', function () {
                if ($(this).hasClass('selected')) {
                    $(this).removeClass('selected');
                }
                else {
                    table.$('tr.selected').removeClass('selected');
                    $(this).addClass('selected');
                }
            });

            $('#button').click(function () {
                table.row('.selected').remove().draw(false);
            });
        });</script>



</body>

</html>
