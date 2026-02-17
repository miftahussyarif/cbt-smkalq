<?php
include "../../config/server.php";
$tgl = date("Y-m-d");

bee_log('INFO', 'TEST_CREATE_REQUEST', 'Permintaan buat tes/bank soal baru', array(
    'kode_soal' => isset($_REQUEST['txt_nama']) ? $_REQUEST['txt_nama'] : '',
    'kode_mapel' => isset($_REQUEST['txt_mapel']) ? $_REQUEST['txt_mapel'] : '',
    'kelas' => isset($_REQUEST['txt_kelas']) ? $_REQUEST['txt_kelas'] : '',
    'jurusan' => isset($_REQUEST['txt_jurusan']) ? $_REQUEST['txt_jurusan'] : '',
    'sesi' => isset($_REQUEST['txt_sesi']) ? $_REQUEST['txt_sesi'] : ''
));

if(str_replace(" ","",$_REQUEST['txt_nama'])!=='')	{
	$sqlcek = mysql_query("select * from cbt_paketsoal where XKodeSoal = '$_REQUEST[txt_nama]'");
	$jumcek = mysql_num_rows($sqlcek);
	if($jumcek<1){
		if(str_replace(" ","",$_REQUEST['txt_jawab'])==""){ $jum = 5; } else { $jum = $_REQUEST['txt_jawab'];}
		$jumsemuasoal = $_REQUEST['txt_jumsoal1']+$_REQUEST['txt_jumsoal2'];
		
		$sql = mysql_query("insert into cbt_paketsoal 
		(XKodeMapel,XLevel,XKodeSoal,XJumPilihan,XAcakSoal,XTglBuat,XGuru,XKodeKelas,XKodeJurusan,XJumSoal,XSesi,XPilGanda,XEsai,XPersenPil,XPersenEsai) values 
		('$_REQUEST[txt_mapel]','$_REQUEST[txt_level]','$_REQUEST[txt_nama]','$jum','$_REQUEST[txt_acak]','$tgl','$_COOKIE[beeuser]',
		'$_REQUEST[txt_kelas]','$_REQUEST[txt_jurusan]','$jumsemuasoal','$_REQUEST[txt_sesi]','$_REQUEST[txt_jumsoal1]','$_REQUEST[txt_jumsoal2]',
		'$_REQUEST[txt_bobotsoal1]','$_REQUEST[txt_bobotsoal2]')");

        if ($sql) {
            bee_log('INFO', 'TEST_CREATE_SUCCESS', 'Buat tes/bank soal berhasil', array(
                'kode_soal' => $_REQUEST['txt_nama'],
                'kode_mapel' => $_REQUEST['txt_mapel'],
                'kelas' => $_REQUEST['txt_kelas'],
                'jurusan' => $_REQUEST['txt_jurusan'],
                'insert_id' => mysql_insert_id()
            ));
        } else {
            bee_log('ERROR', 'TEST_CREATE_FAILED', 'Gagal buat tes/bank soal', array(
                'kode_soal' => $_REQUEST['txt_nama'],
                'kode_mapel' => $_REQUEST['txt_mapel'],
                'kelas' => $_REQUEST['txt_kelas'],
                'jurusan' => $_REQUEST['txt_jurusan'],
                'db_error' => mysql_error()
            ));
        }
	} else {
        bee_log('WARN', 'TEST_CREATE_DUPLICATE', 'Gagal buat tes/bank soal karena kode sudah ada', array(
            'kode_soal' => $_REQUEST['txt_nama']
        ));
	}
} else {
    bee_log('WARN', 'TEST_CREATE_INVALID', 'Gagal buat tes/bank soal karena kode kosong', array(
        'raw_kode' => isset($_REQUEST['txt_nama']) ? $_REQUEST['txt_nama'] : ''
    ));
}	
?>
