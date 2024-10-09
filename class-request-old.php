<?php require('inc/lsapp.php') ?>
<?php 
$coureseid = 0;
$courseid = (isset($_GET['courseid'])) ? $_GET['courseid'] : 0 ?>
<?php getHeader() ?>

<title>New Class Date Service Request | LSApp</title>

<?php getScripts() ?>

<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">
<style>
.dedinfo { display: none }
</style>
<?php getNavigation() ?>

<?php if(canAccess()): ?>

<div class="container">
<div class="row">
<div class="col-md-8 mb-3">

<h1>New Class Date</h1>
<p>This form will add a new class date for a course that already exists within the PSA Learning System. 
If the course you wish to submit dates for is not listed below, please 
<a href="/lsapp/course-request.php">fill out a new course request</a>. Submitting this form adds a new 
class date to the schedule for administrative processing.</p>

<form name="frm_csvEdit" action="class-create.php" method="POST" enctype="multipart/form-data">
<input class="ClassID" type="hidden" name="ClassID" value="">
<input class="Status" type="hidden" name="Status" value="Requested">
<input class="Requested" type="hidden" name="Requested" value="<?php echo date('Y-m-d-H:i') ?>">
<input class="RequestedBy" type="hidden" name="RequestedBy" value="<?php echo stripIDIR($_SERVER["REMOTE_USER"]) ?>">
<input class="Modified" type="hidden" name="Modified" value="<?php echo date('Y-m-d-H:i') ?>">
<input class="ModifiedBy" type="hidden" name="ModifiedBy" value="<?php echo stripIDIR($_SERVER["REMOTE_USER"]) ?>">

<input class="Region" type="hidden" name="Region" value="">
<input class="ItemCode" type="hidden" name="ItemCode" value="">
<input class="EndDate" type="hidden" name="EndDate" value="">
<input class="CourseDays" type="hidden" name="CourseDays" value="">
<input class="CourseCategory" type="hidden" name="CourseCategory" value="">
<input class="CourseCode" type="hidden" name="CourseCode" value="">


<div class="alert alert-warning mb-1 w-50">
<label><input type="checkbox" name="Dedicated" id="Dedicated" class="" value="Dedicated"> Dedicated?</label>
</div>
<div class="alert alert-warning dedinfo">
<a href="docs/dedicated-class-ADHOC-attendance-form.xlsx" class="btn btn-success float-right ml-3 mt-3">Ad hoc attendance spreadsheet</a>
<small>Dedicated classes don't use the PSA Learning System to handle pre-registration. 
Contact the Manager of Learning Delivery to arrange an MOU before submitting a dedicated class.</small></div>

<label>Facilitating
<select class="form-control Facilitating" name="Facilitating">
<option>Unknown</option>
<?php getPeople() ?>
</select>
</label>
<label>Delivery Method
<select name="DeliveryMethod" id="DeliveryMethod" class="form-control">
<option>Classroom</option>
<option>Webinar</option>
<option>eLearning</option>
<!--<option>Web Based</option>-->
</select>
</label>


<div class="alert alert-success">
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

<!-- Testing output with datalist element -->
<input list="courseChoice" id="courseList" name="Course" class="form-select my-3" placeholder="Choose a course"/>
	<datalist id="courseChoice">
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
	</datalist>
</div>
<div class="row">
<div class="col-md-4">
<label for="WebinarDate">Webinar Date</label>
<input class="form-control WebinarDate" type="text" id="WebinarDate" name="WebinarDate" value="">
</div>
<div class="col-md-4">
<label for="WebinarLink">Webinar Link</label>
<input class="form-control WebinarLink" id="WebinarLink" type="text" name="WebinarLink" value="">
</div>
<div class="col-md-4">
<label for="MinMax">Min/Max</label>
<input class="form-control minmax" id="MinMax" placeholder="Min/Max" type="text" name="MinMax" value="" size="5" required="required">
</div>


</div>



<div class="row session">
<div class="col-md-4">
<label for="sd" class="sessionlabel">Start Date</label>
<input class="form-control StartDate date" id="sd" type="text" name="StartDate" value="" required="required">
<div class="enddate"></div>
</div>
<div class="col-md-4">
<label for="t">Times</label>
<input class="form-control times" id="t" placeholder="Times" type="text" name="Times" value="" required="required">
</div>



<div class="col-md-4">
<div class="cityinfo">
<label for="VenueCity">City</label>
<select name="VenueCity" id="VenueCity" class="form-control mb-0" required="required">
	<option value="">Choose a City</option>
	<!-- <option>Provided</option>-->
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


</div> <!-- /.col -->
</div> <!-- /.row -->


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

</div> <!-- /.card-body -->
</div> <!-- /.card -->


</div>




<div class="row">

<div class="col-md-8 offset-md-4">

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
<!--<div class="alert alert-warning mt-3">Please note that this is a service request for interfacing with the PSA Learning System (PeopleSoft ELM) <em>only</em> and does not cover financial reporting. Please <a href="#">read this for more info</a>.</div>-->
<input type="submit" name="submit" class="btn btn-block btn-lg btn-primary mb-3" value="Submit Service Request">


</form>



</div>

<div class="col-md-4 mb-3">
<h3 class="">Upcoming Classes</h3>
<h4 class="coursename"></h4>
<ul class="list-group classlist"></ul>
</div>

</div>
</div>


<?php require('templates/javascript.php') ?>

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
	
	var moment = rome.moment;
	var whorag = rome(document.querySelector('.StartDate'), { time: false, dateValidator: function (d) {
		return moment(d).day() !== 6;
	} });
	var whorag = rome(document.querySelector('.WebinarDate'), { time: false, dateValidator: function (d) {
		return moment(d).day() !== 6;
	} });
	
	$('.summernote').summernote({
		toolbar: [
			// [groupName, [list of button]]
			['style', ['bold', 'italic']],
			['para', ['ul', 'ol']],
			['link']
		],
		placeholder: ''
	});	
	
	//,
	//	hint: {
	//		words: [
	//		<?php foreach($courses as $c): ?>
	//		'<?= $c[2] ?>',
	//		<?php endforeach ?>
	//		],
	//		match: /\b(\w{1,})$/,
	//		search: function (keyword, callback) {
	//			callback($.grep(this.words, function (item) {
	//				return item.indexOf(keyword) === 0;
	//			}));
	//		}
	//	}
	
	
	$('#DeliveryMethod').on('change', function(){
		var dm = $(this).val();
		if(dm == 'Webinar') {
			$('.cityinfo').hide();
			$('#VenueCity').removeAttr('required');
			$('.WebinarLink').prop('required',true);
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
		var mm = deets[28] + '/' + deets[29];
		$('.minmax').val(mm);
		$('.times').val(deets[5]);
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