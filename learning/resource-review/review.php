<?php 
opcache_reset();

require('../../lsapp/inc/lsapp.php');
require('../../lsapp/inc/Parsedown.php');

$idir = stripIDIR($_SERVER["REMOTE_USER"]);
// $idir = 'shamitch';
$file = '../../lsapp/data/reviews/' . $_GET['auditid'] . '.json';
$aud = file_get_contents($file);
$audit = json_decode($aud);
if($audit->createdby != $idir && !canAccess()) {
	echo 'Sorry, you cannot see anyone\'s review but your own.';
	exit;
}
//array_pop($audit);
$Parsedown = new Parsedown();

$audittype = getAuditTypes();
$topics = getAllTopics();
$audience = getAllAudiences ();
$deliverymethods = getDeliveryMethods ();
$levels = getLevels ();
$reportinglist = getReportingList();
$userreviews = getUserReviews($idir);


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
<?php getHeader() ?>

<title>Resource Review</title>

<?php getScripts() ?>

<?php //getNavigation() ?>
<body class="">
<nav class="navbar navbar-expand-lg bg-body-tertiary mb-5">
	<div class="container-fluid">
		<span class="navbar-brand">Learning Resource Review</span>
		<ul class="navbar-nav me-auto mb-2 mb-lg-0">
		<li class="nav-item dropdown">
            <button class="btn btn-link nav-link ml-3 py-2 px-0 px-lg-2 dropdown-toggle d-flex align-items-center" id="bd-theme" type="button" aria-expanded="false" data-bs-toggle="dropdown" data-bs-display="static" aria-label="Toggle theme (dark)">
              <span class="theme-icon-active"><i class="me-2"></i></span>
              <span class="d-none ms-2" id="bd-theme-text">Toggle theme</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bd-theme-text">
              <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="light" aria-pressed="false">
                  <i class="bi bi-sun-fill me-2" data-icon="bi-sun-fill"></i>
                  Light
                  <i class="bi bi-check2 d-none" data-icon="check2"></i>
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item d-flex align-items-center active" data-bs-theme-value="dark" aria-pressed="true">
                  <i class="bi bi-moon-stars-fill me-2" data-icon="bi-moon-stars-fill"></i>
                  Dark
                  <i class="bi bi-check2 d-none" data-icon="check2"></i>
                </button>
              </li>
              <li>
                <button type="button" class="dropdown-item d-flex align-items-center" data-bs-theme-value="auto" aria-pressed="false">
                  <i class="bi bi-circle-half me-2" data-icon="bi-circle-half"></i>
                  Auto
                  <i class="bi bi-check2 d-none" data-icon="check2"></i>
                </button>
              </li>
            </ul>
        </li>
		</ul>
	</div>
</nav>
<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-4 col-lg-3">
	<a href="index.php" class="btn btn-primary float-end mb-3 d-block">New Review</a>
	<?php if(canAccess()): ?>
	<div class="mb-4"><a href="/lsapp/audits.php" class="btn btn-light">All Reviews</a></div>
	<?php endif ?>
	<h2 class="mb-3">Your Reviews</h2>
<?php foreach($userreviews as $ur): ?>
	<div class="my-2 p-2 bg-light-subtle rounded-3">
		<a href="review.php?auditid=<?= $ur[0] ?>">
			<?= $ur[4] ?><br>
			<span class="badge bg-secondary text-white"><?= $ur[1] ?></span>
		</a>
	</div>
<?php endforeach ?>
</div>
<div class="col-md-8 col-lg-7">

<style>
	label { font-weight: bold; }
	.form-group {
		margin: 1.5em 0;
	}
</style>
<form action="/lsapp/audit-delete.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this review?');">
    <input type="hidden" name="filename" value="<?= $ur[0] ?>">
    <button type="submit">Delete</button>
</form>
<a href="review-update-form.php?auditid=<?= $audit->AuditID ?>" class="btn btn-secondary float-end">Edit Review</a>

<!-- <div class="row mt-3 mb-5 sticky-top bg-white p-3 rounded-3 shadow-lg">
	<div class="col-3">
		<a class="font-weight-bold" href="#part1">Part 1</a>
		<div style="font-size: 14px">(8 questions)</div>
	</div>
	<div class="col-3">
		<a class="font-weight-bold" href="#part2">Part 2</a>
		<div style="font-size: 14px">(6 questions)</div>
	</div>
	<div class="col-3">
		<a class="font-weight-bold" href="#part3">Part 3</a>
		<div style="font-size: 14px">(7 questions)</a></div>
	</div>
	<div class="col-3">
		<a class="font-weight-bold" href="#part4">Part 4</a>
		<div style="font-size: 14px">(7 questions)</div>
	</div>
</div> -->

<div>
	<?php if($audit->Status == "Completed"): ?>
	<span class="badge bg-success text-white"><?= $audit->Status ?></span> 
	<?php else: ?>
	<span class="badge bg-warning text-black"><?= $audit->Status ?></span> 
	<?php endif ?>

	<?= $audit->resourceType ?> Review</div>

	
	<?php if(!empty($audit->LSAppCourseid)): ?>
		<h1>
			
				<?= $audit->ResourceName ?? 'Something is wrong. Contact an admin.' ?>
			
		</h1>
		<?php else: ?>
			<h1>
				
				<?= $audit->ResourceName ?? 'Something is wrong. Contact an admin.' ?>
				
			</h1>
			
			<?php endif ?>

			<h2 class="" id="part1">Part 1: Overview</h2>




<div class="my-3 bg-light-subtle p-3 py-4 rounded-3">


<div class="row mb-3">

<div class="col-md-3">


<label for="Audience">Audience</label><br>
<?= $audit->Audience ?>


</div>
<div class="col-md-6">


	<label for="Topic">Topic</label><br>
    <?= $audit->Topic ?>
	


</div>
<div class="col-md-3">

	<label class="" for="DeliveryMethod">Delivery Method</label>
    <?= $audit->DeliveryMethod ?>
	
</div>
</div>





<div class="row">

<div class="col-md-6">


<label for="ResourceOwner">Owner</label> 
<a href="https://learningcentre.gww.gov.bc.ca/learninghub/corporate-learning-partners/"
	target="_blank"
	style="font-size: 14px">
		LearningHUB Partner List
</a>
<br>
<?= $audit->ResourceOwner ?>


</div>
<div class="col-md-3">


<label for="Duration">Duration</label><br>
<?= $audit->Duration ?? '' ?>


</div>
</div>


</div> <!-- /.bg-light-subtle -->


<?php if($audit->resourceType == 'Course') $showcourseinfo = '' ?? 'd-none' ?>
<div class="my-3 bg-light-subtle p-3 py-4 rounded-3 <?= $showcourseinfo ?>" id="">

<label for="OverallCourseOutcomes">Overall Course Outcomes</label><br>
<div><?= $Parsedown->text($audit->OverallCourseOutcomes)  ?></div>



<div class="mt-3">
<label for="Notes">Notes</label><br>

<div><?= $Parsedown->text($audit->Notes ?? '' ) ?></div>
</div>

</div>


<h2 class="mt-5" id="part2">Part 2: Priorities and Impact</h2>

<div class="my-3 bg-light-subtle p-3 py-4 rounded-3">

<label for="MeasurableOutcomesForOrganization">Measurable outcomes for the organization</label> (not for the learning)<br>
<div><?= $Parsedown->text($audit->MeasurableOutcomesForOrganization ?? '')  ?></div>
</div>

<div class="my-3 bg-light-subtle p-3 py-4 rounded-3">

<label for="CurrentOrganizationalMeasureBaseline">If there is a current organizational measure, what is the baseline?</label><br>
<div><?= $Parsedown->text($audit->CurrentOrganizationalMeasureBaseline ?? '')  ?></div>


</div>

<div class="bg-light-subtle p-3 py-4 rounded-3">

<label for="LearningMetric">What is the learning metric?</label>
<div><?= $Parsedown->text($audit->LearningMetric ?? '')  ?></div>


</div>


<div class="my-3 bg-light-subtle p-3 py-4 rounded-3">

<label for="SupportChangingSkills">Does this learning support changing skills needed of our employees? If so, how?</label>
<div><?= $Parsedown->text($audit->SupportChangingSkills ?? '')  ?></div>


</div>








<h2 id="part3">Part 3: Learning Principle Alignment</h2>
<h3>How does this align to our 7 BCPS learning priorities and goals?</h3>
<!-- [free response – Complete this with reference to evidence of each BCPS Learning Principle. Each principle scores a green plus sign for demonstrating the principle, and a red minus sign for a lack of evidence of that BCPS learning principle.. Circle one, then elaborate in the table with evidence from the course.  -->
<!-- <p>Explain the gap or include the evidence you find in the course.</p> -->




<style>
meter {
	min-width: 300px;
	width: 100%;
}
</style>

<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-12">
	<div style="font-size: 1.2rem;">Overall Alignment Precent</div>
	<div style="font-weight: bold; font-size: 1.3em; padding: 1em;">
	<?php if($audit->BCPSPrincipleOverallPercent == 25): ?>
		25% - Significant work to align<br>
		<meter id="fuel" min="0" max="100" low="0" high="100" optimum="100" value="25">25% - Significant work to align</meter>
	<?php elseif($audit->BCPSPrincipleOverallPercent == 50): ?>
		50% - Partially in alignment<br>
		<meter id="fuel" min="0" max="100" low="0" high="100" optimum="100" value="50">50% - Partially in alignment</meter>
	<?php elseif($audit->BCPSPrincipleOverallPercent == 75): ?>
		75% - Mostly in alignment<br>
		<meter id="fuel" min="0" max="100" low="0" high="100" optimum="100" value="75">75% - Mostly in alignment</meter>
	<?php elseif($audit->BCPSPrincipleOverallPercent == 100): ?>
		100% - Completely in alignment<br>
		<meter id="fuel" min="0" max="100" low="0" high="100" optimum="100" value="100">100% - Completely in alignment</meter>
	<?php else: ?>
		Alignment Unknown! Please <a href="/learning/resource-review/review-update-form.php?auditid=<?= $audit->AuditID ?>#overallprinciplepercent">edit</a> and update.
	<?php endif ?>

	</div>
	<!-- <meter id="fuel" min="0" max="100" low="33" high="66" optimum="80" value="50">at 50/100</meter> -->

</div>
</div>
<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">1. Place learners at the center in the design and experience of learning</div>
<?php foreach($audit->BCPSPrincipleLearnerCentre as $yn): ?>
    <span style="display: inline-block; font-weight: bold; font-size: 1.3em; padding: 1em;"><?= $yn ?></span> 
<?php endforeach ?>
</div>
<div class="col-md-6">


<div><?= $Parsedown->text($audit->BCPSPrincipleLearnerCentreSupport ?? '')  ?></div>
<div><?= $Parsedown->text($audit->BCPSPrincipleLearnerCentreSupportYes ?? '')  ?></div>
<div><?= $Parsedown->text($audit->BCPSPrincipleLearnerCentreSupportNo ?? '')  ?></div>

</div>
</div>



<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">2. Be aligned with business priorities and goals</div>
<?php foreach($audit->BCPSPrincipleAlignedBusinessPriority as $yn): ?>
    <span style="display: inline-block; font-weight: bold; font-size: 1.3em; padding: 1em;"><?= $yn ?></span> 
<?php endforeach ?>
</div>
<div class="col-md-6">


<div><?= $Parsedown->text($audit->BCPSPrincipleAlignedBusinessPrioritySupport ?? '')  ?></div>
<div><?= $Parsedown->text($audit->BCPSPrincipleAlignedBusinessPrioritySupportYes ?? '')  ?></div>
<div><?= $Parsedown->text($audit->BCPSPrincipleAlignedBusinessPrioritySupportNo ?? '')  ?></div>

</div>
</div>


<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">3. Be available & just-in-time for learners</div>
<?php foreach($audit->BCPSPrincipleAvailableJIT as $yn): ?>
    <span style="display: inline-block; font-weight: bold; font-size: 1.3em; padding: 1em;"><?= $yn ?></span> 
<?php endforeach ?>
</div>
<div class="col-md-6">

<div><?= $Parsedown->text($audit->BCPSPrincipleAvailableJITSupport ?? '') ?></div>
<div><?= $Parsedown->text($audit->BCPSPrincipleAvailableJITSupportYes ?? '') ?></div>
<div><?= $Parsedown->text($audit->BCPSPrincipleAvailableJITSupportNo ?? '') ?></div>

</div>
</div>


<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">4. Empower our people to grow their career within the BCPS</div>
<?php foreach($audit->BCPSPrincipleEmpowerGrowth as $yn): ?>
    <span style="display: inline-block; font-weight: bold; font-size: 1.3em; padding: 1em;"><?= $yn ?></span> 
<?php endforeach ?>
</div>
<div class="col-md-6">

<div><?= $Parsedown->text($audit->BCPSPrincipleEmpowerGrowthSupport ?? '')  ?></div>
<div><?= $Parsedown->text($audit->BCPSPrincipleEmpowerGrowthSupportYes ?? '')  ?></div>

<div><?= $Parsedown->text($audit->BCPSPrincipleEmpowerGrowthSupportNo ?? '')  ?></div>

</div>
</div>

<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">5. Promote connectedness to others and the BCPS desired culture</div>
<?php foreach($audit->BCPSPrinciplePromoteConnectness as $yn): ?>
    <span style="display: inline-block; font-weight: bold; font-size: 1.3em; padding: 1em;"><?= $yn ?></span> 
<?php endforeach ?>
</div>
<div class="col-md-6">

	<div><?= $Parsedown->text($audit->BCPSPrinciplePromoteConnectnessSupport ?? '')  ?></div>
	<div><?= $Parsedown->text($audit->BCPSPrinciplePromoteConnectnessSupportYes ?? '')  ?></div>
	
<div><?= $Parsedown->text($audit->BCPSPrinciplePromoteConnectnessSupportNo ?? '')  ?></div>

</div>
</div>

<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">6. Anchor on established content and frameworks</div>
<?php foreach($audit->BCPSPrincipleAnchorEstablishedContent as $yn): ?>
    <span style="display: inline-block; font-weight: bold; font-size: 1.3em; padding: 1em;"><?= $yn ?></span> 
<?php endforeach ?>
</div>
<div class="col-md-6">

	<div><?= $Parsedown->text($audit->BCPSPrincipleAnchorEstablishedContentSupport ?? '')  ?></div>
	<div><?= $Parsedown->text($audit->BCPSPrincipleAnchorEstablishedContentSupportYes ?? '')  ?></div>
	
<div><?= $Parsedown->text($audit->BCPSPrincipleAnchorEstablishedContentSupportNo ?? '')  ?></div>

</div>
</div>

<div class="row mb-5 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">7. Encourage active reflection and application to role for learners</div>
<?php foreach($audit->BCPSPrincipleEncourageReflection as $yn): ?>
    <span style="display: inline-block; font-weight: bold; font-size: 1.3em; padding: 1em;"><?= $yn ?></span> 
<?php endforeach ?>
</div>
<div class="col-md-6">

<div><?= $Parsedown->text($audit->BCPSPrincipleEncourageReflectionSupport ?? '')  ?></div>
<div><?= $Parsedown->text($audit->BCPSPrincipleEncourageReflectionSupportYes ?? '')  ?></div>

<div><?= $Parsedown->text($audit->BCPSPrincipleEncourageReflectionSupportNo ?? '')  ?></div>

</div>
</div>




<h2 id="part4">Part 4: Continuous Improvement</h2>


<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">Is the learning currently meeting 
	<a href="https://learningcentre.gww.gov.bc.ca/learning-development-tips/technical-standards-for-elearning-blended-learning/" target="_blank" rel="noopener">
		our accessibility standards
	</a>? 
	If not, what is missing?</div>
<span style="display: inline-block; font-weight: bold; font-size: 1.3em; padding: 1em;"><?= $audit->MeetAccessibilityStandards ?></span>
</div>
<div class="col-md-6">

<div><?= $Parsedown->text($audit->MeetAccessibilityStandardsElaborate ?? '') ?></div>
</div>
</div>



<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">Is there any key content or competency missing in this learning? If so, describe the gap.</div>
<span style="display: inline-block; font-weight: bold; font-size: 1.3em; padding: 1em;"><?= $audit->MissingKeyContent ?></span>
</div>
<div class="col-md-6">

<div><?= $Parsedown->text($audit->MissingKeyContentElaborate ?? '')  ?></div>
</div>
</div>




<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">Does offering this learning reduce risk to BCPS?</div> 
<span style="display: inline-block; font-weight: bold; font-size: 1.3em; padding: 1em;"><?= $audit->ReduceRisk ?></span>
</div>
<div class="col-md-6">

<div><?= $Parsedown->text($audit->ReduceRiskElaborate ?? '')  ?></div>
</div>
</div>

<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">Does this reach a significant number of the intended audience?</div>
<span style="display: inline-block; font-weight: bold; font-size: 1.3em; padding: 1em;"><?= $audit->SignificantReach ?></span>
</div>
<div class="col-md-6">

<div><?= $Parsedown->text($audit->SignificantReachElaborate ?? '')  ?></div>
</div>
</div>

<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">What improvements do you recommend?</div>
<span style="display: inline-block; font-weight: bold; font-size: 1.3em; padding: 1em;"><?= $audit->WhatUpdates ?></span>
</div>
<div class="col-md-6">

<div><?= $Parsedown->text($audit->WhatUpdatesElaborate ?? '')  ?></div>
<?php if(!empty($audit->UncompletedUpdateRisk)): ?>
<div id="uncompletedrisk">
<div style="font-size: 1.2rem;">What is the risk if critical updates are not completed? </div>
<div><?= $Parsedown->text($audit->UncompletedUpdateRisk ?? '')  ?></div>
</div>
<?php endif ?>
</div>
</div>



<div class="row mb-5 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">Is there a resource we can direct people to that would allow for learners to meet the objectives, 
	if we had to stop offering this learning?</div>
	<span style="display: inline-block; font-weight: bold; font-size: 1.3em; padding: 1em;"><?= $audit->ResourceRedirect ?></span>
</div>
<div class="col-md-6">

<div><?= $Parsedown->text($audit->ResourceRedirectElaborate ?? '')  ?></div>
</div>
</div>

<div class="mt-3 mb-5 p-3 bg-light-subtle rounded-3">
				Created on <?= $audit->created ?> by <a href="/lsapp/person.php?idir=<?= $audit->createdby ?>"><?= $audit->createdby ?></a>, 
				modified <?= $audit->edited ?> by <a href="/lsapp/person.php?idir=<?= $audit->editedby ?>"><?= $audit->editedby ?></a>
			</div>


</div>
</div>
</div>




<!-- Modal -->
<div class="modal fade" id="guidanceModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Category Guidance</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
	  <h3>Mandatory</h3>
	<p>Includes Policy compliance – currently 4 required courses fall in this category</p>
	<h3>Essential</h3>
	<p>Strongly recommended by All Employees &amp; All People Leaders</p>
	<h3>Core</h3>
	<p>
	Core for a role. This is  learning for employees working in a specific 
	topic area, and develops foundational skills relevant to current role and promotes consistency in 
	practice and knowledge across the BCPS.  E.g. procurement, project management, financial skills, etc.) 
	Multiple core courses that build on each other will be still categorized as “core,” and may have 
	pre-requisites set up.
	</p>
	<h3>Complementary</h3>
	<p>Courses that are more advanced, or that build on (“extend”) core learning or 
	concepts taught at an essential level. They are not required for a role but may still be of interest for 
	public service employees.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

      </div>
    </div>
  </div>
</div>


</div>
</div>


<?php require('../../lsapp/templates/javascript.php') ?>


<script>
$(document).ready(function(){
	// $('#auditformsubmimt').attr('disabled', true);
	// $('#uncompletedrisk').hide();
	// $('#resourceType').on('change',function(e){
	// 	e.preventDefault();
	// 	$('#auditformsubmimt').attr('disabled', false);
	// 	$('#lsappcourse').addClass('d-none');
	// 	$('#forcourses').addClass('d-none');
	// 	let rt = $(this).val();
	// 	if(rt == 'Course') {
	// 		$('#lsappcourse').removeClass('d-none');
	// 		$('#forcourses').removeClass('d-none');
	// 	}
	// });
	// $('#LSAppCourseid').on('change',function(e){
	// 	e.preventDefault();
	// 	let courseid = $(this).val();
	// 	$.ajax({type:"GET", 
	// 			url:"data/courses.csv", 
	// 			dataType:"text", 
	// 			success: function(data) {
	// 				loadCourseDeets(courseid,data);
	// 			}
	// 	});
	// });
	// $('#WhatUpdatesCritical').on('change',function(){
	// 	$('#uncompletedrisk').show();
	// });


	// function loadCourseDeets(courseid,courses) {
	
	// 	var  courseArray = $.csv.toArrays(courses);
	// 	var deets = [];
	// 	courseArray.forEach(function(course){
	// 		if(course[0] == courseid) deets = course;
	// 	});
	// 	//console.log(deets);
	// 	console.log(deets[40]);
	// 	let coursename = deets[2];
	// 	let clink = '<div class="my-3"><a style="color: #333;" target="_blank" href="/lsapp/course.php?courseid=';
	// 	clink += courseid + '"> View ' + coursename + ' course page</a>.</div>';
	// 	$('#lsappcourselink').html(clink);
	// 	$('#ResourceName').val(coursename);
	// 	$('#DeliveryMethod').val(deets[21]);
	// 	$('#ResourceOwner').val(deets[36]);
	// 	$('#Topic').val(deets[38]);
	// 	$('#Audience').val(deets[39]);
	// 	$('#Level').val(deets[40]);

	// }



	
});
</script>

<?php require('../../lsapp/templates/footer.php') ?>