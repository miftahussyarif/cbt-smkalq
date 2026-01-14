<?php
include "../../config/server.php";
include "../../config/pengawasan.php";

cbt_ensure_pengawasan_table();

if (!isset($_COOKIE['beelogin']) || ($_COOKIE['beelogin'] != 'admin' && $_COOKIE['beelogin'] != 'guru')) {
    echo "<div class=\"alert alert-danger\">Akses ditolak.</div>";
    return;
}
?>

<div class="row" style="margin-top:10px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-eye fa-fw"></i> Pengawasan Peserta Ujian
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="pengawasan-table">
                        <thead>
                            <tr>
                                <th width="4%">No</th>
                                <th width="12%">No Ujian</th>
                                <th width="20%">Nama</th>
                                <th width="10%">Kelas</th>
                                <th width="14%">Mapel</th>
                                <th width="12%">Status</th>
                                <th width="8%">Pindah Tab</th>
                                <th width="8%">Printscreen</th>
                                <th width="8%">Terkunci</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="help-block">Status "Aman" berarti tidak ada flag negatif aktif.</div>
            </div>
        </div>
    </div>
</div>
<?php
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    $debugSql = mysql_query("SELECT * FROM cbt_pengawasan ORDER BY XUpdatedAt DESC LIMIT 20");
    $debugRows = array();
    if ($debugSql) {
        while ($dr = mysql_fetch_assoc($debugSql)) {
            $debugRows[] = $dr;
        }
    }
    echo '<pre id="pengawasan-debug">' . htmlspecialchars(json_encode($debugRows, JSON_PRETTY_PRINT)) . '</pre>';
}
?>

<script>
    (function () {
        function initPengawasan() {
            var $ = window.jQuery;
            if (!$) {
                setTimeout(initPengawasan, 50);
                return;
            }

            function statusLabel(status) {
                if (status === 'pindah_tab') {
                    return '<span class="label label-warning">Pindah Tab</span>';
                }
                if (status === 'tab_hidden') {
                    return '<span class="label label-warning">Minimize / Fokus Keluar</span>';
                }
                if (status === 'tab_close') {
                    return '<span class="label label-danger">Menutup Tab</span>';
                }
                if (status === 'rto') {
                    return '<span class="label label-danger">Koneksi Terputus</span>';
                }
                if (status === 'lock_admin' || status === 'terkunci') {
                    return '<span class="label label-danger">Terkunci</span>';
                }
                if (status === 'printscreen') {
                    return '<span class="label label-danger">Use Printscreen</span>';
                }
                if (status === 'tidak_aman') {
                    return '<span class="label label-danger">Tidak Aman</span>';
                }
                return '<span class="label label-success">Aman</span>';
            }

            function loadPengawasan() {
                $.ajax({
                    url: 'pengawasan_data.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function (resp) {
                        if (!resp || !resp.ok) {
                            return;
                        }
                        var rows = [];
                        for (var i = 0; i < resp.data.length; i++) {
                            var item = resp.data[i];
                            var aksi = item.locked ? 'unlock' : 'lock';
                            var btnClass = item.locked ? 'btn-success' : 'btn-danger';
                            var btnText = item.locked ? 'Unlock' : 'Lock';
                            rows.push(
                                '<tr>' +
                                '<td>' + item.no + '</td>' +
                                '<td>' + item.nomer_ujian + '</td>' +
                                '<td>' + item.nama + '</td>' +
                                '<td>' + item.kelas + '</td>' +
                                '<td>' + item.mapel + '</td>' +
                                '<td>' + statusLabel(item.status) + '</td>' +
                                '<td align="center">' + item.pindah_tab + '</td>' +
                                '<td align="center">' + item.printscreen + '</td>' +
                                '<td align="center">' + (item.locked ? 'Ya' : 'Tidak') + '</td>' +
                                '<td align="center">' +
                                '<button class="btn btn-xs ' + btnClass + ' btn-lock" data-action="' + aksi + '"' +
                                ' data-nomer="' + item.nomer_ujian + '" data-token="' + item.token + '"' +
                                ' data-kodesoal="' + item.kodesoal + '">' + btnText + '</button>' +
                                '</td>' +
                                '</tr>'
                            );
                        }
                        $('#pengawasan-table tbody').html(rows.join(''));
                    }
                });
            }

            $(document).on('click', '.btn-lock', function () {
                var $btn = $(this);
                $.ajax({
                    url: 'pengawasan_action.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: $btn.data('action'),
                        nomer: $btn.data('nomer'),
                        token: $btn.data('token'),
                        kodesoal: $btn.data('kodesoal')
                    },
                    success: function () {
                        loadPengawasan();
                    }
                });
            });

            loadPengawasan();
            setInterval(loadPengawasan, 5000);
        }

        if (document.readyState === 'complete') {
            initPengawasan();
        } else {
            window.addEventListener('load', initPengawasan);
        }
    })();
</script>
