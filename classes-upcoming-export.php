<?php
/*
 * -----------------------------
 * Audit Excel Export
 * -----------------------------
 * Loop through the audit index, look up the JSON, pull out the values,
 * add them to the array, add them to the excel ... 
 *
 */
require('inc/lsapp.php');
require('phpexcel/PHPExcel.php');
$fulldate = date('Y-m-d');

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

$headtitle = 'Learning Centre course offerings as of ' . date('F d Y');
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

$grid->setCellValue('A2', 'Status');
$grid->setCellValue('B2', 'Item Code');
$grid->setCellValue('C2', 'Start Date');
$grid->setCellValue('D2', 'Course');
$grid->setCellValue('E2', 'Venue');
$grid->setCellValue('F2', 'City');
$grid->setCellValue('G2', 'Address');
$grid->setCellValue('H2', 'Postal');
$grid->setCellValue('I2', 'Enrolled');
$grid->setCellValue('J2', 'Reserved');
$grid->setCellValue('K2', 'Pending');
$grid->setCellValue('L2', 'Waitlist');
$grid->setCellValue('M2', 'Dropped');

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
$c = getClasses();
// Pop the headers off the top
array_shift($c);
// Create a temp array for the array_multisort below
$tmp = array();
// loop through everything and add the start date to
// the temp array
foreach($c as $line) {
	$tmp[] = $line[8];
}
// Sort the whole kit and kaboodle by start date from
// oldest to newest
array_multisort($tmp, SORT_ASC, $c);
//
// Now let's run through the whole thing and process it, removing
// classes with dates older than "today" and any requested classes
//
$count = 0;
$inactive = 0;
$upclasses = array();
$today = date('Y-m-d');
foreach($c as $row) {
	//
	// We only wish to see classes which have an end date greater than today
	//
	if($row[1] == 'Deleted') continue;
	if($row[9] < $today) continue;
	array_push($upclasses,$row);
	$count++;
}
$data = array();
foreach($upclasses as $row): 
	//$sdate = goodDateShort($row[8],$row[9]);
	if($row[4] == 'Dedicated') {
		$code = 'Dedicated';
	} else {
		$code = $row[7];
	}
	$newline = array($row[1],$code,$row[8],$row[6],$row[24],$row[25],$row[26],$row[27],$row[18],$row[19],$row[20],$row[21],$row[22]);
	array_push($data,$newline);
endforeach;

$grid->fromArray($data, ' ', 'A3');

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
$fname = "exports/lsapp-upcoming-classes-export-asof-".$fulldate.".xlsx";
$objWriter->save($fname);
$path = 'Location: ' . $fname;
header($path);