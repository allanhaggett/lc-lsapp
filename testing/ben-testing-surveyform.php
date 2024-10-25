<!doctype html>
<html lang="en">
<?php require('../inc/lsapp.php') ?>
<?php opcache_reset(); ?>
<?php 
$coureseid = 0;
$courseid = (isset($_GET['courseid'])) ? $_GET['courseid'] : 0 ?>

<?php getHeader() ?>

<title>New Survey Request | LSApp</title>

<?php getScripts() ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">

<style>
.dedinfo { display: none }
.draftinfo { display: none }
.rd-container { z-index: 1000 }  
</style>

<?php getNavigation() ?>
<body class="bg-light-subtle">
<?php if(canAccess()): ?>

<div class="container">
<div class="row">
<div class="col-md-8 mb-3">
<!--
<div class="alert alert-danger">
<h1>TEMPORARILY DISABLED</h1>
<p>Allan is testing new things and would appreciate your patience for a few hours.</p>
<p>If it's an urgent thing, please feel free to email learning.centre.admin@gov.bc.ca</p>
<h2>Thanks!</h2>
</div>
-->
<div class="card">
<div class="card-header">
	<h1 class="card-title">New Survey</h1>
</div>
<div class="card-body">

<p>This form will create a new survey request to be added to Alchemer.</p>
<!--<p>This form will add a new class date for a course that already exists within the PSA Learning System. 
If the course you wish to submit dates for is not listed below, please 
<a href="/lsapp/course-request.php">fill out a new course request</a>. Submitting this form adds a new 
class date to the schedule for administrative processing.</p>-->

<!--<form name="frm_csvEdit" action="class-create.php" method="POST" enctype="multipart/form-data">-->

<input class="ClassID" type="hidden" name="ClassID" value="">

<input class="Requested" type="hidden" name="Requested" value="<?php echo date('Y-m-d-H:i') ?>">
<input class="RequestedBy" type="hidden" name="RequestedBy" value="<?php echo LOGGED_IN_IDIR ?>">
<input class="Modified" type="hidden" name="Modified" value="<?php echo date('Y-m-d-H:i') ?>">
<input class="ModifiedBy" type="hidden" name="ModifiedBy" value="<?php echo LOGGED_IN_IDIR ?>">

<input class="Region" type="hidden" name="Region" value="">
<input class="ItemCode" type="hidden" name="ItemCode" value="">
<input class="EndDate" type="hidden" name="EndDate" value="">
<input class="CourseDays" type="hidden" name="CourseDays" value="">
<input class="CourseCategory" type="hidden" name="CourseCategory" value="">
<input class="CourseCode" type="hidden" name="CourseCode" value="">



<div class="alert alert-warning dedinfo">
<small>Enter information specific to pilot evaluations</small>
</div>

	<ul type="none">
		<li>
			<label><input type="checkbox" name="Dedicated" id="Dedicated" class="" value="Dedicated"> <strong>Pilot?</strong></label>
		</li>

		<li>
			<label><input type="checkbox" name="Dedicated" id="Dedicated" class="" value="Dedicated"> <strong>Consistent Evaluation Approach (CEA/ELE)</strong></label>
		</li>
		<ul type="none">
			<li>
				<label><input type="checkbox" name="Dedicated" id="Dedicated" class="" value="Dedicated"> <strong>Class Survey</strong></label>
			</li>
			<li>
				<label><input type="checkbox" name="Dedicated" id="Dedicated" class="" value="Dedicated"> <strong>3-6 Month Follow-up Survey</strong></label>
			</li>
		</ul>

		<li>
			<label><input type="checkbox" name="Dedicated" id="Dedicated" class="" value="Dedicated"> <strong>Other</strong></label>
		</li>
	</ul>

<p>
Have you completed the PIA?
<br>Yes/No
</p>

<p>If no, here's the form/instructions?</p>

<p>Has it been approved, and saved in the appropriate location?
<br>Yes/No 
</p>




Who are you contacting?
> Have you processed the attendance in ELM (necessary to have learners "Complete") or,
> 



What is your greeting message? (for email that the survey is embedded in)



What questions are you asking:
ELE/CEA check boxes...
OR
Custom Survey ... > What questions, what Answer Option (textbox, likert, checkbox, other..)


<div class="alert alert-info">

	<h1>Pilot Specific:</h1>

	<p>Email Campaign message</p>




</div>





<div class="alert alert-success">

	<h1>Other:</h1>

	<p>Who are you? (learning centre re corporate learning? Program lead re practices...etc.)

	<p>How are we organizing these surveys (eg, under key streams, department)

	<p>Have you collected the contact email addresses for distribution?

	<p>How long is the survey open for? (standard is one week)

	<p>Email Campaign message

	<p>Does there need to be a reminder email? When?


</div>


<div class="alert alert-primary">


	<h1>CEA/ELE Questions:</h1>

	<p>What course is this for?</p>

	<p>Email Campaign message</p>


	<div class="accordion" id="accordionExample">
  <div class="card">
    <div class="card-header" id="headingOne">
      <h2 class="mb-0">
        <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
          Collapsible Group Item #1
        </button>
      </h2>
    </div>

    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
      <div class="card-body">
        Some placeholder content for the first accordion panel. This panel is shown by default, thanks to the <code>.show</code> class.
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="headingTwo">
      <h2 class="mb-0">
        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
          Collapsible Group Item #2
        </button>
      </h2>
    </div>
    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
      <div class="card-body">
        Some placeholder content for the second accordion panel. This panel is hidden by default.
      </div>
    </div>
  </div>
  <div class="card">
    <div class="card-header" id="headingThree">
      <h2 class="mb-0">
        <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
          Collapsible Group Item #3
        </button>
      </h2>
    </div>
    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
      <div class="card-body">
        And lastly, the placeholder content for the third and final accordion panel. This panel is hidden by default.
      </div>
    </div>
  </div>
</div>



	<h2>ELM</h2>

	<ul type="none">
		<li>
			<label><input type="checkbox" name="" id="" class="" value=""> - The learning I needed was easy to find.</label>
		</li>
		<li>
			<label><input type="checkbox" name="" id="" class="" value=""> - The registration process was simple.</label>
		</li>
		<li>
			<label><input type="checkbox" name="" id="" class="" value=""> - Course descriptions were clear.</label>
		</li>
		<li>
			<label><input type="checkbox" name="" id="" class="" value=""> - Emails from the Learning System were helpful.</label>
		</li>
	</ul>

	<h2>Readiness</h2>

	<ul type="none">
		<li>
			Pre-session communications provided clear expectations of my upcoming learning experience
		</li>
		<li>
			The pre-work helped me prepare for the learning
		</li>
	</ul>

	<h2>Learning</h2>
	<ul>
		<li>
			I learned something relevant to my job
		</li>
		<li>
			I learned from my peers
		</li>
		<li>
			I gained up-to-date information about what is expected of me
		</li>
		<li>
			I know where to find help and resources on the subject 
		</li>
		<li>
			I am aware of what I need to do next
		</li>
	</ul>

	<h2>Competence</h2>
	<ul>
		<li>
			I gained new skills
		</li>
		<li>
			I believe that this learning will improve my performance
		</li>
		<li>
			I feel that my behaviours will be more aligned with the BC Public 
			Service values (Integrity, Service, Accountability, Teamwork, 
			Courage, Curiosity, Passion).
		</li>
	</ul>

	<h2>Engagement</h2>
	<ul>
		<li>
			I was active and responsive in group participation
		</li>
		<li>
			Facilitator(s) supported my learning.
		</li>
		<li>
			The learning materials and tools enhanced my learning
		</li>
		<li>
			There were opportunities to practice in a safe environment
		</li>
		<li>
			The technology supported my learning. 
		</li>
		<li>
			The session was accessible for my learning needs.
		</li>
	</ul>

	<h2>Reinforcement</h2>
	<ul>
		<li>
			The key goals of this learning were well defined
		</li>
		<li>
			The learning components complemented each other
		</li>
		<li>
			The sequencing and timing of sessions and self-study worked for me 
		</li>
		<li>
			I felt I was on a learning path together with other learners
		</li>
		<li>
			I gained useful tools and resources to enable application
		</li>
		<li>
			I have already tried some skills and/or tools
		</li>
	</ul>

	<h2>Development</h2>
	<ul>
		<li>
			This learning offered new perspectives
		</li>
		<li>
			I felt supported to develop further 
		</li>
		<li>
			I am committed to take the next step in developing specific behaviours
		</li>
		<li>
			I will consider peer support to help me feel accountable for my development
		</li>
	</ul>

	<h2>Transfer</h2>
	<ul>
		<li>
			I feel encouraged to learn more about this topic
		</li>
		<li>
			I feel confident that I will be able to apply the knowledge and skills I gained from this course to my job
		</li>
		<li>
			I believe my supervisor will support me with opportunities to transfer this learning back on the job 
		</li>	
		<li>
			I plan to apply learning from this program in my supervisory or leadership practice (pls choose N/A if this does not apply to you)
		</li>
	</ul>

	<h2>Impact</h2>
	<ul>
		<li>
			I was able to apply the knowledge and skills I gained from this learning back on the job 
		</li>
	</ul>


</div>




<!--
<div class="alert alert-primary">
	<select id="courseList" name="Course" class="form-control form-control-lg my-3">
	<option>Choose a course &hellip;</option>
	<?php
	// Get the full class list
	$courses = getCourses();
	// Grab the headers
	// $headers = $courses[0];
	// Pop the headers off the top
	//array_shift($courses);
	// Create a temp array for the array_multisort below
	$tmp = array();
	// loop through everything and add the name to
	// the temp array
	foreach($courses as $line) {
		$tmp[] = $line[2];
	}
	// Sort the whole kit and kaboodle by name
	array_multisort($tmp, SORT_ASC, $courses);
	?>
	<?php foreach($courses as $course): ?>
		<?php if($course[1] == 'Active'): ?>
		<?php if($courseid === $course[0]): ?>
		<option data-cid="<?= $course[0] ?>" selected><?= $course[2] ?></option>
		<?php else: ?>
		<option data-cid="<?= $course[0] ?>"><?= $course[2] ?></option>
		<?php endif ?>
		<?php endif ?>
	<?php endforeach ?>
	</select>
</div>
-->

<!--Delivery Method
<div class="alert alert-primary my-3">
	<div class="row">
		<div class="col-md-3">
			<label for="DeliveryMethod">Delivery Method</label>
			<select name="DeliveryMethod" id="DeliveryMethod" class="form-control">
				<option>Classroom</option>
				<option>Webinar</option>
				<option>eLearning</option>
				<option>Blended</option>
			</select>

		</div>
		<div class="col-md-3">
			<label for="Min">Min Enrolment</label>
			<input class="form-control min" id="Min" placeholder="Min" type="number" name="Min" value="" size="5" required="required">
		</div>
		<div class="col-md-3">
			<label for="Max">Max Enrolment</label>
			<input class="form-control max" id="Max" placeholder="Max" type="number" name="Max" value="" size="5" required="required">
		</div>
	</div>
</div>
-->


<!--Start Date & Times
<div class="alert alert-primary my-3">
	<div class="row session">
		<div class="col-md-4">
			<label for="sd" class="sessionlabel">Start Date</label>
			<input class="form-control StartDate date" id="sd" type="text" name="StartDate" value="" required="required">
			<div class="enddate"></div>
			<small>(YYYY-MM-DD)</small>
		</div>
		<div class="col-md-4">
			<!--<label for="t">Times</label>
			<input class="form-control times" id="t" placeholder="Times" type="text" name="Times" value="" required="required">--><!--
			<div class="row">
				<div class="col-md-6">
					<label for="st">Start time</label>
					<input class="form-control starttime" id="st" type="text" name="StartTime" value="" required="required">
					<small>24 hr clock</small>
				</div>
				<div class="col-md-6">
					<label for="et">End time</label>
					<input class="form-control endtime" id="et" type="text" name="EndTime" value="" required="required">
					<small>24 hr clock</small>
				</div>
			</div>
		</div>
-->

<!-- City --><!--
<div class="col-md-4">
<div class="cityinfo">
<label for="VenueCity">City</label>
<select name="VenueCity" id="VenueCity" class="form-control mb-0" required="required">
	<option value="">Choose a City</option>
	<!-- <option>Provided</option>--><!--
	<option data-region="LM">TBD - Other (see notes)</option>
	<option data-region="LM">TBD - Abbotsford</option>
	<option data-region="LM">TBD - Burnaby</option>
	<option data-region="LM">TBD - Burns Lake</option>
	<option data-region="VI">TBD - Campbell River</option>
	<option data-region="SBC">TBD - Castlegar</option>
	<option data-region="SBC">TBD - Chilliwack</option>
	<option data-region="SBC">TBD - Coquitlam</option>
	<option data-region="SBC">TBD - Cranbrook</option>
	<option data-region="NBC">TBD - Dawson Creek</option>
	<option data-region="NBC">TBD - Fort St. John</option>
	<option data-region="SBC">TBD - Kamloops</option>
	<option data-region="SBC">TBD - Kelowna</option>
	<option data-region="LM">TBD - Langley</option>
	<option data-region="NBC">TBD - Mackenzie</option>
	<option data-region="SBC">TBD - Merrit</option>
	<option data-region="VI">TBD - Nanaimo</option>
	<option data-region="SBC">TBD - Nelson</option>
	<option data-region="LM">TBD - New Westminster</option>
	<option data-region="LM">TBD - Penticton</option>
	<option data-region="SBC">TBD - Powell River</option>
	<option data-region="NBC">TBD - Prince George</option>
	<option data-region="NBC">TBD - Quesnel</option>
	<option data-region="NBC">TBD - Smithers</option>
	<option data-region="SBC">TBD - Squamish</option>
	<option data-region="LM">TBD - Surrey</option>
	<option data-region="SBC">TBD - Terrace</option>
	<option data-region="LM">TBD - Vancouver</option>
	<option data-region="SBC">TBD - Vernon</option>
	<option data-region="VI">TBD - Victoria</option>
	<option data-region="NBC">TBD - Williams Lake</option>
	<option data-region="NBC">TBD - Haida Gwaii</option>
	
</select>


</div> <!-- /.cityinfo -->


<!--</div>  /.col -->
<!--</div>  /.row -->
<!--</div>  /.alert-primary -->

<!--
<div class="alert alert-primary my-3 d-none webinarinfo">
	<div class="row">
		<div class="col-md-4">
			<label for="WebinarDate">Webinar Date</label>
			<input class="form-control WebinarDate" type="text" id="WebinarDate" name="WebinarDate" value="">
		</div>
		<div class="col-md-4">
			<label for="WebinarLink">Webinar Link</label>
			<input class="form-control WebinarLink" id="WebinarLink" type="text" name="WebinarLink" value="">
		</div>
	</div>

</div>
-->

<!--
<div class="venueaddy" style="display: none">
<div class="card my-3">
<div class="card-header">
<h3 class="card-title">Venue Details</h3>
</div>
<div class="card-body">
<label>Venue Name
<input type="text" class="form-control" name="VenueName" id="VenueName" value="">
</label>
<label>Address
<input type="text" class="form-control" name="VenueAddress" id="VenueAddress" value="">
</label>
<label>City
<input type="text" class="form-control" name="VenueCityALT" id="VenueCityALT" value="">
</label>
<label>Postal Code
<input type="text" class="form-control" name="VenuePostalCode" id="VenuePostalCode" value="">
</label>
<label>Contact Name
<input type="text" class="form-control" name="VenueContactName" id="VenueContactName" value="">
</label>
<label>Phone
<input type="text" class="form-control" name="VenuePhone" id="VenuePhone" value="">
</label>
<label>Email
<input type="text" class="form-control" name="VenueEmail" id="VenueEmail" value="">
</label>



<div id="sessions"></div>

<!--</div>  /.card-body -->
<!--</div>  /.card 


</div>-->

<!--
<div class="alert alert-primary my-3">

<div class="row">

<div class="col-md-6">

<label for="Facilitating">Facilitating</label>
<textarea class="form-control Facilitating facnote" name="Facilitating" id="Facilitating"></textarea>
<div class="alert alert-light mt-1">
	A space separated list of IDIRs preceeded by an @, for example:<br><br>@ahaggett @jpendray @mtjohnso<br><br>
	<em>When you type @ a list of valid IDIRs will appear and filter based on what you type (e.g. @a will show 
	you all IDIRs that start with "a").</em> If the person you want
	isn't in the list, they likely need to be added to LSApp's people list. 
</div>
</div>
<div class="col-md-6">

<label>Notes</label>
<textarea class="form-control RequestNotes summernote" name="RequestNotes" rows="6" cols="32"></textarea>
<div class="alert alert-warning mt-2">
PLEASE NOTE: If you are providing venue information in the notes,
<strong>please</strong> give us <em>all</em> relevant info, 
including a <em>complete</em> address (don't forget a postal code),
along with a contact name with at least one method of contact
(phone or email).
</div>
</div>
</div>
</div>
<!--<div class="alert alert-warning mt-3">Please note that this is a service request for interfacing with the PSA Learning System (PeopleSoft ELM) <em>only</em> and does not cover financial reporting. Please <a href="#">read this for more info</a>.</div>-->




<!--
<label><input type="checkbox" name="Draft" id="Draft" class="" value="Draft"> <strong>Draft?</strong></label>
<div class="alert alert-warning draftinfo">
	<p>Draft classes will appear highlighted within the timeline
	<em>if you set the toggle on the home page</em>, 
	<strong>but will not be entered into ELM by an LSA</strong>
	until its status is changed to Requested.</p>
	<p>This allows you to enter potential class dates into the system
			so that you can compare them against other courses and dates
			that you may want to schedule as well.</p>
</div>
-->




<input type="submit" name="submitsurvey" class="btn btn-block btn-lg btn-success text-uppercase my-3" value="Submit Survey Request">


</form>


<!--
<div class="alert alert-success mb-3" id="submitted">

		<h2>A few notes on what happens after you click the green button</h2>
		<ul>
			<li>There is no email verification that is sent (yet)
			<li>The class will appear on your dashboard in the the "requested" section
			<li>If your request is urgent, please email 
				<a href="learning.support.admin@gov.bc.ca">learning.support.admin@gov.bc.ca</a>
				and let them know
			<li>The standard processing time for each request is up to 1 week after submission, 
				but that change based on capacity.
			<li>Once a request has been processed, it will no longer appear on your dashboard,
				unless you're also a designated facilitator.
			
		</ul>
</div>
-->
<!--
<div class="alert alert-success my-3" id="lsastat">
	<h2>Current LSA Status</h2>
	<?php
	$courserequests = getRequestedCourses();
	$classrequests = getRequestedClasses();
	$changes = getChanges();
	$courses = count($courserequests);
	$classes = count($classrequests);
	$changes =  count($changes);
	$totalrequests = $courses + $classes + $changes;
	$coursep = ceil(($courses / $totalrequests) * 100);
	$classp = ceil(($classes / $totalrequests) * 100);
	$changep = ceil(($changes / $totalrequests) * 100);
	?>
	<h3><span class="badge badge-dark"><?= $totalrequests ?></span> total requests </h3>

	<div class="progress" style="height: 50px">
		<div class="progress-bar" role="progressbar" style="width: <?= $coursep ?>%" aria-valuenow="<?= $coursep ?>" aria-valuemin="0" aria-valuemax="100">
			<?= $courses ?> course requests 
		</div>
		<div class="progress-bar bg-success" role="progressbar" style="width: <?= $classp ?>%" aria-valuenow="<?= $classp ?>" aria-valuemin="0" aria-valuemax="100">
			<?= $classes ?> class requests 
		</div>
		<div class="progress-bar bg-info" role="progressbar" style="width: <?= $changep ?>%" aria-valuenow="<?= $changep ?>" aria-valuemin="0" aria-valuemax="100">
			<?= $changes ?> change requests 
		</div>
	</div>

</div>

</div>

</div> <!-- ./card -->

</div>

<!--
<div class="col-md-4 mb-3">
	<h3 class="">Upcoming Classes</h3>
	<h4 class="coursename"></h4>
	<ul class="list-group classlist"></ul>
</div>
-->

</div>
</div>


<?php require('templates/javascript.php') ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

<script src="/lsapp/js/summernote-bs4.js"></script>


<script>
<?php 
$courses = getCourses();
array_shift($courses); 
?>
$(document).ready(function(){
	$('#Dedicated').on('click',function(){
		$('.dedinfo').toggle();
	});
	$('#Draft').on('click',function(){
		$('.draftinfo').toggle();
	});
	
	var moment = rome.moment;
	var whorag = rome(document.querySelector('.StartDate'), { time: false, dateValidator: function (d) {
		return moment(d).day() !== 6;
	} });
	var whorag = rome(document.querySelector('.WebinarDate'), { time: false, dateValidator: function (d) {
		return moment(d).day() !== 6;
	} });

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
	
	$('.summernote').summernote({
		toolbar: [
			// [groupName, [list of button]]
			['style', ['bold', 'italic']],
			['para', ['ul', 'ol']],
			['link']
		],
		placeholder: ''
	});	

	$('.facnote').summernote({
		toolbar: [],
		placeholder: '@mention facilitator IDIRs here',
		hint: {
			<?php $peeps = getPeopleAll() ?>
			mentions: [
			<?php foreach($peeps as $p): ?>
			'<?= $p[0] ?>',
			<?php endforeach ?>
			],
			match: /\B@(\w*)$/,
			search: function (keyword, callback) {
			callback($.grep(this.mentions, function (item) {
				return item.indexOf(keyword) == 0;
			}));
			},
			content: function (item) {
				return '@' + item;
			}
		}
		
	});	
	
	
	
	$('#DeliveryMethod').on('change', function(){
		var dm = $(this).val();
		if(dm == 'Webinar') {
			$('.cityinfo').hide();
			$('.webinarinfo').removeClass('d-none');
			$('#VenueCity').removeAttr('required');
			$('.WebinarLink').prop('required',true);
		} else if(dm == 'eLearning') {
			$('.cityinfo').hide();
			$('#VenueCity').removeAttr('required');
			//$('.WebinarLink').prop('required',true);
		} else if(dm == 'Blended') {
			$('.webinarinfo').removeClass('d-none');
			$('.WebinarLink').prop('required',true);
			$('.WebinarDate').prop('required',true);
			$('.cityinfo').show();
			$('#VenueCity').addAttr('required');
		} else {
			$('#VenueCity').prop('required',true);
			$('.WebinarLink').prop('required',false);
			$('.cityinfo').show();
		}
	});


	$('#VenueCity').on('change',function(){
		var city = $(this).val();
		var region = $(this).find(':selected').data('region');
		$('.Region').val(region);
		console.log(region);
		if(city == 'Provided') {
			
			$('.venueaddy').show();
			$('#VenueName').prop('required',true);
			$('#VenueCityALT').prop('required',true);
			$('#VenueAddress').prop('required',true);
			$('#VenuePostalCode').prop('required',true);
			$('#VenueContactName').prop('required',true);
			$('#VenuePhone').prop('required',true);
			$('#VenueEmail').prop('required',true);
			
		} else {
			
			$('.venueaddy').hide();
			$('#VenueName').prop('required',false);
			$('#VenueCityALT').prop('required',false);
			$('#VenueAddress').prop('required',false);
			$('#VenuePostalCode').prop('required',false);
			$('#VenueContactName').prop('required',false);
			$('#VenuePhone').prop('required',false);
			$('#VenueEmail').prop('required',false);
		}
	});
	var cidcheck = getQueryVariable('courseid');
	console.log(cidcheck);
	if(cidcheck) {
		
		$.ajax({type:"GET", 
				url:"data/classes.csv", 
				dataType:"text", 
				success: function(data) {
					$('.classlist').empty();
					//$('.coursename').html(data[6]);
					loadDates(cidcheck,data);
					console.log(data);
					
				}
		});
		$.ajax({type:"GET", 
				url:"data/courses.csv", 
				dataType:"text", 
				success: function(data) {
					loadCourseDeets(cidcheck,data);
				}
		});
	}
	
	$('#courseList').on('change',function(e){
		
		var coureseid = $(this).find(':selected').data('cid');
		
		$.ajax({type:"GET", 
				url:"data/courses.csv", 
				dataType:"text", 
				success: function(data) {
					loadCourseDeets(coureseid,data);
				}
		});
		$.ajax({type:"GET", 
				url:"data/classes.csv", 
				dataType:"text", 
				success: function(data) {
					$('.classlist').empty();
					loadDates(coureseid,data);

				}
		});
		
	});
	function loadCourseDeets(coureseid,courses) {
	
		var  courseArray = $.csv.toArrays(courses);
		var deets = [];
		courseArray.forEach(function(course){
			if(course[0] == coureseid) deets = course;
		});
		//var mm = deets[28] + '/' + deets[29];
		$('.min').val(deets[28]);
		$('.max').val(deets[29]);
		$('.starttime').val(deets[30]);
		$('.endtime').val(deets[31]);
		$('.CourseCode').val(deets[0]);
		$('.CourseDays').val(deets[6]);
		$('.CourseCategory').val(deets[20]);
		var courselink = '<a href="course.php?courseid=' + deets[0] + '">' + deets[2] + '</a>';
		$('.coursename').html(courselink);
	
		// if coursedays > 1 then clone the session coursedays number of times
		// TODO This doesn't work, so I've set it so that it won't execute '== 3.14'
		if(deets[4] == 3.14) {
			$('#sessions').empty();
			var counter = 2;
			while(counter <= deets[4]) {
				var counterClass = 'date' + counter;
				var dateID = 'date' + counter;
				var labelhtml = 'Session ' + counter;
				var calendarID = 'd' + counter;
				var dateclass = '.' + dateID + '';
				$(".session:first").clone().appendTo("#sessions").addClass(counterClass);
				$(dateclass).find('.date').
							  attr('class',calendarID + ' form-control').
							  attr('name',dateID).
							  attr('id',calendarID).
							  attr('data-rome-id', counter);
				
				$(dateclass).find('.sessionlabel').html(labelhtml);
				counter++;
			}
		} else {
			$('#sessions').empty();
		}

	}

	
	function loadDates(courseid,classdates) {

		var datesArray = $.csv.toArrays(classdates);
		//datesArray.sort(function(a,b){return a.getTime() - b.getTime()});
		var deets = [];
		var today = moment().format('YYYY-MM-DD');
		datesArray.forEach(function(cdate){
			if(cdate[5] == courseid && cdate[8] > today) {
				var courseName = cdate[6];
				let clink = '<li class="list-group-item">';
				clink += '<a href="/lsapp/class.php?classid=' + cdate[0] + '">';
				clink += '<span class="badge badge-secondary float-right">';
				clink += cdate[1];
				clink += '</span>';
				clink += '' + moment(cdate[8]).format('MMM Do YY') + ' | ' + cdate[25];
				clink += '</a></li>';
				$('.classlist').append(clink);
			}
		});
		

	}

	
	function getQueryVariable(variable) {
		
		   var query = window.location.search.substring(1);
		   var vars = query.split("&");
		   for (var i=0;i<vars.length;i++) {
				   var pair = vars[i].split("=");
				   if(pair[0] == variable){return pair[1];}
		   }
		   return(false);
	}

});
</script>

<?php else: ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>


<?php require('templates/footer.php') ?>

</html>