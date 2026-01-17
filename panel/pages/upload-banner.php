<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
?>
<?php
require_once __DIR__ . "/../../config/server.php";
$uploaddir = '../../images/'; 

$namafile1 = isset($_FILES['uploadfile1']['name']) ? basename($_FILES['uploadfile1']['name']) : '';
$file1 = $uploaddir . $namafile1; 
if ($namafile1 !== '' && move_uploaded_file($_FILES['uploadfile1']['tmp_name'], $file1)) {
    try {
        db_query(
            $db,
            "UPDATE cbt_admin SET XBanner = :banner",
            array(':banner' => $namafile1)
        );
        echo "success";
    } catch (Exception $e) {
        echo "error";
    }
} else {
	echo "error";
}

?>
