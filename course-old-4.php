<?php require('inc/lsapp.php') ?>
<?php //opcache_reset() ?>
<?php if(canACcess()): ?>
<?php 
$courseid = (isset($_GET['courseid'])) ? $_GET['courseid'] : 0;

$topics = getAllTopics();
$audience = getAllAudiences ();
$deliverymethods = getDeliveryMethods ();
$levels = getLevels ();
$reportinglist = getReportingList();
$deets = getCourse($courseid);
$audits = getCourseAudits($courseid);
//echo '<pre>'; print_r($audits); exit;
// 0-CourseID,1-Status,2-CourseName,3-CourseShort,4-ItemCode,5-ClassTimes,6-ClassDays,7-ELM,8-PreWork,9-PostWork,
// 10-CourseOwner,11-MinMax,12-CourseNotes,
// 13-Requested, 14-RequestedBy,15-EffectiveDate,16-CourseDescription,17-CourseAbstract,18-Prerequisites,19-Keywords,
// 20-Category,21-Method,22-elearning
?>
<?php getHeader() ?>

<title><?= $deets[2] ?></title>
<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">
<style>
.abstract {
	height: 100px;
	overflow-y: scroll;
}
</style>
<?php getScripts() ?>

<body class="bg-light-subtle">
<?php getNavigation() ?>

<div class="container-fluid">
<div class="row justify-content-md-center my-3">
<div class="col-md-7">
<div class="card">
<div class="card-header">
<div class="float-right p-3" style="background-color:<?= $deets[32] ?>; border-radius: 10px;"><?= $deets[3] ?></div>
<div class="badge badge-light"><?= $deets[1] ?></div>

<div class="text-uppercase"><?= $deets[21] ?></div>
<!--<div class="text-uppercase">LC Ship? <?= $deets[23] ?></div>-->
<h1><?= $deets[2] ?></h1>
<div><?= $deets[4] ?></div>
</div>
<div class="card-body">
<div class="alert alert-warning mb-0">This page contains the standardized information for this course. Individual class dates can vary from this information. 
Please choose a class date from the right to see the information for a particular class date.</div>
<?php if(isAdmin()): ?>
	<div class="mt-3 float-right">
		<a href="course-update.php?courseid=<?= $courseid ?>" class="btn btn-primary">Edit</a>
	</div>
<?php endif ?>
<div class="mt-3">

<?php if(!empty($deets[7])): ?>
<!-- <a href="<?= $deets[7] ?>" target="_blank" class="btn btn-success">ELM</a> -->
<?php endif ?>
<!-- IN DEVELOPMENT TALK TO ALLAN IF YOU GOT QUESTIONS  -->
<!-- <a href="https://gww.bcpublicservice.gov.bc.ca/learning/coursework/courses/<?= strtolower($deets[3]) ?>/" 
	target="_blank" 
	class="btn btn-warning">
		Coursework
</a> -->
<?php if(!empty($deets[8])): ?>
<a href="<?= $deets[8] ?>" target="_blank" class="btn btn-primary">PreWork</a>
<?php endif ?>
<?php if(!empty($deets[9])): ?>
<a href="<?= $deets[9] ?>" target="_blank" class="btn btn-primary">PostWork</a>
<?php endif ?>
<a href="https://learning.gov.bc.ca/psc/CHIPSPLM/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_FND_LRN_FL.GBL?Page=LM_FND_LRN_RSLT_FL&Action=U&MODE=ADV&TITLE=<?php echo urlencode($deets[2]) ?>"
	target="_blank" 
	class="btn btn-dark">
		ELM Search
	</a>
	<a href="class-request.php?courseid=<?= $deets[0] ?>" class="btn btn-success">New Date Request</a>
	<a href="/lsapp/class-bulk-insert.php?courseid=<?= $deets[0] ?>" class="btn btn-secondary">Bulk Requests</a>
</div>
<details class="">
	<summary>New Taxonomy Quick Update</summary>
	<form method="post" action="/lsapp/course-new-tax-up.php" class="mb-3 pb-3">
	<input type="hidden" name="CourseID" value="<?= h($deets[0]) ?>">
	<div class="row">
	<div class="col-md-6">
	<label for="Topics">Topic</label><br>
	<select name="Topics" id="Topics" class="form-control">
	<?php foreach($topics as $t): ?>
	<?php if($deets[38] == $t): ?>
	<option selected><?= $t ?></option>
	<?php else: ?>
	<option><?= $t ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>
	</div>
	<div class="col-md-6">
	<label for="Audience">Audience</label><br>
	<select name="Audience" id="Audience" class="form-control">
	<?php foreach($audience as $a): ?>
	<?php if($deets[39] == $a): ?>
	<option selected><?= $a ?></option>
	<?php else: ?>
	<option><?= $a ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>
	</div>
	<div class="col-md-6">
	<label for="Levels<">Level</label><br>
	<select name="Levels" id="Levels" class="form-control">
	<?php foreach($levels as $l): ?>
	<?php if($deets[40] == $l): ?>
	<option selected><?= $l ?></option>
	<?php else: ?>
	<option><?= $l ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>
	</div>
	<div class="col-md-6">
	<label for="Reporting">Reporting</label><br>
	<select name="Reporting" id="Reporting" class="form-control">
	<?php foreach($reportinglist as $r): ?>
	<?php if($deets[41] == $r): ?>
	<option selected><?= $r ?></option>
	<?php else: ?>
	<option><?= $r ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>
	</div>
	
	</div>
	<button class="btn btn-primary my-3">Save Course Info</button>
	</form>
</details>
<dl class="row">

	<dt class="col-3 text-right">Categories:</dt>
	<dd class="col-9">
	<?php $cats = explode(',',$deets[20]) ?>
	<?php foreach($cats as $cat): ?>
		<a href="category.php?category=<?php echo urlencode($cat) ?>"><?= $cat ?></a>, 
	<?php endforeach ?>
	</dd>
	<dt class="col-3 text-right">Topics:</dt>
		<dd class="col-3"><?= $deets[38] ?></dd>
	<dt class="col-3 text-right">Audience:</dt>
		<dd class="col-3"><?= $deets[39] ?></dd>
	<dt class="col-3 text-right">Level:</dt>
		<dd class="col-3"><?= $deets[40] ?></dd>
	<dt class="col-3 text-right">Reporting:</dt> 
		<dd class="col-3"><?= $deets[41] ?></dd>

<?php $dev = getPerson($deets[34]) ?>
<?php if(is_array($dev)): ?>
	<dt class="col-3 text-right">Developer:</dt>
		<dd class="col-3"><a href="person.php?idir=<?= $deets[34] ?>"><?= $dev[2] ?></a></dd>
<?php else: ?>
	<dt class="col-3 text-right">Developer:</dt> 
		<dd class="col-3"><?= $deets[34] ?></dd>
<?php endif ?>
<?php $person = getPerson($deets[10]) ?>
<?php if(is_array($person)): ?>
	<dt class="col-3 text-right">Steward:</dt>
		<dd class="col-3"><a href="person.php?idir=<?= $deets[10] ?>"><?= $person[2] ?></a></dd>
<?php else: ?>
	<dt class="col-3 text-right">Owner:</dt> 
		<dd class="col-3"><?= $deets[10] ?></dd>
<?php endif ?>
	<dt class="col-3 text-right">Learning Hub Partner:</dt>
		<dd class="col-9"><a href="learning-hub-partner.php?partnerid=<?php echo urlencode($deets[36]) ?>"><?= $deets[36] ?></a></dd>
	<dt class="col-3 text-right">Alchemer?</dt>
		<dd class="col-9"><?= $deets[37] ?></dd>
	<dt class="col-3 text-right">Times:</dt>
		<dd class="col-3"><?= $deets[5] ?></dd>
	<dt class="col-3 text-right">Days:</dt>
		<dd class="col-3"><?= $deets[6] ?></dd>
	<dt class="col-3 text-right">MinMax:</dt> 
		<dd class="col-9"><?= $deets[28] ?>/<?= $deets[29] ?></dd>
	<dt class="col-3 text-right">Color:</dt> 
		<dd class="col-9">
			<?= $deets[32] ?>
			<div style="background-color:<?= $deets[32] ?>; height: 10px; width: 100px;"></div>
		</dd>
	<?php if($deets[18]): ?>
	<dt class="col-3 text-right">Prerequisites:</dt> 
		<dd class="col-9"><?= $deets[18] ?></dd>
	<?php endif ?>

	<dt class="col-3 text-right">Project Number:</dt> 
		<dd class="col-3">
			<?= $deets[24] ?>
		</dd>
	<dt class="col-3 text-right">Responsibility:</dt> 
		<dd class="col-3">
			<?= $deets[25] ?>
		</dd>
	<dt class="col-3 text-right">Service Line:</dt> 
		<dd class="col-3">
			<?= $deets[26] ?>
		</dd>
	<dt class="col-3 text-right">STOB:</dt> 
		<dd class="col-3">
			<?= $deets[27] ?>
		</dd>

	<?php if($deets[22]): ?>
	<dt class="col-3 text-right">eLearning link:</dt> 
		<dd class="col-9"><?= $deets[22] ?></dd>
	<?php endif ?>

	
	<?php if($deets[35]): ?>
	<dt class="col-3 text-right">Evaluations link:</dt> 
		<dd class="col-9"><?= $deets[35] ?></dd>
	<?php endif ?>
	<dt class="col-3 text-right">Audits:</dt>
		<dd><a href="/lsapp/audit-form.php?courseid=<?= $deets[0] ?>" class="btn btn-secondary">Audit this course</a></dd>
	<?php if(!empty($audits)): ?>
	<?php foreach($audits as $audit): ?>
		<dd class="mt-2"><span class="badge badge-light"><?= $audit[6] ?></span> <a href="/lsapp/audit.php?auditid=<?= $audit[0] ?>"><?= $audit[1] ?></a></dd>
	<?php endforeach ?>
	<?php endif ?>
	</dl>
	<details>
		<summary>Developer Paths</summary>
		<!-- //42-PathLAN,43-PathStaging,44-PathLive,45-PathNIK,46-PathTeams -->
		<div><strong>LAN Path:</strong> \\<?= $deets[42] ?>\ <button class="copy btn btn-sm btn-light" data-clipboard-text="\\<?= $deets[42] ?>\">Copy</button></div>
		<div><strong>Staging Path:</strong> <?= $deets[43] ?> <button class="copy btn btn-sm btn-light" data-clipboard-text="<?= $deets[43] ?>">Copy</button></div>
		<div><strong>Live Path:</strong> <?= $deets[44] ?> <button class="copy btn btn-sm btn-light" data-clipboard-text="<?= $deets[44] ?>">Copy</button></div>
		<div><strong>NIK Path:</strong> <?= $deets[45] ?> <button class="copy btn btn-sm btn-light" data-clipboard-text="<?= $deets[45] ?>">Copy</button></div>
		<div><strong>Teams Path:</strong> <?= $deets[46] ?> <button class="copy btn btn-sm btn-light" data-clipboard-text="<?= $deets[46] ?>">Copy</button></div>
	</details>
	<hr>
	<dl class="row">
	<dt class="col-3 text-right">Notes:</dt> 
		<dd class="col-9"><?= $deets[12] ?></dd>
	<dt class="col-3 text-right">Description:</dt>
		<dd class="col-9"><?= $deets[16] ?></dd>
	<dt class="col-3 text-right">Abstract:</dt>
		<dd class="col-9">
		<div class="abstract">
		<?= $deets[17] ?>
		</div>
		</dd>

</dl>	


<div class="row">
<div class="col-md-6">
<?php if(isAdmin()): ?>
<a href="material-create.php?courseid=<?= $courseid ?>" class="btn btn-primary btn-sm float-right">New</a>
<?php endif ?>
<?php $materials = getMaterials($deets[0]) ?>
<?php if(isset($materials)): ?>
<h2>Materials</h2>
<form method="post" action="materials-order-create.php">
<input type="hidden" name="CourseID" id="CourseID" value="<?= $courseid ?>">
<input type="hidden" name="CourseName" id="CourseName" value="<?= $deets[2] ?>">
<table class="table table-sm">
<tr>
	<th></th>
	<th>MaterialName</th>
	<th class="text-center">PerCourse</th>
	<th class="text-center">InStock</th>
</tr>
<!-- // 0-MaterialID,1-CourseName,2-MaterialName,3-PerCourse,4-InStock,5-Partial,6-Restock,7-Notes-->

<?php foreach($materials as $mat): ?>
<tr>
	<td>
		<?php if($mat[7] == 'on' || $mat[7] == 'TRUE'): ?>
		<input type="checkbox" name="material[]" id="material<?= $mat[0] ?>" value="<?= $mat[0] ?>" checked>
		<?php else: ?>
		<input type="checkbox" name="material[]" id="material<?= $mat[0] ?>" value="<?= $mat[0] ?>">
		<?php endif ?>
		
	</td>
	<td><a href="material.php?mid=<?= $mat[0] ?>"><?= $mat[3] ?></a></td>
	<td class="text-center"><?= $mat[4] ?></td>
	<td class="text-center"><?= $mat[5] ?></td>
</tr>
<?php endforeach?>
<?php endif ?>
<?php if(isAdmin()): ?>
<tr>
<td colspan="6"><input type="submit" class="btn btn-primary btn-block" value="Place Materials Order">
</tr>
<?php endif ?>
</table>
</form>
	<!--	<form method="post" action="courses-controller.php" class="coursedel">
			<input type="hidden" name="courseid" id="courseid" value="<?= $deets[0] ?>">
			<input type="hidden" name="action" id="action" value="delete">
			<input type="submit" class="btn btn-secondary btn-sm" value="Delete">
		</form>-->
		
		
		
</div>
<div class="col-md-6">

<h3>Print Orders</h3>

<table class="table table-sm">
<tr>
	<th>Order #</th>
	<th>Status</th>
	<th>Cost</th>
</tr>
<?php $orders = getOrders($deets[0]) ?>
<?php foreach($orders as $order): ?>
<tr>
	<td><a href="materials-order.php?orderid=<?= $order[0] ?>"><?= $order[0] ?></a></td>
	<td><span class="badge badge-light"><?= $order[1] ?></span></td>
	<td>$<?= $order[8] ?></td>
</tr>
<?php endforeach ?>
</table>
<a href="materials.php" class="btn btn-light btn-block">All Orders</a>
</div>
</div>


<?php $checks = getChecklist($deets[0]) ?>
<!-- 
0-checklistID, 1-Manuals,2-Handouts,3-CourseName,4-Resources,5-StandardSupplyKit,6-AdditionalSupplies,7-ProjectorType,8-AdditionalTech,9-AudioSpeakers,
10-AttendanceRoster,11-Equipment,12-RoomSetup,13-Notes,14-OffCampusShipping,15-OffCampusNotes,16-OffCampusEquipment,17-OffCampusRoomSetup

-->
<?php if(is_array($checks)): ?>
<?php if(isAdmin()): ?>
<a href="checklist-update.php?courseid=<?= $deets[0] ?>" class="float-right btn btn-primary">Edit Checklist</a>
<?php endif ?>
<h2>Checklist</h2>
<table class="table table-sm" id="coursechecklist">

<tbody>
<tr><td class="text-right">Manuals</td><td><?= $checks[1] ?></td></tr>
<tr><td class="text-right">Handouts</td><td><?= $checks[2] ?></td></tr>
<tr><td class="text-right">Resources</td><td><?= $checks[4] ?></td></tr>
<tr><td class="text-right">Standard Supply Kit</td><td><?= $checks[5] ?></td></tr>
<tr><td class="text-right">Additional Supplies</td><td><?= $checks[6] ?></td></tr>
<?php
if($checks[7] != 'on') {
	$proreq = 'Not required';
} else {
	$proreq = '<ul><li>Required</li></ul>';
}
?>
<tr><td class="text-right">Projector?</td><td><?= $proreq ?></td></tr>
<tr><td class="text-right">Additional Tech</td><td><?= $checks[8] ?></td></tr>
<tr><td class="text-right">Audio Speakers</td><td><?= $checks[9] ?></td></tr>
<tr><td class="text-right">Attendance Roster</td><td><?= $checks[10] ?></td></tr>
<tr><td class="text-right">Equipment</td><td><?= $checks[11] ?></td></tr>
<tr><td class="text-right">Room Setup</td><td><?= $checks[12] ?></td></tr>
<tr><td class="text-right">Notes</td><td><?= $checks[13] ?></td></tr>
<tr><td class="text-right">Off-Campus Shipping</td><td><?= $checks[14] ?></td></tr>
<tr><td class="text-right">Off-Campus Notes</td><td><?= $checks[15] ?></td></tr>
<tr><td class="text-right">Off-Campus Equipment</td><td><?= $checks[16] ?></td></tr>
<tr><td class="text-right">Off-Campus Room Setup</td><td><?= $checks[17] ?></td></tr>
</tbody>
</table>
<?php else: ?>
<?php if(isAdmin()): ?>
<a href="checklist-create.php?courseid=<?= $deets[0] ?>" class="float-right btn btn-success">Create Checklist</a>
<?php endif ?>
<?php endif ?>

</div> <!-- /.card-body -->
<div class="card-footer">
Created on <?php echo goodDateLong($deets[13]) ?> by <a href="person.php?idir=<?= $deets[14] ?>"><?= $deets[14] ?></a>
</div>
</div>
</div>

<div class="col-md-5">
<?php 
$inactive = 0;
$upcount = 0;
$classes = getCourseClasses($deets[0]);
foreach($classes as $class):
	$today = date('Y-m-d');
	if($class[9] < $today && $class[45] !== "eLearning") continue;
	if($class[1] == 'Inactive') $inactive++;
$upcount++;
endforeach;
?>
<div id="upcoming-classes">
<h3><span class="classcount"><?= ($upcount - $inactive) ?></span>  Current Offerings</h3>
<div class="btn-group">
<a href="course-classes-export.php?courseid=<?= $deets[0] ?>" class="btn btn-primary">Export to Excel</a>
<button class="btn btn-primary copy" 
	href="https://gww.bcpublicservice.gov.bc.ca/lsapp/ical-course.php?courseid=<?= $deets[0] ?>"
	data-clipboard-text="https://gww.bcpublicservice.gov.bc.ca/lsapp/ical-course.php?courseid=<?= $deets[0] ?>"
	title="All scheduled classes for this course">
	Calendar Subscribe
</button>
</div>
<input class="search form-control my-2" placeholder="search">
<table class="table table-sm table-striped">
<thead>
<tr>
	<th>Item Code</th>
	<th><a href="#" class="sort" data-sort="classdate">Class Date</a></th>
	<th><a href="#" class="sort" data-sort="city">City</a></th>
	<th><a href="#" class="sort" data-sort="status">Status</a></th>
</tr>
</thead>
<tbody class="list">
<?php foreach($classes as $class): ?>
<?php
// We only wish to see classes which have an end date greater than today
$today = date('Y-m-d');
if($class[9] < $today && $class[45] !== "eLearning") continue;
?>
<?php if($class[1] == 'Inactive'): ?>
<tr class="cancelled">
<?php else: ?>
<tr>
<?php endif ?>
	<td>
		<?php if($class[4] == 'Dedicated'): ?>
		<span class="badge badge-light">Dedicated</span>
		<?php endif ?>
		<small><?= $class[7] ?></small>
		
	</td>
	<td>
		<a href="/lsapp/class.php?classid=<?= $class[0] ?>">
		<?php echo goodDateShort($class[8],$class[9]) ?>
		</a>
		<div class="classdate" style="display:none"><?= $class[8] ?></div>
	</td>
	<td class="city">
        <a href="city.php?name=<?= $class[25] ?>"><?= $class[25] ?></a>
<!--Ben Update - If no City, show the delivery method-->
        <?php if(!$class[25]): ?>
        <span class="badge badge-light"><?= h($class[45]) ?></span>
        <?php endif ?>
    </td>
<!--/Ben Update-->
    <td class="status">
		<span class="badge badge-light"><?= $class[1] ?></span>
	</td>
</tr>
<?php endforeach ?>
</tbody>
</table>
</div>

<div class="card mb-4">
<div class="card-header">
	<h4 class="card-title">Change Requests</h4>
	<p><small>Request a change to the information <em>on this page</em>. To request a change to a class, please navigate to that class page and submit your request there.</small></p>
</div>
<div class="card-body">
<form action="course-change-create.php" method="post">
<input type="hidden" name="CourseName" id="CourseName" value="<?= h($deets[2]) ?>">
<input type="hidden" name="CourseID" id="CourseID" value="<?= h($deets[0]) ?>">
<textarea name="ChangeRequest" id="ChangeRequest" class="form-control summernote" required></textarea>
<input type="submit" class="btn btn-sm btn-primary btn-block" value="Add Change Request">
</form>
</div>
<ul class="list-group list-group-flush">
<?php $chgs = getCourseChanges($courseid) ?>
<?php if(isset($chgs)): ?>
<?php foreach($chgs as $ch): ?>
<li class="list-group-item">
<?php //0-creqID,1-CourseID,3-CourseName,4-DateRequested,5-RequestedBy,6-Status,7-CompletedBy,8-CompletedDate,9-Request ?>
	<?php if(isSuper()): ?>
	<form method="post" action="course-change-delete.php" class="float-right">
	<input type="hidden" name="CourseID" value="<?= $deets[0] ?>">
	<input type="hidden" name="reqID" value="<?= $ch[0] ?>">
	<input type="submit" value="Delete" class="btn btn-sm btn-danger del">
	</form>
	<?php endif ?>
	
	<small>On <?= h($ch[3]) ?> <?= h($ch[4]) ?> said:</small><br>
	<?= h($ch[8]) ?><br>
	
	<div class="badge badge-dark"><?= h($ch[5]) ?></div>
	
	<small><?= h($ch[6]) ?> on <?= h($ch[7]) ?></small>
	
	<a href="/lsapp/course-change-process.php?changeid=<?= h($ch[0]) ?>&classid=<?= h($deets[0]) ?>"
		class="float-right btn btn-sm btn-success">Do</a>

		</li>
<?php endforeach ?>

</ul>

</div> <!-- /.card -->

<?php else: ?>

<div class="alert alert-warning">
	If you would like to change the information on this page, please email
	<a href="mailto:learning.centre.admin@gov.bc.ca?subject=Course Change Request">learning.centre.admin@gov.bc.ca</a>
</div>

<?php endif; // isAdmin ?>


</div>
</div>

<?php if(isSuper()): ?>
<!-- <div class="row">
<div class="col-md-6">
<div class="alert alert-warning">
A WORK IN PROGRESS. Please don't mess with it :)
<form method="post" action="communication-template-create.php">
<input type="hidden" name="CourseID" id="CourseID" value="<?= h($deets[0]) ?>">
Template Name: <input type="text" id="TemplateName" name="TemplateName" class="form-control"><br>
Template:<br>
<textarea class="form-control summernote" name="Template" id="Template"></textarea>
<input type="submit" class="btn btn-block btn-success" value="Add Template">
</form>
</div>
</div>
</div> -->
<?php endif ?>

</div>
</div>
</div>

<?php else: ?>
<?php getHeader() ?>

<title>LSApp | Dashboard</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<?php require('templates/noaccess.php') ?>

<?php endif ?>

<?php require('templates/javascript.php') ?>

<script src="/lsapp/js/summernote-bs4.js"></script>

<script src="/lsapp/js/clipboard.min.js"></script>
<script>
$(document).ready(function(){

	var clipboard = new Clipboard('.copy');
	$('.copy').on('click',function(){ alert('File path copied!'); });

	
	$('.summernote').summernote({
		toolbar: [
			// [groupName, [list of button]]
			['style', ['bold', 'italic']],
			['para', ['ul', 'ol']],
		],
		placeholder: 'Type here'
	});	
	$('.search').focus();
	
	var upcomingoptions = {
		valueNames: [ 'classdate', 
						'city',
						'status'
					]
	};
	var upcomingClasses = new List('upcoming-classes', upcomingoptions);
	upcomingClasses.on('searchComplete', function(){
		//console.log(upcomingClasses.update().matchingItems.length);
		$('.classcount').html(upcomingClasses.update().matchingItems.length);
	});
});
</script>

<?php require('templates/footer.php') ?>

