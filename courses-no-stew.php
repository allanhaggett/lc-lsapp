<?php 

$path = $_SERVER['DOCUMENT_ROOT'] . '\lsapp\inc\lsapp.php';
require($path);
$topicid = (isset($_GET['topic'])) ? $_GET['topic'] : 0;
$topic = urldecode($topicid); 
$audienceid = (isset($_GET['audience'])) ? $_GET['audience'] : 0;
$audience = urldecode($audienceid); 
$levelid = (isset($_GET['level'])) ? $_GET['level'] : 0;
$level = urldecode($levelid); 

$catid = (isset($_GET['category'])) ? $_GET['category'] : 0;
$cat = urldecode($catid); 


// Retrieve all the active courses 
$courses = getCoursesActive($catid);
// Create our array for courses with the applied filters
$coursesfiltered = array();
// Create a temp array for the array_multisort below
$tmp = [];
// loop through everything and add the name to
// the temp array
$sortdir = SORT_ASC;
$sortfield = 2;
if($_GET['sort'] == 'dateadded') {
	$sortdir = SORT_DESC;
	$sortfield = 0;
}
foreach($courses as $line) {
	$tmp[] = $line[$sortfield];
}
array_multisort($tmp, $sortdir, $courses);

// If a taxonomy has an applied filter, check that the course has it 
// and if it doesn't, don't add it to our list of filtered courses
foreach ($courses as $c) {
	if ($_GET['level']) {
		if (!($_GET['level'] == $c[40])) {
			continue;
		}
	} 
	if ($_GET['audience']) {
		if (!($_GET['audience'] == $c[39])) {
			continue;
		}
	} 
	if ($_GET['topic']) {
		if (!($_GET['topic'] == $c[38])) {
			continue;
		}
	} 
	if ($_GET['delivery']) {
		if (!($_GET['delivery'] == $c[21])) {
			continue;
		}
	}
	if ($_GET['processed']) {
		if (!($c[48] != $_GET['processed'])) {
			continue;	
		}
	}
	array_push($coursesfiltered, $c);
}

$categories = getCategories();
array_shift($categories);


$topics = getAllTopics();
$audiences = getAllAudiences();
$deliverymethods = getDeliveryMethods ();
$levels = getLevels ();
$reportinglist = getReportingList();



if ($_GET['level']) {
	$levelget .= '&level=' . urlencode($_GET['level']);
} 
if ($_GET['audience']) {
	$audienceget .= '&audience=' . urlencode($_GET['audience']);
} 
if ($_GET['topic']) {
	$topicget .= '&topic=' . urlencode($_GET['topic']);
} 
if ($_GET['delivery']) {
	$deliveryget .= '&delivery=' . urlencode($_GET['delivery']);
} 
if ($_GET['processed']) {
	$processedget .= '&processed=' . urlencode($_GET['processed']);
} 




?>
<?php getHeader() ?>


<title>Learning Centre Course Catalog</title>

<?php getScripts() ?>


<body>
<?php getNavigation() ?>


<div id="courses">
<div class="container-fluid">
<div class="row justify-content-md-center">

<div class="col-md-8">
<h1>Courses Without Stewards</h1>
</div>
</div>


<div class="row justify-content-md-center">




<div class="col-md-5">

<div class="list">


<?php foreach($coursesfiltered as $course): ?>
<?php $stewsdevs = getCoursePeople($course[0]) ?>
<?php if(empty($stewsdevs['stewards'][0][2])): ?>
<?php $cats = explode(',', $course[20]) ?>
<div class="mb-2 p-3 bg-light-subtle border border-secondary-subtle rounded-3">
<div>
	<?php if(empty($course[48])): ?>
	<div class="float-end pl-3 pb-3">
		<form method="get" action="course-tax-claim.php" class="d-inline claimform">
			<input type="hidden" name="cid" id="cid" value="<?= $course[0] ?>">
			<input type="submit" class="btn btn-sm bg-light-subtle ml-3" value="Claim">
		</form>

		<a href="/lsapp/person.php?idir=<?= $course[49] ?>"><?= $course[49] ?></a>
	</div>
	<?php endif ?>
	<a class="badge bg-dark-subtle text-primary-emphasis" href="courses.php?delivery=<?= $course[21] ?>">
			<?= $course[21] ?>
	</a>
	<a class="badge bg-dark-subtle text-primary-emphasis" href="courses.php?audience=<?= urlencode($course[39]) ?>"><?= $course[39] ?></a>
	<a class="badge bg-dark-subtle text-primary-emphasis" href="courses.php?level=<?= urlencode($course[40]) ?>"><?= $course[40] ?></a>
	<a class="badge bg-dark-subtle text-primary-emphasis" href="courses.php?topic=<?= urlencode($course[38]) ?>"><?= $course[38] ?></a>
	




</div>
<div class="name" style="font-size: 1.3em">
	<a href="/lsapp/course.php?courseid=<?= $course[0] ?>"><?= $course[2] ?></a>
</div>
<div class="mb-3">
<?php if(!empty($course[4])): ?>
	<a class="badge bg-dark-subtle text-primary-emphasis" title="Find course in ELM by ITEM-code" target="_blank" href="https://learning.gov.bc.ca/psc/CHIPSPLM_6/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_FND_LRN_FL.GBL?Page=LM_FND_LRN_RSLT_FL&Action=U&KWRD=%22<?= h($course[4]) ?>%22"><?= $course[4] ?></a>
	<?php endif ?>
	<?php if($course[48] == 1): ?>
		<!-- <span class="badge bg-dark-subtle text-primary-emphasis">Processed</span> -->
		<?php endif ?>
		<?php if(!empty($course[50])): ?>
		<a class="badge bg-dark-subtle text-primary-emphasis" title="Edit course in ELM" target="_blank" href="https://learning.gov.bc.ca/psp/CHIPSPLM/EMPLOYEE/ELM/c/LM_COURSESTRUCTURE.LM_CI_LA_CMP.GBL?LM_CI_ID=<?= h($course[50]) ?>"><?= $course[50] ?></a>
		<?php endif ?>
</div>
<div class="row bg-light-subtle">
	<div class="col-md-6">

<?php if(!empty($stewsdevs['stewards'][0][2])): ?>
	<div class="p-2">Steward: <a href="/lsapp/person.php?idir=<?= $stewsdevs['stewards'][0][2] ?>"><?= $stewsdevs['stewards'][0][2] ?></a></div>
<?php else: ?>
	<div class="p-2">No steward set!</div>
<?php endif ?>
</div>

<div class="col-md-6">
<div class="p-2">Corp. Partner: <a href="learning-hub-partner.php?partnerid=<?php echo urlencode($course[36]) ?>"><?= $course[36] ?></a></div>
</div>
</div>

</div>
<?php endif ?>
<?php endforeach ?>


</div> <!-- /.card-columns -->
</div> <!-- /#courses -->
</div>
</div>
</div>
<?php require('templates/javascript.php') ?>

<script>
	var options = {
		valueNames: [ 'name', 
						'delivery',
						'category'
					]
	};
	var courses = new List('courses', options);
	


$('.claimform').on('submit',function(e){

	e.preventDefault();

	var form = $(this);
	var url = form.attr('action');

	$.ajax({
		type: "GET",
		url: url,
		data: form.serialize(),
		success: function(returndata)
		{
			userlink = '<a href="person.php?idir='+returndata+'">'+returndata+'</a>';
			console.log(userlink);
			form.after(userlink);
			//form.remove();
			//form.closest('tr').fadeOut().remove();
			
		},
		statusCode: 
		{
			403: function() {
				form.after('<div class="alert alert-warning">You must be logged in.</div>');
			}
		}});
	

});


</script>
<?php require('templates/footer.php') ?>
