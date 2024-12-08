<?php 
opcache_reset();
$path = '../inc/lsapp.php';
require($path); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $changeid = $_POST['changeid'] ?? null;
    // Collect form data
    $data = [
        'changeid' => $changeid,
        'courseid' => $_POST['courseid'],
        'assign_to' => $_POST['assign_to'],
        'crm_ticket_reference' => $_POST['crm_ticket_reference'] ?? null,
        'category' => $_POST['category'],
        'description' => $_POST['description'],
        'scope' => $_POST['scope'],
        'approval_status' => $_POST['approval_status'],
        'urgent' => isset($_POST['urgent']) ? true : false,
        'status' => $_POST['status'],
    ];

    $comment = $_POST['new_comment'] ?? null;
    $logged_in_user = LOGGED_IN_IDIR; // Assuming constant is available.

    
    $courseid = $data['courseid'];

    if ($changeid) {
        // Determine file path
        $filename = "requests/course-{$courseid}-{$changeid}.json";
        if (file_exists($filename)) {
            // Load existing data
            $existingData = json_decode(file_get_contents($filename), true);

            // Maintain assignment history
            if (!isset($existingData['assign_to_history'])) {
                $existingData['assign_to_history'] = [];
            }
            if ($existingData['assign_to'] !== $data['assign_to']) {
                $assignid = uniqid();
                $existingData['assign_to_history'][] = [
                    'id' => $assignid,
                    'name' => $existingData['assign_to'],
                    'assigned_at' => $existingData['last_assigned_at'] ?? time(),
                ];
                $data['last_assigned_at'] = time();
            } else {
                $data['last_assigned_at'] = $existingData['last_assigned_at'] ?? time();
            }

            // Merge history into the new data
            $data['assign_to_history'] = $existingData['assign_to_history'];

            // Maintain status history
            if (!isset($existingData['status_history'])) {
                $existingData['status_history'] = [];
            }
            if ($existingData['status'] !== $data['status']) {
                $historyid = uniqid();
                $existingData['status_history'][] = [
                    'id' => $historyid,
                    'previous_status' => $existingData['status'],
                    'new_status' => $data['status'],
                    'changed_at' => time(),
                ];
            }

            // Merge status history into the new data
            $data['status_history'] = $existingData['status_history'];
            


            // Maintain comment history
            if (!isset($existingData['comments'])) {
                $existingData['comments'] = [];
            }

            // Add new comment if provided
            if ($comment) {
                $commentId = uniqid(); // Generate a unique identifier for the comment
                $existingData['comments'][] = [
                    'id' => $commentId,
                    'comment' => $comment,
                    'commented_by' => $logged_in_user,
                    'commented_at' => time(),
                ];
            }

            $data['comments'] = $existingData['comments'];

            $data['files'] = $existingData['files'] ?? []; // Retain existing files
        }
    } else {

        // Creating a new entry
        $changeid = uniqid(); // Generate a unique identifier for the comment
        $date_created = time();
        $filename = "requests/course-{$courseid}-{$changeid}.json";
        $data['changeid'] = $changeid;
        $data['date_created'] = $date_created;
        $data['date_modififed'] = $date_created;
        $data['created_by'] = LOGGED_IN_IDIR;
        $data['last_assigned_at'] = time();
        $data['assign_to_history'] = [];
        $data['status_history'] = []; // Initialize status history
        $data['comments'] = [];
        // Add initial comment if provided
        if ($comment) {
            $commentId = uniqid(); // Generate a unique identifier for the comment
            $data['comments'][] = [
                'id' => $commentId,
                'comment' => $comment,
                'commented_by' => $logged_in_user,
                'commented_at' => time(),
            ];
        }
        
    }

    // Handle file uploads
    if (!empty($_FILES['uploaded_files']['name'][0])) {
        $uploadDir = "requests/files/";
        
        foreach ($_FILES['uploaded_files']['name'] as $key => $name) {
            $tmpName = $_FILES['uploaded_files']['tmp_name'][$key];
            $error = $_FILES['uploaded_files']['error'][$key];
            $size = $_FILES['uploaded_files']['size'][$key];

            // Basic validation (you can expand this)
            if ($error === UPLOAD_ERR_OK && $size <= 20 * 1024 * 1024) { // Max 5MB
                // Normalize the file name
                $normalizedFilename = strtolower(str_replace(' ', '-', $name));
                $uniqueName = "course-{$courseid}-change-{$changeid}-{$normalizedFilename}";
                $destination = $uploadDir . $uniqueName;
                if (move_uploaded_file($tmpName, $destination)) {
                    $data['files'][] = $uniqueName;
                }
            }
        }
    }

    // Save or update JSON file
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));

    // echo "Request " . ($changeid ? "updated" : "saved") . " successfully!";
     // Redirect to the change details page
     header("Location: ./?courseid={$courseid}&changeid={$changeid}");
     exit;
} else {
    echo "Invalid request method!";
}
?>