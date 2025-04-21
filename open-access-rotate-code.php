<?php
date_default_timezone_set('America/Vancouver');

$jsonPath = __DIR__ . '/data/open-access-code.json';
$data = json_decode(file_get_contents($jsonPath), true);

// Archive current code
$archived = [
    "code" => $data[0]['code'],
    "createdby" => $data[0]['createdby'],
    "created" => $data[0]['created']
];

// Push into history array
$data[1]['history'][] = $archived;

// Generate a new short random code
function generateRandomCode($length = 10) {
    return substr(str_shuffle('abcdefghjkmnpqrstuvwxyz23456789'), 0, $length);
}

$data[0] = [
    "code" => generateRandomCode(),
    "createdby" => "ahaggett",
    "created" => date("Y-m-d H:i:s")
];

// Save it back
file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT));

// Redirect back to UI
header("Location: open-access-code-manager.php");
exit;