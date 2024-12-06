<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $data = [
        'courseid' => $_POST['courseid'],
        'assign_to' => $_POST['assign_to'],
        'crm_ticket_reference' => $_POST['crm_ticket_reference'] ?? null,
        'category' => $_POST['category'],
        'description' => $_POST['description'],
        'scope' => $_POST['scope'],
        'approval_status' => $_POST['approval_status'],
        'urgent' => isset($_POST['urgent']) ? true : false,
        'comments' => $_POST['comments'] ?? null,
        'status' => $_POST['status'],
    ];

    $changeid = $_POST['changeid'] ?? null;

    // Determine file path
    if ($changeid) {
        $filename = "requests/$changeid.json";
        if (file_exists($filename)) {
            // Load existing data
            $existingData = json_decode(file_get_contents($filename), true);

            // Initialize history if not present
            if (!isset($existingData['assign_to_history'])) {
                $existingData['assign_to_history'] = [];
            }

            // Check if the assignee has changed
            if ($existingData['assign_to'] !== $data['assign_to']) {
                // Add the previous assignee and timestamp to history
                $existingData['assign_to_history'][] = [
                    'name' => $existingData['assign_to'],
                    'assigned_at' => $existingData['last_assigned_at'] ?? time(),
                ];
                // Update the timestamp for the new assignee
                $data['last_assigned_at'] = time();
            } else {
                // Retain the last assigned timestamp if no change
                $data['last_assigned_at'] = $existingData['last_assigned_at'] ?? time();
            }

            // Merge history into the new data
            $data['assign_to_history'] = $existingData['assign_to_history'];
        } else {
            // If no existing file, initialize history
            $data['assign_to_history'] = [];
            $data['last_assigned_at'] = time();
        }
    } else {
        // Creating a new entry
        $changeid = time();
        $filename = "requests/$changeid.json";
        $data['assign_to_history'] = [];
        $data['last_assigned_at'] = time();
    }

    // Ensure the directory exists
    if (!is_dir('requests')) {
        mkdir('requests', 0777, true);
    }

    // Save or update JSON file
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));

    echo "Request " . ($changeid ? "updated" : "saved") . " successfully!";
} else {
    echo "Invalid request method!";
}
?>