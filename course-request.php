<?php require('inc/lsapp.php') ?>
<?php opcache_reset(); ?>
<?php $user = LOGGED_IN_IDIR ?>
<?php getHeader() ?>

<title>Course Request</title>


<?php getScripts() ?>

<body class="">

<?php getNavigation() ?>

<?php if(canAccess()): ?>
<div class="container mb-3">
<div class="row justify-content-md-center mb-3">
<div class="col-md-8 mb-3">

<h1>Request a Course</h1>
<p>Submit this form and the Learning Support Admin Team will process your request, entering the information into the PSA Learning System.</p>
<!--div class="alert alert-warning">
	Please <a href="#">read this document</a> before submitting a new course request.
</div>-->


<form method="post" action="course-create.php" class="mb-3 pb-3" id="serviceRequestForm">

<input class="Requested" type="hidden" name="Requested" id="Requested" value="<?php echo date('Y-m-d') ?>">
<input class="RequestedBy" type="hidden" name="RequestedBy" id="RequestedBy" value="<?= $user ?>">

<div class="form-group">

<label for="LearningHubPartner">Learning Hub Partner</label>
<button type="button" class="btn btn-dark btn-sm" data-toggle="modal" data-target="#learnhubinfo">
  Info
</button>
<div class="modal fade" id="learnhubinfo" tabindex="-1" aria-labelledby="learnhubinfoLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">LearningHUB</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p><a href="https://learningcentre.gww.gov.bc.ca/hub">LearningHUB</a> is a portal
		website that attempts to aggregate all corporate learning that is available to all BCPS 
		employees, all in one place. Learning Centre is 1 "Learning Partner" of many.</p>
		<p><strong>In order for a course in ELM to be included in the LearningHUB</strong>, a "Learning Partner"
		keyword needs to be added to each course.</p>
		<p>More info coming soon!</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<?php 
// Get the full list of partners
$partners = getPartnersNew();
$platforms = getAllPlatforms();
// Pop the headers off the top
//array_shift($partners);
?>
<select name="LearningHubPartner" id="LearningHubPartner" class="form-control" required>
	<?php foreach($partners as $p): ?>
	<option><?= $p->name ?></option>
	<?php endforeach ?>
</select>
</div>	
<div class="form-group">	
<label for="CourseName">Course Name (Long)</label><br>
<small>(Max# characters, alpha/numeric =200) | Full/Complete title of the course</small>
<input type="text" name="CourseName" id="CourseName" class="form-control" required>
<div class="alert alert-success" id="cnameCharNum"></div>
</diV>


<div class="form-group">
<label for="CourseShort">Course Name (Short)</label><br>
<small>(Max# characters, alpha/numeric= 10) | <a href="#" title="coming soon">Appropriate acronym following LC guidelines</a></small>
<input type="text" name="CourseShort" id="CourseShort" class="form-control" required>
<div class="alert alert-success" id="cnameshortCharNum"></div>
</div>




<div class="form-group">
<label for="Platform">Platform</label><br>
<select name="Platform" id="Platform" class="form-control">
<?php foreach($platforms as $pl): ?>
<option><?= $pl ?></option>
<?php endforeach ?>
</div>
<div class="form-group">
<label><input type="checkbox" name="HUBInclude" id="HUBInclude"> HUB Include?</label>
</div>





<div class="row">
<div class="col">
<div class="form-group">
<label for="CourseOwner">Owner</label><br>
<small>The manager responsible for delivery</small>
<select name="CourseOwner" id="CourseOwner" class="form-control" required>
<?php getPeople($user) ?>
</select>
</div>
<div class="form-group">
<label for="Developer">Developer</label><br>
<small>The assigned developer responsible for materials creation/revisions.</small>
<select class="form-control Developer" name="Developer">
<?php getPeople($user) ?>
</select>
</div>
</div>
<div class="col">
<div class="form-group">
<label for="EffectiveDate">Effective date</label><br>
<small>Date the course should be made visible to learners</small>
<input type="text" name="EffectiveDate" id="EffectiveDate" class="form-control" required>
</div>
</div>
</div>


<div class="form-group">
<label for="CourseDescription">Course Description</label><br>
<small>(Max# characters, alpha/numeric= 254)<br>
The overall purpose of the training in 2 to 3 sentences (maximum) inclusive of:<br>
<ol>
<li>Course duration (# of days)
<li>Target learners
<li>Delivery method.
</ol>
</small>

<textarea name="CourseDescription" id="CourseDescription" class="form-control" required></textarea>
<div class="alert alert-success" id="cdescChar"></div>
</div>

<div class="form-group">
<label for="CourseAbstract">Course Abstract</label><br>
<small>(Max# characters, alpha/numeric=4,000) <br>
<div>An elaboration of the Course Description providing more information on course context, design and development as well as structure. It has the following information:</div>
<ol>
<li>Background – clarifying business case, the strategic intent and the need it addresses
<li>Learning Objectives
<li>Organizational Benefits
<li>Course Development (if relevant to understanding the course: e.g., developed with the Aboriginal community or the Project Management Community of Practice)
<li>Course Structure (if relevant to understanding the course: e.g., six sections (modularized)
<li>Competencies
</ol></small>
<textarea name="CourseAbstract" id="CourseAbstract" class="form-control" required></textarea>
<div class="alert alert-success" id="CANum"></div>
</div>

<div class="form-group">
<label for="Prerequisites">Pre-requisites</label><br>
<small>Any required stand-alone course/s and/or resources that course registrant needs to attend/complete any time prior to attendance of this course</small>
<input type="text" name="Prerequisites" id="Prerequisites" class="form-control">

</div>


<div class="row">
<!-- Topics,Audience,Levels,Reporting -->
<?php
$topics = getAllTopics();
$audience = getAllAudiences ();
$deliverymethods = getDeliveryMethods ();
$levels = getLevels ();
$reportinglist = getReportingList();
?>
<div class="col-6">
<div class="form-group">
<label for="Topics">Topics</label><br>
<select name="Topics" id="Topics" class="form-control">
<option>Select one</option>
<?php foreach($topics as $t): ?>
<option><?= $t ?></option>
<?php endforeach ?>
</select>
</div>
<div class="form-group">
<label for="Audience">Audience</label><br>
<select name="Audience" id="Audience" class="form-control">
<option>Select one</option>
<?php foreach($audience as $a): ?>
<option><?= $a ?></option>
<?php endforeach ?>
</select>
</div>


</div>
<div class="col-6">

<div class="form-group">

<label for="Levels">Levels</label><br>
<select name="Levels" id="Levels" class="form-control">
<option>Select one</option>
<?php foreach($levels as $l): ?>
<option><?= $l ?></option>
<?php endforeach ?>
</select>
</div>

<div class="form-group">
<label for="Reporting<?= $deets[0] ?>">Evaluation</label><br>
	<select name="Reporting" id="Reporting<?= $course[0] ?>" class="form-control">
	<option>Select one</option>
	<?php foreach($reportinglist as $r): ?>
	<option><?= $r ?></option>
	<?php endforeach ?>
	</select>
</div>


</div>
</div>
<div class="form-group">
<label for="Keywords">Keywords</label><br>
<small>Any word not included in the title or short description that could be used by a learner to search for the course</small>
<input type="text" name="Keywords" id="Keywords" class="form-control">
</div>
<div class="row">


<div class="col">

<div class="form-group">

<label>Delivery Method</label><br>
<small>Please select from these options</small>
<div class="card">
<div class="card-body">

<div class="form-check">
  <input type="radio" name="Method" id="classroom" class="form-check-input" value="Classroom" required>
  <label class="form-check-label" for="classroom">Classroom</label>
</div>
<div class="form-check">
  <input type="radio" name="Method" id="elearning" class="form-check-input" value="eLearning" required>
  <label class="form-check-label" for="elearning">eLearning</label>
</div>
<div class="form-check">
  <input type="radio" name="Method" id="blended" class="form-check-input" value="Blended" required>
  <label class="form-check-label" for="blended">Blended</label>
</div>
<div class="form-check">
  <input type="radio" name="Method" id="webinar" class="form-check-input" value="Webinar" required>
  <label class="form-check-label" for="webinar">Webinar</label>
</div>

</div>
</div>
</div>
</div>
</div>


<div class="row">
<div class="col-3">
<div class="form-group">
<label for="MinEnroll">Minimum # of Participants</label><br>
<input type="text" name="MinEnroll" id="MinEnroll" class="form-control" required>
</div>
</div>

<div class="col-3">
<div class="form-group">
<label for="MaxEnroll">Maximum # of Participants</label><br>
<input type="text" name="MaxEnroll" id="MaxEnroll" class="form-control" required>
</div>
</div>

<div class="col">
<div class="form-group">
<label for="elearning">eLearning Course</label><br>
<small>Include the URL link for the course.</small>
<input type="text" name="elearning" id="elearning" class="form-control">
</div>
</div>
</div>

<div class="row">
<div class="col-6">
<div class="form-group"> 	
<label for="PreWork">Pre-work Link</label><br>
<input type="text" name="PreWork" id="PreWork" class="form-control">
<label for="PostWork">Post-work Link</label><br>
<input type="text" name="PostWork" id="PostWork" class="form-control">
</div>
</div>
<div class="col-6">
<div class="form-group"> 	
<label for="ClassDays">How Many Days?</label><br>
<input type="text" name="ClassDays" id="ClassDays" class="form-control" required>
<div class="row">
<div class="col-md-6">
<label for="st">Start time</label>
<input class="form-control starttime" id="st" type="text" name="StartTime" value="" required="required">
</div>
<div class="col-md-6">
<label for="et">End time</label>
<input class="form-control endtime" id="et" type="text" name="EndTime" value="" required="required">
</div>
</div>
</div>
</div>


<div class="col-12">
	<label for="CourseNotes">Notes</label>
	<textarea name="CourseNotes" id="CourseNotes" class="form-control mb-3"></textarea>
</div>

<div class="col-6">
<div class="alert alert-warning">
<label><input type="checkbox" name="WeShip" id="WeShip"> 
Is the Learning Centre responsible for managing &amp; shipping course materials?
</label>
</div>
</div>

<div class="col-6">
<div class="alert alert-info">
<label>
	<input type="checkbox" name="Alchemer" id="Alchemer" value="1"> 
	Does this course use an Alchemer survey?
</label>
</div>
</div>




</div>
	
<button class="btn btn-block btn-primary my-3">Submit New Course Request</button>
</form>
	
</div>
</div>
</div>



<?php else: ?>


<div class="container">
<div class="row justify-content-md-center">
<div class="col-md-3">


	<p class="my-3 p-3" style="background: #000">This is a restricted tool. Please email <a href="mailto:Learning.Centre.Admin@gov.bc.ca?subject=Service Request Access Request">Learning Centre Operations</a> with your IDIR to request access.</p>

	
	</div>
</div>
</div>






<?php endif ?>




<?php require('templates/javascript.php') ?>

<script>
$(document).ready(function(){
	var moment = rome.moment;
	var endtime = rome(document.querySelector('.endtime'), { 
						date: false,
						timeValidator: function (d) {
							var m = moment(d);
							var start = m.clone().hour(07).minute(59).second(59);
							var end = m.clone().hour(16).minute(30).second(1);
							return m.isAfter(start) && m.isBefore(end);
						}
				});
	var starttime = rome(document.querySelector('.starttime'), { 
						date: false,
						timeValidator: function (d) {
							var m = moment(d);
							var start = m.clone().hour(07).minute(59).second(59);
							var end = m.clone().hour(16).minute(00).second(1);
							return m.isAfter(start) && m.isBefore(end);
						}
				});
	$('#CourseName').keyup(function () {
	  var max = 201;
	  var len = $(this).val().length;
	  if (len >= max) {
		$('#cnameCharNum').removeClass('alert-success').addClass('alert-danger').text('Sorry, but you are over the character limit.');
	  } else {
		var char = max - len;
		$('#cnameCharNum').text(char + ' characters left');
	  }
	});
	$('#CourseShort').keyup(function () {
	  var max = 11;
	  var len = $(this).val().length;
	  if (len >= max) {
		$('#cnameshortCharNum').removeClass('alert-success').addClass('alert-danger').text('Sorry, but you are over the character limit.');
	  } else {
		var char = max - len;
		$('#cnameshortCharNum').text(char + ' characters left');
	  }
	});
	$('#CourseDescription').keyup(function () {
	  var max = 255;
	  var len = $(this).val().length;
	  if (len > max) {
		$('#cdescChar').removeClass('alert-success').addClass('alert-danger').text('Sorry, but you are over the character limit.');
	  } else {
		var char = max - len;
		$('#cdescChar').removeClass('alert-danger').addClass('alert-success').text(char + ' characters left');
		
	  }
	});	
});
</script>

<?php require('templates/footer.php') ?>