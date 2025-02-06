<?php require('inc/lsapp.php')?>

<?php getHeader() ?>

<title>Service Requests</title>

<?php getScripts() ?>

<body class="bg-light-subtle">
	
<?php getNavigation() ?>

<div class="container mb-3">

<div class="row justify-content-md-center mb-3">

<div class="col-md-6 mb-3">
<div class="">
<div class="">
<h1 class="">Service Requests</h1>
</div>
<div class="">
<p>In the PSA Learning System there are courses, and courses have classes. 
If your course doesn't exist in <a href="courses.php">the course list</a>, you need probably need to start with <a href="course-request.php">a new course request</a>. </p>
<div class="my-2 p-2 bg-light-subtle rounded-3">
<h2>Courses</h2>

<p>Add a new course into the PSA Learning System. Class dates cannot be scheduled until the course has first been requested and processed.</p>
<!--<div class="alert alert-warning">
	Please <a href="docs/ELM-Course-Hosting-Onboarding-Assessment.docx">read this document</a> before submitting a new course request.
</div>-->
<a href="course-request.php" class="btn btn-block btn-primary btn-lg">Request a New Course</a>
<!--<a href="courses-requested.php" class="btn btn-sm btn-link btn-block">All Requested Courses</a>-->
</div>
<div class="my-2 p-2 bg-light-subtle rounded-3">
<h2>Class Dates</h2>
<p class="">Schedule a class date for a course that already exists in the Learning System.</p>
<div class="alert alert-warning">
	<p>A recent upgrade caused an issue with the old Service Request (SR) form. 
		The new SR form is similar to the old one but works slightly differently.
		Please contact the LST with any questions.</p>
</div>
<p>To submit new requests, please choose from the courses below.</p>

<?php $courses = getCoursesActive() ?>
<?php // Sort the courses by CourseName
usort($courses, function($a, $b) {
	return $a[2] <=> $b[2];
})
?>

<form action="class-bulk-insert.php" method="get">
	<select name="courseid" id="courseid" class="form-select" required>
		<option value="" selected>Choose a course&hellip;</option>
		<?php foreach($courses as $c): ?>
		<option value="<?= $c[0] ?>"><?= $c[2] ?></option>
		<?php endforeach ?>
	</select>
	<button class="btn btn-primary mt-2">Create Service Requests</button>
</form>

<!-- <div class="my-3">
	<a href="class-request.php" class="btn btn-block btn-primary btn-lg">One-at-a-time Request Form</a>
</div> -->
<!-- <p>If you want to submit <em>multiple requests at the same time</em>, we have you covered! Simply
	<a href="/lsapp/courses.php?sort=dateadded">navigate to the course</a> you want to submit for and click the "New Date Requests" button.
	You'll be taken to a form that allows you to submit as many requests in one shot as you like.</p> -->
<!--<a href="classes-requested.php" class="btn btn-sm btn-link btn-block">All Requested Classes</a>-->
</div>
<!-- <div class="my-2 p-2 bg-light-subtle rounded-3">
<h2>Changes</h2>
<p>Every class date/course has its own page. The form for submitting a change request to a class date/course 
is on its page. Change request forms for materials and checklists are planned, but not yet functional. 
To request a change to those aspects of a course, please submit an email explaining what you'd like changed 
to learning.centre.admin@gov.bc.ca</p>
<h2>Venues</h2>
<p>Do you know of a venue that would be a good fit for delivering courses? Suggest it, and the venues coordinator will review 
and possibly include it in the list.</p>
<a href="venue-request.php" class="btn btn-block btn-primary btn-lg">Suggest a Venue</a>
<hr>
</div> -->

</div>
</div> <!-- /.card -->
</div>
</div>

<?php require('templates/javascript.php') ?>

<?php require('templates/footer.php'); ?>