<?php 
require('inc/lsapp.php');
header('Content-Type: text/plain; charset=utf-8');
$action = (isset($_POST['action'])) ? $_POST['action'] : 0;
if($action == "upload") {

	try {
	   
		// Undefined | Multiple Files | $_FILES Corruption Attack
		// If this request falls under any of them, treat it invalid.
		if (
			!isset($_FILES['elmfile']['error']) ||
			is_array($_FILES['elmfile']['error'])
		) {
			throw new RuntimeException('Invalid parameters.');
		}
		// Check $_FILES['elmfile']['error'] value.
		switch ($_FILES['elmfile']['error']) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				throw new RuntimeException('No file sent.');
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				throw new RuntimeException('Exceeded filesize limit.');
			default:
				throw new RuntimeException('Unknown errors.');
		}
		// You should also check filesize here.
		if ($_FILES['elmfile']['size'] > 1000000) {
			throw new RuntimeException('ELM File Exceeded filesize limit.');
		}
		if ($_FILES['elmfile']['type'] != 'text/plain' && $_FILES['elmfile']['type'] != 'application/vnd.ms-excel') {
			throw new RuntimeException('Wrong type of file. MUST be a CSV. You tried to upload: ' . $_FILES['elmfile']['type']);
		}
		$rosterfilename = 'rosters/' . $_POST['itemcode'] . '.csv';
		$rostersentfilename = 'rosters/' . $_POST['itemcode'] . '-sent.csv';
		if (!move_uploaded_file($_FILES['elmfile']['tmp_name'],$rosterfilename)) {
			throw new RuntimeException('Failed to move ELM file.');   
		}

	//	if(!file_exists($rostersentfilename)) {
	//		$sentfile = fopen($rostersentfilename, 'w');
	//		fclose($sentfile);
	//	}
		
	// ITEM-CODE.csv	
	// "0- Course","1 - Class Code","2 - First Name","3 - Last Name","4 - EmpID","5 - Job Title","6 - Position","7 - Ministry",
	// "8 - Current Status","9 - Previous Status", "10 - Waitlist Number","11 - Enrolled Date","12 - Approved On","13 - Approved By (learner ID)",
	// "14 - Completion Date","15 - Drop Date","16 - Date Learner Added","17 - Learner Added By",
	// "18- Enrollment Last Modified","19 - Enrollment Last Modified By","20 - Learner Email"
	// ADDING ON TO CLASSID.csv
	// "21 - ", "22 - "

	/*$rosterfile = 'rosters/' . $_POST['itemcode'] . '.csv';
	if(file_exists($rosterfile)):
		$rf = fopen($rosterfile, 'r'); 
		$headers = fgetcsv($rf);
		while ($row = fgetcsv($rf)):
			
		endwhile;
	endif;
	*/	
		$redir = 'Location: /lsapp/roster.php?classid='.$_POST['classid'].'&message=YUP';
		//header($redir);
		echo 'huh';

	} catch (RuntimeException $e) {

		echo $e->getMessage();

	}
}
if($action == 'send') {
	
	$fromform = $_POST;
	
	$itemcode = (isset($_POST['itemcode'])) ? $_POST['itemcode'] : 0;
	
	// creqID,ClassID,Date,NotedBy,Note
	$requestor = LOGGED_IN_IDIR;
	$now = date('Y-m-d H:i:s');
	$noteID = LOGGED_IN_IDIR . '-' . date('Ymd-His');
	
	$fpath = 'rosters/' . $itemcode . '-sent.csv';
	$fp = fopen($fpath, 'a+');
	$sen = array();
	foreach($_POST['sendto'] as $empid) {
		$new = array($empid,$now);
		array_push($sen,$new);
	}
	foreach($sen as $arg) {
		fputcsv($fp, $arg);
	}
	fclose($fp);
	$redir = 'Location: /lsapp/roster.php?classid='.$_POST['classid'];
	header($redir);
	
}
