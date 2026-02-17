<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
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

 <?php $tgljam = date("Y/m/d H:i");  
 $tgl = date("Y/m/d"); 
 ?>
  <link rel="stylesheet" type="text/css" href="./jquery.datetimepicker.css"/>
<style type="text/css">

.custom-date-style {
	background-color: red !important;
}

.input{	
}
.input-wide{
	width: 500px;
}

</style>
<?php 
$tgx = 29;
$blx = 09;
$thx = 2016;

$tglx = date("Y/m/d");
$jamx = date("H:i:s");
?>
<body>
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">Cetak Berita Acara</h3>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                           <table width="100%"><tr><td>Daftar Pelaksanaan Test</td><td align="right">
                                        
                                        </td></tr>
							</table>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                        		<table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
	                                    <th width="5%">No.</th>
										<th width="5%">Ujian</th>                                        
                                        <th width="5%">Soal</th>
                                        <th width="20%">Mata Pelajaran</th>
	                                    <th width="5%">Kelas</th>	
	                                    <th width="5%">Ruang</th>
	                                    <th width="5%">Token</th>	
	                                    <th width="12%">Waktu</th>
                                        <th width="3%">Durasi</th>  
                                        <th width="5%">Pengawas</th>
                                        <th width="10%">Status</th>                                        
                                 </tr>
                                </thead>
                                <tbody>
<?php 
$sql = mysql_query("select u.*,m.*,u.Urut as Urutan,u.XKodeKelas as kokel from cbt_ujian u left join cbt_mapel m on m.XKodeMapel = u.XKodeMapel 
left join cbt_paketsoal p on p.XKodeSoal = u.XKodeSoal
where u.XStatusUjian in ('0','1','9')
or ADDTIME(CONCAT(u.XTglUjian,' ',u.XJamUjian),u.XLamaUjian) <= NOW()
order by u.XTglUjian desc, u.XJamUjian desc");
if (!$sql) {
    if (function_exists('bee_log')) {
        bee_log('ERROR', 'BERITA_ACARA_QUERY_FAILED', 'Gagal mengambil daftar berita acara', array(
            'mysql_error' => mysql_error()
        ));
    }
}
if ($sql && mysql_num_rows($sql) > 0) {
	while($s = mysql_fetch_array($sql)){ 
	$tokenNow = mysql_real_escape_string($s['XTokenUjian']);
	$kodesoalNow = mysql_real_escape_string($s['XKodeSoal']);
	$sqlRuang = mysql_query("SELECT DISTINCT TRIM(sw.XRuang) AS XRuang
		FROM cbt_siswa_ujian su
		LEFT JOIN cbt_siswa sw ON TRIM(sw.XNomerUjian) = TRIM(su.XNomerUjian)
		WHERE TRIM(su.XTokenUjian) = TRIM('$tokenNow')
		AND TRIM(su.XKodeSoal) = TRIM('$kodesoalNow')
		AND TRIM(sw.XRuang) <> ''
		ORDER BY sw.XRuang");
	$ruangList = array();
	if ($sqlRuang) {
		while ($rowR = mysql_fetch_array($sqlRuang)) {
			$ru = trim($rowR['XRuang']);
			if ($ru !== '') {
				$ruangList[] = $ru;
			}
		}
	}
	if (count($ruangList) < 1) {
		$ruangList[] = '';
	}
	foreach ($ruangList as $ruangItem) {
		$ruangDisplay = ($ruangItem === '') ? 'Tanpa Ruang' : $ruangItem;
		$rowKey = preg_replace('/[^A-Za-z0-9_]/', '_', $s['XTokenUjian'] . '_' . $ruangDisplay);
		$tokenEncoded = urlencode($s['XTokenUjian']);
		$ruangEncoded = urlencode($ruangItem);

$time1 = "$s[XJamUjian]";
$time2 = "$s[XLamaUjian]";

$secs = strtotime($time2)-strtotime("00:00:00");
$jamhabis = date("H:i:s",strtotime($time1)+$secs);	
$sekarang = date("H:i:s");	
$tglsekarang = date("Y-m-d");	
$tglujian = "$s[XTglUjian]";	
		
								?>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript" src="jquery-1.4.js"></script>
                                
<script>    
$(document).ready(function(){
	$("#awas<?php echo $rowKey; ?>").click(function(){
		//alert("<?php echo $s['Urutan']; ?>");
		 var txt_tokenx = $("#txt_tokenx<?php echo $rowKey; ?>").val();
		 var txt_pengawasx = $("#txt_pengawasx<?php echo $rowKey; ?>").val();
		 var txt_nip_pengawasx = $("#txt_nip_pengawasx<?php echo $rowKey; ?>").val();
	
	  
	//alert(txt_ujianx);  
	  
	 $.ajax({
		 type:"POST",
		 url:"ubahpengawas.php",    
		 data: "aksi=simpan&txt_tokenx=" + txt_tokenx + "&txt_pengawas=" + txt_pengawasx + "&txt_nippengawas=" + txt_nip_pengawasx,
		 success: function(data){
			  //alert("Soal Sukses Digandakan");
			  document.location.reload();
			
		 }
		 });
	});


});
</script>                               
                                    <tr class="odd gradeX">
                                        <td>
                                        <input type="hidden" value="<?php echo $s['Urutan']; ?>" id="txt_mapel<?php echo $s['Urutan']; ?>"><?php echo $s['Urutan']; ?>
                                        <input type="hidden" value="<?php echo $s['Urutan']; ?>" id="txt_ujian<?php echo $s['Urutan']; ?>">
                                        </td>
                                        <td><?php echo $s['XKodeUjian']; ?></td>
	                                        <td><?php echo $s['XKodeSoal']; ?></td>
	                                        <td><?php echo $s['XNamaMapel']; ?></td>
	                                        <td><?php echo $s['kokel']." | ".$s['XKodeJurusan']."."; ?></td> 
	                                        <td><?php echo $ruangDisplay; ?></td>
	                                        <td><?php echo $s['XTokenUjian']; ?></td>
                                        <td align="center">
                                        <?php echo $s['XTglUjian']." ".$s['XJamUjian'] ; ?>
                                        </td>                                        
                                        <td align="center">
                                        <?php echo $s['XLamaUjian']; ?>
                                        </td>
                                       <td align="center">
                                        <?php 
											echo $s['XPengawas']; 
											?>
	                                        </td>
	                                        <td>													
	                                        <a href="?modul=cetak_berita&token=<?php echo $tokenEncoded; ?>&ruang=<?php echo $ruangEncoded; ?>">
	                                        <button type="button" class="btn btn-info"><i class="fa fa-print"></i></button></a>
	                                        <button type="button" class="btn btn-warning btn-small"  data-toggle="modal" 
	                                        data-target="#myPengawas<?php echo $rowKey; ?>"><i class="fa fa-edit"></i></button>
	                                        </td>     
                                                                                                              
                                    </tr>
  <!-- Button trigger modal -->
  <!-- Modal -->
                            <div class="modal fade" id="myPengawas<?php echo $rowKey; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                        <h4 class="modal-title" id="myModalLabel"><?php echo "Pengawas Ujian Mapel : $s[XNamaMapel] (Ruang: $ruangDisplay)"; ?></h4>
                                        </div>
                                        <div class="modal-body" >
                                        
                                        <input type="hidden" value="<?php echo $s['XTokenUjian']; ?>" id="txt_tokenx<?php echo $rowKey; ?>"><br>
                                        <span>Nama Pengawas : </span><br><span><input type="text" id="txt_pengawasx<?php echo $rowKey; ?>" width="90%">
                                        </span><br>
                               			<span>NIP  Pengawas : </span><br><span><input type="text" id="txt_nip_pengawasx<?php echo $rowKey; ?>"
                                         width="90%"></span>				
                                        <br><br>
                                        
                                        </div>

                                        <div class="modal-footer">
                                        <input type="submit"  class="btn btn-info btn-lg btn-small" 
                                        id="awas<?php echo $rowKey; ?>" value="Simpan" name="awas<?php echo $rowKey; ?>">    
                                        </div>                                        
                                        
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>
                            <!-- /.modal --> 
                                           

                              <?php } ?>
                              <?php } ?>
                              <?php } else { ?>
                                    <tr>
                                        <td colspan="11" align="center">Belum ada data ujian selesai untuk berita acara.</td>
                                    </tr>
                              <?php } ?>
                                   
                                </tbody>
                            </table>
                            
<!-- /.panel-heading -->
                                                   
                            <!-- /.table-responsive -->
                            <div class="well">
                            <p>Untuk Reset Status Peserta : Tekan tombol token untuk menampilkan daftar siswa yang akan di reset</p>
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
    $(document).ready(function() {
        $('#dataTables-example').DataTable({
            responsive: true
        });
    
	
	
	});
    </script>
    <script>$(document).ready(function() {
    var table = $('#example').DataTable();
 
    $('#example tbody').on( 'click', 'tr', function () {
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    } );
 
    $('#button').click( function () {
        table.row('.selected').remove().draw( false );
    } );
} );</script>
    
 
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Buat Bank Soal Baru</h4>
      </div>
      <div class="modal-body">
        <?php include "buat_banksoalbaru.php";?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
</div>
</div>

</body>

</html>
