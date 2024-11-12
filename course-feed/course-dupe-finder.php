<?php
opcache_reset();

// Path to the CSV file
$csvFile = '../data/courses.csv';

// Array to store courses with unique CourseName and ItemCode
$courses = [];
$duplicates = [];

// Open the CSV file
if (($handle = fopen($csvFile, 'r')) !== false) {
    // Get the headers
    $headers = fgetcsv($handle);

    // Find the index for CourseName and ItemCode
    $courseNameIndex = array_search('CourseName', $headers);
    $itemCodeIndex = array_search('ItemCode', $headers);

    if ($courseNameIndex === false || $itemCodeIndex === false) {
        die("CourseName or ItemCode columns not found in the CSV file.");
    }

    // Process each row
    while (($row = fgetcsv($handle)) !== false) {
        $courseName = $row[$courseNameIndex];
        $itemCode = $row[$itemCodeIndex];
        $uniqueKey = $courseName . '_' . $itemCode;

        // Check for duplicate
        if (isset($courses[$uniqueKey])) {
            // Duplicate found, store it in duplicates array
            $duplicates[] = $row;
        } else {
            // No duplicate, store the unique course
            $courses[$uniqueKey] = $row;
        }
    }
    fclose($handle);

    // Output the results
    if (!empty($duplicates)) {
        echo "Duplicate entries found:<br>";
        foreach ($duplicates as $duplicate) {
            echo "" . $duplicate[$courseNameIndex] . ": " . $duplicate[$itemCodeIndex] . "<br>";
        }
    } else {
        echo "No duplicate entries found.\n";
    }
} else {
    echo "Error: Unable to open the CSV file.\n";
}