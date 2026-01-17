<?php
require_once __DIR__ . "/../../config/server.php";
$uploaddir = '../../images/'; 
$namafile = isset($_FILES['uploadfile']['name']) ? basename($_FILES['uploadfile']['name']) : '';
$file = $uploaddir . $namafile; 
if ($namafile !== '' && move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) {
    try {
        db_query(
            $db,
            "UPDATE cbt_admin SET XLogo = :logo",
            array(':logo' => $namafile)
        );
        echo "success";
    } catch (Exception $e) {
        // echo "error";
    }
} else {
//	echo "error";
}

?>
