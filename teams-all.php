<?php 
// In the name of under-engineering, I am intentionally doing this
// in a very naive way that violates DRY (only by a single factor)
require('inc/lsapp.php');
// IDIR,Role,Name,Email,Status,Phone,Title
$peeps = getPeopleAll();
$dm = array();
$operations = array();
$peopleleaders = array();
$allemployees = array();
$governance = array();
foreach($peeps as $peep) {
	
	if($peep[0] == 'leanhill') {
		array_push($dm,$peep);
	}
	
	if($peep[1] == 'Operations') {
		if($peep[4] == 'Active') {
			array_push($operations,$peep);
		}
	} elseif($peep[1] == 'Leaders') {
		if($peep[4] == 'Active') {
			array_push($peopleleaders,$peep);
		}
	} elseif($peep[1] == 'Employees') {
		if($peep[4] == 'Active') {
			array_push($allemployees,$peep);
		}
	} elseif($peep[1] == 'Governance') {
		if($peep[4] == 'Active') {
			array_push($governance,$peep);
		}
	}
}

getHeader();

?>
<title>People</title>
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

<h1 class="text-center">Learning Centre - Functional Teams</h1>
</div>

<div class="col-md-3">
	<div class="card mb-3">
	<div class="card-body">

		<?php if(isAdmin()): ?>
		<div class="float-right"><a href="person-update.php?idir=<?= $dm[0][0] ?>" class="btn btn-light btn-sm">Edit</a></div>
		<?php endif ?>
		<span class="badge badge-success">EXECUTIVE DIRECTOR</span>
		<div class="name"><strong><a href="/lsapp/person.php?idir=<?= $dm[0][0] ?>"><?= $dm[0][2] ?></a></strong>, 
		<span class="title"><?php if(isset($dm[0][6])) echo $dm[0][6] ?></span></div>
		<!-- <div class="email"><?= $dm[0][3] ?></div>
		<div class="phone"><?php if(isset($dm[0][5])) echo $dm[0][5] ?></div> -->
		<?php
		$colors = explode('|',$dm[0][10]);
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
<div class="col-md-3">

<h2 class="">
	<a href="/lsapp/teams.php?teamname=Operations">Operations &amp; Technology</a>
</h2>

<?php foreach($operations as $peep): ?>

	<div class="p-2 mb-1 bg-light-subtle rounded-3">
	
		<?php if($peep[8]): ?>
		<?php if($peep[8] == 100): ?>
		<span class="badge badge-success">Executive Director</span>
		<?php else: ?>
		<span class="badge badge-success">Director</span>
		<?php endif ?>
		<?php endif ?>
		<?php if(isAdmin()): ?>
		<!-- <div class="float-right"><a href="person-update.php?idir=<?= $peep[0] ?>" class="btn btn-light btn-sm">Edit</a></div> -->
		<?php endif ?>
		<div class="name"><strong><a href="/lsapp/person.php?idir=<?= $peep[0] ?>"><?= $peep[2] ?></a></strong>
		<?php if($peep[9] != 'Unspecified'): ?>
		(<span class="pronouns"><?= $peep[9] ?></span>)<?php endif ?>
		<div class="title"><?php if(isset($peep[6])) echo $peep[6] ?></div>
		</div>
		
		<!-- <div class="email"><?= $peep[3] ?></div>
		<div class="phone"><?php if(isset($peep[5])) echo $peep[5] ?></div> -->
		<?php
		$colors = explode('|',$peep[10]);
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
</div>


<div class="col-md-3">
<h2 class="">
	<a href="/lsapp/teams.php?teamname=Employees">Corp. Learning - All Employees</a>
</h2>

<?php foreach($allemployees as $peep): ?>

	<div class="p-2 mb-1 bg-light-subtle rounded-3">
	
		<?php if($peep[8]): ?>
		<?php if($peep[8] == 100): ?>
		<span class="badge badge-success">Executive Director</span>
		<?php else: ?>
		<span class="badge badge-success">Director</span>
		<?php endif ?>
		<?php endif ?>
		<?php if(isAdmin()): ?>
		<!-- <div class="float-right"><a href="person-update.php?idir=<?= $peep[0] ?>" class="btn btn-light btn-sm">Edit</a></div> -->
		<?php endif ?>
		<div class="name"><strong><a href="/lsapp/person.php?idir=<?= $peep[0] ?>"><?= $peep[2] ?></a></strong>
		<?php if($peep[9] != 'Unspecified'): ?>
		(<span class="pronouns"><?= $peep[9] ?></span>)<?php endif ?>
		<div class="title"><?php if(isset($peep[6])) echo $peep[6] ?></div>
		</div>
		<!-- <div class="email"><?= $peep[3] ?></div>
		<div class="phone"><?php if(isset($peep[5])) echo $peep[5] ?></div> -->
		<?php
		$colors = explode('|',$peep[10]);
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

</div>


<div class="col-md-3">
<h2>
	<a href="/lsapp/teams.php?teamname=Leaders">Corp. Learning - People Leaders</a>
</h2>


<?php foreach($peopleleaders as $peep): ?>

	<div class="p-2 mb-1 bg-light-subtle rounded-3">
	
		<?php if($peep[8]): ?>
		<?php if($peep[8] == 100): ?>
		<span class="badge badge-success">Executive Director</span>
		<?php else: ?>
		<span class="badge badge-success">Director</span>
		<?php endif ?>
		<?php endif ?>
		<?php if(isAdmin()): ?>
		<!-- <div class="float-right"><a href="person-update.php?idir=<?= $peep[0] ?>" class="btn btn-light btn-sm">Edit</a></div> -->
		<?php endif ?>
		<div class="name"><strong><a href="/lsapp/person.php?idir=<?= $peep[0] ?>"><?= $peep[2] ?></a></strong>
		<?php if($peep[9] != 'Unspecified'): ?>
		(<span class="pronouns"><?= $peep[9] ?></span>)<?php endif ?>
		<div class="title"><?php if(isset($peep[6])) echo $peep[6] ?></div>
		</div>
		<!-- <div class="email"><?= $peep[3] ?></div>
		<div class="phone"><?php if(isset($peep[5])) echo $peep[5] ?></div> -->
		<?php
		$colors = explode('|',$peep[10]);
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

</div>

<div class="col-md-3">
<h2>
	<a href="/lsapp/teams.php?teamname=Governance">Planning, Evaluation, &amp; Governance</a>
</h2>


<?php foreach($governance as $peep): ?>

	<div class="p-2 mb-1 bg-light-subtle rounded-3">
	
		<?php if($peep[8]): ?>
		<?php if($peep[8] == 100): ?>
		<span class="badge badge-success">Executive Director</span>
		<?php else: ?>
		<span class="badge badge-success">Director</span>
		<?php endif ?>
		<?php endif ?>
		<?php if(isAdmin()): ?>
		<!-- <div class="float-right"><a href="person-update.php?idir=<?= $peep[0] ?>" class="btn btn-light btn-sm">Edit</a></div> -->
		<?php endif ?>
		<div class="name"><strong><a href="/lsapp/person.php?idir=<?= $peep[0] ?>"><?= $peep[2] ?></a></strong>
		<?php if($peep[9] != 'Unspecified'): ?>
		(<span class="pronouns"><?= $peep[9] ?></span>)<?php endif ?>
		<div class="title"><?php if(isset($peep[6])) echo $peep[6] ?></div>
		</div>
		<!-- <div class="email"><?= $peep[3] ?></div>
		<div class="phone"><?php if(isset($peep[5])) echo $peep[5] ?></div> -->
		<?php
		$colors = explode('|',$peep[10]);
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

</div>


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