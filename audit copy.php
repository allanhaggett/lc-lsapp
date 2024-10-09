<?php 
require('inc/lsapp.php');
//$evals = getEvaluations();

$file = 'data/backups/' . $_GET['auditid'] . '.json';
$a = file_get_contents($file);
$audit = json_decode($a);
array_pop($audit);
?>
<?php getHeader() ?>
<title>Audit</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6">
<a href="audit-form.php">Audit Form</a> | <a href="audits.php">All Audits</a>
<h1><?= $audit->ResourceName ?></h1>
<div><a href="audit-update-form.php?auditid=<?= $_GET['auditid'] ?>">Edit</a></div>
<?php
foreach($audit as $key => $val) {
    echo '<strong>' . $key . '</strong>:<br> ';
    if(is_array($val)) {
        echo '<ul>';
        foreach($val as $v) {
            echo '<li>' . nl2br($v) . '</li>';
        }
        echo '</ul>';
    } else {
        echo nl2br($val) . '<br>';
    }
}
?>
</div>

</body>
</html>