<?php
include "../../config/server.php";

/*
 * Restore MySQL dump using PHP
 * (c) 2006 Daniel15
 * Last Update: 9th December 2006
 * Version: 0.2
 * Edited: Cleaned up the code a bit. 
 *
 * Please feel free to use any part of this, but please give me some credit :-)
 */

// Name of the file
$filex = isset($_REQUEST['anu']) ? $_REQUEST['anu'] : '';
$filename = '/opt/lampp/backup/' . $filex;
if ($filex === '' || !is_file($filename)) {
	echo "<div class=\"alert alert-danger\">File backup tidak ditemukan.</div>";
	return;
}

// Legacy restore snippet removed during PDO migration.

// Temporary variable, used to store current query
$templine = '';
// Read in entire file
$lines = file($filename);
// Loop through each line
foreach ($lines as $line) {
	// Skip it if it's a comment
	if (substr($line, 0, 2) == '--' || $line == '')
		continue;

	// Add this line to the current segment
	$templine .= $line;
	// If it has a semicolon at the end, it's the end of the query
	if (substr(trim($line), -1, 1) == ';') {
		// Perform the query
		try {
			$db->exec($templine);
		} catch (PDOException $e) {
			print ('Error performing query \'<strong>' . $templine . '\': ' . $e->getMessage() . '<br /><br />');
		}
		// Reset temp variable to empty
		$templine = '';
	}
}

?>
<br />
<div class="alert alert-success alert-dismissable" id="ndelik">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	Data telah direstore.
</div>
