<?php 

require('inc/lsapp.php');

// IDIR,Role,Name,Email,Status,Phone,Title
$peeps = getPeopleAll();
$teams = getTeams();

$allFolks = array();
foreach($teams as $team => $teamDetails) {
	$allFolks[$team] = [];
}

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
	text-shadow: 2px 2px 0 #FFF;
	
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
<div class="col-md-12">

<h1 class="text-center">Corporate Learning Branch</h1>
</div>

<div class="col-md-3">
	<div class="card mb-3">
	<div class="card-body">
		<span class="badge text-bg-success"><?= $allFolks['ExecutiveDirector'][0][8] ?></span>
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
			<div style="border-right: 2px solid black; height: 100%; position:absolute; width: 50%  "></div>
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

<div class="row justify-content-md-center mb-3">

<!-- $allFolks loop -->
<?php foreach($allFolks as $teamName => $currentTeam): ?>

	<?php // skip Internal/External and ED
	if($teams[$teamName]['isBranch'] == 0 or $teams[$teamName]['name'] == 'Executive Director') {
		continue;
	}
	?>

<div class="col-md-3">
	<h2>
		<a href="/lsapp/teams.php?teamname=<?php echo $teamName ?>"><?php echo $teams[$teamName]['name'] ?></a>
	</h2>

	<?php foreach($currentTeam as $person): ?>
		<div class="p-2 mb-1 bg-light-subtle rounded-3">
	
			<?php if($person[8]): ?>
				<span class="badge text-bg-success"><?= $person[8] ?></span>
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
				<div style="border-right: 2px solid black; height: 100%; position:absolute; width: 50%  "></div>
				<div id="bluebar" class="bar" style="border-top-left-radius: 10px; background-color: DodgerBlue; width: <?= $colors[1] ?>%"></div>
				<div id="greenbar" class="bar" style="background-color: MediumSeaGreen; width: <?= $colors[2] ?>%"></div>
				<div id="yellowbar" class="bar" style="background-color: gold; width: <?= $colors[3] ?>%"></div>
				<div id="redbar" class="bar" style="border-bottom-left-radius: 10px; background-color: Crimson; width: <?= $colors[4] ?>%"></div>
			</div>
			<?php endif ?>
		</div>

	<?php endforeach ?>

</div> <!-- /col -->

<?php endforeach ?>	<!-- /foreach $allFolks -->
</div>



</div>
</div>


</div>
</div>

<?php else: ?>

	<?php require('templates/noaccess.php'); ?>

<?php endif ?>

<?php require('templates/javascript.php') ?>

<?php include('templates/footer.php') ?>