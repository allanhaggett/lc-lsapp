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
    $filename = "requests/course-{$courseid}-change-{$changeid}.json";

    if (!file_exists($filename)) {
        die("Error: Change ID {$changeid} not found.");
    }

    // Load the change data
    $changeData = json_decode(file_get_contents($filename), true);

    if (!$changeData) {
        die("Error: Unable to load change data.");
    }

    // Track whether the comment was deleted and its metadata
    $commentDeleted = false;
    $originalAuthor = null;
    $originalTimestamp = null;

    // Locate the comment in the timeline by comment_id
    foreach ($changeData['timeline'] as $key => $event) {
        if ($event['field'] === 'comment' && ($event['comment_id'] ?? null) === $commentId) {
            // Ensure the logged-in user is the commenter
            if ($event['changed_by'] === LOGGED_IN_IDIR) {
                // Capture the original author and timestamp
                $originalAuthor = $event['changed_by'];
                $originalTimestamp = $event['changed_at'];

                // Remove the comment
                unset($changeData['timeline'][$key]); // Remove the comment
                $changeData['timeline'] = array_values($changeData['timeline']); // Re-index the array
                $commentDeleted = true;
                break;
            } else {
                die("Error: You can only delete your own comments.");
            }
        }
    }

    if ($commentDeleted) {
        // Format the deletion message with the original author and timestamp
        $deletionMessage = sprintf(
            "Comment by %s made at %s was deleted.",
            htmlspecialchars($originalAuthor),
            date('Y-m-d H:i:s', $originalTimestamp)
        );

        // Add a new timeline entry for the deletion
        $changeData['timeline'][] = [
            'field' => 'comment_deleted',
            'changed_by' => LOGGED_IN_IDIR,
            'old_value' => 'N/A', // No need to log the original comment content
            'new_value' => $deletionMessage,
            'changed_at' => time(),
        ];

        // Save the updated JSON file
        file_put_contents($filename, json_encode($changeData, JSON_PRETTY_PRINT));

        // Redirect back to the change details page
        header("Location: ./?courseid={$courseid}&changeid={$changeid}");
        exit;
    } else {
        die("Error: Comment ID {$commentId} not found or already deleted.");
    }
} else {
    die("Invalid request method.");
}