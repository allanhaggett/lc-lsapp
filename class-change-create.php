<?php 

require('inc/lsapp.php');

$fromform = $_POST;
//creqID,ClassID,CourseName,StartDate,City,DateRequested,RequestedBy,Status,CompletedBy,CompletedDate,Request,RequestType,Response,Scheduled
$requestor = stripIDIR($_SERVER["REMOTE_USER"]);
$now = date('Y-m-d H:i:s');
$creqID = date('Ymd-His');
//$creqID = stripIDIR($_SERVER["REMOTE_USER"]) . '-' . date('Ymd-His');
$newchange = Array(
				$creqID,
				h($fromform['ClassID']),
				h($fromform['CourseName']),
				h($fromform['StartDate']),
				h($fromform['City']),
				$now,
				$requestor,
				'Pending',
				'',
				'',
				h($fromform['ChangeRequestNote']),
				h($fromform['ChangeType']),
				'',
				h($fromform['Scheduled'])
			);
	

$change = array($newchange);
$fp = fopen('data/changes-class.csv', 'a+');
foreach ($change as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);





// $curl = curl_init();
// curl_setopt_array($curl, array(
//   CURLOPT_URL => 'https://dev.loginproxy.gov.bc.ca/auth/realms/comsvcauth/protocol/openid-connect/token',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'POST',
//   CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
//   CURLOPT_HTTPHEADER => array(
//     'Content-Type: application/x-www-form-urlencoded',
//     'Authorization: Basic Q0RBQ0M5REYtQjlDNTg3NUM5NEM6OTRlYzdjNTctNGZmZi00ZmRmLWE0MzktMzViMmRkYjZiNzJi'
//   ),
// ));

// $tokenres = curl_exec($curl);
// $token = json_decode($tokenres);

// curl_setopt_array($curl, array(
//   CURLOPT_URL => 'https://ches-dev.api.gov.bc.ca/api/v1/email',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'POST',
//   CURLOPT_POSTFIELDS =>'{
//   "bcc": [],
//   "bodyType": "html",
//   "body": "<h1>Change Request</h1><p>A colleague has issued a change request.</p>",
//   "cc": [],
//   "delayTS": 0,
//   "encoding": "utf-8",
//   "from": "noreply_curator@gov.bc.ca",
//   "priority": "normal",
//   "subject": "Curator Activity Report",
//   "to": ["allan.haggett@gov.bc.ca"],
//   "tag": "email_0b7565ca"
// }
// ',
//   CURLOPT_HTTPHEADER => array(
//     'Content-Type: application/json',
//     'Authorization: Bearer ' . $token['access_token'],
//     'Cookie: 0662357d14092c112d042bc0007de896=0103b8f16bed8abcb82235ad88974279'
//   ),
// ));

// $response = curl_exec($curl);

// curl_close($curl);
//echo $response;






header('Location: /lsapp/class.php?classid=' . $fromform['ClassID']);
?>