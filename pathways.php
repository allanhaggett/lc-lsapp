<?php require('inc/lsapp.php') ?>

<?php if(canAccess()): ?>
<?php

opcache_reset();

?>
<?php getHeader() ?>

<title>LC Pathways</title>

<?php getScripts() ?>
<style>
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


<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6">
<h1>Learning Centre Learning Pathways </h1>
<p>One of the principles for a new organizational alignment that the Learning Centre raised as a priority was to nurture a learning organization. As we evolve in our practices and approaches to how we work, the Leadership Team would like to launch a Learning Centre learning pathway. The goal is that every one of us has a grasp on the essentials and that we can lead with shared knowledge, and through experience from a learner mindset. We want to support your learning journey, enhance the culture of our Branch and focus on how we work. </p>
 
<p>As such, we are supporting each of you to invest in your own development over the next several months. Please take the following courses or engage in the following resources by the end of 2023. If you have already completed a course, you may want to go back in for a refresher. If you feel fresh in your understanding, no need to re-do the learning. </p>
</div>
<div class="col-md-6">  
<h2>How we work </h2>
<li>PM 101: Project Foundations (2 hours) 
<li>Gender-Based Analysis Plus (GBA+): Introduction to Intersectional Analysis (Provincial) (35 mins) 
 
<h2>Working with others </h2>
<li>Building Capacity in Indigenous Relations  
<li>Fierce Foundations (3 hours plus session prep) 
 
<h2>Focusing on Self </h2>
<li>Building Respectful and Inclusive Workplaces 101: Self Awareness (1 hour session prep, 3- hour virtual classroom) 
<li>The Impact of Bias and Assumptions in the Workplace (30 mins session preparation and 90 min workshop) 
 
<h2>Other suggestions (if you have time or interest) </h2>
<li>Learning Curator: Equity, Diversity, and Inclusion Pathway (varies) 
<li>Learning Curator: House of Indigenous Learning Pathway (varies)  
<li>Building a Respectful and Inclusive Workplace 102: Promoting Trust (1 hour session prep, 3- hour virtual classroom) 
<h3>Some tips </h3>
<li>Block time in your calendar (an hour a week over the next 7 months) to engage in a Learning Pathway, or part of a pathway. 
<li>Consider getting a group together (your team or a dyad/triad) and go through a resource at the same time and discuss the learning as you go. 
<li>Plan ahead and register for the synchronous classes sooner than later. 
<li>Talk with your supervisor about planning your learning or about the learning itself. 
<li>Add the learning to your Performance Development Platform. 
<li>Add an agenda item to your team meetings to discuss what you learned. 
</div>
</div>
</div>






<?php else: ?>

<?php require('templates/noaccess.php'); ?>

<?php endif ?>


<?php require('templates/javascript.php') ?>


<?php include('templates/footer.php') ?>