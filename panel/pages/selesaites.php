<?php 
include "../../config/server.php";	
?>
<?php
bee_log('INFO', 'TEST_FINISH_REQUEST', 'Permintaan selesai tes', array(
    'id_ujian' => isset($_REQUEST['txt_ujian']) ? $_REQUEST['txt_ujian'] : ''
));

$sqlselesai = mysql_query("update cbt_ujian set XStatusUjian = '9' where Urut = '$_REQUEST[txt_ujian]'");
if ($sqlselesai) {
    bee_log('INFO', 'TEST_FINISH_SUCCESS', 'Tes berhasil diakhiri', array(
        'id_ujian' => isset($_REQUEST['txt_ujian']) ? $_REQUEST['txt_ujian'] : '',
        'affected_rows' => mysql_affected_rows()
    ));
} else {
    bee_log('ERROR', 'TEST_FINISH_FAILED', 'Gagal mengakhiri tes', array(
        'id_ujian' => isset($_REQUEST['txt_ujian']) ? $_REQUEST['txt_ujian'] : '',
        'db_error' => mysql_error()
    ));
}
?>

