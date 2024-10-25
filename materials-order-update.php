<?php
require('inc/lsapp.php');
if(isAdmin()):
if($_POST):

$fromform = $_POST;

$orderid = h($fromform['OrderID']);
$filename = h($fromform['FilePath']);
if(is_uploaded_file($_FILES['file']['tmp_name'])) {
	$course = getCourse($fromform['CourseID']);
	$courseshort = $course[3];
	$fname = $courseshort . '-' . date('Y-m-d:His') . '-' . $orderid;
	$filename = createSlug($fname) . '.pdf';
	$full_path = 'printorders/' . $filename;
}

try {

	// Undefined | Multiple Files | $_FILES Corruption Attack
	// If this request falls under any of them, treat it invalid.
	if (
		!isset($_FILES['file']['error']) ||
		is_array($_FILES['file']['error'])
	) {
		throw new RuntimeException('Invalid parameters.');
	}
	// Check $_FILES['file']['error'] value.
	switch ($_FILES['file']['error']) {
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
	if ($_FILES['file']['size'] > 1000000) {
		throw new RuntimeException('ELM File Exceeded filesize limit.');
	}
	if ($_FILES['file']['type'] != 'application/pdf') { //  && $_FILES['file']['type'] != 'application/vnd.ms-excel'
		throw new RuntimeException('Wrong type of file. You tried to upload: ' . $_FILES['file']['type']);
	}
	if (!move_uploaded_file($_FILES['file']['tmp_name'],$full_path)) {
		throw new RuntimeException('Failed to move file.');
	}

} catch (RuntimeException $e) {

	echo $e->getMessage();

}


$f = fopen('data/materials-orders.csv','r');
$temp_table = fopen('data/materials-orders-temp.csv','w');
// pop the headers off the source file and start the new file with those headers
$headers = fgetcsv($f);
fputcsv($temp_table,$headers);

$modified = date('Y-m-d');
$modifiedby = LOGGED_IN_IDIR;
//OrderID,Status,Created,CreatedBy,Modified,ModifiedBy,CourseID,CourseName,Cost,DateOrdered,DateArrived,Notes,FilePath,QuotedBy,SigningAuthority,PONumber,ConsigneeFile,PreviousStatus
$order = Array($orderid,
		h($fromform['Status']),
		h($fromform['Created']),
		h($fromform['CreatedBy']),
		$modified,
		$modifiedby,
		h($fromform['CourseID']),
		h($fromform['CourseName']),
		h($fromform['Cost']),
		h($fromform['DateOrdered']),
		h($fromform['DateArrived']),
		h($fromform['Notes']),
		$filename,
		'', // QuotedBy
		'', // SigningAuthority
		h($fromform['PONumber']),
		'', // ConsigneeFile
		h($fromform['Status']) // PreviousStatus
		);


while (($data = fgetcsv($f)) !== FALSE){

	if($data[0] == $orderid) {
		fputcsv($temp_table,$order);
	} else {
		fputcsv($temp_table,$data);
	}
}
fclose($f);
fclose($temp_table);
rename('data/materials-orders-temp.csv','data/materials-orders.csv');

//
// If we're receiving an order, we want to update the materials with the new quantities
//
//
// materials-order-items.csv:
// OrderID,MaterialID,MaterialName,MaterialQTY,MaterialDetails
//
// materials.csv:
// MaterialID,CourseName,CourseID,MaterialName,PerCourse,InStock,Partial,Restock,Notes,FileName,ProjectNumber,Responsibility,ServiceLine,STOB
//

if($fromform['Status'] == 'Received' && $fromform['PreviousStatus'] != 'Received') {

	$items = getOrderItems($orderid);
	foreach($items as $item) {
		$f = fopen('data/materials.csv','r');
		$temp_table = fopen('data/materials-temp.csv','w');
		$headers = fgetcsv($f);
		fputcsv($temp_table,$headers);
		while (($data = fgetcsv($f)) !== FALSE){
			if($data[0] == $item[1]) {
				$data[5] = $data[5] + $item[3];
			}
			fputcsv($temp_table,$data);
		}
		fclose($f);
		fclose($temp_table);
		rename('data/materials-temp.csv','data/materials.csv');
	}
}

header('Location: materials-order.php?orderid=' . $orderid);?>



<?php else: ?>

<?php $orderid = (isset($_GET['orderid'])) ? $_GET['orderid'] : 0 ?>
<?php $order = getOrder($orderid) ?>

<?php getHeader() ?>

<title>Order Update</title>

<?php getScripts() ?>

<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">

<?php getNavigation() ?>

<?php if(canAccess()): ?>
<div class="container mb-3">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6 mb-3">
<div><a href="course.php?courseid=<?= $order[6] ?>"><?= $order[7] ?></a></div>
<h1 class="card-title"><a href="/lsapp/materials-order.php?orderid=<?= $order[0] ?>"><?= $order[0] ?></a></h1>
<?php //// OrderID,Status,Created,CreatedBy,Modified,ModifiedBy,CourseID,CourseName,8-Cost,DateOrdered,DateArrived,Notes,FilePath ?>

<form  enctype="multipart/form-data" method="post" accept-charset="utf-8" action="materials-order-update.php">
<input type="hidden" name="OrderID" id="OrderID" value="<?= $order[0] ?>">
<input type="hidden" name="Created" id="Created" value="<?= $order[2] ?>">
<input type="hidden" name="CreatedBy" id="CreatedBy" value="<?= $order[3] ?>">
<input type="hidden" name="CourseID" id="CourseID" value="<?= $order[6] ?>">
<input type="hidden" name="CourseName" id="CourseName" value="<?= $order[7] ?>">
<input type="hidden" name="PreviousStatus" id="PreviousStatus" value="<?= $order[13] ?>">
<div class="row">
<div class="col-3">
<label for="Status">Status</label>
<select name="Status" id="Status" class="form-control">
<?php $statuses = array('Draft','Estimate Requested','Ordered','Received') ?>
<?php foreach($statuses as $stat): ?>
<?php if($stat == $order[1]): ?>
<option selected><?= $stat ?></option>
<?php else: ?>
<option><?= $stat ?></option>
<?php endif ?>
<?php endforeach ?>
</select>
</div>
<div class="col-3">
<label for="PONumber">PO # (supplied by QP)</label>
<input type="text" name="PONumber" id="PONumber" value="<?= $order[9] ?>" class="form-control">
</div>
<div class="col-3">
<label for="Cost">Cost</label>
<input type="text" name="Cost" id="Cost" value="<?= $order[8] ?>" class="form-control">
</div>
<div class="col-3">
<label for="DateOrdered">Date Ordered</label>
<input type="text" name="DateOrdered" id="DateOrdered" value="<?= $order[9] ?>" class="form-control">
</div>
<div class="col-3">
<label for="DateArrived">Date Arrived</label>
<input type="text" name="DateArrived" id="DateArrived" value="<?= $order[10] ?>" class="form-control">
</div>
</div>
<label for="Notes">Notes</label>
<textarea name="Notes" id="Note" class="form-control summernote mb-3"><?= $order[11] ?></textarea>

<label for="FilePath">File path:</label>
<input type="file" name="file" class="form-control-file">
<input type="text" name="FilePath" id="FilePath" value="<?= $order[12] ?>" class="form-control">
<input type="submit" value="Update Order" class="btn btn-primary">
</form>
</div>
</div>
</div>



<?php endif ?>
<?php endif ?>
<?php else: ?>
<?php require('templates/noaccess.php') ?>
<?php endif ?>




<?php require('templates/javascript.php') ?>
<script src="/lsapp/js/summernote-bs4.js"></script>

<script>
$(document).ready(function(){


	$('.summernote').summernote({
		toolbar: [
			// [groupName, [list of button]]
			['style', ['bold', 'italic']],
			['para', ['ul', 'ol']],
			['color', ['color']],
			['link']
		],
		placeholder: 'Type here'

	});

});
</script>
<?php require('templates/footer.php') ?>
