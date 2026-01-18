<?php
include "../../config/server.php";
include "../../config/pengawasan.php";

cbt_ensure_pengawasan_config_table();

if (!isset($_COOKIE['beelogin']) || $_COOKIE['beelogin'] != 'admin') {
    echo "<div class=\"alert alert-danger\">Akses ditolak. Hanya admin yang dapat mengubah konfigurasi.</div>";
    return;
}

$configs = mysql_query("SELECT * FROM cbt_pengawasan_config ORDER BY id");
?>

<div class="row" style="margin-top:10px;">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-cog fa-fw"></i> Konfigurasi Pengawasan
            </div>
            <div class="panel-body">
                <p class="help-block">Atur fitur pengawasan mana yang akan diaktifkan atau dinonaktifkan.</p>
                <form id="config-form">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="30%">Fitur Pengawasan</th>
                                <th width="50%">Deskripsi</th>
                                <th width="20%" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($cfg = mysql_fetch_assoc($configs)): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($cfg['config_label']); ?></strong></td>
                                <td><?php echo htmlspecialchars($cfg['config_description']); ?></td>
                                <td class="text-center">
                                    <label class="switch">
                                        <input type="checkbox" 
                                               name="<?php echo $cfg['config_key']; ?>" 
                                               value="1"
                                               <?php echo $cfg['config_value'] == 1 ? 'checked' : ''; ?>>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Konfigurasi</button>
                </form>
                <div id="config-message" style="margin-top:15px;"></div>
            </div>
        </div>
    </div>
</div>

<style>
.switch {
    position: relative;
    display: inline-block;
    width: 60px;
    height: 34px;
}

.switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #ccc;
    transition: .4s;
    border-radius: 34px;
}

.slider:before {
    position: absolute;
    content: "";
    height: 26px;
    width: 26px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

input:checked + .slider {
    background-color: #2196F3;
}

input:checked + .slider:before {
    transform: translateX(26px);
}
</style>

<script>
(function() {
    function initConfig() {
        if (!window.jQuery) {
            setTimeout(initConfig, 50);
            return;
        }
        var $ = window.jQuery;

        $(document).ready(function() {
            $('#config-form').on('submit', function(e) {
                e.preventDefault();
                
                var formData = {};
                $(this).find('input[type="checkbox"]').each(function() {
                    formData[$(this).attr('name')] = $(this).is(':checked') ? 1 : 0;
                });
                
                $.ajax({
                    url: 'pengawasan_config_save.php',
                    type: 'POST',
                    dataType: 'json',
                    data: { configs: JSON.stringify(formData) },
                    success: function(resp) {
                        if (resp.ok) {
                            $('#config-message').html('<div class="alert alert-success"><i class="fa fa-check"></i> Konfigurasi berhasil disimpan.</div>');
                        } else {
                            $('#config-message').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Gagal menyimpan: ' + (resp.error || 'Unknown error') + '</div>');
                        }
                        setTimeout(function() {
                            $('#config-message').html('');
                        }, 3000);
                    },
                    error: function() {
                        $('#config-message').html('<div class="alert alert-danger"><i class="fa fa-exclamation-triangle"></i> Terjadi kesalahan saat menyimpan.</div>');
                    }
                });
            });
        });
    }

    initConfig();
})();
</script>
