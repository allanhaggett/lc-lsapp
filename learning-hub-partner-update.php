<?php
$filePath = 'data/learning_partners.json';
$data = json_decode(file_get_contents($filePath), true);

// Collect form data
$formData = [
    'id' => isset($_POST['id']) ? (int)$_POST['id'] : null,
    'name' => $_POST['name'] ?? '',
    'description' => $_POST['description'] ?? '',
    'link' => $_POST['link'] ?? '',
    'slug' => $_POST['slug'] ?? '',
    'admin_idir' => $_POST['admin_idir'] ?? 'ahaggett',
    'admin_email' => $_POST['admin_email'] ?? 'allan.haggett@gov.bc.ca',
    'admin_name' => $_POST['admin_name'] ?? 'Allan Haggett',
];

// Check if the record already exists
$recordFound = false;
foreach ($data as &$record) {
    if ($record['id'] === $formData['id']) {
        $record = $formData;  // Update existing record
        $recordFound = true;
        break;
    }
}

if (!$recordFound) {
    $data[] = $formData;  // Add as a new record if no match was found
}

// Save back to the JSON file
file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

// Redirect or show a confirmation message
echo $recordFound ? "Record updated successfully." : "New record added successfully.";
