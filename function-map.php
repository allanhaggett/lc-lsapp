<?php 
/**
 * Learning Centre Functional Map
 * There was a function map created for all of the things that the LC does.
 * I took the powerpoint slide deck outlining the structure of the functions,
 * created two files: 
 * 1) list all the functions, assigning each an ID
 * 2) a map of user IDIRs to the function id
 */
// Let's depend on LSApp for stuff
require('inc/lsapp.php');

$funid = $_GET['functionid'] ?? 0;

// 
$f = fopen('data/functional-map.csv', 'r');
$functionlist = [];
fgetcsv($f);
while ($row = fgetcsv($f)) {
    array_push($functionlist,$row);
}
fclose($f);

$categories = [];
foreach($functionlist as $fun) {
    if($fun[0] == $funid) $current = $fun;
    array_push($categories,$fun[1]);
}
$categories = array_unique($categories);


if($funid) {
    $fpeep = fopen('data/functional-map-people.csv', 'r');
    $peoplelist = [];
    fgetcsv($fpeep);
    while ($row = fgetcsv($fpeep)) {
        array_push($peoplelist,$row);
    }
    fclose($fpeep);
    $people = [];
    foreach($peoplelist as $func) {
        if($func[1] == $funid) {
            $details = getPerson($func[0]);
            array_push($people, $details);
        }
    }
}


$alllcpeeps = getPeopleAll();


getHeader();

?>
<title>Function Map of Learning Centre</title>
<?php getScripts() ?>

<body>
<?php getNavigation() ?>
<?php if(canAccess()): ?>

<div class="container-fluid" id="peoplelist">
<div class="row justify-content-md-center mb-3">
<div class="col-md-8">
<h1 class="text-center">Learning Centre Function Map</h1>
</div>
</div>
<div class="row justify-content-md-center mb-3">

<?php if(!$funid): ?>

<?php else: ?>
<div class="col-sm-9 col-md-8 col-lg-7 col-xl-6" id="people">
<div class="bg-light-subtle p-3 rounded-3">
<h2 class=""><?= $current[1] ?></h2>
<h3><?= $current[2] ?></h3>
<?php foreach($people as $peep): ?>
    <div>
        <a href="person.php?idir=<?= $peep[0] ?>"><?= $peep[2] ?></a>, <?= $peep[6] ?>
        <form action="function-map-remove-person.php" method="post" class="py-3 d-inline">
        <input type="hidden" name="functionid" id="functionid" value="<?= $funid ?>">
        <input type="hidden" name="idir" id="idir" value="<?= $peep[0] ?>">
        <input type="submit" value="Remove" class="d-inline btn btn-sm btn-link">
        </form> 
    </div>
<?php endforeach ?>
</div>
<form action="function-map-add-person.php" method="post" class="py-3">
<input type="hidden" name="functionid" id="functionid" value="<?= $funid ?>">
<label for="idirs" class="sr-only">Add a person:</label>
<input list="lcpeeps" id="idirs" name="idirs" required>
<datalist id="lcpeeps">
<?php foreach($alllcpeeps as $p): ?>
    <option value="<?= $p[0] ?>"><?= $p[2] ?></option>
<?php endforeach ?>
</datalist>
<input type="submit" value="Add Person">
</form>
</div>
<?php endif ?>


<div class="col-sm-3 col-md-4 col-lg-5 col-xl-4 order-md-first">
    
<?php foreach($categories as $cat): ?>
<div class="p-2">
<div><strong><?= $cat ?></strong></div>
<?php $active = 'btn-light' ?>
<?php foreach($functionlist as $fun): ?>
<?php if($cat == $fun[1]): ?>
<?php if($fun[0] == $funid) $active = 'btn-secondary' ?>
<a class="btn btn-sm <?= $active ?>" href="function-map.php?functionid=<?= $fun[0] ?>"><?= $fun[2] ?></a> 
<?php endif ?>
<?php $active = 'btn-light' ?>
<?php endforeach ?>
</div>
<?php endforeach ?>

</div>

</div>


<?php else: ?>

<?php require('templates/noaccess.php'); ?>

<?php endif ?>


<?php require('templates/javascript.php') ?>
<script>
$(document).ready(function(){

	// $('.getpeople').on('click',function(e){
    //     e.preventDefault();
    //     $('#people').load($(this).attr('href'));
    //     return false;
    // });


});
</script>

<?php include('templates/footer.php') ?>