<?php 
require('inc/lsapp.php');
$audits = getAudits();
array_shift($audits);
?>
<?php getHeader() ?>
<title>All Audits</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-12">
<a href="/learning/resource-review/" class="btn btn-primary float-end">Review Form</a>
<h1>All Reviews</h1>
<!-- Evaluationid,Created,CreatedBy,LSAppcourseID,ResourceName,resourceType,ResourceOwner -->
<table class="table table-striped">
    <tr>
        <th>Status</th>
        <th>Type</th>
        <th>Submitter</th>
        <th>Name</th>
        <th>7P Alignment</th>
        <!-- <th>Submitted On</th> -->
        <!-- <th>Submitted By</th> -->
        <!-- <th>LSApp Course ID</th> -->
</tr>

<?php foreach($audits as $a): // AuditID,Created,CreatedBy,LSAppcourseID,ResourceName,resourceType,Status ?>
    <tr style="border-radius: 5px; padding: 1em;">
    <td>
    <?php if($a[6] == "Completed"): ?>
	<span class="badge bg-success text-white"><?= $a[6] ?></span> 
	<?php else: ?>
	<span class="badge bg-warning "><?= $a[6] ?></span> 
	<?php endif ?>
    </td>
    <td><?= $a[5] ?></td>
    <td><a href="/lsapp/person.php?idir=<?= $a[2] ?>"><?= $a[2] ?></a></td>
    <td>
        <a style="font-weight: bold;" href="/learning/resource-review/review.php?auditid=<?= $a[0] ?>"><?= $a[4] ?></a><br>
        <!-- <small><a style="font-weight: bold;" href="course.php?courseid=<?= $a[3] ?>">LSApp</a></small> -->
    </td>
    
    <td>
    <?php if($a[7] == 25): ?>
		<!-- 25% - Significant work to align<br> -->
		<meter id="fuel" min="0" max="100" low="0" high="100" optimum="100" value="25">25% - Significant work to align</meter>
	<?php elseif($a[7] == 50): ?>
		<!-- 50% - Partially in alignment<br> -->
		<meter id="fuel" min="0" max="100" low="0" high="100" optimum="100" value="50">50% - Partially in alignment</meter>
	<?php elseif($a[7] == 75): ?>
		<!-- 75% - Mostly in alignment<br> -->
		<meter id="fuel" min="0" max="100" low="0" high="100" optimum="100" value="75">75% - Mostly in alignment</meter>
	<?php elseif($a[7] == 100): ?>
		<!-- 100% - Completely in alignment<br> -->
		<meter id="fuel" min="0" max="100" low="0" high="100" optimum="100" value="100">100% - Completely in alignment</meter>
	<?php else: ?>
		Alignment unknown
	<?php endif ?>    
    </td>
    <!-- <td><?= $a[1] ?></td> -->
    <!-- <td><?= $a[3] ?></td> -->
</tr>
<?php endforeach ?>
</table>
</div>
</div>
</div>
<?php require('templates/javascript.php') ?>
<?php require('templates/footer.php') ?>