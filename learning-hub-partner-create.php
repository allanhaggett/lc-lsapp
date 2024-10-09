<?php 
require('inc/lsapp.php');

$fromform = $_POST;
$partnerid = date('YmdHis');

$newpartner = Array($partnerid,
				h($fromform['PartnerName']),
				h($fromform['PartnerDescription'],
				h($fromform['PartnerLink']))
			);
$partner = array($newpartner);
$fp = fopen('data/learning-hub-partner.csv', 'a+');
foreach ($partner as $fields) {
	fputcsv($fp, $fields);
}
fclose($fp);
header('Location: learning-hub-partners.php');
