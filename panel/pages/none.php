	          <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Dashboard </h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->

<?php
include "../../config/server.php";

if (!function_exists('cbt_dashboard_server_id')) {
	function cbt_dashboard_server_id()
	{
		$hostRaw = '';
		if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== '') {
			$hostRaw = $_SERVER['HTTP_HOST'];
		} elseif (isset($_SERVER['SERVER_NAME'])) {
			$hostRaw = $_SERVER['SERVER_NAME'];
		}

		$host = $hostRaw;
		if (strpos($host, ':') !== false) {
			$host = preg_replace('/:\d+$/', '', $host);
		}
		$host = strtolower(trim($host));

		if ($host === '' || $host === 'localhost' || $host === '127.0.0.1' || $host === '::1') {
			return 'localhost';
		}

		$ipsV4 = array();
		$ipsV6 = array();

		$v4 = @gethostbynamel($host);
		if (is_array($v4)) {
			foreach ($v4 as $ip4) {
				if (filter_var($ip4, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
					$ipsV4[] = $ip4;
				}
			}
		}

		if (function_exists('dns_get_record')) {
			$v6 = @dns_get_record($host, DNS_AAAA);
			if (is_array($v6)) {
				foreach ($v6 as $row) {
					if (isset($row['ipv6']) && filter_var($row['ipv6'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
						$ipsV6[] = $row['ipv6'];
					}
				}
			}
		}

		$ipsV4 = array_values(array_unique($ipsV4));
		$ipsV6 = array_values(array_unique($ipsV6));

		$oneLine = array();
		if (count($ipsV4) > 0) {
			$oneLine[] = 'IPv4: ' . $ipsV4[0];
		}
		if (count($ipsV6) > 0) {
			$oneLine[] = 'IPv6: ' . $ipsV6[0];
		}

		if (count($oneLine) > 0) {
			return implode(' | ', $oneLine);
		}

		return $host;
	}
}

if (!function_exists('cbt_is_local_access')) {
	function cbt_is_local_access()
	{
		$hostRaw = '';
		if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] !== '') {
			$hostRaw = $_SERVER['HTTP_HOST'];
		} elseif (isset($_SERVER['SERVER_NAME'])) {
			$hostRaw = $_SERVER['SERVER_NAME'];
		}
		$host = preg_replace('/:\d+$/', '', strtolower(trim($hostRaw)));
		return ($host === '' || $host === 'localhost' || $host === '127.0.0.1' || $host === '::1');
	}
}

$serverIdDisplay = cbt_dashboard_server_id();
$isLocalAccess = cbt_is_local_access();
if($mode=="pusat"){
$sqlklien = mysql_query("select * from cbt_admin");
$sk = mysql_fetch_array($sqlklien);
$kodesekolah = $sk['XKodeSekolah'];
//echo $kodesekolah;

		include "../../config/ipserver.php";
			  if(isset($_SERVER['SERVER_NAME'])){
			  $serverIP = $_SERVER['SERVER_NAME'];
			  $alamat2 = $_SERVER['SERVER_PORT'];
			  }

		
		
		if ($sock = @fsockopen($domain, 80, $num, $error, 5)){
		$status_server = 1;
		//$status_internet = "Tidak ada koneksi Internet | Offline"; ?>
		
		<?php
		 $host_name = gethostbyaddr($_SERVER['REMOTE_ADDR']); //untuk mendeteksi computer name
		// echo"Nama Komputer : $host_name";
		?>
		<?php
		$pc_client = $host_name;
		//echo "Server : $pc_client";z
		include "../../config/server_status.php";		
		
		//echo $status_konek;
		if($status_konek=='1'){
		//$sqlhost = mysql_query("select * from server_sekolah where XServerName = '$pc_client' and XServerId = '$kodesekolah'");
		$sqlhost = mysql_query("select * from server_sekolah where XServerId = '$kodesekolah'");
		$sqlstatus = mysql_num_rows($sqlhost);
		//echo "select * from server_sekolah where XServerName = '$pc_client'";
		$sq = mysql_fetch_array($sqlhost);
		$var_status = $sq['XStatus'];}
		else{
		$var_status = '';$sqlstatus = 9;}		
		//echo "var_server : |$var_status|,sqlstatus : $sqlstatus ";
		
			if($sqlstatus>0&&$var_status=='0'){
				$warna = "warning"; $server_status = "STANDBY";$txt_server_status = "Akses ke Server Pusat Ditutup SN sudah terdaftar di Server Pusat";$huruf ="#ffca01";$bg=
				"#ffca01";
				}
			elseif($var_status==''&&$sqlstatus>0){
				$warna = "danger"; $server_status = "STANDBY";$txt_server_status = "CBTSync tidak terkoneksi ke server pusat";$huruf ="red";$bg=
				"red";}
			elseif($sqlstatus==0&&$var_status=='') { 
				$warna = "danger"; $server_status = "OFFLINE";$txt_server_status = "ID Server / SN tidak sesuai dengan data server pusat"; $huruf ="red";$bg="red";}
			elseif($sqlstatus>0&&$var_status>0){
				$warna = "info"; $server_status = "AKTIF";$txt_server_status = "CBTSync Siap Digunakan"; $huruf ="#10d8f3";$bg="#15c0d7"; }
			
		?>
		<div style="width:55%">   
					<div class="row" style="width:75%">
						<div class="col-lg-6">
								<h4 style="color:<?php echo $huruf; ?>; font-size:35px"><p><b><?php echo $server_status; ?></b> <span style="color:#999; font-size:14px"><?php //echo $status_internet; ?></span></p></h4>
						</div>
					</div>
							<div class="alert alert-<?php echo $warna; ?>" style="background-color:15c0d7">
							<?php echo $txt_server_status; ?>
							</div>
								<div class="well" style="">
									<h4>Server ID : 
									<span class="label" style="background-color:<?php echo $bg; ?>; padding-left:40px; padding-right:40px;  padding-top:6px; padding-bottom:6px; 
	                                font-size:24px">
									<?php echo $serverIdDisplay; ?>
									</span></h4>
								</div>
							<div>
						   <?php if($server_status == "AKTIF"){ ?>
							 <a href="?modul=sinkron"><button type="button"  class="btn btn-success" aria-hidden="true">Sinkronisasi</button></a>
							<?php } else { ?>
							<button type="button"  class="btn btn-default" aria-hidden="true" disabled="disabled">Sinkronisasi</button>
							<?php } ?>
							
							</div>
		
		</div>
		<?php 
		}

}else{

include "../../config/server.php";
$host_name = gethostbyaddr($_SERVER['REMOTE_ADDR']); //untuk mendeteksi computer name
?>
<?php
$pc_client = $host_name;
$status_server = 0;
$status_internet = "Jaringan server ke Internet : Terhubung";
$serverTitle = $isLocalAccess ? "SERVER LOKAL" : "SERVER PUSAT";
$serverConnectText = $isLocalAccess ? "CBTSync Terhubung ke Server Lokal" : "CBTSync Terhubung ke Server Pusat";
?>
  <div style="width:55%">           
  <div class="row">
                <div class="col-lg-6">
                        <h4 style="color:#33b68f; font-size:35px"><p><b><?php echo $serverTitle; ?></b> <span style="color:#999; font-size:14px"><?php //echo $status_internet; ?></span></p>	
                        </h4>
                </div>
            </div>
            
            					<div class="alert alert-success" style="background-color:15c0d7">
						<?php echo $serverConnectText; ?>
						</div>
 <div class="well" style="">
                        <h4>Server ID : 
							<span class="label" style="background-color:#33b68f; padding-left:40px; padding-right:40px;  padding-top:6px; padding-bottom:6px; 
	                        font-size:24px">
	                        <?php echo $serverIdDisplay; ?>
	                        </span></h4>
 </div>
 </div>
            
<?php

}
?>


