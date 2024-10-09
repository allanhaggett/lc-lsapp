<?php 
opcache_reset();
require('inc/lsapp.php');
if(canAccess()):

if($_POST):

	$classesbackup = 'data/checklists.csv';
	$newfile = 'data/backups/checklists'.date('Ymd\THis').'.csv';

	if (!copy($classesbackup, $newfile)) {
		echo 'Failed to backup ' . $classesbackup . '...\nPlease contact learning.centre.admin@gov.bc.ca';
		exit;
	}

	$fromform = $_POST;
	
	if(isset($fromform['ProjectorRequired'])) {
		$preq = 'on';
	} else {
		$preq = 0;
	}
	
	//$checkid = $fromform['ChecklistID'];
	$checkid = date('Ymdhis');
	//checklistID,Manuals,Handouts,CourseID,4-Resources,StandardSupplyKit,AdditionalSupplies,
	//ProjectorRequired,8-AdditionalTech,AudioSpeakers,AttendanceRoster,11-Equipment,RoomSetup,Notes,
	//OffCampusShipping,15-OffCampusNotes,OffCampusEquipment,OffCampusRoomSetup
	$check = Array($checkid,
				h($fromform['Manuals']),
				h($fromform['Handouts']),
				h($fromform['CourseID']),
				h($fromform['Resources']),
				h($fromform['StandardSupplyKit']),
				h($fromform['AdditionalSupplies']),
				$preq,
				h($fromform['AdditionalTech']),
				h($fromform['AudioSpeakers']),
				h($fromform['AttendanceRoster']),
				h($fromform['Equipment']),
				h($fromform['RoomSetup']),
				h($fromform['Notes']),
				h($fromform['OffCampusShipping']),
				h($fromform['OffCampusNotes']),
				h($fromform['OffCampusEquipment']),
				h($fromform['OffCampusRoomSetup'])
		);
	
	
	$fp = fopen('data/checklists.csv', 'a+');
	fputcsv($fp, $check);
	fclose($fp);

	//header('Location: checklist-update.php?courseid=' . h($fromform['CourseName']));
	header('Location: course.php?courseid=' . h($fromform['CourseID']));
	
else: ?>



<?php $courseid = $_GET['courseid'] ?>

<?php $courz = getCourse($courseid) ?>
<?php //CourseID,Status,CourseName,CourseShort,ItemCode,ClassTimes,ClassDays,ELM,PreWork,PostWork,CourseOwner,
//MinMax,CourseNotes,Requested,RequestedBy,EffectiveDate,CourseDescription,CourseAbstract,
//Prerequisites,Keywords,Category,Method,elearning,WeShip,ProjectNumber,Responsibility,ServiceLine,STOB,MinEnroll,MaxEnroll
?>
<?php getHeader() ?>

<title>Create Checklist for <?= $courz[2] ?></title>

<?php getScripts() ?>
<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">
<?php getNavigation() ?>

<div class="container mb-3">
<div class="row justify-content-md-center mb-3">

<div class="col-md-6 mb-3">

<h1>Create Checklist </h1>
<h2> <a href="course.php?courseid=<?= $courz[0] ?>"><?= $courz[2] ?></a></h2>

<form method="post" action="checklist-create.php" class="mb-3 pb-3" id="serviceRequestForm">

<!--<input class="Requested" type="hidden" name="Requested" value="<?php echo date('Y-m-d') ?></textarea>
<input class="RequestedBy" type="hidden" name="RequestedBy" value="">-->

<input class="CourseName" type="hidden" name="CourseName" value="<?= $courz[2] ?>">
<input class="CourseID" type="hidden" name="CourseID" value="<?= $courseid ?>">


<!-- AudioSpeakers is deprecated since all projector kits now have speakers -->
<input type="hidden" name="AudioSpeakers" id="AudioSpeakers" value="">
<!-- AttendanceRoster is deprecated since every class gets a roster, it's hard-coded -->
<input type="hidden" name="AttendanceRoster" id="AttendanceRoster" value="">
<!-- StandardSupplyKit is deprecated since every class gets one, it's hard-coded -->
<input type="hidden" name="StandardSupplyKit" id="StandardSupplyKit" value="">

<div class="form-group">
	<label for="Manuals">Manuals: </label>
	<textarea type="text" name="Manuals" id="Manuals" id="" class="summernote form-control"></textarea>
</div>
<div class="form-group">
	<label for="Handouts">Handouts: </label>
	<textarea type="text" name="Handouts" id="Handouts" class="summernote form-control"></textarea>
</div>
<!--checklistID,Manuals,Handouts,CourseName,Resources,StandardSupplyKit,AdditionalSupplies,ProjectorType,
AdditionalTech,AudioSpeakers,AttendanceRoster,Equipment,RoomSetup,Notes,OffCampusShipping,OffCampusNotes,
OffCampusEquipment,OffCampusRoomSetup-->
<div class="form-group">
	<label for="Resources">Resources:</label>

	<textarea name="Resources" id="Resources" class="summernote form-control"></textarea></textarea>
</div>

<div class="form-group">
	<label for="AdditionalSupplies">Additional Supplies: </label>
	<textarea  name="AdditionalSupplies" id="AdditionalSupplies" class="summernote form-control"></textarea>
</div>



<div class="form-group">
	<label for="AdditionalTech">Additional Tech: </label>
	<textarea  name="AdditionalTech" id="AdditionalTech" class="summernote form-control"></textarea>
</div>

<div class="form-group">
	<label for="ProjectorRequired">Projector Required: </label>
	<input type="checkbox" name="ProjectorRequired" id="ProjectorRequired" class="form-control">
</div>


<div class="form-group">
	<label for="Equipment">Equipment: </label>
	<textarea  name="Equipment" id="Equipment" class="summernote form-control"></textarea>
</div>
<div class="form-group">
	<label for="RoomSetup">Room Setup: </label>
	<textarea  name="RoomSetup" id="RoomSetup" class="summernote form-control"></textarea>
</div>
<div class="form-group">
	<label for="Notes">Notes: </label>
	<textarea  name="Notes" id="Notes" class="summernote form-control"></textarea>
</div>
	<!--//checklistID,Manuals,Handouts,CourseName,4-Resources,StandardSupplyKit,AdditionalSupplies,
	//ProjectorType,8-AdditionalTech,AudioSpeakers,AttendanceRoster,11-Equipment,RoomSetup,Notes,
	//OffCampusShipping,15-OffCampusNotes,OffCampusEquipment,OffCampusRoomSetup-->
<div class="form-group">
	<label for="OffCampusShipping">Off-Campus Shipping: </label>
	<textarea  name="OffCampusShipping" id="OffCampusShipping" class="summernote form-control"></textarea>
</div>
<div class="form-group">
	<label for="OffCampusNotes">Off-Campus Notes: </label>
	<textarea  name="OffCampusNotes" id="OffCampusNotes" class="summernote form-control"></textarea>
</div>
<div class="form-group">
	<label for="OffCampusEquipment">Off-Campus Equipment: </label>
	<textarea  name="OffCampusEquipment" id="OffCampusEquipment" class="summernote form-control"></textarea>
</div>
<div class="form-group">
	<label for="OffCampusRoomSetup">Off-Campus Room Setup: </label>
	<textarea  name="OffCampusRoomSetup" id="OffCampusRoomSetup" class="summernote form-control"></textarea>
</div>


<button class="btn btn-block btn-primary my-3">Create Checklist</button>

</form>
	
</div>
</div>


</div>

<?php require('templates/javascript.php') ?>
<script src="/lsapp/js/summernote-bs4.js"></script>
<script>
$(document).ready(function(){
	
	$('.showvenue').on('click',function(e){
		e.preventDefault();
		$('#venuedeets').toggle();
	});
	
	$('.summernote').summernote({
		//airMode: true,
		toolbar: [
			// [groupName, [list of button]]
			['style', ['bold', 'italic']],
			['para', ['ul', 'ol']],
			['color', ['color']],
			['link']
		],
		placeholder: 'Enter your words here'
		
	});	
	
	
	$('.resourcesedit').on('focus', function(e) {
		e.preventDefault;
		$('.resourcesedit').summernote({focus: true});
	});

	
	
});
</script>
<?php require('templates/footer.php') ?>

<?php endif ?>

<?php else: ?>

<?php require('templates/noaccess.php') ?>

<?php endif ?>