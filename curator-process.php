<?php
opcache_reset();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comments'])) {
    $comments = $_POST['comments'];

    // Generate static HTML from the submitted data
    $outputHtml = generateStaticHtml($comments);

    // Save the HTML file to the server
    $outputFile = 'compiled_bookmarks.html';
    file_put_contents($outputFile, $outputHtml);

    // Provide a download link for the generated HTML file
    echo '<div class="container mt-5">';
    echo '<h1>Bookmark Compilation Complete</h1>';
    echo '<p>Your bookmarks have been compiled. <a href="' . $outputFile . '" download>Click here to download the compiled HTML file.</a></p>';
    echo '</div>';
} else {
    echo '<p>No data received. Please submit the form with bookmarks and comments.</p>';
}

// Function to generate the static HTML
function generateStaticHtml($comments) {
    $html = '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <title>Compiled Bookmarks</title>
</head>
<body>
<div class="container mt-4">
    <h1>Compiled Bookmarks</h1>';

    foreach ($comments as $folder => $links) {
        $html .= '<details class="mb-3">';
        $html .= '<summary class="fs-4">' . htmlspecialchars($folder) . '</summary>';
        foreach ($links as $linkHref => $linkData) {
            $title = htmlspecialchars($linkData['title'] ?? '[Untitled]');
            $href = htmlspecialchars($linkHref);
            $comment = htmlspecialchars($linkData['comment'] ?? '');

            $html .= '<div class="mb-2">
                        <a href="' . $href . '" target="_blank" class="d-block">' . $title . '</a>
                        <p>' . $comment . '</p>
                      </div>';
        }
        $html .= '</details>';
    }

    $html .= '</div></body></html>';
    return $html;
}
