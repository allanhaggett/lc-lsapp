<?php
/*
 * -----------------------------
 * LSApp Excel Export
 * -----------------------------
 *
 */
require('inc/lsapp.php');
require_once dirname(__FILE__) . '/phpexcel/PHPExcel.php';
$fulldate = date('Y-m-d-hi');
$courseid = (isset($_GET['courseid'])) ? $_GET['courseid'] : 0;
$desc = "LSApp Export";
$modifiedby = stripIDIR($_SERVER["REMOTE_USER"]);
$phpxl = new PHPExcel();
$phpxl->getProperties()->setCreator("Allan Haggett")
				 ->setLastModifiedBy($modifiedby)
				 ->setTitle($desc)
				 ->setSubject($desc)
				 ->setDescription($desc);

PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);

$phpxl->getActiveSheet()->setAutoFilter('A2:M2');
// Grab the sheet for everything else
$grid = $phpxl->getSheet(0);

$headtitle = 'Learning Centre venues listing as of ' . date('F d Y');
$grid->mergeCells('A1:I1');
$grid->setCellValue('A1', $headtitle);
$grid->getStyle('A1')->getFont()->setSize(16);

// Set up the datagrid headers
$grid->getColumnDimension('A')->setWidth(10);
$grid->getColumnDimension('B')->setWidth(15);
$grid->getColumnDimension('C')->setWidth(15);
$grid->getColumnDimension('D')->setWidth(40);
$grid->getColumnDimension('E')->setWidth(45);
$grid->getColumnDimension('F')->setWidth(18);
$grid->getColumnDimension('G')->setWidth(35);
// VenueID,VenueName,ContactName,BusinessPhone,Address,City,StateProvince,ZIPPostal,email,Notes,Active,Union,Region


$grid->setCellValue('E2', 'Venue');
$grid->setCellValue('F2', 'City');
$grid->setCellValue('G2', 'Address');
$grid->setCellValue('H2', 'Postal');


$header = 'A2:M2';
$grid->getStyle($header)->getFill()
			->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)
			->getStartColor()
			->setARGB('F1F1F1');

$style = array(
    'font' => array('bold' => true,),
    'alignment' => array(
		'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	)
    );
$grid->getStyle($header)->applyFromArray($style);
$grid->getStyle('A1:I1')->applyFromArray($style);
$grid->getStyle('C3:C400')->applyFromArray($style);
$grid->getStyle('I3:I400')->applyFromArray($style);
$grid->getStyle('J3:J400')->applyFromArray($style);
$grid->getStyle('K3:K400')->applyFromArray($style);
$grid->getStyle('L3:L400')->applyFromArray($style);
$grid->getStyle('M3:M400')->applyFromArray($style);




// Get the full class list
$venues = getVenues();

// Pop the headers off the top
array_shift($venues);
// Create a temp array for the array_multisort below
$tmp = array();
// loop through everything and add the start date to
// the temp array
foreach($venues as $line) {
	$tmp[] = $line[1];
}
// Sort the whole kit and kaboodle by start date from
// oldest to newest
array_multisort($tmp, SORT_ASC, $venues);

$count = 0;
$data = array();
foreach($upclasses as $row): 
	
	if($row[5] == $courseid) {
		$newline = array($row[1],$code,$row[8],$row[6],$row[24],$row[25],$row[26],$row[27],$row[18],$row[19],$row[20],$row[21],$row[22]);
		array_push($data,$newline);
	}
	
endforeach;


$grid->fromArray($data, ' ', 'A3'); 
// Get a records count so we can style the items with a border
//$num = count($data);
//$numrows = 8 + $num;
//$bordergrid = 'A8:G'.$numrows;


$styleArray = array(
      'borders' => array(
          'allborders' => array(
              'style' => PHPExcel_Style_Border::BORDER_THIN
          )
      )
  );
//$grid->getStyle($bordergrid)->applyFromArray($styleArray);
$grid->getStyle('A3:H999')->getAlignment()->setWrapText(true);
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$phpxl->setActiveSheetIndex(0);
$objWriter = PHPExcel_IOFactory::createWriter($phpxl, 'Excel2007');
$path = "exports/";
$fname = "lsapp-venues-export-asof-".$fulldate.".xlsx";


$objWriter->save($path.$fname);
$next = "Location: " . $path . $fname;
header($next);
//echo '<a href="exports/'.$fname.'">Download</a>';
