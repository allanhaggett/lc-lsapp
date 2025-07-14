<?php 
opcache_reset();
require('../../lsapp/inc/lsapp.php');

$idir = stripIDIR($_SERVER["REMOTE_USER"]);
$file = '../../lsapp/data/reviews/' . $_GET['auditid'] . '.json';
$aud = file_get_contents($file);
$audit = json_decode($aud);
//array_pop($audit);
if($audit->createdby != $idir || !canAccess()) {
	echo 'Sorry, you cannot edit anyone\'s review but your own.';
	exit;
}
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

<title>Resource Review Update Form</title>

<?php getScripts() ?>

<body class="">
<nav class="navbar navbar-expand-lg bg-body-tertiary mb-5">
	<div class="container-fluid">
		<span class="navbar-brand">Learning Resource Review</span>
		<a class="nav-link" href="https://learningcentre.gww.gov.bc.ca/learninghub/">Learning<strong>HUB</strong></a>

	</div>
</nav>
<div class="container-fluid">
<div class="row justify-content-md-center mb-3">
<div class="col-md-3">
	<?php if(canAccess()): ?>
	<div class="mb-4"><a href="/lsapp/audits.php" class="btn btn-light">All Reviews</a></div>
	<?php endif ?>
	<h2>Your Reviews</h2>
<?php foreach($userreviews as $ur): ?>
	<div class="my-2 p-2 bg-light-subtle rounded-3">
		<a href="review.php?auditid=<?= $ur[0] ?>">
			<?= $ur[4] ?><br>
			<span class="badge bg-secondary text-white"><?= $ur[1] ?></span>
		</a>
	</div>
<?php endforeach ?>
</div>
<div class="col-md-6">

<style>
	label { font-weight: bold; }
	.form-group {
		margin: 1.5em 0;
	}
</style>

<h1><?= $audit->Status ?> - Reviewing: <?= $audit->ResourceName ?? 'Something is wrong. Contact an admin.' ?></h1>
<div class="row mt-3 mb-5 sticky-top bg-light-subtle p-3 rounded-3 shadow-lg">
	<div class="col-3">
		<a class="font-weight-bold" href="#part1">Part 1</a>
		<div style="font-size: 14px">(8 questions)</div>
	</div>
	<div class="col-3">
		<a class="font-weight-bold" href="#part2">Part 2</a>
		<div style="font-size: 14px">(4 questions)</div>
	</div>
	<div class="col-3">
		<a class="font-weight-bold" href="#part3">Part 3</a>
		<div style="font-size: 14px">(7 questions)</a></div>
	</div>
	<div class="col-3">
		<a class="font-weight-bold" href="#part4">Part 4</a>
		<div style="font-size: 14px">(7 questions)</div>
	</div>
</div>
<div class="alert alert-secondary">Note: text boxes support <a href="https://www.markdownguide.org/getting-started/" target="_blank rel="noopener">Markdown formatting</a></div>
<form method="post" action="/lsapp/audit-update.php" class="p-2 mb-5">
<input type="hidden" name="AuditID" value="<?= $audit->AuditID ?>">
<input type="hidden" name="created" value="<?= $audit->created ?>">
<input type="hidden" name="createdby" value="<?= $audit->createdby ?>">




<h2 class="" id="part1">Part 1: Overview</h2>
<div class="row">
<div class="col-md-4 mb-3">
<label for="Status">Status</label><br>
<select name="Status" id="Status" class="form-select" >
<?php $statuses = ['Draft','Completed']; ?>
<?php foreach($statuses as $s): ?>
	<?php if($audit->Status == $s): ?>
	<option selected><?= $s ?></option>
	<?php else: ?>
	<option><?= $s ?></option>
	<?php endif ?>
<?php endforeach ?>
</select>

<label for="resourceType">Resource Type</label><br>
<select name="resourceType" id="resourceType" class="form-select">

<?php foreach($audittype as $at): ?>
	<?php if($at == $audit->resourceType): ?>
	<option selected><?= $at ?></option>
	<?php else: ?>
	<option><?= $at ?></option>
	<?php endif ?>
<?php endforeach ?>
</select>

</div>
</div>
<?php if($audit->LSAppCourseid): ?>
<a href="/lsapp/course.php?courseid=<?= $audit->LSAppCourseid ?>" target="_blank">View in LSApp (requires access)</a><hr>
<?php endif ?>
<label for="ResourceName">Resource Name</label><br>
<input type="text" name="ResourceName" id="ResourceName" class="form-control form-control-lg" value="<?= $audit->ResourceName ?? 'Something is wrong. Contact an admin.' ?>" required>



<div class="my-3 bg-light-subtle p-3 py-4 rounded-3">


<div class="row mb-3">

<div class="col-md-3">


<label for="Audience">Audience</label><br>
<select name="Audience" id="Audience" class="form-select" required>
<?php foreach($audience as $a): ?>
	<?php if($a == $audit->Audience): ?>
	<option selected><?= $a ?></option>
	<?php else: ?>
	<option><?= $a ?></option>
	<?php endif ?>
<?php endforeach ?>
</select>


</div>
<div class="col-md-6">


	<label for="Topic">Topic</label><br>
	<select name="Topic" id="Topic" class="form-select" required>
	<?php foreach($topics as $t): ?>
	<?php if($t == $audit->Topic): ?>
	<option selected><?= $t ?></option>
	<?php else: ?>
	<option><?= $t ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>


</div>
<div class="col-md-3">

	<label class="" for="DeliveryMethod">Delivery Method</label>
	<select name="DeliveryMethod" id="DeliveryMethod" class="form-select" required>
	<?php foreach($deliverymethods as $dm): ?>
	<?php if($dm == $audit->DeliveryMethod): ?>
	<option selected><?= $dm ?></option>
	<?php else: ?>
	<option><?= $dm ?></option>
	<?php endif ?>
	<?php endforeach ?>
	</select>

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
<?php 
// Get the full list of partners
$partners = getPartners();
// Pop the headers off the top
array_shift($partners);
?>
<!-- <select name="ResourceOwner" id="ResourceOwner" class="form-select" required>
<option disabled selected>Please select&hellip;</option>
	<?php foreach($partners as $p): ?>
	<?php if($p[1] == $audit->ResourceOwner): ?>
	<option selected><?= $p[1] ?></option>
	<?php else: ?>
	<option><?= $p[1] ?></option>
	<?php endif ?>
	<?php endforeach ?>
</select> -->
<input type="text" name="ResourceOwner" id="ResourceOwner" class="form-control" >



</div>
<div class="col-md-3">


<label for="Duration">Duration</label><br>
<input type="text" name="Duration" id="Duration" class="form-control" value="<?= $audit->Duration ?? '' ?>">


</div>
</div>


</div> <!-- /.bg-light-subtle -->


<?php if($audit->resourceType == 'Course') $showcourseinfo = '' ?? 'd-none' ?>
<div class="my-3 bg-light-subtle p-3 py-4 rounded-3 <?= $showcourseinfo ?>" id="">

<label for="OverallCourseOutcomes">Overall Course Outcomes</label><br>
<div>[free response – Complete this with the course-level outcomes. Limit to top 7.]</div>
<details style="font-size: 14px">
	<summary>See an example from PCMP 206</summary>
	<p>Employees will:</p>
	<ul>
		<li>Function in accordance with established procurement and contract management policies and procedures</li>
		<li>Plan procurements effectively using resources on the BC Bid Resources </li>
		<li>Select the most appropriate solicitation type for the planned procurement </li>
		<li>Award procurements to the most qualified vendor based on the procurement policies and evaluation processes</li>
		<li>Practice effective contract administration and monitoring </li>
		<li>Conduct Post-contract Evaluations </li>
		<li>Practice excellent vendor relationship management</li>
	</ul>
</details>
<textarea name="OverallCourseOutcomes" id="OverallCourseOutcomes" class="form-control" rows="5"><?= $audit->OverallCourseOutcomes ?? ''  ?></textarea>



<div class="mt-3">
<label for="Notes">Notes</label><br>
<div style="font-size: 14px">[Free response – OPTIONAL – can use this space to expand on the outcomes 
	with the objectives learners will meet during the course. This is useful 
	when analyzing where there is overlap with similar courses.]</div>
<textarea name="Notes" id="Notes" class="form-control" rows="5"><?= $audit->Notes ?? ''  ?></textarea>
</div>

</div>


<h2 class="mt-5" id="part2">Part 2: Priorities and Impact</h2>

<div class="my-3 bg-light-subtle p-3 py-4 rounded-3">

<label for="MeasurableOutcomesForOrganization">Measurable outcomes for the organization</label> (not for the learning)<br>
<textarea name="MeasurableOutcomesForOrganization" id="MeasurableOutcomesForOrganization" class="form-control mb-3" rows="5"><?= $audit->MeasurableOutcomesForOrganization ?? ''  ?></textarea>
<div style="font-size: 14px">
	<ul>
		<li>E.g., a reduced number of conflicts of interest reports on an annual basis. 
		<li>Many courses do not yet have a connection to a Key Performance Indicator for the organization. 
		<li>Can you articulate an organizational goal that the learning aligns with?
			<ul>
				<li>If so, do you know if there is a metric for this goal in place? <br>
				<li>If there is not, what, in your opinion, should it be?
			</ul>
	</ul>

</div>
</div>

<div class="my-3 bg-light-subtle p-3 py-4 rounded-3">

<label for="CurrentOrganizationalMeasureBaseline">If there is a current organizational measure, what is the baseline?</label><br>
<textarea name="CurrentOrganizationalMeasureBaseline" id="CurrentOrganizationalMeasureBaseline" class="form-control mb-3" rows="5"><?= $audit->CurrentOrganizationalMeasureBaseline ?? ''  ?></textarea>
<div style="font-size: 14px">E.g. the number of conflict of interest reports in 2022  </div>

</div>

<div class="bg-light-subtle p-3 py-4 rounded-3">

<label for="LearningMetric">What is the learning metric?</label>
<div style="font-size: 14px">What are the specific metrics that the Learning Centre/Partner uses to measure success of the learning?<br>
(Don’t count formative assessments that provide feedback to the learner but score is not retained for learning record)</div>
<textarea name="LearningMetric" id="LearningMetric" class="form-control mb-3" rows="5"><?= $audit->LearningMetric ?? ''  ?></textarea>
<div style="font-size: 14px">[Could be a final test, a 3-month post learning checkup with learners in poll or survey, etc.]</div>

</div>


<div class="my-3 bg-light-subtle p-3 py-4 rounded-3">

<label for="SupportChangingSkills">Does this learning support changing skills needed of our employees? If so, how?</label>
<textarea name="SupportChangingSkills" id="SupportChangingSkills" class="form-control mb-3" rows="5"><?= $audit->SupportChangingSkills ?? ''  ?></textarea>
<div style="font-size: 14px">[optional free response – Complete this with reference to competencies that teach future skills 
	that allow BCPS to solve problems in emergent contexts – including skills related to digital 
	government, emergency management, climate response, data science, artificial intelligence, 
	innovation etc.]</div>

</div>











<h2 id="part3">Part 3: Learning Principle Alignment</h2>
<h3>How does this align to our 7 BCPS learning priorities and goals?</h3>
<!-- [free response – Complete this with reference to evidence of each BCPS Learning Principle. Each principle scores a green plus sign for demonstrating the principle, and a red minus sign for a lack of evidence of that BCPS learning principle.. Circle one, then elaborate in the table with evidence from the course.  -->
<p>Explain the gap or include the evidence you find in the course.</p>

<details class="my-3">

<summary><strong>Example of principle alignment in our GBA+ course</strong></summary>

<div class="row mb-3 p-3 bg-light-subtle">
<div class="col-md-3">
	<div style="font-size: 1.2rem;">Place learners at the center in the design and experience of learning</div>
	<div style="font-weight:bold" class="my-3">Yes &amp; No</div>
</div>
<div class="col-md-9">
	<ul>
		<li>This course acknowledges that every learner is on our own journey with this learning, which centres learners. 
		<li>The video presents comprehensive information about GBA+; however, it does not teach learners how to do GBA+ - there is no supported practice, demonstration of analysis, or opportunity to identify when you want to apply GBA+ and how. This does not centre the learner in the design of the experience.
		<li>The course also speaks to an audience as though information about sex and gender is new to them—without acknowledging that this basic information may be something the audience is well acquainted with. There were no options to stream the learner based on their background or familiarity with the topic. The learning could be more learner centred with a knowledge assessment that directs learner to this part of the video if they demonstrated a lack of knowledge, or takes the learner to the next relevant part of the learning for them.
	</ul>
</div>
</div>

<div class="row p-3 mb-3">
<div class="col-md-3">
<div style="font-size: 1.2rem;">Be aligned with business priorities and goals</div>
<div style="font-weight:bold" class="my-3">Yes</div>
</div>
<div class="col-md-9">
	<ul>
		<li>Yes – the comprehensive analysis of GBA+ supports commitments to Reconciliation, EDI, accessibility, anti-racism, gender equity.
		<li>This video also points to the other provincial course in the framework shows how GBA+ aligns with other provincial commitments 
	</ul>
</div>
</div>

<div class="row mb-3 p-3 bg-light-subtle">
<div class="col-md-3">
<div style="font-size: 1.2rem;">Be available & just-in-time for learners</div>
<div style="font-weight:bold" class="my-3">Yes</div>
</div>
<div class="col-md-9">
	<ul>
	<li>As a half-hour video with no barrier to registration, this course is in alignment. 
	<li>The additional resources list links to some great relevant tools such as the inclusive language documents. 
	<li>To go further, the GBA+ course could provide a “top 10 core concepts” tip sheet or similar for learners who want to reference something in the flow of work before they are able to watch the video.
	</ul>
</div>
</div>
<div class="row mb-3 p-3">
<div class="col-md-3">
	<div style="font-size: 1.2rem;">Empower our people to grow their career within the BCPS</div>
	<div style="font-weight:bold" class="my-3">Yes &amp; No</div>
</div>
<div class="col-md-9">
	<ul>
	<li>This course provides foundational knowledge about intersectionality, the basis for GBA+, and how it supports the work we do for a diverse population—something that applies to everyone in the BCPS no matter where they are in their career. 
	<li>Comprehensive analysis by public servants requires the GBA+ lens, so this information supports career growth, but with a major gap: there is no information on how to do GBA+ analysis. To grow our careers, we need the “how” in addition to the “what” and “why.”
	</ul>
</div>
</div>
<div class="row mb-3 p-3 bg-light-subtle">
<div class="col-md-3">
	<div style="font-size: 1.2rem;">Promote connectedness to others and the BCPS desired culture</div>
</div>
<div class="col-md-9">
	<ul>
	<li>Encourages learners to walk gently with each other, but there isn’t a cohort component to this course. 
	<li>Mentions ministry teams to connect to Finance, AG, SDPR, MIRR, and EDI in PSA. Mindful of approaching colleagues.
	<li>Connects GBA+ to the Standards of Conduct, which is the basis for our desired culture.
	</ul>
</div>
</div>

<div class="row mb-3 p-3">
<div class="col-md-3">
	<div style="font-size: 1.2rem;">Anchor on established content and frameworks</div>
	<div style="font-weight:bold" class="my-3">Yes</div>
</div>
<div class="col-md-9">
	<ul>
	<li>Clearly outlines “where to next” for this learning with the other provincial course and the federal course. Shows the connection to federal context.
	</ul>
</div>
</div>
<div class="row mb-3 p-3 bg-light-subtle">
<div class="col-md-3">
	<div style="font-size: 1.2rem;">Encourage active reflection and application to role for learners</div>
	<div style="font-weight:bold" class="my-3">No</div>
</div>
<div class="col-md-9">
	<ul>
	<li>The video introduction says the course will define how we apply the analysis to our work, but there was no evidence of this. The equity and equality section toward the end got close, but does not yet guide the learner in how to weigh equity considerations alongside others when making decisions, or show any applied examples to help the learner apply the skill to their work.
	<li>The course asks the learner to sit and think about their intentions toward reconciliation and learning about intersectionality, but does not go beyond that invitation to say how or why we might do this, or provide guidance for what to do with our reflections.
	<li>The final section says there is no standard template or formula for doing GBA+ analysis, but does not yet give the learner examples for how to do this analysis, or what steps might look like in a specific instance. The section mentions that the Gender Equity Office has tools and templates to help you through your application, and an application guide, so it directs you to get this training elsewhere. 
	<li>Slide 26 (ABC questions) presents the process for GBA+ with the questions, but does not show how the questions are used with an applied example or offer the learner a supported experience in using the questions. This seals this course as a video with good information about GBA+, but not an applied learning experience for employees.
	</ul>
</div>
</div>
</details>





<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">1. Place learners at the center in the design and experience of learning</div>
<div class="form-check d-inline-block mt-3 mr-3">
  <input type="checkbox" name="BCPSPrincipleLearnerCentre[]" id="BCPSPrincipleLearnerCentreYes" value="Yes" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrincipleLearnerCentreYes) && is_array($audit->BCPSPrincipleLearnerCentreYes) && in_array('Yes', $audit->BCPSPrincipleLearnerCentreYes)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrincipleLearnerCentreYes">Yes</label>
</div>

<div class="form-check d-inline-block">
  <input type="checkbox" name="BCPSPrincipleLearnerCentre[]" id="BCPSPrincipleLearnerCentreNo" value="No" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrincipleLearnerCentreYes) && is_array($audit->BCPSPrincipleLearnerCentreYes) && in_array('No', $audit->BCPSPrincipleLearnerCentreYes)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrincipleLearnerCentreNo">No</label>
</div>
</div>
<div class="col-md-6">

<?= $audit->BCPSPrincipleLearnerCentreSupport ?? ''  ?><br>
<label for="BCPSPrincipleLearnerCentreSupportYes">Yes? Please add supporting context</label>
<textarea name="BCPSPrincipleLearnerCentreSupportYes" id="BCPSPrincipleLearnerCentreSupportYes" class="form-control" rows="5">
	<?= $audit->BCPSPrincipleLearnerCentreSupportYes ?? ''  ?>
</textarea>

<label for="BCPSPrincipleLearnerCentreSupportNo">No? Please add supporting context</label>
<textarea name="BCPSPrincipleLearnerCentreSupportNo" id="BCPSPrincipleLearnerCentreSupportNo" class="form-control" rows="5"><?= $audit->BCPSPrincipleLearnerCentreSupportNo ?? ''  ?></textarea>

</div>
</div>



<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">2. Be aligned with business priorities and goals</div>
<div class="form-check d-inline-block mt-3 mr-3">
  <input type="checkbox" name="BCPSPrincipleAlignedBusinessPriority[]" id="BCPSPrincipleAlignedBusinessPriorityYes" value="Yes" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrincipleAlignedBusinessPriority) && is_array($audit->BCPSPrincipleAlignedBusinessPriority) && in_array('Yes', $audit->BCPSPrincipleAlignedBusinessPriority)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrincipleAlignedBusinessPriorityYes">Yes</label>
</div>

<div class="form-check d-inline-block">
  <input type="checkbox" name="BCPSPrincipleAlignedBusinessPriority[]" id="BCPSPrincipleAlignedBusinessPriorityNo" value="No" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrincipleAlignedBusinessPriority) && is_array($audit->BCPSPrincipleAlignedBusinessPriority) && in_array('No', $audit->BCPSPrincipleAlignedBusinessPriority)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrincipleAlignedBusinessPriorityNo">No</label>
</div>
</div>
<div class="col-md-6">


<?= $audit->BCPSPrincipleAlignedBusinessPrioritySupport ?? ''  ?><br>
<label for="BCPSPrincipleAlignedBusinessPrioritySupportYes">Yes? Please add supporting context</label>
<textarea name="BCPSPrincipleAlignedBusinessPrioritySupportYes" id="BCPSPrincipleAlignedBusinessPrioritySupportYes" class="form-control" rows="5">
	<?= $audit->BCPSPrincipleAlignedBusinessPrioritySupportYes ?? ''  ?>
</textarea>

<label for="BCPSPrincipleAlignedBusinessPrioritySupportNo">No? Please add supporting context</label>
<textarea name="BCPSPrincipleAlignedBusinessPrioritySupportNo" id="BCPSPrincipleAlignedBusinessPrioritySupportNo" class="form-control" rows="5"><?= $audit->BCPSPrincipleAlignedBusinessPrioritySupportNo ?? ''  ?></textarea>

</div>
</div>


<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">3. Be available & just-in-time for learners</div>
<div class="form-check d-inline-block mt-3 mr-3">
  <input type="checkbox" name="BCPSPrincipleAvailableJIT[]" id="BCPSPrincipleAvailableJITYes" value="Yes" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrincipleAvailableJIT) && is_array($audit->BCPSPrincipleAvailableJIT) && in_array('Yes', $audit->BCPSPrincipleAvailableJIT)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrincipleAvailableJITYes">Yes</label>
</div>

<div class="form-check d-inline-block">
  <input type="checkbox" name="BCPSPrincipleAvailableJIT[]" id="BCPSPrincipleAvailableJITNo" value="No" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrincipleAvailableJIT) && is_array($audit->BCPSPrincipleAvailableJIT) && in_array('No', $audit->BCPSPrincipleAvailableJIT)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrincipleAvailableJITNo">No</label>
</div>
</div>
<div class="col-md-6">

	
<?= $audit->BCPSPrincipleAvailableJITSupport ?? ''  ?><br>
<label for="BCPSPrincipleAvailableJITSupportYes">Yes? Please add supporting context</label>
<textarea name="BCPSPrincipleAvailableJITSupportYes" id="BCPSPrincipleAvailableJITSupportYes" class="form-control" rows="5">
	<?= $audit->BCPSPrincipleAvailableJITSupportYes ?? ''  ?>
</textarea>

<label for="BCPSPrincipleAvailableJITSupportNo">No? Please add supporting context</label>
<textarea name="BCPSPrincipleAvailableJITSupportNo" id="BCPSPrincipleAvailableJITSupportNo" class="form-control" rows="5"><?= $audit->BCPSPrincipleAvailableJITSupportNo ?? ''  ?></textarea>

</div>
</div>


<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">4. Empower our people to grow their career within the BCPS</div>
<div class="form-check d-inline-block mt-3 mr-3">
  <input type="checkbox" name="BCPSPrincipleEmpowerGrowth[]" id="BCPSPrincipleEmpowerGrowthYes" value="Yes" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrincipleEmpowerGrowth) && is_array($audit->BCPSPrincipleEmpowerGrowth) && in_array('Yes', $audit->BCPSPrincipleEmpowerGrowth)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrincipleEmpowerGrowthYes">Yes</label>
</div>

<div class="form-check d-inline-block">
  <input type="checkbox" name="BCPSPrincipleEmpowerGrowth[]" id="BCPSPrincipleEmpowerGrowthNo" value="No" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrincipleEmpowerGrowth) && is_array($audit->BCPSPrincipleEmpowerGrowth) && in_array('No', $audit->BCPSPrincipleEmpowerGrowth)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrincipleEmpowerGrowthNo">No</label>
</div>
</div>
<div class="col-md-6">


<?= $audit->BCPSPrincipleEmpowerGrowthSupport ?? ''  ?><br>
<label for="BCPSPrincipleEmpowerGrowthSupportYes">Yes? Please add supporting context</label>
<textarea name="BCPSPrincipleEmpowerGrowthSupportYes" id="BCPSPrincipleEmpowerGrowthSupportYes" class="form-control" rows="5">
	<?= $audit->BCPSPrincipleEmpowerGrowthSupportYes ?? ''  ?>
</textarea>

<label for="BCPSPrincipleEmpowerGrowthSupportNo">No? Please add supporting context</label>
<textarea name="BCPSPrincipleEmpowerGrowthSupportNo" id="BCPSPrincipleEmpowerGrowthSupportNo" class="form-control" rows="5"><?= $audit->BCPSPrincipleEmpowerGrowthSupportNo ?? ''  ?></textarea>
</div>
</div>

<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">5. Promote connectedness to others and the BCPS desired culture</div>
<div class="form-check d-inline-block mt-3 mr-3">
  <input type="checkbox" name="BCPSPrinciplePromoteConnectness[]" id="BCPSPrinciplePromoteConnectnessYes" value="Yes" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrinciplePromoteConnectness) && is_array($audit->BCPSPrinciplePromoteConnectness) && in_array('Yes', $audit->BCPSPrinciplePromoteConnectness)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrinciplePromoteConnectnessYes">Yes</label>
</div>

<div class="form-check d-inline-block">
  <input type="checkbox" name="BCPSPrinciplePromoteConnectness[]" id="BCPSPrinciplePromoteConnectnessNo" value="No" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrinciplePromoteConnectness) && is_array($audit->BCPSPrinciplePromoteConnectness) && in_array('No', $audit->BCPSPrinciplePromoteConnectness)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrinciplePromoteConnectnessNo">No</label>
</div>
</div>
<div class="col-md-6">


<?= $audit->BCPSPrinciplePromoteConnectnessSupport ?? ''  ?><br>
<label for="BCPSPrinciplePromoteConnectnessSupportYes">Yes? Please add supporting context</label>
<textarea name="BCPSPrinciplePromoteConnectnessSupportYes" id="BCPSPrinciplePromoteConnectnessSupportYes" class="form-control" rows="5">
	<?= $audit->BCPSPrinciplePromoteConnectnessSupportYes ?? ''  ?>
</textarea>

<label for="BCPSPrinciplePromoteConnectnessSupportNo">No? Please add supporting context</label>
<textarea name="BCPSPrinciplePromoteConnectnessSupportNo" id="BCPSPrinciplePromoteConnectnessSupportNo" class="form-control" rows="5"><?= $audit->BCPSPrinciplePromoteConnectnessSupportNo ?? ''  ?></textarea>
</div>
</div>

<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">6. Anchor on established content and frameworks</div>
<div class="form-check d-inline-block mt-3 mr-3">
  <input type="checkbox" name="BCPSPrincipleAnchorEstablishedContent[]" id="BCPSPrincipleAnchorEstablishedContentYes" value="Yes" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrincipleAnchorEstablishedContent) && is_array($audit->BCPSPrincipleAnchorEstablishedContent) && in_array('Yes', $audit->BCPSPrincipleAnchorEstablishedContent)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrincipleAnchorEstablishedContentYes">Yes</label>
</div>

<div class="form-check d-inline-block">
  <input type="checkbox" name="BCPSPrincipleAnchorEstablishedContent[]" id="BCPSPrincipleAnchorEstablishedContentNo" value="No" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrincipleAnchorEstablishedContent) && is_array($audit->BCPSPrincipleAnchorEstablishedContent) && in_array('No', $audit->BCPSPrincipleAnchorEstablishedContent)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrincipleAnchorEstablishedContentNo">No</label>
</div>
</div>
<div class="col-md-6">


<?= $audit->BCPSPrincipleAnchorEstablishedContentSupport ?? ''  ?><br>
<label for="BCPSPrincipleAnchorEstablishedContentSupportYes">Yes? Please add supporting context</label>
<textarea name="BCPSPrincipleAnchorEstablishedContentSupportYes" id="BCPSPrincipleAnchorEstablishedContentSupportYes" class="form-control" rows="5">
	<?= $audit->BCPSPrincipleAnchorEstablishedContentSupportYes ?? ''  ?>
</textarea>

<label for="BCPSPrincipleAnchorEstablishedContentSupportNo">No? Please add supporting context</label>
<textarea name="BCPSPrincipleAnchorEstablishedContentSupportNo" id="BCPSPrincipleAnchorEstablishedContentSupportNo" class="form-control" rows="5"><?= $audit->BCPSPrincipleAnchorEstablishedContentSupportNo ?? ''  ?></textarea>
</div>
</div>

<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">7. Encourage active reflection and application to role for learners</div>
<div class="form-check d-inline-block mt-3 mr-3">
  <input type="checkbox" name="BCPSPrincipleEncourageReflection[]" id="BCPSPrincipleEncourageReflectionYes" value="Yes" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrincipleEncourageReflection) && is_array($audit->BCPSPrincipleEncourageReflection) && in_array('Yes', $audit->BCPSPrincipleEncourageReflection)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrincipleEncourageReflectionYes">Yes</label>
</div>

<div class="form-check d-inline-block">
  <input type="checkbox" name="BCPSPrincipleEncourageReflection[]" id="BCPSPrincipleEncourageReflectionNo" value="No" class="form-check-input" 
    <?php if (!empty($audit->BCPSPrincipleEncourageReflection) && is_array($audit->BCPSPrincipleEncourageReflection) && in_array('No', $audit->BCPSPrincipleEncourageReflection)) echo 'checked'; ?>>
  <label class="form-check-label" for="BCPSPrincipleEncourageReflectionNo">No</label>
</div>
</div>
<div class="col-md-6">


<?= $audit->BCPSPrincipleEncourageReflectionSupport ?? ''  ?><br>

<label for="BCPSPrincipleEncourageReflectionSupportYes">Yes? Please add supporting context</label>
<textarea name="BCPSPrincipleEncourageReflectionSupportYes" id="BCPSPrincipleEncourageReflectionSupportYes" class="form-control" rows="5">
	<?= $audit->BCPSPrincipleEncourageReflectionSupportYes ?? ''  ?>
</textarea>

<label for="BCPSPrincipleEncourageReflectionSupportNo">No? Please add supporting context</label>
<textarea name="BCPSPrincipleEncourageReflectionSupportNo" id="BCPSPrincipleEncourageReflectionSupportNo" class="form-control" rows="5"><?= $audit->BCPSPrincipleEncourageReflectionSupportNo ?? ''  ?></textarea>
</div>
</div>


<div class="row mb-5 bg-light-subtle p-3 py-4 rounded-3">

<div class="col-md-12">


<!-- 100%=completely in alignment 75% mostly in alignment 50% partially in alignment 25% significant work to bring this into alignment -->
<div class="mb-3 fs-5" id="overallprinciplepercent"> Overall percent of principles aligment:</div>
<div class="p-3 mb-2 bg-light-subtle">
<input type="radio" name="BCPSPrincipleOverallPercent" id="SignificantWork" value="25" class="form-check-input" <?php if($audit->BCPSPrincipleOverallPercent == '25') echo 'checked' ?>>
<label class="form-check-label" for="SignificantWork">25% - Significant work to align</label>
</div>
<div class="p-3 mb-2 bg-light-subtle">
<input type="radio" name="BCPSPrincipleOverallPercent" id="PartialAlignment" value="50" class="form-check-input" <?php if($audit->BCPSPrincipleOverallPercent == '50') echo 'checked' ?>>
<label class="form-check-label" for="PartialAlignment">50% - Partially in alignment</label>
</div>
<div class="p-3 mb-2 bg-light-subtle">
<input type="radio" name="BCPSPrincipleOverallPercent" id="MostlyAlignment" value="75" class="form-check-input" <?php if($audit->BCPSPrincipleOverallPercent == '75') echo 'checked' ?>>
<label class="form-check-label" for="MostlyAlignment">75% - Mostly in alignment</label>
</div>
<div class="p-3 mb-2 bg-light-subtle">
<input type="radio" name="BCPSPrincipleOverallPercent" id="CompleteAlignment" value="100" class="form-check-input" <?php if($audit->BCPSPrincipleOverallPercent == '100') echo 'checked' ?>>
<label class="form-check-label" for="CompleteAlignment">100% - Completely in alignment</label>
</div>


</div>
</div>



<h2 id="part4">Part 4: Continuous Improvement</h2>


<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">Is the learning currently meeting <a href="#">our accessibility standards</a>? If not, what is missing?</div>
<div class="form-check d-inline-block mt-3 mr-3">
	<input type="radio" name="MeetAccessibilityStandards" id="MeetAccessibilityStandardsYes" value="Yes" class="form-check-input" <?php if($audit->MeetAccessibilityStandards == 'Yes') echo 'checked' ?>>
	<label class="form-check-label" for="MeetAccessibilityStandardsYes">Yes</label>
</div>
<div class="form-check d-inline-block">
	<input type="radio" name="MeetAccessibilityStandards" id="MeetAccessibilityStandardsNo" value="No" class="form-check-input" <?php if($audit->MeetAccessibilityStandards == 'No') echo 'checked' ?>>
	<label class="form-check-label" for="MeetAccessibilityStandardsNo">No</label>
</div>
</div>
<div class="col-md-6">
<label for="MeetAccessibilityStandardsElaborate">Please elaborate</label>
<textarea name="MeetAccessibilityStandardsElaborate" id="MeetAccessibilityStandardsElaborate" class="form-control" rows="5"><?= $audit->MeetAccessibilityStandardsElaborate ?? '' ?></textarea>
</div>
</div>



<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">Is there any key content or competency missing in this learning? If so, describe the gap.</div>
<div class="form-check d-inline-block mt-3 mr-3">
	<input type="radio" name="MissingKeyContent" id="MissingKeyContentYes" value="Yes" class="form-check-input" <?php if($audit->MissingKeyContent == 'Yes') echo 'checked' ?>>
	<label class="form-check-label" for="MissingKeyContentYes">Yes</label>
</div>
<div class="form-check d-inline-block">
	<input type="radio" name="MissingKeyContent" id="MissingKeyContentNo" value="No" class="form-check-input" <?php if($audit->MissingKeyContent == 'No') echo 'checked' ?>>
	<label class="form-check-label" for="MissingKeyContentNo">No</label>
</div>
</div>
<div class="col-md-6">
<label for="MissingKeyContentElaborate">Please elaborate</label>
<textarea name="MissingKeyContentElaborate" id="MissingKeyContentElaborate" class="form-control" rows="5"><?= $audit->MissingKeyContentElaborate ?? ''  ?></textarea>
</div>
</div>




<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">Does offering this learning reduce risk to BCPS?</div> 
<div class="form-check d-inline-block mt-3 mr-3">
	<input type="radio" name="ReduceRisk" id="ReduceRiskYes" value="Yes" class="form-check-input" <?php if($audit->ReduceRisk == 'Yes') echo 'checked' ?>>
	<label class="form-check-label" for="ReduceRiskYes">Yes</label>
</div>
<div class="form-check d-inline-block">
	<input type="radio" name="ReduceRisk" id="ReduceRiskNo" value="No" class="form-check-input" <?php if($audit->ReduceRisk == 'No') echo 'checked' ?>>
	<label class="form-check-label" for="ReduceRiskNo">No</label>
</div>
<div style="font-size: 14px" class="mt-3">(Is there a potential workplace escalation due to non-delivery of this training? How serious is the knowledge or skill gap in our current workforce?)</div>
</div>
<div class="col-md-6">
<label for="ReduceRiskElaborate">Please elaborate</label>
<textarea name="ReduceRiskElaborate" id="ReduceRiskElaborate" class="form-control" rows="5"><?= $audit->ReduceRiskElaborate ?? ''  ?></textarea>
</div>
</div>

<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">Does this reach a significant number of the intended audience?</div>
<div class="form-check d-inline-block mt-3 mr-3">
  <input type="radio" name="SignificantReach" id="SignificantReachYes" value="Yes" class="form-check-input" <?php if($audit->SignificantReach == 'Yes') echo 'checked' ?>>
  <label class="form-check-label" for="SignificantReachYes">Yes</label>
</div>
<div class="form-check d-inline-block">
  <input type="radio" name="SignificantReach" id="SignificantReachNo" value="No" class="form-check-input" <?php if($audit->SignificantReach == 'No') echo 'checked' ?>>
  <label class="form-check-label" for="SignificantReachNo">No</label>
</div>
</div>
<div class="col-md-6">
<label for="SignificantReachElaborate">Please elaborate</label>
<textarea name="SignificantReachElaborate" id="SignificantReachElaborate" class="form-control" rows="5"><?= $audit->SignificantReachElaborate ?? ''  ?></textarea>
</div>
</div>

<div class="row mb-3 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">What improvements do you recommend?</div>
<div class="form-check d-inline-block mt-3 mr-3">
  <input type="radio" name="WhatUpdates" id="WhatUpdatesCritical" class="form-check-input" value="Critical Updates" <?php if($audit->WhatUpdates == 'Critical Updates') echo 'checked' ?>>
  <label class="form-check-label" for="WhatUpdatesCritical">Critical updates</label>
</div>
<div class="form-check d-inline-block">
  <input type="radio" name="WhatUpdates" id="WhatUpdatesIfTimeAllows" class="form-check-input"  value="If Time Allows" <?php if($audit->WhatUpdates == 'If Time Allows') echo 'checked' ?>>
  <label class="form-check-label" for="WhatUpdatesIfTimeAllows">If Time Allows</label>
</div>
</div>
<div class="col-md-6">
<label for="WhatUpdatesElaborate">Please elaborate</label>
<textarea name="WhatUpdatesElaborate" id="WhatUpdatesElaborate" class="form-control" rows="5"><?= $audit->WhatUpdatesElaborate ?? ''  ?></textarea>
<div id="uncompletedrisk">
<label for="UncompletedUpdateRisk">What is the risk if critical updates are not completed? </label>
<textarea name="UncompletedUpdateRisk" id="UncompletedUpdateRisk" class="form-control" rows="5"><?= $audit->UncompletedUpdateRisk ?? ''  ?></textarea>
</div>
</div>
</div>



<div class="row mb-5 bg-light-subtle p-3 py-4 rounded-3">
<div class="col-md-6">
<div style="font-size: 1.2rem;">Is there a resource we can direct people to that would allow for learners to meet the objectives, 
	if we had to stop offering this learning?</div>
<div class="form-check d-inline-block mt-3 mr-3">
  <input type="radio" name="ResourceRedirect" id="ResourceRedirectYes" value="Yes" class="form-check-input" <?php if($audit->ResourceRedirect == 'Yes') echo 'checked' ?>>
  <label class="form-check-label" for="ResourceRedirectYes">Yes</label>
</div>
<div class="form-check d-inline-block">
  <input type="radio" name="ResourceRedirect" id="ResourceRedirectNo" value="No" class="form-check-input" <?php if($audit->ResourceRedirect == 'No') echo 'checked' ?>>
  <label class="form-check-label" for="ResourceRedirectNo">No</label>
</div>
</div>
<div class="col-md-6">
<label for="ResourceRedirectElaborate">Please elaborate</label>
<textarea name="ResourceRedirectElaborate" id="ResourceRedirectElaborate" class="form-control" rows="5"><?= $audit->ResourceRedirectElaborate ?? ''  ?></textarea>
</div>
</div>
<div class="row justify-content-md-center bg-light-subtle ">
<div class="col-md-6">
<input type="submit" id="auditformsubmimt" name="submit" value="Update Audit" class="btn btn-success btn-lg btn-block my-3">

</div>
</div>
</form>




</div>
</div>
</div>

<?php require('templates/javascript.php') ?>


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



