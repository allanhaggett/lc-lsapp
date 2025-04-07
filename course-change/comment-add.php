<?php
// Handler for adding a comment to a request

// Include necessary files
$path = '../inc/lsapp.php';
require($path);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseid = htmlspecialchars($_POST['courseid'] ?? '');
    $changeid = htmlspecialchars($_POST['changeid'] ?? '');
    $newComment = trim($_POST['new_comment'] ?? '');

    if (!$courseid || !$changeid || empty($newComment)) {
        echo '<div class="alert alert-danger">Error: All fields are required.</div>';
        exit;
    }

    $filename = "requests/course-{$courseid}-change-{$changeid}.json";

    if (!file_exists($filename)) {
        echo '<div class="alert alert-danger">Error: Request not found.</div>';
        exit;
    }

    $existingData = json_decode(file_get_contents($filename), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo '<div class="alert alert-danger">Error reading request data: ' . json_last_error_msg() . '</div>';
        exit;
    }

    $logged_in_user = LOGGED_IN_IDIR; // Replace with appropriate user identifier logic

    // Add the new comment to the timeline
    $commentId = uniqid();
    $existingData['timeline'][] = [
        'field' => 'comment',
        'comment_id' => $commentId,
        'new_value' => $newComment,
        'changed_by' => $logged_in_user,
        'changed_at' => time(),
    ];

    // Save the updated data back to the file
    file_put_contents($filename, json_encode($existingData, JSON_PRETTY_PRINT));

    // Redirect back with a success message
    header("Location: view.php?courseid={$courseid}&changeid={$changeid}&message=CommentAdded");
    exit;
} else {
    echo "Invalid request method!";
}
