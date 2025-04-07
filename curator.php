<?php
opcache_reset();
$bookmarks = null; // Initialize bookmarks
$error = null; // Initialize error message
$debugOutput = ''; // For debugging information

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['bookmark_file'])) {
    $uploadedFile = $_FILES['bookmark_file'];

    if ($uploadedFile['error'] === UPLOAD_ERR_OK && pathinfo($uploadedFile['name'], PATHINFO_EXTENSION) === 'html') {
        $content = file_get_contents($uploadedFile['tmp_name']);
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Suppress parsing errors for invalid HTML

        if ($dom->loadHTML($content)) {
            libxml_clear_errors();
            $dlElements = $dom->getElementsByTagName('dl');

            if ($dlElements->length > 0) {
                // Debug: Log the number of <DL> elements found
                $debugOutput .= "<p>Found {$dlElements->length} <code>&lt;DL&gt;</code> elements in the HTML.</p>";

                // Start parsing from the first <DL> element
                $bookmarks = parseBookmarks($dlElements->item(0));
            } else {
                $error = "No <DL> elements found in the uploaded bookmarks file.";
            }
        } else {
            $error = "Failed to parse the uploaded file. Please ensure it is a valid HTML bookmarks file.";
        }
    } else {
        $error = "Invalid file. Please upload a valid HTML bookmarks file.";
    }
}

// Recursive function to parse bookmarks
function parseBookmarks($element, $currentFolder = null) {
    $bookmarks = [];

    // Debugging output
    global $debugOutput;

    foreach ($element->childNodes as $node) {
        if ($node->nodeName === 'dt') {
            $child = $node->firstChild;

            if ($child && $child->nodeName === 'h3') {
                // Found a folder
                $currentFolder = trim($child->textContent);
                if (!isset($bookmarks[$currentFolder])) {
                    $bookmarks[$currentFolder] = [];
                }
                $debugOutput .= "<p>Found folder: {$currentFolder}</p>";
            } elseif ($child && $child->nodeName === 'a') {
                // Found a link
                $linkTitle = trim($child->textContent);
                $linkHref = $child->getAttribute('href');

                if ($currentFolder !== null) {
                    $bookmarks[$currentFolder][] = [
                        'title' => $linkTitle,
                        'href' => $linkHref,
                    ];
                    $debugOutput .= "<p>Found link in folder '{$currentFolder}': {$linkTitle} ({$linkHref})</p>";
                } else {
                    $debugOutput .= "<p>Found link outside folder: {$linkTitle} ({$linkHref})</p>";
                }
            }
        } elseif ($node->nodeName === 'dl') {
            // Recursively parse nested folders
            $nestedBookmarks = parseBookmarks($node, $currentFolder);
            foreach ($nestedBookmarks as $folder => $links) {
                if (!isset($bookmarks[$folder])) {
                    $bookmarks[$folder] = [];
                }
                $bookmarks[$folder] = array_merge($bookmarks[$folder], $links);
            }
        }
    }

    return $bookmarks;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookmark Manager</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Bookmark Manager</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($debugOutput): ?>
        <!--<div class="alert alert-info">
            <h4>Debug Output:</h4>
            <?= $debugOutput ?>
        </div>-->
    <?php endif; ?>

    <?php if (!isset($bookmarks)): ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="bookmark_file" class="form-label">Upload Bookmarks HTML File:</label>
                <input type="file" class="form-control" id="bookmark_file" name="bookmark_file" accept=".html" required>
            </div>
            <button type="submit" class="btn btn-primary">Upload</button>
        </form>
    <?php else: ?>
        <form method="POST" action="curator-process.php">
        <h2>Select a Folder and Add Comments</h2>
        <?php if (!empty($bookmarks)): ?>
            <?php foreach ($bookmarks as $folder => $links): ?>
                <h3><?= htmlspecialchars($folder) ?></h3>
                <?php foreach ($links as $link): ?>
                    <?php
                    // Ensure title and href exist
                    $linkTitle = $link['title'] ?? '[Untitled Link]';
                    $linkHref = $link['href'] ?? '#';
                    ?>
                    <div class="mb-3">
                        <label class="form-label"><?= htmlspecialchars($linkTitle) ?></label>
                        <input type="hidden" name="comments[<?= htmlspecialchars($folder) ?>][<?= htmlspecialchars($linkHref) ?>][title]" value="<?= htmlspecialchars($linkTitle) ?>">
                        <textarea class="form-control" name="comments[<?= htmlspecialchars($folder) ?>][<?= htmlspecialchars($linkHref) ?>][comment]" rows="2" placeholder="Add a comment"></textarea>
                    </div>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No bookmarks found in the uploaded file.</p>
        <?php endif; ?>
        <button type="submit" class="btn btn-success">Generate HTML</button>
    </form>
    <?php endif; ?>
</div>
</body>
</html>