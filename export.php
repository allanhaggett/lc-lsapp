<?php require('inc/lsapp.php') ?>
<?php getHeader() ?>
<title>The Learning Centre | PSA | Learning Support Application</title>
<?php getScripts() ?>
<?php getNavigation() ?>


<?php if(canAccess()): ?>
<div class="container mb-3">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6 mb-3">

<div class="card mb-3">
<div class="card-header">
<h1 class="card-title">Export</h1>
</div>
<div class="card-body">
	<a href="export-all.php" target="_blank" class="btn btn-block btn-success mb-2 text-uppercase">LSApp Escape Hatch</a>
	<div class="alert alert-success mb-0">
		The above button will produce a zipped archive of <em>all of the data files below</em> for you to download.
			The links below will download individual files. 
			These are data-only files that do not contain any business logic.
	</div>
</div>
<ul class="list-group list-group-flush">
<li class="list-group-item"><a href="data/courses.csv" class="">Courses</a></li>
<li class="list-group-item"><a href="data/classes.csv">Classes</a> <small>Starting from 2019-01-01</small></li>
<li class="list-group-item"><a href="data/venues.csv">Venues</a></li>
<li class="list-group-item"><a href="data/categories.csv">Categories</a></li>
<li class="list-group-item"><a href="data/changes-class.csv">Class Changes</a></li>
<li class="list-group-item"><a href="data/changes-course.csv">Course Changes</a></li>
<li class="list-group-item"><a href="data/checklists.csv">Checklists</a></li>
<li class="list-group-item"><a href="data/couriers.csv">Couriers</a></li>
<li class="list-group-item"><a href="data/elm.csv">ELM.csv</a></li>
<li class="list-group-item"><a href="data/links.csv">Links</a></li>
<li class="list-group-item"><a href="data/materials.csv">Materials</a></li>
<li class="list-group-item"><a href="data/materials-order-items.csv">Materials Order Items</a></li>
<li class="list-group-item"><a href="data/materials-orders.csv">Materials Orders</a></li>
<li class="list-group-item"><a href="data/notes.csv">Notes</a></li>
<li class="list-group-item"><a href="data/notes-booking.csv">Booking Notes</a></li>
<li class="list-group-item"><a href="data/people.csv">People</a></li>
<li class="list-group-item"><a href="data/regions.csv">Regions</a></li>

</ul>
</div>


</div>
</div>
</div>
<?php else: // if canAccess() ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>


<?php require('templates/javascript.php') ?>


<?php require('templates/footer.php'); ?>