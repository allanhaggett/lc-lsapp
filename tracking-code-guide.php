<?php require('inc/lsapp.php') ?>
<?php $idir = stripIDIR($_SERVER["REMOTE_USER"]); ?>
<?php if(canACcess()): ?>
<?php 
$cityname = (isset($_GET['name'])) ? $_GET['name'] : 0;
$venues = getVenues($cityname);
?>
<?php getHeader() ?>

<title>URL Tracking Codes (UTM/MTM) Guide</title>

<?php getScripts() ?>
<body class="bg-light-subtle">
<?php getNavigation() ?>

<div class="container">
<div class="row justify-content-md-center">
<div class="col-md-6">

<h1>URL Tracking Codes (UTM/MTM) Guide</h1>

<p>"<abbr title="Urchin Tracking Module">UTM</abbr>" Codes are designed to allow you
to add a special code to the end of a URL that will show up as unique in a given
"front end analytics" system. This allows you to "see" how many people have clicked
a link that you have given them so you can see how they got there.</p>
<p>For example, say you have a course in the LearningHUB that you want to include in a new 
	general communication, let's say "Information Management (IM) 117"</p>
<p>You would navigate to its page and copy it's URL:</p>
<div class="mb-3">
	<code>https://learningcentre.gww.gov.bc.ca/learninghub/course/im-117-information-management-managing-government-information-privacy-access-to-information-and-security/</code>
</div>
<p>Next you would come up with a unique campaign name that applies specifically to your communication:</p>
<div class="mb-3"><code>myfancycampaign</code></div>
<p>You would then use <a href="https://matomo.org/faq/tracking-campaigns-url-builder/">a tool</a> to add the 
	to the URL in the appropriate format.</p>
<p>In our context with the BCPS, you can't add a tracking code to just any link and expect results. 
	The link in question must be on a platform that we control and that has front-end analytics 
	installed and working.</p>
<p>The platforms that we use that currently have analytics installed are:</p>
<ul>
	<li>LearningHUB</li>
	<li>Learning Curator</li>
</ul>
<p>Note that we do not currently know if PSALS (ELM) provides these analytics. You can add codes 
	to ELM links, but we cannot provide data in return yet.</p>

<p>Here's a tool you can use to help you create links:</p>
<div><a href="https://matomo.org/faq/tracking-campaigns-url-builder/">https://matomo.org/faq/tracking-campaigns-url-builder/</a></div>

</div>
</div>
</div>



<?php require('templates/javascript.php') ?>
<script src="js/clipboard.min.js"></script>
<script>
$(document).ready(function(){
	
	var clipboard = new Clipboard('.copy');
	$('.copy').on('click',function(){ alert('Calendar URL Copied! In Outlook, Add Calendar From Internet, and paste it'); });
	
});
</script>

<?php require('templates/footer.php') ?>

<?php else: ?>


<?php require('templates/noaccess.php') ?>

<?php endif ?>