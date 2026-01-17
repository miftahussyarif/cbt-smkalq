<?php
require_once __DIR__ . "/../../config/server.php";
?>
<?php
$txt_ujian = isset($_REQUEST['txt_ujian']) ? $_REQUEST['txt_ujian'] : '';
if ($txt_ujian !== '') {
    db_query(
        $db,
        "UPDATE cbt_ujian SET XStatusUjian = '9' WHERE Urut = :urut",
        array(':urut' => $txt_ujian)
    );
}
?>

