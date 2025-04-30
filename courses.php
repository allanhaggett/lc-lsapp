<?php 

$path = 'inc/lsapp.php';
require($path);

$topicid = (isset($_GET['topic'])) ? $_GET['topic'] : 0;
$topic = urldecode($topicid); 
$audienceid = (isset($_GET['audience'])) ? $_GET['audience'] : 0;
$audience = urldecode($audienceid); 
$levelid = (isset($_GET['level'])) ? $_GET['level'] : 0;
$level = urldecode($levelid); 

$catid = (isset($_GET['category'])) ? $_GET['category'] : 0;
$cat = urldecode($catid); 

// Retrieve all the courses 
$courses = getCourses();
array_shift($courses); // Remove the header row from the courses list
// Create our array for courses with the applied filters
$coursesfiltered = [];
$coursesfilteredactive = 0;
// Create a temp array for the array_multisort below
$active_tmp = [];
$inactive_tmp = [];
// loop through everything and add the name to
// the temp array
$sortdir = SORT_ASC;
$sortfield = 2;
if(!empty($_GET['sort']) == 'dateadded') {
	$sortdir = SORT_DESC;
	$sortfield = 13;
}

function sortCourses($a, $b) {
	global $sortdir, $sortfield;
	if ($sortdir == SORT_ASC) {
		return strcmp($a[$sortfield], $b[$sortfield]);
	} else {
		return strcmp($b[$sortfield], $a[$sortfield]);
	}
};

foreach($courses as $line) {
	if ($line[1] == 'Inactive') {
		$inactive_tmp[] = $line;
	} else {
		$active_tmp[] = $line;
	}
}

usort($active_tmp, 'sortCourses');
usort($inactive_tmp, 'sortCourses');

$courses_sorted = [];
if (!empty($_GET['status'])) {
	$status = $_GET['status'];
	if ($status == 'active') {
		$courses_sorted = $active_tmp;
	} elseif ($status == 'inactive') {
		$courses_sorted = $inactive_tmp;
	}
} else {
	$courses_sorted = array_merge($active_tmp, $inactive_tmp);
}

// If a taxonomy has an applied filter, check that the course has it 
// and if it doesn't, don't add it to our list of filtered courses
foreach ($courses_sorted as $c) {
	if (!empty($_GET['level'])) {
		if (!($_GET['level'] == $c[40])) {
			continue;
		}
	} 
	if (!empty($_GET['audience'])) {
		if (!($_GET['audience'] == $c[39])) {
			continue;
		}
	} 
	if (!empty($_GET['topic'])) {
		if (!($_GET['topic'] == $c[38])) {
			continue;
		}
	} 
	if (!empty($_GET['delivery'])) {
		if (!($_GET['delivery'] == $c[21])) {
			continue;
		}
	}
	if (!empty($_GET['processed'])) {
		if (!($c[48] != $_GET['processed'])) {
			continue;	
		}
	}
	if (!empty($_GET['openaccess'])) {
		if (!($c[57] === 'true' || $c[57] === 'on')) {
			continue;
		}
	}
	if (!empty($_GET['hubonly']) && strtolower($_GET['hubonly']) === 'true' && strtolower($c[53]) !== 'yes') {
		continue;
	}
	if ($c[1] == 'Active') {
		$coursesfilteredactive++;
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

$levelget = '';
$audienceget = '';
$processedget = '';
$topicget = '';
$deliveryget = '';
$dmethod = '';
$hubonlyget = '';

if (!empty($_GET['level'])) {
	$levelget .= '&level=' . urlencode($_GET['level']);
} 
if (!empty($_GET['audience'])) {
	$audienceget .= '&audience=' . urlencode($_GET['audience']);
} 
if (!empty($_GET['topic'])) {
	$topicget .= '&topic=' . urlencode($_GET['topic']);
} 
if (!empty($_GET['delivery'])) {
	$deliveryget .= '&delivery=' . urlencode($_GET['delivery']);
	$dmethod = $_GET['delivery'];
} 
if (!empty($_GET['processed'])) {
	$processedget .= '&processed=' . urlencode($_GET['processed']);
} 
$openaccessget = '';
if (!empty($_GET['openaccess'])) {
    $openaccessget .= '&openaccess=' . urlencode($_GET['openaccess']);
}
} 
$hubonlyget = '';
if (!empty($_GET['hubonly'])) {
    $hubonlyget .= '&hubonly=' . urlencode($_GET['hubonly']);
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
<?php if(!empty($coursesfiltered)): ?>
	<?php if(!empty($_GET['status']) && $_GET['status'] == 'inactive'): ?>
		<h1><span class="badge bg-secondary"><?php echo count($inactive_tmp) ?></span> Inactive Courses</h1>
	<?php else: ?>
		<h1><span class="badge bg-primary"><?php echo $coursesfilteredactive ?></span> Active Courses</h1>
	<?php endif; ?>
<?php else: ?>
	<h1><span class="badge bg-primary"><?php echo count($active_tmp) ?></span> <?= $heading ?> Courses</h1>
<?php endif; ?>
</div>
</div>


<div class="row justify-content-md-center">
<div class="col-md-4 col-xl-3">

<input class="search form-control mb-2" placeholder="search">

<div class="mb-2">
	<a class="badge bg-light-subtle text-primary-emphasis" href="./courses.php">All Alphabetically</a> 
	<a class="badge bg-light-subtle text-primary-emphasis" href="./courses.php?sort=dateadded">All Recent</a>
	<a class="badge bg-light-subtle text-primary-emphasis" href="./courses.php?status=active">All Active</a>
	<a class="badge bg-light-subtle text-primary-emphasis" href="./courses.php?status=inactive">All Inactive</a>
</div>
<div class="mb-3">
<?php if (!empty($_GET['openaccess'])): ?>
    <a href="courses.php?<?php echo $processedget . $audienceget . $topicget . $levelget . $deliveryget . $hubonlyget ?>" 
       class="badge bg-dark-subtle text-primary-emphasis">&times; Open Access</a>
<?php else: ?>
    <a href="courses.php?openaccess=true<?php echo $processedget . $audienceget . $topicget . $levelget . $deliveryget . $hubonlyget ?>" 
       class="badge bg-light-subtle text-primary-emphasis">Open Access</a>
<?php endif; ?>

<?php
$hubget = '';
if (!empty($_GET['hubonly']) && strtolower($_GET['hubonly']) === 'true') {
	// Show a "remove" button if active
    $hubquery = $_GET;
    unset($hubquery['hubonly']);
    $querystring = http_build_query($hubquery);
    echo '<a class="badge bg-dark-subtle text-primary-emphasis" href="./courses.php?' . $querystring . '">&times; LearningHUB</a>';
} else {
	// Add HUB filter to current query
    $hubquery = $_GET;
    $hubquery['hubonly'] = 'true';
    $querystring = http_build_query($hubquery);
    echo '<a class="badge bg-light-subtle text-primary-emphasis" href="./courses.php?' . $querystring . '">LearningHUB</a>';
}
?>
</div>


	<!-- Delivery Methods update --> 
	<div class="mb-3">
	<div>Delivery Method</div>
	<?php foreach($deliverymethods as $dm): ?>
	    <?php $active = 'light-subtle text-primary-emphasis'; ?>
		<?php if($dm == $dmethod):
			$active = 'dark-subtle text-primary-emphasis'; ?>
			<a href="courses.php?<?php echo $processedget . $audienceget . $topicget . $levelget . $hubonlyget ?>" 
			class="badge bg-<?= $active ?>"><?= '&times; ' . $dm ?></a>
		<?php else: ?>
			<a href="courses.php?delivery=<?php echo urlencode($dm) . $processedget . $audienceget . $topicget . $levelget . $hubonlyget ?>" 
			class="badge bg-<?= $active ?>"><?= $dm ?></a>
		<?php endif; ?>	
	<?php endforeach; ?>
	</div>
	<div class="mb-3">
	<div>Audience</div>
	<?php foreach($audiences as $a): ?>
	<?php if($a == $audience): ?>
		<?php $active = 'dark-subtle text-primary-emphasis'; ?>
		<a href="courses.php?<?php echo $processedget . $topicget . $levelget . $hubonlyget ?>" 
			class="badge bg-<?= $active ?>"><?= '&times; ' . $a ?></a>
	<?php else: ?>
		<?php $active = 'light-subtle text-primary-emphasis'; ?>
		<a href="courses.php?audience=<?php echo urlencode($a) . $processedget . $topicget . $levelget . $hubonlyget ?>" 
			class="badge bg-<?= $active ?>"><?= $a ?></a>
	<?php endif; ?>	
	<?php endforeach ?>
	</div>


	<!-- Topics update -->
	<div class="mb-3">
	<div>Topics</div>
	<?php foreach($topics as $t): ?>
		<?php $active = 'light-subtle text-primary-emphasis'; ?>
		<?php if($t == $topic): 
			$active = 'dark-subtle text-primary-emphasis'; ?>
			<a href="courses.php?<?php echo $processedget . $audienceget . $levelget . $deliveryget . $hubonlyget ?>" 
			class="badge bg-<?= $active ?>"><?= '&times; ' . $t ?></a>
		<?php else: ?>
			<a href="courses.php?topic=<?php echo urlencode($t) . $processedget . $levelget . $audienceget . $deliveryget . $hubonlyget ?>" 
			class="badge bg-<?= $active ?>"><?= $t ?></a> 
		<?php endif; ?>
	<?php endforeach; ?>
	</div>

</div>



<div class="col-md-5">

<div class="list">
<?php foreach($coursesfiltered as $course): ?>
<?php $cats = explode(',', $course[20]) ?>

<div class="mb-2 p-3 bg-light-subtle border border-secondary-subtle rounded-3">
<div>
	<div class="float-end pl-3 pb-3">
		<?php $statusbg = ($course[1] == 'Inactive') ? 'secondary' : 'primary'; ?>
		<span class="badge text-light-subtle bg-<?php echo $statusbg; ?>"><?php echo $course[1]; ?></span> 
	</div>

	<a class="badge bg-body text-primary-emphasis" href="courses.php?delivery=<?= $course[21] ?>">
			<?= $course[21] ?>
	</a>
	<a class="badge bg-body text-primary-emphasis" href="courses.php?audience=<?= urlencode($course[39]) ?>"><?= $course[39] ?></a>
	<a class="badge bg-body text-primary-emphasis" href="courses.php?topic=<?= urlencode($course[38]) ?>"><?= $course[38] ?></a>
	<?php if($course[53] == 'Yes' || $course[53] == 1): ?>
	<span class="badge bg-body">Learning<strong>HUB</strong></span>
	<?php endif ?>




</div>
<div class="name" style="font-size: 1.3em">
	<a href="/lsapp/course.php?courseid=<?= $course[0] ?>"><?= $course[2] ?></a>
</div>
<div class="mb-3">
<?php if(!empty($course[4])): ?>
	<a class="badge bg-light-subtle text-primary-emphasis" title="Find course in ELM by ITEM-code" target="_blank" href="https://learning.gov.bc.ca/psc/CHIPSPLM_6/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_FND_LRN_FL.GBL?Page=LM_FND_LRN_RSLT_FL&Action=U&KWRD=%22<?= h($course[4]) ?>%22"><?= $course[4] ?></a>
	<?php endif ?>
	<?php if($course[48] == 1): ?>
		<!-- <span class="badge bg-dark-subtle text-primary-emphasis">Processed</span> -->
		<?php endif ?>
		<?php if(!empty($course[50])): ?>
		<a class="badge bg-light-subtle text-primary-emphasis" title="Edit course in ELM" target="_blank" href="https://learning.gov.bc.ca/psp/CHIPSPLM/EMPLOYEE/ELM/c/LM_COURSESTRUCTURE.LM_CI_LA_CMP.GBL?LM_CI_ID=<?= h($course[50]) ?>"><?= $course[50] ?></a>
		<?php endif ?>
</div>
<div class="row bg-light-subtle">
	<div class="col-md-6">
	<div class="p-2">Platform: <a href="#<?php echo urlencode($course[0]) ?>"><?= $course[52] ?></a></div>
<?php $stewsdevs = getCoursePeople($course[0]) ?>
<?php if(!empty($stewsdevs['stewards'][0][2])): ?>
	<!-- <div class="p-2">Steward: <a href="/lsapp/person.php?idir=<?= $stewsdevs['stewards'][0][2] ?>"><?= $stewsdevs['stewards'][0][2] ?></a></div> -->
<?php else: ?>
	<!-- <div class="p-2">No steward set!</div> -->
<?php endif ?>
</div>

<div class="col-md-6">
<div class="p-2">Corp. Partner: <a href="partners/view.php?slug=<?php echo strtolower(preg_replace('/[^a-z0-9\s-]/i', '', str_replace(' ', '-', $course[36]))) ?>"><?= $course[36] ?></a></div>
</div>
</div>

</div>
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
