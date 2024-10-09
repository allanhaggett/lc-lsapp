<?php require('inc/lsapp.php') ?>

<?php $classid = (isset($_GET['classid'])) ? $_GET['classid'] : 0; ?>
<?php $deets = getClass($classid) ?>
<?php getHeader() ?>

<title>
<?php if($deets[4] == 'Dedicated'): ?>
DEDICATED | 
<?php endif ?>
<?php if($deets[7]): ?>
<?= h($deets[7]) ?> | 
<?php endif ?>
<?php print goodDateLong($deets[8],$deets[9]) ?> | 
<?= h($deets[45]) ?> <?= h($deets[6]) ?>.
<?php $tbdtest = explode('TBD - ',$deets[25]) ?>
<?php if(isset($tbdtest[1])): ?><?= h($deets[25]) ?>
<?php else: ?><?= h($deets[24]) ?> in <?= h($deets[25]) ?>
<?php endif ?>
<?php if($deets[14]): ?>
<?= h($deets[14]) ?> facilitating. 
<?php else: ?>Unknown facilitating. 
<?php endif ?>
</title>
<style>
@page { size: auto;  margin: 0mm; }
</style>
<meta name="description" content="Learning Support Adminstration Application (LSApp)">

<?php getScripts() ?>

<div class="container-fluid">

<?php if(canAccess()): ?>

<?php $outcount = 1 ?>
<?php $incount = 1 ?>
<?php $boxes = $deets[34] ?>
<?php if($boxes > 0): ?>
<div class="row">

<?php while($outcount <= $boxes): ?>
<?php if($outcount > 1 && ($outcount+1)%2 == 0): ?>
</div>
<div class="row" style="page-break-before: always !important">
<?php endif ?>
<div class="col-6 mb-3" style="padding: 50px">

<div class="row">
<div class="col-md-3 text-right">
	<!--<div style="font-size: 30px" class="m-0"><?= $deets[36] ?></div>-->
	<?php $couriers = getCouriers($deets[36]) ?>
	<?php foreach($couriers as $courier): ?>
	<?php if($courier[1] == $deets[36]): ?>
	<img src="<?= $courier[7] ?>" class="mt-3">
	<!--<?= $courier[1] ?>-->
	<?php endif ?>
	<?php endforeach ?>
</div>
<div class="col-md-9">
<div style="font-size: 50px" class="m-0" ><?= $deets[37] ?></div>
<div style="font-size: 30px; font-weight: bold"><?= $outcount ?> of <?= $boxes ?></div>
</div>
</div>
<hr>

<?php $tbdtest = explode('TBD - ',$deets[25]) ?>
<?php if(isset($tbdtest[1])): ?>
	<?= h($deets[25]) ?>
<?php else: ?>
	<div class="text-uppercase">Please deliver to:</div>
	<h1><?= h($deets[24]) ?></h1>
	<h2><?= h($deets[26]) ?></h2>
	<h3><?= h($deets[25]) ?>, BC</h3>
	<h3><strong><?= h($deets[27]) ?></strong></h3>

	<strong>Attention: <?= h($deets[31]) ?></strong>

<hr>
<?php endif ?>
<div class="row text-left">
<div class="col-6">
<div class="text-uppercase">FROM: BC Public Service Agency</div>
<h4>The Learning Centre</h4>
<div class="row">
<div class="col-3">
<div class="mt-2" id="qrcodeIn<?= $outcount ?>"></div>
<script type="text/javascript">
//TODO the following phone number should NOT be hard coded
var qrcode = new QRCode("qrcodeIn<?= $outcount ?>", {
text: "tel:250-516-8915",
    width: 50,
    height: 50,
    colorDark : "#333",
    colorLight : "#ffffff",
    correctLevel : QRCode.CorrectLevel.H
});
</script>
</div>
<div class="col-9">
716 Courtney St<br>
Victoria, BC<br>
V8W 1C2<br>
250-516-8915
</div>

</div>

</div>
<div class="col-6">
With regards to:<br>
<h4><strong><?= $deets[6] ?></strong></h4>
<h5><?php print goodDateLong($deets[8],$deets[9]) ?></h5>
<p><?= $deets[7] ?></p>
<!--<div class="mt-2" id="qrcodeLSApp<?= $outcount ?>"></div>
<script type="text/javascript">

var qrcode = new QRCode("qrcodeLSApp<?= $outcount ?>", {
	text: "https://gww.bcpublicservice.gov.bc.ca/lsapp/class.php?classid=<?= $deets[1] ?>",
    width: 50,
    height: 50,
    colorDark : "#333",
    colorLight : "#ffffff",
    correctLevel : QRCode.CorrectLevel.H
});
</script>-->
</div>
</div>

<hr>
</div>
<?php $outcount++ ?>
<?php endwhile ?>
</div>
<div class="row">
<?php while($incount <= $boxes): ?>
<?php if($incount > 1 && ($incount+1)%2 == 0): ?>
</div>
<div class="row" style="page-break-before: always !important">
<?php endif ?>
<div class="col-6 mb-3" style="padding: 50px">
<div class="row">
<div class="col-md-3 text-right">
	<!--<div style="font-size: 30px" class="m-0"><?= $deets[36] ?></div>-->
	<?php $couriers = getCouriers($deets[36]) ?>
	<?php foreach($couriers as $courier): ?>
	<?php if($courier[1] == $deets[36]): ?>
	<img src="<?= $courier[7] ?>" class="mt-3">
	<?php endif ?>
	<?php endforeach ?>
</div>
<div class="col-md-9">
<div style="font-size: 50px" class="m-0" ><?= $deets[38] ?></div>
<div style="font-size: 30px; font-weight: bold"><?= $incount ?> of <?= $boxes ?></div>
</div>
</div>


<hr>
<div class="text-uppercase">Please deliver to:</div>
<h1>The Learning Centre</h1>
<h2>716 Courtney St</h2>
<h3>Victoria, BC</h3>
<h3><strong>V8W 1C2</strong></h3>
250-516-8915<br>
learning.centre.admin@gov.bc.ca
<?php $incount++ ?>
<hr>

<div class="row">
<div class="col-6">
FROM:
<h4><?= h($deets[24]) ?></h4>
<?= h($deets[26]) ?><br>
<?= h($deets[25]) ?>, BC<br>
<?= h($deets[27]) ?>
</div>
<div class="col-6">
With regards to:<br>
<h4><strong><?= $deets[6] ?></strong></h4>
<h5><?php print goodDateLong($deets[8],$deets[9]) ?></h5>	
<p><?= $deets[7] ?></p>
</div>
</div>
<hr>
</div>
<?php endwhile ?>
</div>
<?php else: ?>
<div class="row justify-content-md-center">
<div class="col-md-3">
	<h1 class="my-3">How many boxes would we like to ship?</h1>
	<form method="get" action="class-change-boxes.php" class="my-3">
		<input type="hidden" name="cid" id="cid" value="<?= h($deets[0]) ?>">
		<input type="text" name="boxes" id="boxes" class="form-control mb-3" placeholder="Number of boxes">
		<select name="courier" class="form-control mb-3">
		<option>Select a courier</option>
		<?php $couriers = getCouriers($deets[36]) ?>
		<?php foreach($couriers as $courier): ?>
		<?php if($courier[1] == $deets[36]): ?>
		<option selected><?= $courier[1] ?></option>
		<?php else: ?>
		<option><?= $courier[1] ?></option>
		<?php endif ?>
		<?php endforeach ?>
		</select>
		<div class="row">
		<div class="col-6">
			<label for="TrackingOut">Outgoing Tracking #</label>
			<input type="text" class="form-control" name="TrackingOut" placeholder="TrackingOut" value="<?= h($deets[37]) ?>">
		</div>
		<div class="col-6">
			<label for="TrackingIn">Incoming Tracking #</label>
			<input type="text" class="form-control" name="TrackingIn" placeholder="TrackingIn" value="<?= h($deets[38]) ?>">
		</div>
		</div>
		<input type="submit" class="btn btn-lg btn-primary btn-block my-3" value="Update">
	</form>
</div>
</div>
<?php endif // boxes > 0 ?>
<?php endif // canAccess ?>
</div>

</body>
</html>