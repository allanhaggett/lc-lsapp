<?php
// Include necessary files
$path = '../inc/lsapp.php';
require($path);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseid = htmlspecialchars($_POST['courseid'] ?? '');
    $changeid = htmlspecialchars($_POST['changeid'] ?? '');

    if (!$courseid || !$changeid) {
        echo '<div class="alert alert-danger">Error: Course ID and Change ID are required to delete a request.</div>';
        exit;
    }

    $filename = "requests/course-{$courseid}-change-{$changeid}.json";

    if (!file_exists($filename)) {
        echo '<div class="alert alert-danger">Error: Request not found.</div>';
        exit;
    }

    // Delete the file
    if (unlink($filename)) {
        // Redirect back with a success message
        header("Location: ./?courseid={$courseid}&message=RequestDeleted");
        exit;
    } else {
        echo '<div class="alert alert-danger">Error: Unable to delete the request file.</div>';
        exit;
    }
} else {
    echo "Invalid request method!";
    exit;
}
