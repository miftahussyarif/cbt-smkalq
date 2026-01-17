<?php
	if(!isset($_COOKIE['beeuser'])){
	header("Location: login.php");}
?>
<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */
require_once __DIR__ . "/../../config/server.php";
/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once 'PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");
							 
function cellColor($cells,$color){
    global $objPHPExcel;

    $objPHPExcel->getActiveSheet()->getStyle($cells)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => $color
        )
    ));
}


$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A1:N1');
$objPHPExcel->getActiveSheet()->getStyle("A1:N1")->getFont()->setSize(18);
   $style = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        )
    );

    $objPHPExcel->getActiveSheet()->getStyle("A1:N1")->applyFromArray($style);


cellColor('A3:N3', 'e7e7e7');
//cellColor('A30:Z30', 'F28A8C');							 
				 
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setAutoSize(true);
// Add some data
$objPHPExcel->setActiveSheetIndex(0)

			->setCellValue('A1', 'HASIL UJIAN CBT')
			->setCellValue('A3', 'No.')
			->setCellValue('B3', 'Nomer Ujian')
			->setCellValue('C3', 'Nama Peserta')
			->setCellValue('D3', 'Kelas')
			->setCellValue('E3', 'Jurusan')	
			->setCellValue('F3', 'Sesi Ujian')						
			->setCellValue('G3', 'Mata Pelajaran')
			->setCellValue('H3', 'Menjawab')
			->setCellValue('I3', 'Benar')
			->setCellValue('J3', 'Jawaban Esai')			
			->setCellValue('K3', 'Nilai Pilihan Ganda')
			->setCellValue('L3', 'Nilai Soal Esai')		
			->setCellValue('M3', 'Total Nilai')
			->setCellValue('N3', 'TOKEN');			
						
$soal = isset($_REQUEST['soal']) ? $_REQUEST['soal'] : '';
$uj = db_fetch_one(
    db_query(
        $db,
        "SELECT * FROM cbt_ujian WHERE XKodeSoal = :soal LIMIT 1",
        array(':soal' => $soal)
    )
);
$txt_kelas = $uj ? $uj['XKodeKelas'] : '';
$txt_jurusan = $uj ? $uj['XKodeJurusan'] : '';
$var_mapel = $uj ? $uj['XKodeMapel'] : '';
$var_soal = $uj ? $uj['XKodeSoal'] : '';
$var_jumsoal = $uj ? $uj['XJumSoal'] : 0;
$var_token = $uj ? $uj['XTokenUjian'] : '';

if ($txt_kelas == 'ALL' && $txt_jurusan == 'ALL') {
    $hasil = db_query($db, "SELECT * FROM cbt_siswa", array());
} elseif ($txt_kelas == 'ALL' && $txt_jurusan !== 'ALL') {
    $hasil = db_query($db, "SELECT * FROM cbt_siswa WHERE XKodeJurusan = :jurusan", array(':jurusan' => $txt_jurusan));
} elseif ($txt_kelas !== 'ALL' && $txt_jurusan == 'ALL') {
    $hasil = db_query($db, "SELECT * FROM cbt_siswa WHERE XKodeKelas = :kelas", array(':kelas' => $txt_kelas));
} else {
    $hasil = db_query($db, "SELECT * FROM cbt_siswa WHERE XKodeKelas = :kelas", array(':kelas' => $txt_kelas));
}

$p1 = db_fetch_one(
    db_query(
        $db,
        "SELECT p.*, m.XNamaMapel FROM cbt_paketsoal p LEFT JOIN cbt_mapel m ON m.XKodeMapel = p.XKodeMapel WHERE p.XKodeSoal = :soal LIMIT 1",
        array(':soal' => $var_soal)
    )
);
$per_pil = $p1 ? $p1['XPersenPil'] : 0;
$per_esai = $p1 ? $p1['XPersenEsai'] : 0;
$var_pil = $p1 ? $p1['XPilGanda'] : 0;
$var_esai = $p1 ? $p1['XEsai'] : 0;
$namamapel = $p1 ? $p1['XNamaMapel'] : '';


$baris = 4;
$no = 1;	
while($p = $hasil->fetch()){

    $var_siswa = "{$p['XNomerUjian']}";
	$var_nama = "{$p['XNamaSiswa']}";
	$var_sesi = "{$p['XSesi']}";
    $var_kelas = "{$p['XKodeKelas']}";
	$var_jurusan = "{$p['XKodeJurusan']}";
	$grup = "{$p['XKodeKelas']} - {$p['XKodeJurusan']}";
	
$var_siswa = $p['XNomerUjian'];
$var_sesi = $p['XSesi'];

//ambil nilai esai masing2 siswa
$nilai_esai = (float) db_fetch_value(
    db_query(
        $db,
        "SELECT SUM(XNilaiEsai) FROM cbt_jawaban WHERE XKodeSoal = :soal AND XUserJawab = :siswa AND XTokenUjian = :token",
        array(':soal' => $var_soal, ':siswa' => $var_siswa, ':token' => $var_token)
    )
);


$sqldijawab = (int) db_fetch_value(
    db_query(
        $db,
        "SELECT COUNT(*) FROM `cbt_jawaban` WHERE XKodeSoal = :soal AND XUserJawab = :siswa AND XJawaban != ''",
        array(':soal' => $var_soal, ':siswa' => $var_siswa)
    )
);
	$jumbenar = (int) db_fetch_value(
        db_query(
            $db,
            "SELECT COUNT(XNilai) FROM `cbt_jawaban` WHERE XKodeSoal = :soal AND XUserJawab = :siswa AND XNilai = '1'",
            array(':soal' => $var_soal, ':siswa' => $var_siswa)
        )
    );
	$tokenujian = (string) db_fetch_value(
        db_query(
            $db,
            "SELECT MIN(XTokenUjian) FROM `cbt_jawaban` WHERE XKodeSoal = :soal AND XUserJawab = :siswa AND XNilai = '1'",
            array(':soal' => $var_soal, ':siswa' => $var_siswa)
        )
    );
	$nilai_pil = $var_pil > 0 ? ($jumbenar / $var_pil) * 100 : 0;
	$total_pil = $nilai_pil*($per_pil/100);	
	$total_esai = $nilai_esai*($per_esai/100);	
	$total_nilai = $total_pil+$total_esai;	

	
// Miscellaneous glyphs, UTF-8
$objPHPExcel->setActiveSheetIndex(0)
            //->setCellValue('A4', 'Miscellaneous glyphs')
            //->setCellValue('A5', 'sfdsdf');
			->setCellValue("A$baris", $no)
			->setCellValue("B$baris", "$var_siswa")
			->setCellValue("C$baris", "$var_nama")
			->setCellValue("D$baris", "$var_kelas")
			->setCellValue("E$baris", "$var_jurusan")
			->setCellValue("F$baris", "$var_sesi")
			->setCellValue("G$baris", "$namamapel")
			->setCellValue("H$baris", "$sqldijawab")
			->setCellValue("I$baris", "$jumbenar")
			->setCellValue("J$baris", "$nilai_esai")			
			->setCellValue("K$baris", "$total_pil")			
			->setCellValue("L$baris", "$total_esai")			
			->setCellValue("M$baris", "$total_nilai")
			->setCellValue("N$baris", "$tokenujian");			
			
			$no = $no +1;			
					
	$baris = $baris + 1;
}
 
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle($namamapel);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client's web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="HasilUjian.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>
