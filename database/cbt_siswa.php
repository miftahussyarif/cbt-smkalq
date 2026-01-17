<?php
require_once __DIR__ . '/../config/db.php';
function &backup_tables($host, $user, $pass, $name, $tables = '*')
{
  $data = "\n/*---------------------------------------------------------------" .
    "\n  SQL DB BACKUP " . date("d.m.Y H:i") . " " .
    "\n  HOST: {$host}" .
    "\n  DATABASE: {$name}" .
    "\n  TABLES: {$tables}" .
    "\n  ---------------------------------------------------------------*/\n";
  $db = db_connect($host, $user, $pass, $name, 'utf8');
  $db->exec("SET NAMES `utf8` COLLATE `utf8_general_ci`"); // Unicode

  if ($tables == '*') { //get all of the tables
    $tables = array();
    $result = $db->query("SHOW TABLES");
    while ($row = $result->fetch(PDO::FETCH_NUM)) {
      $tables[] = $row[0];
    }
  } else {
    $tables = is_array($tables) ? $tables : explode(',', $tables);
  }

  foreach ($tables as $table) {
    $data .= "\n/*---------------------------------------------------------------" .
      "\n  TABLE: `{$table}`" .
      "\n  ---------------------------------------------------------------*/\n";
    $data .= "DROP TABLE IF EXISTS `{$table}`;\n";
    $res = $db->query("SHOW CREATE TABLE `{$table}`");
    $row = $res->fetch(PDO::FETCH_NUM);
    if ($row) {
      $data .= $row[1] . ";\n";
    }

    $result = $db->query("SELECT * FROM `{$table}`");
    $vals = array();
    $z = 0;
    while ($items = $result->fetch(PDO::FETCH_NUM)) {
      $vals[$z] = "(";
      for ($j = 0; $j < count($items); $j++) {
        if ($items[$j] !== null) {
          $vals[$z] .= $db->quote($items[$j]);
        } else {
          $vals[$z] .= "NULL";
        }
        if ($j < (count($items) - 1)) {
          $vals[$z] .= ",";
        }
      }
      $vals[$z] .= ")";
      $z++;
    }
    if (!empty($vals)) {
      $data .= "INSERT INTO `{$table}` VALUES ";
      $data .= "  " . implode(";\nINSERT INTO `{$table}` VALUES ", $vals) . ";\n";
    }

    if (isset($_REQUEST['aksi']) && $_REQUEST['aksi'] == '2') {
      $db->exec("TRUNCATE TABLE `$table`");
    }

  }

  $db = null;
  return $data;
}
?>

<?php
// create backup
//////////////////////////////////////

if (!file_exists('/opt/lampp/backup')) {
  mkdir('/opt/lampp/backup', 0777, true);
}

$tabel = "cbt_kelas,cbt_mapel,cbt_siswa";

//$backup_file = 'data/'.time().'-'.$tabel.'.sql';
$backup_file = '/opt/lampp/backup/dbee-siswa_' . time() . '.sql';
// get backup
//$mybackup = backup_tables("localhost:3306","root","","beesmartv3","*");
$mybackup = backup_tables("localhost:3306", "root", "", "beesmartv3", $tabel);

// save to file
$handle = fopen($backup_file, 'w+');
fwrite($handle, $mybackup);
fclose($handle);

//echo "Tabel $tabel telah di Backup";

?>
<br />
<div class="alert alert-success alert-dismissable">
  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
  Tabel <?php echo $tabel; ?> telah di Backup<br />
  Lokasi file Backup, Silahkan Lihat folder /opt/lampp/backup/
</div>
