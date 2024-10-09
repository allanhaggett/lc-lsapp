<?php 
require('inc/lsapp.php');
$props = getProposals();
?>
<?php getHeader() ?>
<title>Proposals</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6">
<a href="/learning/proposal/">Proposal Form</a> | <a href="proposals.php">All Proposals</a>
<h1>Learning Proposals</h1>

<?php
$file = 'data/backups/' . $_GET['proposalid'] . '.json';
$prop = file_get_contents($file);
$proposal = json_decode($prop);
array_pop($proposal);
foreach($proposal as $key => $val) {
    echo '<strong>' . $key . '</strong>:<br> ' . $val . '<br>';
}
?>
</div>

</body>
</html>