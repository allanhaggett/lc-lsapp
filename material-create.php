<?php 
require('inc/lsapp.php');
if(isAdmin()):

if($_POST):

	$fromform = $_POST;
	$mid = date('YmdHis');
	$course = getCourse($fromform['CourseID']);
	$courseshort = $course[3];
	$material_filename = '';
	if(is_uploaded_file($_FILES['file']['tmp_name'])) {
		$ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
		$fname = $courseshort . '-' . $fromform['MaterialName'] . '-' . date('Y-m-d:His');
		$material_filename = createSlug($fname) . '.' . $ext;
		$material_full_path = 'materials/' . $material_filename;
	
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
			if ($_FILES['file']['type'] != 'application/pdf' && $_FILES['file']['type'] != 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
			// && $_FILES['file']['type'] != 'application/vnd.ms-excel'
			throw new RuntimeException('Wrong type of file. You tried to upload: ' . $_FILES['file']['type']);
			}
			if (!move_uploaded_file($_FILES['file']['tmp_name'],$material_full_path)) {
				throw new RuntimeException('Failed to move file.');
			}

		} catch (RuntimeException $e) {

			echo $e->getMessage();

		}
	}
	
	// MaterialID,CourseName,CourseID,MaterialName,PerCourse,InStock,Partial,Restock,Notes,FileName
	// MaterialID,CourseName,CourseID,MaterialName,PerCourse,InStock,Partial,Restock,PrintingInstructions,FileName,CreatedOn,CreatedBy,ModifiedOn,ModifiedBy,Notes
	$today = date('Y-m-d\TH:i:s');
	$createdby = LOGGED_IN_IDIR;
	$material = Array($mid,
				h($fromform['CourseName']),
				h($fromform['CourseID']),
				h($fromform['MaterialName']),
				h($fromform['PerCourse']),
				h($fromform['InStock']),
				'',
				'No',
				h($fromform['PrintingInstructions']),
				$material_filename,
				$today,
				$createdby,				
				$today,
				$createdby,
				h($fromform['Notes'])
		);
	
		$new = array($material);
		$fp = fopen('data/materials.csv', 'a+');
		foreach ($new as $fields) {
			fputcsv($fp, $fields);
		}
		fclose($fp);

	header('Location: material.php?mid=' . $mid);
	
else: ?>



<?php $courseid = $_GET['courseid'] ?>
<?php $course = getCourse($courseid) ?>

<?php getHeader() ?>

<title><?= $course[2] ?> new material</title>

<?php getScripts() ?>

<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">

<?php getNavigation() ?>

<div class="container mb-3">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6 mb-3">

<h1>New Material for <?= $course[2] ?></h1>

<form enctype="multipart/form-data" method="post" 
									accept-charset="utf-8" 
									class="up" 
									action="material-create.php">

<input class="CourseName" type="hidden" name="CourseName" value="<?= $course[2] ?>">
<input class="CourseID" type="hidden" name="CourseID" value="<?= $course[0] ?>">

<div class="form-row">
<div class="form-group col-md-12">
	<label for="MaterialName">Material Name:</label>
	<input type="text" name="MaterialName" id="MaterialName" class="form-control">
</div>

<div class="form-group col-3">
	<label for="PerCourse">Per Course:</label>
	<input type="text" name="PerCourse" id="PerCourse" class="form-control">
</div>
<div class="form-group col-3">
	<label for="InStock">In Stock:</label>
	<input type="text" name="InStock" id="InStock" class="form-control">
</div>
<div class="form-group col-12">
	<label for="FileName">File Name:</label>
	<input type="file" name="file" class="form-control-file">
	<input type="text" name="FileName" id="FileName" class="form-control">
</div>
<div class="form-group">
	<label for="PrintingInstructions">Print Instructions:</label>
	<textarea name="PrintingInstructions" id="PrintingInstructions" class="form-control summernote"></textarea>
</div>
<div class="form-group">
	<label for="Notes">Notes:</label>
	<textarea name="Notes" id="Notes" class="form-control summernote"></textarea>
</div>

</div>

<button class="btn btn-block btn-primary my-3">Create Material</button>


</form>
	
</div>
</div>


</div>

<?php require('templates/javascript.php') ?>

<script src="/lsapp/js/summernote-bs4.js"></script>


<script>

$(document).ready(function(){
	
	$('.summernote').summernote();
});
</script>

<?php require('templates/footer.php') ?>

<?php endif ?>

<?php else: ?>

<?php require('templates/noaccess-adminonly.php') ?>

<?php endif ?>