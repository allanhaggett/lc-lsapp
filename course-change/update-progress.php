<?php
opcache_reset();
require('../inc/lsapp.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die(json_encode(['progress' => 'error', 'message' => 'Invalid request method']));
}

// Validate input
$request_id = $_POST['changeid'] ?? null;
$logged_in_user = LOGGED_IN_IDIR;

if (!$request_id) {
    die(json_encode(['progress' => 'error', 'message' => 'Missing request ID']));
}

// Find the JSON file
$courseid = $_POST['courseid'] ?? null;
$filename = "requests/course-{$courseid}-change-{$request_id}.json";

if (!file_exists($filename)) {
    die(json_encode(['progress' => 'error', 'message' => 'Request not found']));
}

// Load existing request
$data = json_decode(file_get_contents($filename), true);

if (!$data) {
    die(json_encode(['progress' => 'error', 'message' => 'Invalid request data']));
}

// Define progress progression
$progress_progression = [
    'Not Started' => 'In Progress',
    'In Progress' => 'In Review',
    'In Review' => 'Ready to Publish',
    'Ready to Publish' => 'Closed',
    'Closed' => 'Closed',
];

// Determine the next progress
$current_progress = $data['progress'] ?? 'Not Started';
$next_progress = $progress_progression[$current_progress] ?? 'Not Started';

// Prevent updating if already completed
if ($current_progress === 'Completed') {
    die(json_encode(['progress' => 'error', 'message' => 'This request is already completed.']));
}

// Update progress
$data['progress'] = $next_progress;
$data['date_modified'] = time();

// Log the change in timeline
$data['timeline'][] = [
    'field' => 'progress',
    'previous_value' => $current_progress,
    'new_value' => $next_progress,
    'changed_by' => $logged_in_user,
    'changed_at' => time(),
];

// Save the updated JSON
file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));

echo json_encode([
    'progress' => 'success',
    'message' => "Request updated to {$next_progress}",
    'new_progress' => $next_progress
]);
