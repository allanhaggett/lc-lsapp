<?php
opcache_reset();
require('../inc/lsapp.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['status' => 'error', 'message' => 'Invalid request method']));
}

// Validate input
$request_id = $_POST['changeid'] ?? null;
$logged_in_user = LOGGED_IN_IDIR;

if (!$request_id) {
    die(json_encode(['status' => 'error', 'message' => 'Missing request ID']));
}

// Find the JSON file
$courseid = $_POST['courseid'] ?? null;
$filename = "requests/course-{$courseid}-change-{$request_id}.json";

if (!file_exists($filename)) {
    die(json_encode(['status' => 'error', 'message' => 'Request not found']));
}

// Load existing request
$data = json_decode(file_get_contents($filename), true);

if (!$data) {
    die(json_encode(['status' => 'error', 'message' => 'Invalid request data']));
}

// Check if already assigned
if (!empty($data['assign_to']) && $data['assign_to'] === $logged_in_user) {
    die(json_encode(['status' => 'error', 'message' => 'You have already claimed this request']));
}

// Update assignment
$previous_assignee = $data['assign_to'] ?? 'Unassigned';
$data['assign_to'] = $logged_in_user;
$data['date_modified'] = time();

// Log the change in timeline
$data['timeline'][] = [
    'field' => 'assign_to',
    'previous_value' => $previous_assignee,
    'new_value' => $logged_in_user,
    'changed_by' => $logged_in_user,
    'changed_at' => time(),
];

// Save the updated JSON
file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));

echo json_encode(['status' => 'success', 'message' => 'Request claimed successfully', 'assign_to' => $logged_in_user]);