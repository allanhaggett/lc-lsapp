<?php
opcache_reset();
$path = '../inc/lsapp.php';
require($path);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $courseid = $_POST['courseid'] ?? null;
    $changeid = $_POST['changeid'] ?? null;
    $file = $_POST['file'] ?? null;

    if ($courseid === null || $changeid === null || $file === null) {
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

    // Remove the file from the files array
    $fileIndex = array_search($file, $changeData['files']);
    if ($fileIndex !== false) {
        unset($changeData['files'][$fileIndex]); // Remove the file
        $changeData['files'] = array_values($changeData['files']); // Re-index the array

        // Delete the actual file from the server
        $filepath = 'requests/files/' . $_POST['file'] ?? null;
        if ($filepath && file_exists($filepath)) {
            if (unlink($filepath)) {
                // Log file removal in the timeline
                $changeData['timeline'][] = [
                    'field' => 'file_removal',
                    'previous_value' => $file,
                    'new_value' => null,
                    'changed_by' => LOGGED_IN_IDIR, // Log the user who removed the file
                    'changed_at' => time(),
                ];

                // Save the updated JSON file
                file_put_contents($filename, json_encode($changeData, JSON_PRETTY_PRINT));

                // Redirect back to the change details page
                header("Location: ./?courseid={$courseid}&changeid={$changeid}");
                exit;
            } else {
                die("Error: Unable to delete file from server.");
            }
        } else {
            die("Error: File not found on server.");
        }
    } else {
        die("Error: File not found in change data.");
    }
} else {
    die("Invalid request method.");
}
