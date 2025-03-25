<?php 

require('inc/lsapp.php');

// IDIR,Role,Name,Email,Status,Phone,Title
$peeps = getPeopleAll();
$teams = getTeams();

// populate our people array with our pre-defined teams as arrays
$allFolks = array();
foreach($teams as $team => $teamDetails) {
	$allFolks[$team] = [];
}

// add our peeps to their team array
// if they are in a leadership role, add them to the front of the array
foreach($peeps as $person) {
	if($person[4] == 'Active') {
		if(array_key_exists($person[1], $allFolks)) {
			if($person[8]) {
				array_unshift($allFolks[$person[1]], $person);
			} else {
				$allFolks[$person[1]][] = $person;
			}
		} 
	}
}

getHeader();

?>
<title>Branch</title>
<?php getScripts() ?>
<style>
.pronouns {
	color: #666;
	font-style: italic;

}

h1, h2 { 
	/* border-bottom: 1px solid #FFF; */
	color: #036; 
	
}
.bar {
	border-top-right-radius: 10px; 
	border-bottom-right-radius: 10px;
	box-shadow: 0 0 2px #666;
	height: 10px;
	margin: 0;
	padding: 0;
}


</style>
<body>
<?php getNavigation() ?>
<?php if(canAccess()): ?>
<div class="container-fluid" id="peoplelist">
<div class="row justify-content-md-center mb-3">
<div class="col-12">

<h1 class="text-center text-primary-emphasis">Corporate Learning Branch</h1>
</div>

<div class="col">
	<div class="card mb-3 mx-auto" style="min-width: 375px; max-width: 410px;">
	<div class="card-body">
		<span class="badge text-bg-success float-end"><?= $allFolks['ExecutiveDirector'][0][8] ?></span>
		<div class="name"><strong><a href="/lsapp/person.php?idir=<?= $allFolks['ExecutiveDirector'][0][0] ?>"><?= $allFolks['ExecutiveDirector'][0][2] ?></a></strong>, 
		<span class="title"><?php if(isset($allFolks['ExecutiveDirector'][0][6])) echo $allFolks['ExecutiveDirector'][0][6] ?></span></div>
		<?php
		$colors = explode('|', $allFolks['ExecutiveDirector'][0][10]);
		$prevpercnt = 0;
		$cc = 0;
		// Cool Blue, Earth Green, Sunshine Yellow, Fiery Red
		foreach($colors as $percnt) {
			if($percnt > $prevpercnt) {
				$highest = $percnt;
				if($cc == 1) {
					$type = 'Cool Blue';
				} else if($cc == 2) {
					$type = 'Earth Green';
				} else if($cc == 3) {
					$type = 'Sunshine Yellow';
				} else if($cc == 4) {
					$type = 'Fiery Red';
				}
			}
			$prevpercnt = $percnt;
			$cc++;
		}
		if($colors[0] > 0):
		?>
		<div class="mb-0 bg-light-subtle shadow-sm" style="border-radius: 10px; border: 1px solid #F1F1F1; margin: 0; padding: 0; position: relative; width: 100%;">
			<div style="border-right: solid rgb(256, 256, 256, 100%); height: 100%; position:absolute; width: 50%  "></div>
			<div id="bluebar" class="bar" style="border-top-left-radius: 10px; background-color: DodgerBlue; width: <?= $colors[1] ?>%"></div>
			<div id="greenbar" class="bar" style="background-color: MediumSeaGreen; width: <?= $colors[2] ?>%"></div>
			<div id="yellowbar" class="bar" style="background-color: gold; width: <?= $colors[3] ?>%"></div>
			<div id="redbar" class="bar" style="border-bottom-left-radius: 10px; background-color: Crimson; width: <?= $colors[4] ?>%"></div>
		</div>
		<?php endif ?>
	</div>
	</div>
</div>
</div>

<div class="row justify-content-md-center mb-3 mx-5">

<div class="col-lg-2" name="side-nav">
	<div class="card sticky-top m-auto z-0 overflow-hidden" style="top: 65px; max-width: 310px;">
		<h5 class="card-header">Teams</h4>
		<ul class="list-group list-group-flush">
		<?php foreach($teams as $teamId => $teamDeets): ?>
			<?php // skip Internal/External and ED
				if($teamDeets['isBranch'] == 0 || $teamDeets['name'] == 'Executive Director') {
				continue;
				}
			?>
			<li class="list-group-item">
			<div class="form-check">
				<input class="form-check-input" type=checkbox value="" id="<?= $teamId ?>-checkbox" autocomplete="off">
				<label class="form-check-label" for="<?= $teamId ?>-checkbox"><?= $teamDeets['name'] ?></label>
			</div>
			</li>
		<?php endforeach ?>
		</ul>
		<div class="card-footer">
			<a href="#" id="expand-all" class="card-link">Expand all</a>
			<a href="#" id="collapse-all" class="card-link">Close all</a>	
		</div>
	</div>
</div>

<div class="col" name="teams-area" style="max-width: 1250px;">



<!-- $allFolks loop -->
<?php foreach($allFolks as $teamName => $currentTeam): ?>

	<?php // skip Internal/External and ED
	if($teams[$teamName]['isBranch'] == 0 || $teams[$teamName]['name'] == 'Executive Director') {
		continue;
	}
	?>

	<details class="m-3 p-2 bg-secondary-subtle rounded" id="<?= $teamName ?>-details">

	<summary>
		<h2 class="text-primary-emphasis" style="display: inline;">
			<?php echo $teams[$teamName]['name'] ?>
		</h2>
	</summary>

	<div class="row justify-content-start p-1 g-1">	
	<?php foreach($currentTeam as $person): ?>
		<div class="col-xxl-4 col-xl-6 col-md-6">
		<div class="p-2 m-2 bg-light-subtle rounded-3">
	
			<?php if($person[8]): ?>
				<span class="badge text-bg-success float-end"><?= $person[8] ?></span>
			<?php endif ?>
			
			<div class="name"><strong><a href="/lsapp/person.php?idir=<?= $person[0] ?>"><?= $person[2] ?></a></strong>
			
			<?php if($person[9] != 'Unspecified'): ?>
				(<span class="pronouns"><?= $person[9] ?></span>)
			<?php endif ?>
			<div class="title"><?php if(isset($person[6])) echo $person[6] ?></div>
			</div>
			
			<?php
			$colors = explode('|',$person[10]);
			$prevpercnt = 0;
			$cc = 0;
			// Cool Blue, Earth Green, Sunshine Yellow, Fiery Red
			foreach($colors as $percnt) {
				if($percnt > $prevpercnt) {
					$highest = $percnt;
					if($cc == 1) {
						$type = 'Cool Blue';
					} else if($cc == 2) {
						$type = 'Earth Green';
					} else if($cc == 3) {
						$type = 'Sunshine Yellow';
					} else if($cc == 4) {
						$type = 'Fiery Red';
					}
				}
				$prevpercnt = $percnt;
				$cc++;
			}
			if($colors[0] > 0):
			?>
				<div class="mb-0 bg-light-subtle shadow-sm" style="border-radius: 10px; border: 1px solid #F1F1F1; margin: 0; padding: 0; position: relative; width: 100%;">
					<div style="border-right: solid rgb(256, 256, 256, 100%); height: 100%; position:absolute; width: 50%  "></div>
					<div id="bluebar" class="bar" style="border-top-left-radius: 10px; background-color: DodgerBlue; width: <?= $colors[1] ?>%"></div>
					<div id="greenbar" class="bar" style="background-color: MediumSeaGreen; width: <?= $colors[2] ?>%"></div>
					<div id="yellowbar" class="bar" style="background-color: gold; width: <?= $colors[3] ?>%"></div>
					<div id="redbar" class="bar" style="border-bottom-left-radius: 10px; background-color: Crimson; width: <?= $colors[4] ?>%"></div>
				</div>
			<?php else: ?>
				<div class="mb-0 bg-light-subtle shadow-sm" style="border-radius: 10px; border: 1px solid #F1F1F1; margin: 0; padding: 0; position: relative; width: 100%; height: 42.5px;">
				</div>
			<?php endif ?>
		</div>
		</div> <!-- /col-4 -->
	<?php endforeach ?> <!-- /person -->

	</details>
	
<?php endforeach ?>	<!-- /foreach $allFolks -->

</div> <!-- /teams-area -->

</div>

</div>
</div>

</div>
</div>

<script>

const allDetails = document.querySelectorAll('details');
const allCheckboxes = document.querySelectorAll('.form-check-input')

// open all details
function openAll() {
	allDetails.forEach((item) => {
		item.open = true;
	})
	allCheckboxes.forEach((item) => {
		item.checked = true;
	})
}
const expandAll = document.getElementById('expand-all');
expandAll.addEventListener("click", openAll);

// close all details
function collapseAll() {
	allDetails.forEach((item) => {
		item.open = false;
	})
	allCheckboxes.forEach((item) => {
		item.checked = false;
	})
}
const closeAll = document.getElementById('collapse-all');
closeAll.addEventListener("click", collapseAll);

// toggle details on change to checkbox
allDetails.forEach((detail) => {
	const checkboxId = `${detail.id.split("-")[0]}-checkbox`;
	const box = document.getElementById(checkboxId);
	box.addEventListener("change", () => {
		detail.open = box.checked;
	})
	detail.addEventListener("toggle", () => {
		box.checked = detail.open;
	})
})

</script>

<?php else: ?>

	<?php require('templates/noaccess.php'); ?>

<?php endif ?>

<?php require('templates/javascript.php') ?>

<?php include('templates/footer.php') ?>