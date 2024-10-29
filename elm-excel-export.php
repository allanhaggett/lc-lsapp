<?php
opcache_reset();
/*
 * -----------------------------
 * LSApp Excel Export
 * -----------------------------
 *
 */
 
require('inc/lsapp.php');
require_once dirname(__FILE__) . '/phpexcel/PHPExcel.php';
$fulldate = date('Y-m-d');

$desc = "LSApp Export";
$modifiedby = LOGGED_IN_IDIR;
$phpxl = new PHPExcel();
$phpxl->getProperties()->setCreator("Allan Haggett")
				 ->setLastModifiedBy($modifiedby)
				 ->setTitle($desc)
				 ->setSubject($desc)
				 ->setDescription($desc);

PHPExcel_Shared_Font::setAutoSizeMethod(PHPExcel_Shared_Font::AUTOSIZE_METHOD_EXACT);

$phpxl->getActiveSheet()->setAutoFilter('A2:T2');
// Grab the sheet for everything else
$grid = $phpxl->getSheet(0);

$headtitle = 'BC PSA Learning System (ELM) course offerings as of ' . date('F d Y H:i');
$grid->mergeCells('A1:I1');
$grid->setCellValue('A1', $headtitle);
$grid->getStyle('A1')->getFont()->setSize(16);

// "Course Name",Class,"Start Date",Type,Facility,"Class Status","Min Enroll",7-"Max Enroll",
// Enrolled,"Reserved Seats","Pending Approval",Waitlisted,Dropped,13 - Denied,
// Completed,"Not Completed","In Progress",Planned,18-Waived
// Set up the datagrid headers
$grid->getColumnDimension('A')->setWidth(60);
$grid->getColumnDimension('B')->setWidth(15);
$grid->getColumnDimension('C')->setWidth(20);
$grid->getColumnDimension('D')->setWidth(20);
$grid->getColumnDimension('E')->setWidth(30);
$grid->getColumnDimension('F')->setWidth(10);
$grid->getColumnDimension('G')->setWidth(10);
$grid->getColumnDimension('H')->setWidth(10);
$grid->getColumnDimension('I')->setWidth(10);
$grid->getColumnDimension('J')->setWidth(10);
$grid->getColumnDimension('K')->setWidth(10);
$grid->getColumnDimension('L')->setWidth(10);
$grid->getColumnDimension('M')->setWidth(10);
$grid->getColumnDimension('N')->setWidth(10);
$grid->getColumnDimension('O')->setWidth(10);
$grid->getColumnDimension('P')->setWidth(10);
$grid->getColumnDimension('Q')->setWidth(10);
$grid->getColumnDimension('R')->setWidth(10);
$grid->getColumnDimension('S')->setWidth(10);
$grid->getColumnDimension('T')->setWidth(10);

$grid->setCellValue('A2', 'Course');
$grid->setCellValue('B2', 'Item Code');
$grid->setCellValue('C2', 'Start Date');
$grid->setCellValue('D2', 'Type');
$grid->setCellValue('E2', 'Facility');
$grid->setCellValue('F2', 'City');
$grid->setCellValue('G2', 'Status');
$grid->setCellValue('H2', 'Min Enroll');
$grid->setCellValue('I2', 'Max Enroll');
$grid->setCellValue('J2', 'Enrolled');
$grid->setCellValue('K2', 'Reserved');
$grid->setCellValue('L2', 'Pending');
$grid->setCellValue('M2', 'Waitlist');
$grid->setCellValue('N2', 'Dropped');
$grid->setCellValue('O2', 'Denied');
$grid->setCellValue('P2', 'Completed');
$grid->setCellValue('Q2', 'Not Completed');
$grid->setCellValue('R2', 'In Progress');
$grid->setCellValue('S2', 'Planned');
$grid->setCellValue('T2', 'Waived');

$header = 'A2:T2';
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
$grid->getStyle('C3:C4000')->applyFromArray($style);
$grid->getStyle('I3:I4000')->applyFromArray($style);
$grid->getStyle('J3:J4000')->applyFromArray($style);
$grid->getStyle('K3:K4000')->applyFromArray($style);
$grid->getStyle('L3:L4000')->applyFromArray($style);
$grid->getStyle('M3:M4000')->applyFromArray($style);
$grid->getStyle('N3:N4000')->applyFromArray($style);
$grid->getStyle('O3:O4000')->applyFromArray($style);
$grid->getStyle('P3:P4000')->applyFromArray($style);
$grid->getStyle('Q3:Q4000')->applyFromArray($style);
$grid->getStyle('R3:R4000')->applyFromArray($style);
$grid->getStyle('S3:S4000')->applyFromArray($style);
$grid->getStyle('T3:T4000')->applyFromArray($style);

// Get the full list from ELM (data/elm.csv is uploaded and 
// processed during the LSApp sync process
$classes = getELMClasses();


// "Course Name",Class,"Start Date",Type,Facility,"Class Status","Min Enroll",7-"Max Enroll",
// Enrolled,"Reserved Seats","Pending Approval",Waitlisted,Dropped,13 - Denied,
// Completed,"Not Completed","In Progress",Planned,18-Waived

// "Course Name",Class,"Start Date",Type,Facility,"Class Status","Min Enroll","Max Enroll",
// Enrolled,"Reserved Seats","Pending Approval",Waitlisted,Dropped,Denied,
// Completed,"Not Completed","In Progress",Planned,Waived,
// "Enroll Date","Drop Date","Reminder Date","Last Waitlist Date"

$elmclasses = array();
foreach($classes as $c) {
	//$c[2] = elmMakeSane($c[2]);
	// pop off the last 4 fields as folks don't need to see the dates; they're included 
	// for the audit tool
	// array_pop($c);
	// array_pop($c);
	// array_pop($c);
	// array_pop($c);
	$line = array($c[0],$c[1],$c[2],$c[3],$c[4],$c[23],$c[5],$c[6],$c[7],$c[8],$c[9],$c[10],$c[11],$c[12],$c[13],$c[14],$c[15],$c[16],$c[17],$c[18]);
	array_push($elmclasses,$line);
}

$grid->fromArray($elmclasses, ' ', 'A3');

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
$path = "data/backups/";
$fname = "ELM-upcoming-classes-export-asof-".$fulldate.".xlsx";


$objWriter->save($path.$fname);
$next = "Location: " . $path . $fname;
header($next);
//echo '<a href="exports/'.$fname.'">Download</a>';
