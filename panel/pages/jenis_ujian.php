<?php
if (!isset($_COOKIE['beeuser'])) {
    header("Location: login.php");
}
include "../../config/server.php";

if (isset($_REQUEST['aksi']) && $_REQUEST['aksi'] == "hapus") {
    $urut = mysql_real_escape_string($_REQUEST['urut']);
    $cek = mysql_query("select XKodeUjian from cbt_tes where Urut = '$urut'");
    if ($cek && mysql_num_rows($cek) > 0) {
        $row = mysql_fetch_array($cek);
        $kode = mysql_real_escape_string($row['XKodeUjian']);
        $cekUjian = mysql_num_rows(mysql_query("select 1 from cbt_ujian where XKodeUjian = '$kode' limit 1"));
        $cekNilai = mysql_num_rows(mysql_query("select 1 from cbt_nilai where XKodeUjian = '$kode' limit 1"));
        if ($cekUjian > 0 || $cekNilai > 0) {
            $message = "Jenis ujian masih digunakan. Hapus dibatalkan.";
            echo "<script type='text/javascript'>alert('$message');</script>";
        } else {
            mysql_query("delete from cbt_tes where Urut = '$urut'");
        }
    }
}

if (isset($_REQUEST['simpan'])) {
    $id = mysql_real_escape_string($_REQUEST['id']);
    $nama = mysql_real_escape_string($_REQUEST['txt_nama']);
    if ($nama == "") {
        $message = "Nama jenis ujian tidak boleh kosong.";
        echo "<script type='text/javascript'>alert('$message');</script>";
    } else {
        mysql_query("update cbt_tes set XNamaUjian = '$nama' where Urut = '$id'");
    }
}

if (isset($_REQUEST['tambah'])) {
    $kode = mysql_real_escape_string($_REQUEST['txt_kode']);
    $nama = mysql_real_escape_string($_REQUEST['txt_nama']);
    if ($kode == "" || $nama == "") {
        $message = "Kode dan nama jenis ujian tidak boleh kosong.";
        echo "<script type='text/javascript'>alert('$message');</script>";
    } else {
        $cek = mysql_num_rows(mysql_query("select 1 from cbt_tes where XKodeUjian = '$kode' limit 1"));
        if ($cek > 0) {
            $message = "Kode jenis ujian sudah ada.";
            echo "<script type='text/javascript'>alert('$message');</script>";
        } else {
            mysql_query("insert into cbt_tes (XKodeUjian, XNamaUjian) values ('$kode', '$nama')");
        }
    }
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

</head>

<body>
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Jenis Ujian</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Data Jenis Ujian
                    <?php echo "<a href='#myTam' id='custId' data-toggle='modal' data-id=''>"; ?>
                    <button type="button" class="btn btn-info btn-small"><i class="fa fa-plus-circle"></i>
                        Tambah Jenis Ujian</button>
                    <?php echo "</a>"; ?>
                </div>
                <div class="panel-body">
                    <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                            <tr>
                                <th width="7%">No.</th>
                                <th width="20%">Kode</th>
                                <th>Nama Ujian</th>
                                <th width="15%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = mysql_query("select * from cbt_tes order by Urut");
                            while ($s = mysql_fetch_array($sql)) {
                                ?>
                                <tr class="odd gradeX">
                                    <td><?php echo $s['Urut']; ?></td>
                                    <td><?php echo $s['XKodeUjian']; ?></td>
                                    <td><?php echo $s['XNamaUjian']; ?></td>
                                    <?php echo "<td><a href='#myModal' id='custId' data-toggle='modal' data-id=" . $s['Urut'] . ">"; ?>
                                    <button type="button" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></button></a>
                                    <a href="?modul=jenis_ujian&aksi=hapus&urut=<?php echo $s['Urut']; ?>"
                                        onclick="return confirm('Hapus jenis ujian ini?');">
                                        <button type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i></button>
                                    </a></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="../vendor/jquery/jquery-1.12.3.js"></script>
    <script src="../vendor/jquery/jquery.dataTables.min.js"></script>
    <script src="../vendor/jquery/jquery.min.js"></script>
    <script src="../vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../vendor/metisMenu/metisMenu.min.js"></script>
    <script src="../vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../vendor/datatables-responsive/dataTables.responsive.js"></script>
    <script src="../dist/js/sb-admin-2.js"></script>

    <script>
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            responsive: true
        });
    });
    </script>

    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Ubah Jenis Ujian</h4>
                </div>
                <div class="modal-body">
                    <div class="fetched-data"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myTam" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Tambah Jenis Ujian</h4>
                </div>
                <div class="modal-body">
                    <form action="?modul=jenis_ujian&tambah=yes" method="post">
                        <div class="form-group">
                            <label>Kode Ujian</label>
                            <input type="text" class="form-control" name="txt_kode">
                        </div>
                        <div class="form-group">
                            <label>Nama Ujian</label>
                            <input type="text" class="form-control" name="txt_nama">
                        </div>
                        <button class="btn btn-primary" type="submit">Tambah</button>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.1.1.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $('#myModal').on('show.bs.modal', function (e) {
            var rowid = $(e.relatedTarget).data('id');
            $.ajax({
                type: 'post',
                url: 'edit_jenis_ujian.php',
                data: 'urut=' + rowid,
                success: function(data){
                    $('.fetched-data').html(data);
                }
            });
        });
    });
    </script>
</body>

</html>
