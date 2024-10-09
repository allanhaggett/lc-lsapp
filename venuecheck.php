<?php

require('inc/lsapp.php');
// Get the full course list
$courses = getCourses();
// Pop the headers off the top
array_shift($courses);
// Create a temp array for the array_multisort below
$tmp = array();
// loop through everything and add the name to
// the temp array
foreach($courses as $line) {
	$tmp[] = $line[2];
}
// Sort the whole kit and kaboodle by name
array_multisort($tmp, SORT_ASC, $courses);

if(!isset($_GET['courseids'])) {
	$cids = '';
	$courseids = array();
	// Get the full class list
	$c =  getClasses();
	
} else {
	$cids = $_GET['courseids'];
	// $cids just returns a simple comma-separated list of IDs 
	// (e.g. 8,22,20190806123344,12)
	// And we need this value to append to self-referencing 
	// URLs below. We ALSO need this list as an array, so, 
	// now we explode the list by the commas
	$courseids = explode(',',$_GET['courseids']);
	// if there are 
	//print_r($courseids); exit;
	
	if($courseids[0] > 0) {
		$c =  getCoursesClasses($courseids);
	} else {
		$cids = '';
		$c =  getClasses();
	}
}

$activeids = array();


// Pop the headers off the top
array_shift($c);
// Create a temp array for the array_multisort below
$tmp = array();
// loop through everything and add the start date to
// the temp array
foreach($c as $line) {
	$tmp[] = $line[8];
}
// Sort the whole kit and kaboodle by start date from
// oldest to newest
array_multisort($tmp, SORT_ASC, $c);
//
// Now let's run through the whole thing and process it, removing
// classes with dates older than "today" and any requested classes
//
$count = 0;
$inactive = 0;
$upclasses = array();
$today = date('Y-m-d');
foreach($c as $row) {

	if($row[1] == 'Deleted') continue;
	$tbdcitytest = explode('TBD - ',$row[25]);
	$tbdvenuetest = explode('TBD - ',$row[24]);
	if(isset($tbdcitytest[1]) || isset($tbdvenuetest[1])) {
		if($row[23] == '') array_push($upclasses,$row);
	}
	$count++;
}
?>
<?php getHeader() ?>
<title>Classes with no venue ID :(</title>
<link href="/lsapp/css/summernote-bs4.css" rel="stylesheet">
<style>
.newannouce {
	display: none;
}
thead {
	background-color: #FFF;
}
</style>

<?php getScripts() ?>

<body>
<?php getNavigation() ?>
<?php if(canAccess()): ?>
<div class="container-fluid">
<div class="row">
<div class="col-md-12">	
<div><?php echo count($upclasses) ?> classes need a venue ID</div>
<ul>
	<?php foreach($upclasses as $row): ?>
	<li>
	
	<a href="class.php?classid=<?= h($row[0]) ?>" target="_blank">
		<?php print goodDateShort($row[8],$row[9]) ?>
	</a>
			
		<?= h($row[6]) ?>
	</li>

<?php endforeach ?>
</ul>

</div>
</div>
</div>

<?php else: ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>

<?php require('templates/javascript.php') ?>
<script src="/lsapp/js/summernote-bs4.js"></script>
<script src="https://unpkg.com/sticky-table-headers"></script>
<script>
$(document).ready(function(){

	$('.search').focus();
	
	$('.newannform').on('click',function(e){
		e.preventDefault();
		$('.announcing').focus();
		$('.newannouce').toggle();
	});
	var upcomingoptions = {
		valueNames: [ 'status', 
						'startdate', 
						'course', 
						'facilitator', 
						'region', 
						'venue', 
						'city', 
						'itemcode', 
						'enrolled', 
						'pending', 
						'waitlist', 
						'dropped', 
						'reserved'
					]
	};
	var upcomingClasses = new List('upcoming-classes', upcomingoptions);
	upcomingClasses.on('searchComplete', function(){
		//console.log(upcomingClasses.update().matchingItems.length);
		$('.classcount').html(upcomingClasses.update().matchingItems.length);
	});
	
	
	
	
	$('.summernote').summernote({
		//airMode: true,
		popover: {
			air: [
				['color', ['color']],
				['font', ['bold', 'underline', 'clear']],
				['table']
			]
		},
		toolbar: [
			// [groupName, [list of button]]
			['style'],
			['style', ['bold', 'italic']],
			['para', ['ul', 'ol']],
			['color', ['color']],
			['link'] //,['codeview']
		],
		placeholder: 'Type here',
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
	
	$('table').stickyTableHeaders();
	
	
	$('.facilitatorclaim').on('submit',function(e){

	if (confirm('This will assign you as a facilitator of this class. Proceed?')) {
		var form = $(this);
		var url = form.attr('action');

		//form.nextAll('.alert').first().fadeOut().remove();
		
		$.ajax({
			type: "GET",
			url: url,
			data: form.serialize(),
			success: function(data)
			{
				userlink = '<a href="person.php?idir='+data+'">'+data+'</a>';
				form.after(userlink);
				form.remove();
				//form.closest('tr').fadeOut().remove();
				
			},
			statusCode: 
			{
				403: function() {
					form.after('<div class="alert alert-warning">You must be logged in.</div>');
				}
			}});
		e.preventDefault();	
		
	} else {
		e.preventDefault();
		return false;
	}


});
	
});
</script>

<?php require('templates/footer.php'); ?>