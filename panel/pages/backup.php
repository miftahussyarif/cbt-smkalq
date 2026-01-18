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
    $backupDir = '/opt/lampp/backup';
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['file_action'])) {
        $action = $_POST['file_action'];
        if (!class_exists('ZipArchive')) {
            $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>ZipArchive tidak tersedia pada server.</div>";
        } elseif ($baseDir === false) {
            $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Base directory tidak ditemukan.</div>";
        } elseif ($action === 'backup_files') {
            if (!is_dir($backupDir)) {
                mkdir($backupDir, 0777, true);
            }
            $zipName = 'dbee-files_' . time() . '.zip';
            $zipPath = $backupDir . '/' . $zipName;
            $zip = new ZipArchive();
            if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
                $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Gagal membuat file backup.</div>";
            } else {
                $added = 0;
                foreach ($fileBackupDirs as $dirName) {
                    $dirPath = $baseDir . '/' . $dirName;
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
                        }
                    }
                }
                $zip->close();
                $safeZip = htmlspecialchars($zipName, ENT_QUOTES, 'UTF-8');
                $fileBackupMessage = "<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Backup file berhasil dibuat: <strong>$safeZip</strong> ($added file).</div>";
            }
        } elseif ($action === 'restore_files') {
            $backupFile = isset($_POST['backup_file']) ? basename($_POST['backup_file']) : '';
            if ($backupFile === '' || strpos($backupFile, 'dbee-files_') !== 0 || substr($backupFile, -4) !== '.zip') {
                $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>File restore tidak valid.</div>";
            } else {
                $zipPath = $backupDir . '/' . $backupFile;
                if (!is_file($zipPath)) {
                    $fileBackupMessage = "<div class=\"alert alert-danger alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>File backup tidak ditemukan.</div>";
                } else {
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
                                    mkdir($destPath, 0755, true);
                                }
                                continue;
                            }
                            $destDir = dirname($destPath);
                            if (!is_dir($destDir)) {
                                mkdir($destDir, 0755, true);
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
                        $safeZip = htmlspecialchars($backupFile, ENT_QUOTES, 'UTF-8');
                        $fileBackupMessage = "<div class=\"alert alert-success alert-dismissable\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>Restore file selesai dari <strong>$safeZip</strong> ($extracted file, $skipped dilewati).</div>";
                    }
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
            $out .= "<a class='btn btn-info btn-xs' style='margin-left:6px;' href='pages/download_backup.php?file=$basename'>Download</a></div>";
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
                        Lokasi file Backup, Silahkan Lihat folder /opt/lampp/backup/
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
                                    <form method="post">
                                        <input type="hidden" name="file_action" value="restore_files">
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
