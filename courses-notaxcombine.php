<?php 

$path = $_SERVER['DOCUMENT_ROOT'] . '\lsapp\inc\lsapp.php';
require($path);
$topicid = (isset($_GET['topic'])) ? $_GET['topic'] : 0;
$topic = urldecode($topicid); 
$audienceid = (isset($_GET['audience'])) ? $_GET['audience'] : 0;
$audience = urldecode($audienceid); 
$levelid = (isset($_GET['level'])) ? $_GET['level'] : 0;
$level = urldecode($levelid); 
$reportid = (isset($_GET['reporting'])) ? $_GET['reporting'] : 0;
$reporting = urldecode($reportid); 
$catid = (isset($_GET['category'])) ? $_GET['category'] : 0;
$cat = urldecode($catid); 

if ($topic) {
	$courses = getCoursesByTopic($topic);
	$heading = $topic;
} elseif($audience) {
	$courses = getCoursesByAudience($audience);
	$heading = $audience;
} elseif($level) {
	$courses = getCoursesByLevels($level);
	$heading = $level;
} elseif($reporting) {
	$courses = getCoursesByReporting($reporting);
	$heading = $reporting;
} elseif($catid) {
	$courses = getCoursesByCategory($cat);
	$heading = $cat;
} else {
	$courses = getCoursesActive($catid);
}
$categories = getCategories();
array_shift($categories);

$f = fopen('paths.csv','r');
fgetcsv($f);
$paths = array();
while($path = fgetcsv($f)) {
		array_push($paths,$path);
}

$audittype = getAuditTypes();
$topics = getAllTopics();
$audiences = getAllAudiences();
$deliverymethods = getDeliveryMethods ();
$levels = getLevels ();
$reportinglist = getReportingList();
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

// Sort the whole kit and kaboodle by name
array_multisort($tmp, $sortdir, $courses);
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
<?php if($heading): ?>
<h1><span class="badge bg-primary"><?php echo count($courses) ?></span> <?= $heading ?> courses</h1>
<?php else: ?>
	<h1><span class="badge bg-primary"><?php echo count($courses) ?></span> Courses</h1>
<?php endif ?>
</div>
</div>
<div class="row justify-content-md-center">
<div class="col-md-4 col-xl-3">

<input class="search form-control mb-2" placeholder="search">
<div class="mb-3">
	<a class="badge bg-light-subtle " href="./courses.php">All Alphabetically</a> 
	<a class="badge bg-light-subtle " href="./courses.php?sort=dateadded">All Recent</a>
	<a class="badge bg-light-subtle " href="./courses-wtaxup.php">Taxonomy Updater</a>
</div>
<div class="mb-2">
	<div>Levels</div>
	<div class="mb-3">
	<?php foreach($levels as $l): ?>
	<?php $active = 'light ' ?>
	<?php if($l == $level) $active = 'dark text-white' ?>
	<a href="courses.php?level=<?php echo urlencode($l) ?>" 
		class="badge bg-<?= $active ?>">
			<?= $l ?>
	</a> 
	<?php endforeach ?>
	</div>
	<div class="mb-3">
	<div>Audience</div>
	<?php foreach($audiences as $a): ?>
	<?php $active = 'light ' ?>
	<?php if($a == $audience) $active = 'dark text-white' ?>
	<a href="courses.php?audience=<?php echo urlencode($a) ?>" 
		class="badge bg-<?= $active ?>">
			<?= $a ?>
	</a> 
	<?php endforeach ?>
	</div>
	<div class="mb-3">
	<div>Topics</div>
	<?php foreach($topics as $t): ?>
	<?php $active = 'light ' ?>
	<?php if($t == $topic) $active = 'dark text-white' ?>
	<a href="courses.php?topic=<?php echo urlencode($t) ?>" 
		class="badge bg-<?= $active ?>">
			<?= $t ?>
	</a> 
	<?php endforeach ?>

	</div>
	<div class="mb-3">
	
	<div>Reporting</div>
	<?php foreach($reportinglist as $r): ?>
	<?php $active = 'light ' ?>
	<?php if($r == $reporting) $active = 'dark text-white' ?>
	<a href="courses.php?reporting=<?php echo urlencode($r) ?>" 
		class="badge bg-<?= $active ?>">
			<?= $r ?>
	</a> 
	<?php endforeach ?>
	</div>
	<details class="my-5">
		<summary>Old Categories</summary>
	<?php foreach($categories as $category): ?>
	<a href="courses.php?category=<?php echo urlencode($category[1]) ?>" 
		class="badge bg-light-subtle ">
		<?= $category[1] ?>
	</a> 
	<?php endforeach ?>
	</details>


</div>
</div>


<div class="col-md-5">

<div class="list">
<?php foreach($courses as $course): ?>
<?php $cats = explode(',', $course[20]) ?>

<div class="mb-2 p-3 bg-light-subtle rounded-3">

<!-- <div class="badge bg-light-subtle  delivery"><?= $course[21] ?></div> -->
<div class="name">
	<a href="/lsapp/course.php?courseid=<?= $course[0] ?>"><?= $course[2] ?></a>
</div>

<div>
	<a class="badge bg-light-subtle " href="courses.php?audience=<?= urlencode($course[39]) ?>"><?= $course[39] ?></a>
	<a class="badge bg-light-subtle " href="courses.php?level=<?= urlencode($course[40]) ?>"><?= $course[40] ?></a>
	<a class="badge bg-light-subtle " href="courses.php?topic=<?= urlencode($course[38]) ?>"><?= $course[38] ?></a>
	<!-- <a href="courses.php?reporting=<?= urlencode($course[41]) ?>"><?= $course[41] ?></a> -->
</div>

</div>
<?php endforeach ?>


</div> <!-- /.card-columns -->
</div> <!-- /#courses -->
</div>
</div>
</div>
<script src="/lsapp/js/jquery-3.4.0.min.js"></script>
<script src="/lsapp/js/popper.min.js"></script>
<script src="/lsapp/js/bootstrap.min.js"></script>
<script src="/lsapp/js/list.min.js"></script>
<script>
	var options = {
		valueNames: [ 'name', 
						'delivery',
						'category'
					]
	};
	var courses = new List('courses', options);
	
</script>
<?php require('templates/footer.php') ?>
