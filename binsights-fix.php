<?php
/**
 * Script to reassign courses from "Behavioural Insights" to "PSA Corporate Learning Branch"
 * Excludes specific course IDs that should remain with Behavioural Insights
 * 
 * Usage: Run this script from the command line or browser
 * Backup your data/courses.csv before running!
 */

// Set up error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define file paths
$csvFile = 'data/courses.csv';
$backupFile = 'data/courses-backup-' . date('Ymd-His') . '.csv';

// Course IDs to exclude from the update (keep with Behavioural Insights)
$excludeIds = [
    '20250609123357176',
    '20250609123357177', 
    '20250609123357178',
    '20231023144725-4',
    '20250609123357053',
    '20250609123357054'
];

// Partner names
$oldPartner = 'Behavioural Insights';
$newPartner = 'PSA Corporate Learning Branch';

echo "=== Course Partner Reassignment Script ===\n";
echo "From: {$oldPartner}\n";
echo "To: {$newPartner}\n";
echo "Excluding course IDs: " . implode(', ', $excludeIds) . "\n\n";

// Check if CSV file exists
if (!file_exists($csvFile)) {
    die("Error: {$csvFile} not found!\n");
}

// Create backup
if (!copy($csvFile, $backupFile)) {
    die("Error: Could not create backup file {$backupFile}!\n");
}
echo "✓ Backup created: {$backupFile}\n";

// Read the CSV file
$csvData = [];
if (($handle = fopen($csvFile, 'r')) !== FALSE) {
    while (($data = fgetcsv($handle)) !== FALSE) {
        $csvData[] = $data;
    }
    fclose($handle);
}

if (empty($csvData)) {
    die("Error: Could not read CSV data or file is empty!\n");
}

// Get headers (first row)
$headers = $csvData[0];
echo "✓ Loaded " . (count($csvData) - 1) . " courses from CSV\n";

// Find the partner column (LearningHubPartner is typically at index 36)
$partnerColumnIndex = -1;
$courseIdColumnIndex = -1;

foreach ($headers as $index => $header) {
    if (strtolower(trim($header)) === 'learninghubpartner') {
        $partnerColumnIndex = $index;
    }
    if (strtolower(trim($header)) === 'courseid') {
        $courseIdColumnIndex = $index;
    }
}

if ($partnerColumnIndex === -1) {
    die("Error: Could not find 'LearningHubPartner' column in CSV!\n");
}

if ($courseIdColumnIndex === -1) {
    die("Error: Could not find 'CourseID' column in CSV!\n");
}

echo "✓ Found LearningHubPartner at column index: {$partnerColumnIndex}\n";
echo "✓ Found CourseID at column index: {$courseIdColumnIndex}\n\n";

// Process the data
$updatedCount = 0;
$excludedCount = 0;
$totalBehaviouralInsights = 0;

for ($i = 1; $i < count($csvData); $i++) { // Skip header row
    $row = $csvData[$i];
    
    // Check if this course belongs to Behavioural Insights
    if (isset($row[$partnerColumnIndex]) && trim($row[$partnerColumnIndex]) === $oldPartner) {
        $totalBehaviouralInsights++;
        $courseId = isset($row[$courseIdColumnIndex]) ? trim($row[$courseIdColumnIndex]) : '';
        
        // Check if this course ID should be excluded
        if (in_array($courseId, $excludeIds)) {
            $excludedCount++;
            echo "⏭️  Skipping course ID {$courseId} (excluded)\n";
        } else {
            // Update the partner
            $csvData[$i][$partnerColumnIndex] = $newPartner;
            $updatedCount++;
            echo "✅ Updated course ID {$courseId}\n";
        }
    }
}

echo "\n=== Summary ===\n";
echo "Total courses with '{$oldPartner}': {$totalBehaviouralInsights}\n";
echo "Courses updated to '{$newPartner}': {$updatedCount}\n";
echo "Courses excluded (kept with '{$oldPartner}'): {$excludedCount}\n";

// Verify the counts
if ($totalBehaviouralInsights !== ($updatedCount + $excludedCount)) {
    echo "⚠️  Warning: Count mismatch! Please review the results.\n";
}

// Check for auto-confirmation via URL parameter or command line argument
$autoConfirm = false;

// Check URL parameter (for web access)
if (isset($_GET['confirm']) && strtolower($_GET['confirm']) === 'yes') {
    $autoConfirm = true;
    echo "\n✅ Auto-confirmed via URL parameter\n";
}

// Check command line argument (for CLI access)
if (isset($argv)) {
    foreach ($argv as $arg) {
        if (strtolower($arg) === '--confirm' || strtolower($arg) === '--yes' || strtolower($arg) === '-y') {
            $autoConfirm = true;
            echo "\n✅ Auto-confirmed via command line argument\n";
            break;
        }
    }
}

if (!$autoConfirm) {
    // Ask for confirmation before writing
    echo "\nDo you want to save these changes? (y/N): ";
    
    // Check if we're running in CLI or web
    if (php_sapi_name() === 'cli') {
        $handle = fopen("php://stdin", "r");
        $confirmation = trim(fgets($handle));
        fclose($handle);
    } else {
        // For web interface, show instructions
        echo "\n\n📝 To auto-confirm, add ?confirm=yes to the URL\n";
        echo "❌ Operation cancelled. No changes saved.\n";
        echo "Your backup is safe at: {$backupFile}\n";
        unlink($backupFile);
        exit(0);
    }

    if (strtolower($confirmation) !== 'y' && strtolower($confirmation) !== 'yes') {
        echo "❌ Operation cancelled. No changes saved.\n";
        // Remove the backup file since we're not proceeding
        unlink($backupFile);
        exit(0);
    }
}

// Write the updated CSV
if (($handle = fopen($csvFile, 'w')) !== FALSE) {
    foreach ($csvData as $row) {
        fputcsv($handle, $row);
    }
    fclose($handle);
    echo "✅ Changes saved to {$csvFile}\n";
} else {
    echo "❌ Error: Could not write to {$csvFile}!\n";
    echo "Your backup is safe at: {$backupFile}\n";
    exit(1);
}

echo "\n🎉 Script completed successfully!\n";
echo "📁 Backup file: {$backupFile}\n";
echo "\nRecommendation: Test your application to ensure everything works correctly.\n";
echo "If there are issues, you can restore from the backup file.\n";

?>