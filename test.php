
<form method="get" action="test.php">
	<input type="text" name="status">
	<input type="submit" value="Search">
</form>

<?php 
opcache_reset();
$stat = $_GET['status'];

$f = fopen('resources.csv', 'r');
$count = 0;
fgetcsv($f);
while ($row = fgetcsv($f)) {
	$tags = explode('|',$row[4]);
	if($row[3] == $stat) {
		echo $row[1] . '<br>';
	}
	
}
fclose($f);