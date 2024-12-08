<?php 
opcache_reset();
$path = '../inc/lsapp.php';
require($path); 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $courseid = $_POST['courseid'] ?? null;
        $changeid = $_POST['changeid'] ?? null;
        $commentId = $_POST['comment_id'] ?? null;

        if ($changeid === null || $commentId === null) {
            die("Error: Missing required parameters.");
        }

        // Find the JSON file for the given change ID
        $files = glob("requests/*-{$changeid}.json");

        if (empty($files)) {
            die("Error: Change ID {$changeid} not found.");
        }

        $filename = $files[0];
        $changeData = json_decode(file_get_contents($filename), true);

        if (!$changeData) {
            die("Error: Unable to load change data.");
        }

        // Locate the comment by ID
        foreach ($changeData['comments'] as $key => $comment) {
            if ($comment['id'] === $commentId) {
                if($comment['commented_by'] === LOGGED_IN_IDIR) {
                    unset($changeData['comments'][$key]);
                    $changeData['comments'] = array_values($changeData['comments']); // Re-index the array
                    break;
                } else {
                    die("Error: You can only delete your own comments.");
                }
            }
        }

        // Save the updated JSON file
        file_put_contents($filename, json_encode($changeData, JSON_PRETTY_PRINT));

        // Redirect back to the change page
        header("Location: ./?courseid={$courseid}&changeid={$changeid}");
        exit;

} else {
    die("Invalid request method.");
}
