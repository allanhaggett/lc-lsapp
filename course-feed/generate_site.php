<?php

// Load JSON data
$jsonFile = 'data/bcps-corporate-learning-courses.json';
$jsonData = file_get_contents($jsonFile);
$courses = json_decode($jsonData, true)['items'];

// Sort courses by date_modified in reverse chronological order
usort($courses, function ($a, $b) {
    return strtotime($b['date_modified']) - strtotime($a['date_modified']);
});

// Paths to header and footer templates
$headerTemplate = 'header.html';
$footerTemplate = 'footer.html';

// Ensure header and footer files exist
if (!file_exists($headerTemplate) || !file_exists($footerTemplate)) {
    die("Header or footer template file is missing.");
}

// Load header and footer templates
$header = file_get_contents($headerTemplate);
$footer = file_get_contents($footerTemplate);

// Directory for generated course HTML files
$outputDir = 'courses';
$outputDir = '../../learning/hub/courses/';

if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Start content for index.html
$indexContent = $header;

$indexContent .= "<ul>\n";

// Loop through each course and generate an HTML file
foreach ($courses as $course) {
    // Define variables from JSON data
    $courseId = htmlspecialchars($course['id']);
    $title = htmlspecialchars($course['title']);
    $summary = htmlspecialchars($course['summary']);
    $slug = $course['_slug'] ?: "course-{$courseId}";
    $fileName = "{$outputDir}/{$slug}.html";
    $fileUrl = "{$slug}.html"; // Corrected relative URL for the index page link

    // Create HTML content for individual course file
    $htmlContent = $header;
    $htmlContent .= "<h1>{$title}</h1>\n";
    $htmlContent .= "<p><strong>Summary:</strong> {$summary}</p>\n";
    $htmlContent .= "<p><strong>Content:</strong> {$course['content_text']}</p>\n";
    $htmlContent .= "<p><strong>Delivery Method:</strong> {$course['delivery_method']}</p>\n";
    $htmlContent .= "<p><strong>Audience:</strong> {$course['_audience']}</p>\n";
    $htmlContent .= "<p><strong>Topic:</strong> {$course['_topic']}</p>\n";
    $htmlContent .= "<p><strong>Learning Partner:</strong> {$course['_learning_partner']}</p>\n";
    $htmlContent .= "<p><strong>URL:</strong> <a href=\"{$course['url']}\" target=\"_blank\">Course Link</a></p>\n";
    $htmlContent .= $footer;

    // Save the HTML content to a file
    file_put_contents($fileName, $htmlContent);
    echo "Generated HTML file for course: {$title}\n";

    // Add course details to index.html content
    $indexContent .= "<li>\n";
    $indexContent .= "<h2><a href=\"{$fileUrl}\">{$title}</a></h2>\n";
    $indexContent .= "<p><strong>Summary:</strong> {$summary}</p>\n";
    $indexContent .= "<p><strong>Delivery Method:</strong> {$course['delivery_method']}</p>\n";
    $indexContent .= "<p><strong>Topic:</strong> {$course['_topic']}</p>\n";
    $indexContent .= "</li>\n";
}

$indexContent .= "</ul>\n";
$indexContent .= $footer;

// Save the index content to index.html
file_put_contents("{$outputDir}/index.html", $indexContent);
echo "Generated index.html with links to each course.\n";

echo "All course HTML files and index.html have been generated in the '{$outputDir}' directory.\n";