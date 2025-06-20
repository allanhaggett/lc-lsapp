<?php require('inc/lsapp.php')?>

<?php getHeader() ?>

<title>Service Requests</title>

<?php getScripts() ?>

<body class="bg-light-subtle">
	
<?php getNavigation() ?>

<div class="container mb-3">

<div class="row justify-content-md-center mb-3">

<div class="col-md-6 mb-3">

	<h1 class="">Service Requests</h1>

	<div class="">
		<p>In the PSA Learning System there are courses, and courses have classes. 
		If your course doesn't exist in <a href="courses.php">the course list</a>, 
		you need probably need to start with <a href="course-request.php">a new 
		course request</a>.</p>
	</div>

	<div class="my-3 p-3 bg-light-subtle border border-secondary-subtle rounded-3 shadow-sm">
		<h2>New Courses</h2>

		<p>Add a new course into the PSA Learning System. Class dates cannot be scheduled until the course has first been requested and processed.</p>
		<!--<div class="alert alert-warning">
			Please <a href="docs/ELM-Course-Hosting-Onboarding-Assessment.docx">read this document</a> before submitting a new course request.
		</div>-->
		<a href="course-request.php" class="btn btn-block btn-primary btn-lg">Request a New Course</a>
		<!--<a href="courses-requested.php" class="btn btn-sm btn-link btn-block">All Requested Courses</a>-->
	</div>

	<div class="my-3 p-3 bg-light-subtle border border-secondary-subtle rounded-3 shadow-sm">
		<h2>Existing Courses and Classes</h2>
		
		<p class="">Request changes to an existing course or new class offerings.</p>
		
		<p>To start, please choose a course.</p>

		<?php $courses = getCoursesActive() ?>
		<?php // Sort the courses by CourseName
			usort($courses, function($a, $b) {
				return $a[2] <=> $b[2];
			});
		?>

		<form action="requests-controller.php" method="post">
			<select name="courseid" id="courseid" class="form-select mb-3" required>
				<option value="" selected>Choose a course&hellip;</option>
				<?php foreach($courses as $c): ?>
					<option value="<?= $c[0] ?>"><?= $c[2] ?></option>
				<?php endforeach ?>
			</select>
			<p>Next, choose the type of request.</p>
			<div class="form-check m-3">
				<input class="form-check-input" type="radio" name="categoryid" id="changeCourse" value="Course Change" required>
				<label class="form-check-label" for="changeCourse">Change a Course</label>
			</div>
			<div class="form-check m-3">
				<input class="form-check-input" type="radio" name="categoryid" id="newClass" value="New Class Date" required>
				<label class="form-check-label" for="newClass">New Class Offering</label>
			</div>
			<button class="btn btn-primary">Create Request</button>
		</form>
	</div>

</div> <!-- /col -->
</div> <!-- /row -->
</div> <!-- /container -->

<?php require('templates/javascript.php') ?>

<?php require('templates/footer.php'); ?>

</body>