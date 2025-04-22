<?php
date_default_timezone_set('America/Vancouver');

$jsonPath = __DIR__ . '/data/open-access-code.json';
$data = json_decode(file_get_contents($jsonPath), true);

if (isset($_GET['restore']) && is_numeric($_GET['restore'])) {
    if (restorePreviousCode((int)$_GET['restore'], $data)) {
        file_put_contents($jsonPath, json_encode($data, JSON_PRETTY_PRINT));
    }
    header("Location: open-access-code-manager.php");
    exit;
}

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

function restorePreviousCode($index, &$data) {
    if (!isset($data[1]['history'][$index])) {
        return false;
    }
    $restored = $data[1]['history'][$index];
    unset($data[1]['history'][$index]);
    $data[1]['history'] = array_values($data[1]['history']);
    $data[0] = $restored;
    return true;
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