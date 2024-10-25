<?php 
require('inc/lsapp.php');
if(isAdmin()):

if($_POST):

	$fromform = $_POST;

	$course = getCourse($fromform['CourseID']);
	$courseshort = $course[3];
	if($_FILES['file']['tmp_name']) {
		$fname = $courseshort . '-' . $fromform['MaterialName'] . '-' . date('YmdHis');
		$material_filename = createSlug($fname) . '.pdf';
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
			if ($_FILES['file']['type'] != 'application/pdf') { //  && $_FILES['file']['type'] != 'application/vnd.ms-excel'
				throw new RuntimeException('Wrong type of file. You tried to upload: ' . $_FILES['file']['type']);
			}
			if (!move_uploaded_file($_FILES['file']['tmp_name'],$material_full_path)) {
				throw new RuntimeException('Failed to move file.');
			}

		} catch (RuntimeException $e) {

			echo $e->getMessage();

		}
	} else {
		$material_filename = $fromform['FileName'];
	}// end of if materialname
	
	$f = fopen('data/materials.csv','r');
	$temp_table = fopen('data/materials-temp.csv','w');
	// pop the headers off the source file and start the new file with those headers
	$headers = fgetcsv($f);
	fputcsv($temp_table,$headers);
	
	$mid = $fromform['MaterialID'];
	$reup = 'Yes';
	if(isset($fromform['Restock'])) $reup = $fromform['Restock'];
	
	
	
	// MaterialID,CourseName,CourseID,MaterialName,PerCourse,InStock,Partial,Restock,Notes,FileName,
	// ProjectNumber,Responsibility,ServiceLine,STOB,QuotedBy,SigningAuthority
	
	// MaterialID,CourseName,CourseID,MaterialName,PerCourse,InStock,Partial,Restock,Notes,FileName,
	// CreatedOn,CreatedBy,ModifiedOn,ModifiedBy,Notes
	$today = date('Y-m-d\TH:i:s');
	$modby = LOGGED_IN_IDIR;
	$material = Array($mid,
				h($fromform['CourseName']),
				h($fromform['CourseID']),
				h($fromform['MaterialName']),
				h($fromform['PerCourse']),
				h($fromform['InStock']),
				'',
				$reup,
				h($fromform['PrintingInstructions']),
				$material_filename,
				h($fromform['CreatedOn']),
				h($fromform['CreatedBy']),
				$today,
				$modby,
				h($fromform['Notes'])
		);
	
	while (($data = fgetcsv($f)) !== FALSE){
		if($data[0] == $mid) {
			fputcsv($temp_table,$material);
		} else {
			fputcsv($temp_table,$data);
		}
	}
	
	fclose($f);
	fclose($temp_table);

	rename('data/materials-temp.csv','data/materials.csv');

	header('Location: material.php?mid=' . $mid);
	
else: ?>



<?php $mid = $_GET['mid'] ?>
<?php $v = getMaterial($mid) ?>

<?php getHeader() ?>

<title>Edit <?= $v[1] ?></title>

<?php getScripts() ?>

<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">

<?php getNavigation() ?>

<div class="container mb-3">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6 mb-3">

<div>
<a href="course.php?courseid=<?= $v[2] ?>"><?= $v[1] ?></a> > 
<a href="material.php?mid=<?= $v[0] ?>"><?= $v[3] ?></a>

</div>
<h1>Edit <?= $v[3] ?></h1>



<form enctype="multipart/form-data" method="post" 
									accept-charset="utf-8" 
									class="up" 
									action="material-update.php">


<!--<input class="Requested" type="hidden" name="Requested" value="<?php echo date('Y-m-d') ?>">
<input class="RequestedBy" type="hidden" name="RequestedBy" value="">-->

<!-- // MaterialID,CourseName,CourseID,MaterialName,PerCourse,InStock,Partial,Restock,Notes,FileName  -->

<input class="MaterialID" type="hidden" name="MaterialID" value="<?= $v[0] ?>">
<input class="CourseName" type="hidden" name="CourseName" value="<?= $v[1] ?>">
<input class="CourseID" type="hidden" name="CourseID" value="<?= $v[2] ?>">
<input class="CourseID" type="hidden" name="CreatedOn" value="<?= $v[10] ?>">
<input class="CourseID" type="hidden" name="CreatedBy" value="<?= $v[11] ?>">


<div class="form-row">
<div class="form-group col-md-12">
	<label for="MaterialName">Material Name:</label>
	<input type="text" name="MaterialName" id="MaterialName" class="form-control" value="<?= $v[3] ?>">
</div>

<div class="form-group col-3">
	<label for="PerCourse">Per Course:</label>
	<input type="text" name="PerCourse" id="PerCourse" class="form-control" value="<?= $v[4] ?>">
</div>
<div class="form-group col-3">
	<label for="InStock">In Stock:</label>
	<input type="text" name="InStock" id="InStock" class="form-control" value="<?= $v[5] ?>">
</div>
<div class="form-group col-3">
	<label for="Restock">Restock:</label>
	<?php if($v[7] == 'on'): ?>
	<input type="checkbox" name="Restock" id="Restock" class="form-control" checked>
	<?php else: ?>
	<input type="checkbox" name="Restock" id="Restock" class="form-control">
	<?php endif ?>
</div>
<div class="form-group col-12">
	<label for="FileName">File Name:</label>
	<input type="file" name="file" class="form-control-file">
	<input type="hidden" name="FileName" id="FileName" value="<?= $v[9] ?>">
</div>


</div>

<button class="btn btn-block btn-primary my-3">Update Material</button>

</div>
<!-- MaterialID,CourseName,CourseID,MaterialName,PerCourse,InStock,Partial,Restock,8-Notes,FileName,CreatedOn,CreatedBy,ModifiedOn,ModifiedBy,Notes-->
<div class="col-md-6">
<div class="form-group">
	<label for="Notes">Printing Instructions:</label>
	<textarea name="PrintingInstructions" id="PrintingInstructions" class="form-control summernote"><?= $v[8] ?></textarea>
</div>
<div class="form-group">
	<label for="Notes">Notes:</label>
	<textarea name="Notes" id="Notes" class="form-control summernote"><?= $v[14] ?></textarea>
</div>


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

<?php require('templates/noaccess.php') ?>

<?php endif ?>