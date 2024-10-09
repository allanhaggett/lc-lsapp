<?php require('inc/lsapp.php') ?>
<?php getHeader() ?>
<title>The Learning Centre | PSA | Learning Support Application</title>
<?php getScripts() ?>
<style>.carousel-item { padding: 80px 140px;}</style>
<?php //getNavigation() ?>
<body style="background:#333;border-radius:3px;color:#FFF">
<div class="container">
<!-- see me -->
<div class="text-center" style="margin-top: 100px">
	<a href="/lsapp/">
		<img src="img/LSApp.png" alt="LSApp logo" width="420" class="mx-auto">
	</a>
</div>

<div id="carouselExampleControls" class="carousel slide" data-ride="" data-pause="true">
  <ol class="carousel-indicators">
    <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="3"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="4"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="5"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="6"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="7"></li>
    <li data-target="#carouselExampleIndicators" data-slide-to="8"></li>
    
  </ol>
  <div class="carousel-inner">
    <div class="carousel-item active">
		<h1>Integrating Development, Delivery, and Operations with 
		a central repository of meta data and communications for 
		all aspects of course creation and delivery.</h1>
    </div>
    <div class="carousel-item">
		<h1>Bespoke Content Management System</h1>
		Web-based and mobile-friendly<br>
		Lives on “Bellini” where all course content resides
    </div>
    <div class="carousel-item">
		<h1>Focused around people and tracking their contributions.</h1>
		No new accounts/passwords; just your IDIR.<br>
		Simple Access Control List.
    </div>    
	<div class="carousel-item">
		<h1>Each course gets its own page</h1>
		Listing all associated changes, materials, checklists, meta data (e.g. prework links) and upcoming classes
	</div>
	<div class="carousel-item">
		<h1>Each class date gets its own page</h1>
		Listing all associated changes, notes, materials, checklists, venue, shipping information, etc
    </div>    
	<div class="carousel-item">
		<h1>Changes are submitted and tracked on the course/class pages, <strong>in context</strong></h1>
    </div>	

	<div class="carousel-item">
		<h1>Different views allow you to see the big picture</h1>
		Or zoom in to the details
	    </div>	
	<div class="carousel-item">
		<h1>Checks and balances verify information and surface potential issues</h1>
		Transparency. Accountability. Succession management.
  </div>
  
  </div>
    <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
    <span class="sr-only">Previous</span>
  </a>
  <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Next</span>
  </a>
</div>




</div>



<?php require('templates/javascript.php') ?>


<?php require('templates/footer.php'); ?>