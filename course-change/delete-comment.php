<?php 
opcache_reset();
$path = '../inc/lsapp.php';
require($path);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseid = $_POST['courseid'] ?? null;
    $changeid = $_POST['changeid'] ?? null;
    $commentId = $_POST['comment_id'] ?? null;

    if ($courseid === null || $changeid === null || $commentId === null) {
        die("Error: Missing required parameters.");
    }

    // Find the JSON file for the given change ID
    $filename = "requests/course-{$courseid}-{$changeid}.json";

    if (!file_exists($filename)) {
        die("Error: Change ID {$changeid} not found.");
    }

    // Load the change data
    $changeData = json_decode(file_get_contents($filename), true);

    if (!$changeData) {
        die("Error: Unable to load change data.");
    }

    // Locate the comment in the timeline by comment_id
    foreach ($changeData['timeline'] as $key => $event) {
        if ($event['field'] === 'comment' && ($event['comment_id'] ?? null) === $commentId) {
            // Ensure the logged-in user is the commenter
            if ($event['changed_by'] === LOGGED_IN_IDIR) {
                unset($changeData['timeline'][$key]); // Remove the comment
                $changeData['timeline'] = array_values($changeData['timeline']); // Re-index the array
                break;
            } else {
                die("Error: You can only delete your own comments.");
            }
        }
    }

    // Save the updated JSON file
    file_put_contents($filename, json_encode($changeData, JSON_PRETTY_PRINT));

    // Redirect back to the change details page
    header("Location: ./?courseid={$courseid}&changeid={$changeid}");
    exit;
} else {
    die("Invalid request method.");
}