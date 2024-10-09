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
	//
	// We only wish to see classes which have an end date greater than today
	// and don't show it if it's been deleted
	if($row[1] == 'Deleted') continue;
	if($row[9] < $today) continue;
	//
	// We want to continue to show inactive classes, but we also want an accurate
	// count of classes that are upcoming; we count the inactives and subtract them 
	// from the total 
	//
	if($row[1] == 'Inactive') $inactive++;
	//
	// If the status is Requested, we skip the line entirely
	// NONONONO!
	//
	//if($row[1] == 'Requested') continue;
	//
	// Add the class to the array that we'll loop through below
	//
	array_push($upclasses,$row);
	$count++;
}
?>
<?php getHeader() ?>
<title>Upcoming Classes offered by the BC Public Service Agency Learning Centre</title>
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
<div id="upcoming-classes">
	<div class="row">
		<div class="col-md-6">
			<?php $post = getAnnounceLast() ?>
			<?php if($post[3]): ?>
			<?php $n = preg_replace('/(^|\s)@([\w_\.]+)/', '$1<a href="person.php?idir=$2">@$2</a>', $post[3]) ?>
			<div class="my-3"><?= $n ?></div>
			<!--<small>Posted <?= goodDateShort($post[1]) ?> by <a href="person.php?idir=<?= $post[2] ?>"><?= $post[2] ?></a></small>-->
			<?php endif ?>
			<?php if(isAdmin()): ?>
		
			<form method="post" action="announcement-create.php" class="newannouce">
			<textarea name="Body" id="Body" class="form-control summernote announcing"></textarea>
			<input type="submit" class="btn btn-light my-2 btn-block" value="Post Announcement">
			</form>
			<?php endif ?>
			<div class="">
			<div class="dropdown">
				<button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Course Filter
				</button>
				<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				<?php foreach($courses as $course): ?>
					<?php if($course[1] == 'Active'): ?>
					<?php if(in_array($course[0],$courseids)): ?>
						<?php $cdeet = array($course[0],$course[2]) ?>
						<?php array_push($activeids,$cdeet) ?>
					<?php else: ?>
					<?php if($cids): ?>
					<a class="dropdown-item" href="index.php?courseids=<?= $cids ?>,<?= h($course[0]) ?>" class="btn btn-sm btn-primary">
						<?= h($course[2]) ?>
						<span class="badge badge-light"><?= $course[4] ?></span>
					</a>  
					
					<?php else: ?>
					<a class="dropdown-item" href="index.php?courseids=<?= h($course[0]) ?>" class="btn btn-sm btn-primary">
						<?= h($course[2]) ?>
						<span class="badge badge-light"><?= $course[4] ?></span>
					</a>  
					<?php endif ?>
					<?php endif ?>
					<?php endif ?>
				<?php endforeach ?>
				</div>
			</div>

			<?php if($activeids): ?>
			Showing only:
			<?php else: ?>
			Showing all courses
			<?php endif ?>
			<div class="mb-3">
			<?php foreach($activeids as $aid): ?>
			<?php 
			$newcourseids = ''; 
			foreach($courseids as $id) {
			if($id == $aid[0]) {
				continue;
			} else {
				if(!$newcourseids) {
					$newcourseids = $id;
				} else {
					$newcourseids = $newcourseids . ',' . $id;
				}
			}
			}
			?>
			<span class="badge badge-light">
			<a href="index.php?courseids=<?= $newcourseids ?>" class="" style="font-size: 16px" title="Remove course from filter">
				<span aria-hidden="true">&times;</span>
			</a> 
			<?= $aid[1] ?>
			</span>
			<?php endforeach ?>
			</div>
			</div>
		</div>
		<div class="col-md-6">
			
			<div class="float-right">
				<a href="subscribe.php" class="btn btn-sm btn-secondary">Subscribe</a>
			</div>
			
			<h1 class="mb-0">
				<span class="classcount"><?= ($count - $inactive) ?></span> 
				Upcoming Classes 
			</h1>
			<small>
				Enrollment numbers as of <?php echo date ("F d Y H:i", filemtime('data/elm.csv')) ?> 
				<!--| <a href="classes-upcoming-export.php">Export</a> -->
				<?php if(isAdmin()): ?> | <a href="#" class="newannform">New Announcement</a><?php endif ?>
			</small>

			<input class="search form-control" placeholder="search">
			<div class="mb-3 mt-1">
				<span class="alert alert-primary text-center py-0">Shipped</span>
				<span class="alert alert-success text-center py-0">Arrived</span>
				<span class="alert alert-warning text-center py-0">
					<a href="#" class="sort asc" data-sort="status">Requested</a>
				</span>
			</div>
		</div>

	</div>
	<div class="table-responsive">
    <table class="table table-sm table-hover">
        <thead>
            <tr>
				<th scope="col" width="5"></th>
				<th scope="col" width="100" class="text-right">Item Code</th>
                <th scope="col" width="138" class="text-right"><a href="#" class="sort" data-sort="startdate">Class Date</a></th>
                <th scope="col" width="300"><a href="#" class="sort" data-sort="course">Course</a></th>
                <th scope="col" width="250"><a href="#" class="sort" data-sort="venue">Venue</a></th>
                <th scope="col" width="130"><a href="#" class="sort" data-sort="city">City</th>
                <!--<th scope="col" width="80"><a href="#" class="sort" data-sort="region">Region</th>-->
                <th scope="col" width="100"><a href="#" class="sort" data-sort="facilitator">Facilitator</th>
				<th scope="col" width="55"><a href="#" class="sort text-center" data-sort="enrolled"><small>Enrolled</small></a></th>
				<th scope="col" width="55"><a href="#" class="sort text-center" data-sort="reserved"><small>Reserved</small></a></th>
				<th scope="col" width="55"><a href="#" class="sort text-center" data-sort="pending"><small>Pending</small></a></th>
				<th scope="col" width="55"><a href="#" class="sort text-center" data-sort="waitlist"><small>Waitlist</small></a></th>
				<th scope="col" width="55"><a href="#" class="sort text-center" data-sort="dropped"><small>Dropped</small></a></th>
            </tr>
        </thead>
	<tbody class="list">
	<?php foreach($upclasses as $row): ?>
	<?php 
	$statrow = '';
	$issueflag = '';
	if(!$row[7] && $row[4] != 'Dedicated' && $row[1] != 'Requested' && $row[1] != 'Inactive') {
		$issueflag = '<span class="badge badge-danger">???</span>';
	}
	//if($row[1] == 'Pending') {
	//	$statrow = 'table-primary';
	//} else
	if($row[1] == 'Inactive') {
		$statrow = 'cancelled';
	}
	if($row[1] == 'Requested') {
		$statrow = 'table-warning';
	}	
	if($row[49] == 'Shipped') {
		$statrow = 'table-primary';
	} elseif($row[49] == 'Arrived') {
		$statrow = 'table-success';
	}
	?>
	<tr class="<?= $statrow ?>">
		<td class="status"><div style="display:none"><?= h($row[1]) ?> <?= h($row[49]) ?></div></td>
		<td class="text-right itemcode">
			<?= $issueflag ?>
			<small><?= h($row[7]) ?></small>
			<?php if($row[4] == 'Dedicated'): ?>
			<span class="badge badge-light">Dedicated</span>
			<?php endif ?>
		</td>
		<td class="text-right">
			<a href="class.php?classid=<?= h($row[0]) ?>">
				<?php print goodDateShort($row[8],$row[9]) ?>
			</a>
			<div class="startdate" style="display: none"><?= h($row[8]) ?></div>
		</td>
		<td class="course"><a href="course.php?courseid=<?= h($row[5]) ?>"><?= h($row[6]) ?></a></td>
		<td class="venue">
		<?php if($row[23]): ?>
		<a href="venue.php?vid=<?= h($row[23]) ?>"><?= h($row[24]) ?></a>
		<?php else: ?>
		<span title="One off venue; no page create for it yet."><?= h($row[24]) ?></span>
		<?php endif ?>
		<div style="display: none">
			<?= h($row[28]) ?><br>
			<?= h($row[29]) ?><br>
			<?= h($row[30]) ?><br>
			<?= h($row[26]) ?><br>
			<?= h($row[25]) ?><br>
			<?= h($row[27]) ?>
		</div>
		<?php if($row[49] == 'No Ship'): ?>
		<span class="badge badge-light"><?= h($row[49]) ?></span>
		<?php endif ?>
		</td>
		<td class="city"><a href="city.php?name=<?= h($row[25]) ?>"><?= h($row[25]) ?></a></td>
		<!--<td class="region"><a href="region.php?name=<?= h($row[47]) ?>"><?= h($row[47]) ?></a></td>-->
		<td class="facilitator">
		<?php if($row[14] != 'Unknown' && $row[14] != ''): ?>
			<a href="person.php?idir=<?= h($row[14]) ?>"><?= h($row[14]) ?></a>
		<?php else: ?>
			<form method="get" action="class-facilitator-claim.php" class="facilitatorclaim">
			<input type="hidden" name="cid" id="cid" value="<?= h($row[0]) ?>">
			<input type="submit" class="btn btn-sm btn-link" value="Claim">
			</form>
		<?php endif ?>
		</td>
		<!--<td><?= h($row[47]) ?></td>-->
		<td class="enrolled text-center" style="background:rgba(0,0,0,.05)">
		<?php if($row['18'] < $row[11] && $row[1] != 'Inactive' && $row[4] != 'Dedicated'): ?>
		<span class="badge badge-danger" title="Enrollment is currently below the set minimum"><?= h($row[18]) ?></span>
		<?php else: ?>
		<span class="badge badge-light"><?= h($row[18]) ?></span>
		<?php endif ?>
		</td>
		<td class="reserved text-center"><span class="badge badge-light"><?= h($row[19]) ?></span></td>
		<td class="pending text-center" style="background:rgba(0,0,0,.05)"><span class="badge badge-light"><?= h($row[20]) ?></span></td>
		<td class="waitlist text-center"><span class="badge badge-light"><?= h($row[21]) ?></span></td>
		<td class="dropped text-center" style="background:rgba(0,0,0,.05)"><span class="badge badge-light"><?= h($row[22]) ?></span></td>
	</tr>
<?php endforeach ?>
</tbody>
</table>
</div>
</div>
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
						'region', 
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