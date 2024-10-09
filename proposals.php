<?php 
require('inc/lsapp.php');
$props = getProposals();
array_shift($props);
?>
<?php getHeader() ?>
<title>Proposals</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-8">
<a href="/learning/proposal/">Proposal Form</a>
<h1>Learning Proposals</h1>
<table class="table">
    <tr>
        <th>Proposal Name</th>
        <th>Status</th>
        <th>Submitted On</th>
        <th>Submitted By</th>
</tr>
<?php foreach($props as $p): // proposalID,name,created,createdby,status ?>
<tr style="background-color: #F1F1F1; border-radius: 5px; padding: 1em;">
    <td>
        <a style="font-weight: bold;" href="proposal.php?proposalid=<?= $p[0] ?>"><?= $p[1] ?></a>
    </td>
    <td><?= $p[4] ?></td>
    <td><?= $p[2] ?></td>
    <td><?= $p[3] ?></td>
</tr>
<?php endforeach ?>
</table>
</div>
</div>
</div>
<?php require('templates/javascript.php') ?>
<?php require('templates/footer.php') ?>