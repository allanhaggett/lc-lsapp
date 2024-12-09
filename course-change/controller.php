<?php
opcache_reset();
$path = '../inc/lsapp.php';
require($path);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $changeid = $_POST['changeid'] ?? null;
    $date_created = time();

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
    $logged_in_user = LOGGED_IN_IDIR;

    $courseid = $data['courseid'];

    if ($changeid) {
        // Determine file path
        $filename = "requests/course-{$courseid}-change-{$changeid}.json";
        if (file_exists($filename)) {
            // Load existing data
            $existingData = json_decode(file_get_contents($filename), true);

            // Preserve `date_created` and `created_by`
            $data['date_created'] = $existingData['date_created'] ?? $date_created;
            $data['created_by'] = $existingData['created_by'] ?? LOGGED_IN_IDIR;

            // Always update `date_modified`
            $data['date_modified'] = time();

            // Ensure `timeline` exists
            if (!isset($existingData['timeline'])) {
                $existingData['timeline'] = [];
            }

            // Track changes to all fields (only on updates)
            $trackedFields = ['assign_to', 'crm_ticket_reference', 'category', 'description', 'scope', 'approval_status', 'status', 'urgent'];
            foreach ($trackedFields as $field) {
                if (isset($existingData[$field]) && $existingData[$field] !== $data[$field]) {
                    $existingData['timeline'][] = [
                        'field' => $field,
                        'previous_value' => $existingData[$field],
                        'new_value' => $data[$field],
                        'changed_by' => $logged_in_user,
                        'changed_at' => time(),
                    ];
                }
            }

            // Add a new comment to the timeline
            if ($comment) {
                $commentId = uniqid();
                $existingData['timeline'][] = [
                    'field' => 'comment',
                    'comment_id' => $commentId,
                    'new_value' => $comment,
                    'changed_by' => $logged_in_user,
                    'changed_at' => time(),
                ];
            }

            // Retain files
            $data['files'] = $existingData['files'] ?? [];

            // Merge timeline back into data
            $data['timeline'] = $existingData['timeline'];
        }
    } else {
        // Creating a new entry
        $changeid = uniqid();
        $filename = "requests/course-{$courseid}-change-{$changeid}.json";

        $data['changeid'] = $changeid;
        $data['date_created'] = $date_created;
        $data['date_modified'] = $date_created;
        $data['created_by'] = LOGGED_IN_IDIR;
        $data['timeline'] = []; // Initialize timeline but do not populate it for the first create
        $data['files'] = [];

        // Add an initial timeline entry for creation
        $data['timeline'][] = [
            'field' => 'creation',
            'previous_value' => null,
            'new_value' => "Change created by {$logged_in_user}",
            'changed_by' => $logged_in_user,
            'changed_at' => $date_created,
        ];
        if ($comment) {
            $commentId = uniqid();
            $data['timeline'][] = [
                'field' => 'comment',
                'comment_id' => $commentId,
                'new_value' => $comment, // The actual comment content
                'changed_by' => $logged_in_user, // The user who made the comment
                'changed_at' => time(), // Timestamp of when the comment was made
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

            // Basic validation
            if ($error === UPLOAD_ERR_OK && $size <= 20 * 1024 * 1024) { // Max 20MB
                // Normalize the file name
                $normalizedFilename = strtolower(str_replace(' ', '-', $name));
                $uniqueName = "course-{$courseid}-change-{$changeid}-{$normalizedFilename}";
                $destination = $uploadDir . $uniqueName;

                if (move_uploaded_file($tmpName, $destination)) {
                    $data['files'][] = $uniqueName;

                    // Add file upload to the timeline only for updates
                    if ($changeid) {
                        $data['timeline'][] = [
                            'field' => 'file_upload',
                            'new_value' => $uniqueName,
                            'changed_by' => $logged_in_user,
                            'changed_at' => time(),
                        ];
                    }
                }
            }
        }
    }

    // Save or update JSON file
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));

    // Redirect to the change details page
    header("Location: ./?courseid={$courseid}&changeid={$changeid}&message=Success");
    exit;
} else {
    echo "Invalid request method!";
}
