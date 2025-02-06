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

// Define status progression
$status_progression = [
    'Not Started' => 'In Progress',
    'In Progress' => 'Completed',
    'Completed' => 'Completed' // Already done, no further progression
];

// Determine the next status
$current_status = $data['status'] ?? 'Not Started';
$next_status = $status_progression[$current_status] ?? 'Not Started';

// Prevent updating if already completed
if ($current_status === 'Completed') {
    die(json_encode(['status' => 'error', 'message' => 'This request is already completed.']));
}

// Update status
$data['status'] = $next_status;
$data['date_modified'] = time();

// Log the change in timeline
$data['timeline'][] = [
    'field' => 'status',
    'previous_value' => $current_status,
    'new_value' => $next_status,
    'changed_by' => $logged_in_user,
    'changed_at' => time(),
];

// Save the updated JSON
file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));

echo json_encode([
    'status' => 'success',
    'message' => "Request updated to {$next_status}",
    'new_status' => $next_status
]);