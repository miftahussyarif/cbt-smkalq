<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>AJAX File Upload - Web Developer Plus Demos</title>
<script type="text/javascript" src="js/jquery-1.3.2.js" ></script>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>
<link rel="stylesheet" type="text/css" href="./styles.css" />
<script type="text/javascript" >
	$(function(){
		var btnUpload=$('#upload');
		var status=$('#status');
		new AjaxUpload(btnUpload, {
			action: 'upload-file.php',
			name: 'uploadfile',
			onSubmit: function(file, ext){
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status.text('Only JPG, PNG or GIF files are allowed');
					return false;
				}
				status.text('Uploading...');
			},
			onComplete: function(file, response){
				//On completion clear the status
				status.text('');
				//Add uploaded file to list
				
				if(response==="success"){
				$('#upload').html('<img src="../../images/'+file+'"  width="200" alt="" />').addClass('success');
//					$('<li></li>').appendTo('#files').html('<img src="./uploads/'+file+'" alt="" /><br />'+file).addClass('success');
				} else{
					$('<li></li>').appendTo('#files').text(file).addClass('error');
				}
			}
		});
		
	});
</script>
<script type="text/javascript" >
	$(function(){
		var btnUpload1=$('#upload1');
		var status1=$('#status1');
		new AjaxUpload(btnUpload1, {
			action: 'upload-banner.php',
			name: 'uploadfile1',
			onSubmit: function(file1, ext){
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status1.text('Only JPG, PNG or GIF files are allowed');
					return false;
				}
				status1.text('Uploading...');
			},
			onComplete: function(file1, response){
				//On completion clear the status
				status1.text('');
				//Add uploaded file to list
				
				if(response==="success"){
				$('#upload1').html('<img src="../../images/'+file1+'"  width="200" alt="" />').addClass('success');
//					$('<li></li>').appendTo('#files').html('<img src="./uploads/'+file+'" alt="" /><br />'+file).addClass('success');
				} else{
					$('<li></li>').appendTo('#files1').text(file1).addClass('error');
				}
			}
		});
		
	});
</script>
<script type="text/javascript" >
	$(function(){
		var btnUpload2=$('#upload2');
		var status2=$('#status2');
		new AjaxUpload(btnUpload2, {
			action: 'upload-admin.php',
			name: 'uploadfile2',
			onSubmit: function(file2, ext){
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status2.text('Only JPG, PNG or GIF files are allowed');
					return false;
				}
				status2.text('Uploading...');
			},
			onComplete: function(file2, response){
				//On completion clear the status
				status2.text('');
				//Add uploaded file to list
				
				if(response==="success"){
				$('#upload2').html('<img src="../../images/'+file2+'"  width="100" alt="" />').addClass('success');
//					$('<li></li>').appendTo('#files').html('<img src="./uploads/'+file+'" alt="" /><br />'+file).addClass('success');
				} else{
					$('<li></li>').appendTo('#files2').text(file2).addClass('error');
				}
			}
		});
		
	});
</script>
<style>
.left {
    float: left;
    width: 25%;
}
.right {
    float: right;
    width: 73%;
}
.group:after {
    content:"";
    display: table;
    clear: both;
}
img {
    max-width: 100%;
    height: auto;
}
.field-error {
    color: #cc0000;
    font-size: 12px;
    margin: 4px 0 0 12px;
}
.input-error {
    border: 1px solid #cc0000;
}
#info.text-success {
    color: #2d8a3f;
}
#info.text-danger {
    color: #cc0000;
}
@media screen and (max-width: 480px) {
    .left, 
    .right {
        float: none;
        width: auto;
		margin-top:10px;		
    }
	
}
</style>
</head>
<body>
<script type="text/javascript" src="../js/jquery.js"></script>
<script type="text/javascript">
$(document).ready(function(){
 	var loading = $("#loading");
	var tampilkan = $("#tampilkan");

	loading.hide()
//apabila terjadi event onchange terhadap object <select id=propinsi>
 function setError(id, message) {
     $("#err_" + id).text(message);
     $("#" + id).addClass("input-error");
 }

 function clearError(id) {
     $("#err_" + id).text("");
     $("#" + id).removeClass("input-error");
 }

 function clearAllErrors() {
     $(".field-error").text("");
     $(".school-field").removeClass("input-error");
 }

 function isValidEmail(value) {
     var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
     return emailPattern.test(value);
 }

 function isValidPhone(value) {
     var phonePattern = /^[0-9+()\s.-]+$/;
     return phonePattern.test(value);
 }

 function isValidHexColor(value) {
     var raw = value.replace("#", "");
     return /^[0-9a-fA-F]{6}$/.test(raw);
 }

 function validateForm() {
     var ok = true;
     var txt_kode = $("#txt_kode").val().trim();
     var txt_nama = $("#namaskul").val().trim();
     var txt_ting = $("#tingkatskul").val().trim();
     var txt_alam = $("#alamatskul").val().trim();
     var txt_telp = $("#telpskul").val().trim();
     var txt_facs = $("#faxskul").val().trim();
     var txt_emai = $("#emailskul").val().trim();
     var txt_webs = $("#webskul").val().trim();
     var txt_ip = $("#kepsek").val().trim();
     var txt_adm = $("#txt_adm").val().trim();
     var txt_nip1 = $("#nipkepsek").val().trim();
     var txt_nip2 = $("#nipadmin").val().trim();
     var txt_col = $("#txt_col").val().trim();

     if (txt_kode === "") {
         setError("txt_kode", "Kode sekolah wajib diisi.");
         ok = false;
     }
     if (txt_nama === "") {
         setError("namaskul", "Nama sekolah wajib diisi.");
         ok = false;
     }
     if (txt_ting === "") {
         setError("tingkatskul", "Level sekolah wajib dipilih.");
         ok = false;
     }
     if (txt_alam === "") {
         setError("alamatskul", "Alamat sekolah wajib diisi.");
         ok = false;
     }
     if (txt_telp === "") {
         setError("telpskul", "No. Telp wajib diisi.");
         ok = false;
     } else if (!isValidPhone(txt_telp)) {
         setError("telpskul", "No. Telp hanya boleh angka/simbol telepon.");
         ok = false;
     }
     if (txt_facs !== "" && !isValidPhone(txt_facs)) {
         setError("faxskul", "No. Fax hanya boleh angka/simbol telepon.");
         ok = false;
     }
     if (txt_emai === "") {
         setError("emailskul", "Email sekolah wajib diisi.");
         ok = false;
     } else if (!isValidEmail(txt_emai)) {
         setError("emailskul", "Format email tidak valid.");
         ok = false;
     }
     if (txt_webs !== "" && txt_webs.indexOf(".") === -1) {
         setError("webskul", "Website tidak valid.");
         ok = false;
     }
     if (txt_ip === "") {
         setError("kepsek", "Nama kepala sekolah wajib diisi.");
         ok = false;
     }
     if (txt_nip1 === "") {
         setError("nipkepsek", "NIP KepSek wajib diisi.");
         ok = false;
     } else if (!/^[0-9]+$/.test(txt_nip1)) {
         setError("nipkepsek", "NIP KepSek harus berupa angka.");
         ok = false;
     }
     if (txt_adm === "") {
         setError("txt_adm", "Nama admin wajib diisi.");
         ok = false;
     }
     if (txt_nip2 === "") {
         setError("nipadmin", "NIP Admin wajib diisi.");
         ok = false;
     } else if (!/^[0-9]+$/.test(txt_nip2)) {
         setError("nipadmin", "NIP Admin harus berupa angka.");
         ok = false;
     }
     if (txt_col === "") {
         setError("txt_col", "Warna header wajib diisi.");
         ok = false;
     } else if (!isValidHexColor(txt_col)) {
         setError("txt_col", "Format warna harus #RRGGBB.");
         ok = false;
     }

     return ok;
 }

 $(".school-field").on("input change", function () {
     var fieldId = $(this).attr("id");
     if (fieldId) {
         clearError(fieldId);
     }
 });

 $("#simpan").click(function(e){
 e.preventDefault();
 clearAllErrors();
 $("#info").stop(true, true).show().removeClass("text-success text-danger").text("");

 if (!validateForm()) {
     return;
 }

 var txt_nama = $("#namaskul").val().trim();
 var txt_ting = $("#tingkatskul").val().trim();
 var txt_alam = $("#alamatskul").val().trim();
 var txt_telp = $("#telpskul").val().trim();
 var txt_facs = $("#faxskul").val().trim();
 var txt_emai = $("#emailskul").val().trim();
 var txt_webs = $("#webskul").val().trim();  
 var txt_ip = $("#kepsek").val().trim();
 var txt_adm = $("#txt_adm").val().trim();  
 var txt_nip1 = $("#nipkepsek").val().trim();   
 var txt_nip2 = $("#nipadmin").val().trim();  
 var txt_col = $("#txt_col").val().trim();  
 var txt_kode = $("#txt_kode").val().trim();

 loading.fadeIn();
 $.ajax({
     type:"POST",
     url:"ubahdata.php",    
     dataType: "json",
     data: {
         aksi: "simpan",
         txt_nama: txt_nama,
         txt_ting: txt_ting,
         txt_alam: txt_alam,
         txt_telp: txt_telp,
         txt_facs: txt_facs,
         txt_emai: txt_emai,
         txt_webs: txt_webs,
         txt_ip: txt_ip,
         txt_adm: txt_adm,
         txt_col: txt_col,
         txt_kode: txt_kode,
         txt_nip1: txt_nip1,
         txt_nip2: txt_nip2
     },
	 success: function(data){
	 	loading.fadeOut();
        if (!data || !data.ok) {
            if (data && data.errors) {
                $.each(data.errors, function (key, message) {
                    if (key === "__all__") {
                        $("#info").addClass("text-danger").text(message);
                    } else {
                        setError(key, message);
                    }
                });
            } else {
                $("#info").addClass("text-danger").text("Gagal menyimpan data.");
            }
            return;
        }
        $("#info").addClass("text-success").text(data.message || "Ubah data berhasil.");
		$("#info").fadeOut(2000);
	 }
	 });
	 });

});
 
</script>
<div id="mainbody" >
<?php
include "../../config/server.php";
$sql = mysql_query("select * from cbt_admin");
$xadm = mysql_fetch_array($sql);
$skulpic= $xadm['XLogo'];
$skulban= $xadm['XBanner'];
$skulnam= $xadm['XSekolah']; 
$skultin= $xadm['XTingkat']; 
$skulala= $xadm['XAlamat'];
$skultel= $xadm['XTelp']; 
$skulfax= $xadm['XFax'];
$skulema= $xadm['XEmail']; 
$skulweb= $xadm['XWeb'];
$skulkep= $xadm['XKepSek']; 
$skulweb= $xadm['XWeb'];
$skuladm= $xadm['XAdmin']; 
$admpic= $xadm['XPicAdmin']; 
$skulkode= $xadm['XKodeSekolah']; 
$skulnip1= $xadm['XNIPKepsek']; 
$skulnip2= $xadm['XNIPAdmin']; 
$colhead= $xadm['XWarna'];  //#ffca01
?><br />
<span>

<div class="group">
    <div class="left">


				<div class="panel panel-info" style="padding-top:20">
                        <div class="panel-heading" style=" text-align:center">
                            Update Logo Sekolah : 
                        </div>
                        <div class="panel-body">
                          
                        <!-- Upload Button, use any id you wish-->
                        <div id="upload" style="text-align:center"><img src="../../images/<?php echo "$skulpic"; ?>" width="200"/></div><span id="status" ></span>
                        <ul id="files"></ul>
           				</div>
               			<div class="panel-footer" style=" text-align:center">Klik Picture untuk Ganti Logo Sekolah<br/>Ekstensi File harus ; Jpg
                        </div>
               
                </div>
                
                <div class="panel panel-info" style="padding-top:20">
                        <div class="panel-heading" style=" text-align:center">
                            Update Banner Sekolah : 
                        </div>
                        <div class="panel-body">
                          
                        <!-- Upload Button, use any id you wish-->
                        <div id="upload1" style="text-align:center"><img src="../../images/<?php echo "$skulban"; ?>" width="150"/></div><span id="status1" ></span>
                        <ul id="files1"></ul>
           				</div>
               			<div class="panel-footer" style=" text-align:center">Klik Picture untuk Ganti Banner
                        <br/>Ukuran Banner 470x101 pixel</div>
               
                </div>
				<div class="panel panel-info" style="padding-top:20">
                        <div class="panel-heading" style=" text-align:center">
                            Upload Foto Admin: 
                        </div>
                        <div class="panel-body">
                          
                        <!-- Upload Button, use any id you wish-->
                        <div id="upload2" style="text-align:center"><img src="../../images/<?php echo "$admpic"; ?>" width="100"/></div><span id="status2" ></span>
                        <ul id="files2"></ul>
           				</div>
               			<div class="panel-footer" style=" text-align:center">Klik Picture untuk Ganti Foto
                        </div>
               
                </div>


                

    </div>
    <div class="right">
    
    
    
    				<div class="panel panel-primary">
                        <div class="panel-heading">
                            Data Sekolah
                        </div>
                        <div class="panel-body">
                            <table width="100%">
                            <tr height="42px"><td width="40%">Kode Sekolah&nbsp;</td><td>: <input type="text" id="txt_kode" class="school-field" value="<?php echo "$skulkode"; ?>"/><div class="field-error" id="err_txt_kode"></div></td></tr>
                            <tr height="42px"><td width="40%">Nama Sekolah&nbsp;</td><td>: <input type="text" id="namaskul" class="school-field" value="<?php echo "$skulnam"; ?>"/><div class="field-error" id="err_namaskul"></div></td></tr>
                            <tr height="42px"><td>Level Sekolah&nbsp;</td><td>: 
                            <select id="tingkatskul" class="school-field">
                            <?php if($skultin=="SMA"){echo "Selected";}?>                            
                            <option value="PG">TK</option>
                            <option value="TK">TK</option>                            
                            <option value="SD">SD</option>
                            <option value="MI">MI</option>                            
                            <option value="SMP" <?php if($skultin=="SMP"){echo "Selected";}?>>SMP</option>
                            <option value="MTs" <?php if($skultin=="MTs"){echo "Selected";}?>>MTs</option>                            
                            <option value="SMU" <?php if($skultin=="SMU"){echo "Selected";}?>>SMU</option>
                            <option value="MA" <?php if($skultin=="MA") {echo "Selected";}?>>MA</option>                            
                            <option value="SMK" <?php if($skultin=="SMK"){echo "Selected";}?>>SMK</option>  
                            
                            </select>
                            <div class="field-error" id="err_tingkatskul"></div>
                            </td></tr>
                            <tr height="42px"><td>Alamat Sekolah&nbsp;</td><td>: <input type="text" id="alamatskul" class="school-field"  value="<?php echo "$skulala"; ?>"/><div class="field-error" id="err_alamatskul"></div></td></tr>
                            <tr height="42px"><td>No. Telp&nbsp;</td><td>: <input type="text" id="telpskul" class="school-field"  value="<?php echo "$skultel"; ?>"/><div class="field-error" id="err_telpskul"></div></td></tr>
                            <tr height="42px"><td>No. Fax.&nbsp;</td><td>: <input type="text" id="faxskul" class="school-field"  value="<?php echo "$skulfax"; ?>"/><div class="field-error" id="err_faxskul"></div></td></tr>
                            <tr height="42px"><td>Email Sekolah &nbsp;</td><td>: <input type="text" id="emailskul" class="school-field"  value="<?php echo "$skulema"; ?>"/><div class="field-error" id="err_emailskul"></div></td></tr>
                            <tr height="42px"><td>Website Sekolah &nbsp;</td><td>: <input type="text" id="webskul" class="school-field" value="<?php echo "$skulweb"; ?>" /><div class="field-error" id="err_webskul"></div></td></tr>
                            <tr height="42px"><td>Kepala Sekolah&nbsp;</td><td>: <input type="text" id="kepsek" class="school-field" value="<?php echo "$skulkep"; ?>" /><div class="field-error" id="err_kepsek"></div></td></tr>
                            <tr height="42px"><td>NIP KepSek&nbsp;</td><td>: <input type="text" id="nipkepsek" class="school-field" value="<?php echo "$skulnip1"; ?>" /><div class="field-error" id="err_nipkepsek"></div></td></tr>
                            <tr height="42px"><td>CBT Administrator &nbsp;</td><td>: <input type="text" id="txt_adm" class="school-field" value="<?php echo "$skuladm"; ?>" /><div class="field-error" id="err_txt_adm"></div></td></tr>
                            <tr height="42px"><td>NIP Admin&nbsp;</td><td>: <input type="text" id="nipadmin" class="school-field" value="<?php echo "$skulnip2"; ?>" /><div class="field-error" id="err_nipadmin"></div></td></tr>
                            <tr height="42px"><td>Warna Header &nbsp;</td><td>: <input id="txt_col" class="school-field" type="text" value="<?php echo $colhead; ?>" />
<script>
    $(function() {
        $('#cp1').colorpicker();
    });
</script>
<div class="field-error" id="err_txt_col"></div></td></tr>
                            </table>
                        </div>
                        <div class="panel-footer">
                            <input type="submit"  class="btn btn-info btn-lg btn-small" id="simpan" name="simpan">
                            <div id="info"></div><div id="loading"><img src="images/loading.gif" height="10"></div>
                        </div>
                    </div>
    
    
    
	</div>
</div>    

</div>                    
</body>
