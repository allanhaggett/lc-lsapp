<?php
date_default_timezone_set('America/Vancouver');

class SimpleHtmlToMarkdown {
    public function convert($html) {
        // Convert table markup before stripping tags
        $html = $this->convertTables($html);

        // Basic replacements for common HTML tags
        $markdown = $html;

        // Block elements
        $markdown = preg_replace('/<h[1-6]>(.*?)<\/h[1-6]>/i', "\n# $1\n", $markdown);
        $markdown = preg_replace('/<p>(.*?)<\/p>/i', "\n$1\n", $markdown);
        $markdown = preg_replace('/<br\s*\/?>/i', "\n", $markdown);
        $markdown = preg_replace('/<ul>(.*?)<\/ul>/is', "\n$1\n", $markdown);
        $markdown = preg_replace('/<ol>(.*?)<\/ol>/is', "\n$1\n", $markdown);
        $markdown = preg_replace('/<li>(.*?)<\/li>/i', "- $1\n", $markdown);

        // Inline elements
        $markdown = preg_replace('/<strong>(.*?)<\/strong>/i', '**$1**', $markdown);
        $markdown = preg_replace('/<b>(.*?)<\/b>/i', '**$1**', $markdown);
        $markdown = preg_replace('/<em>(.*?)<\/em>/i', '*$1*', $markdown);
        $markdown = preg_replace('/<i>(.*?)<\/i>/i', '*$1*', $markdown);
        
        // Handle links with any attributes (not just href)
        // First, handle links with various attributes
        $markdown = preg_replace('/<a\s+(?:[^>]*?\s+)?href="([^"]*)"[^>]*?>(.*?)<\/a>/i', '[$2]($1)', $markdown);
        // Also handle single-quoted hrefs
        $markdown = preg_replace('/<a\s+(?:[^>]*?\s+)?href=\'([^\']*)\'[^>]*?>(.*?)<\/a>/i', '[$2]($1)', $markdown);

        // Strip remaining tags
        $markdown = strip_tags($markdown);

        return trim($markdown);
    }

    private function convertTables($html) {
        return preg_replace_callback('/<table.*?>(.*?)<\/table>/is', function ($matches) {
            $tableHtml = $matches[1];

            // Extract all rows
            preg_match_all('/<tr>(.*?)<\/tr>/is', $tableHtml, $rows);
            $markdown = '';
            $headerDone = false;

            foreach ($rows[1] as $rowHtml) {
                preg_match_all('/<(td|th)>(.*?)<\/\1>/is', $rowHtml, $cells);

                $cellTexts = array_map(function ($cell) {
                    return trim(strip_tags($cell));
                }, $cells[2]);

                $markdown .= '| ' . implode(' | ', $cellTexts) . " |\n";

                // Add separator after header
                if (!$headerDone && !empty($cellTexts)) {
                    $markdown .= '| ' . implode(' | ', array_fill(0, count($cellTexts), '---')) . " |\n";
                    $headerDone = true;
                }
            }

            return "\n" . trim($markdown) . "\n";
        }, $html);
    }
}

$converter = new SimpleHtmlToMarkdown();

$xmlFile = '../data/learninghub.WordPress.2025-06-15.xml';
$csvFile = '../data/courses.csv';
$logFile = '../data/added-courses-log-' . date('Ymd_His') . '.csv';

// Load the XML file
if (!file_exists($xmlFile)) {
    die("WXR XML file not found.\n");
}

$xml = simplexml_load_file($xmlFile, 'SimpleXMLElement', LIBXML_NOCDATA);
$xml->registerXPathNamespace('wp', 'http://wordpress.org/export/1.2/');
$xml->registerXPathNamespace('dc', 'http://purl.org/dc/elements/1.1/');
$xml->registerXPathNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
$xml->registerXPathNamespace('excerpt', 'http://wordpress.org/export/1.2/excerpt/');

$items = $xml->xpath('//channel/item');

// Load existing CSV
$existingCourses = [];
$headers = [];
if (($handle = fopen($csvFile, 'r')) !== false) {
    $headers = fgetcsv($handle);
    while (($row = fgetcsv($handle)) !== false) {
        $record = array_combine($headers, $row);
        $existingCourses[$record['CourseName']] = $record;
    }
    fclose($handle);
} else {
    die("Could not read CSV file.\n");
}

// Required CSV fields
$requiredFields = ['CourseID', 'CourseName', 'CourseNameSlug', 'CourseDescription', 'Topics', 'Method', 'Audience', 'LearningHubPartner', 'Platform', 'Status', 'HUBInclude', 'RegistrationLink', 'elearning'];

// Prepare new rows
$newRows = [];

foreach ($items as $item) {
    $postType = (string)$item->children('wp', true)->post_type;
    if ($postType !== 'course') continue;

    $title = trim((string)$item->title);
    $content = trim((string)$item->children('content', true)->encoded);

    if (isset($existingCourses[$title])) continue; // already in CSV

    // Prepare taxonomy values
    $taxonomies = [
        'topics'            => [],
        'delivery_method'  => [],
        'audience'         => [],
        'external_system'  => [],
        'learning_partner' => [],
    ];

    foreach ($item->category as $category) {
        $domain = (string)$category['domain'];
        $term = (string)$category;
        if (isset($taxonomies[$domain]) && empty($taxonomies[$domain])) {
            $taxonomies[$domain][] = $term;
        }
    }

    $row = array_fill_keys($headers, ''); // initialize all fields with blank

    static $courseIdCounter = 0;
    $row['CourseID']         = date('YmdHis') . str_pad(++$courseIdCounter, 3, '0', STR_PAD_LEFT);
    $wpStatus = (string)$item->children('wp', true)->status;
    $row['Status'] = ($wpStatus === 'publish') ? 'Active' : 'Inactive';
    $row['HUBInclude'] = ($wpStatus === 'publish') ? 'Yes' : 'No';
    $row['CourseName']       = $title;
    $row['CourseNameSlug']   = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    $row['CourseDescription'] = $converter->convert($content);
    $row['Topics']           = $taxonomies['topics'][0] ?? '';
    $row['Method']           = $taxonomies['delivery_method'][0] ?? '';
    $row['Audience']         = $taxonomies['audience'][0] ?? '';
    $row['LearningHubPartner'] = $taxonomies['learning_partner'][0] ?? '';
    $row['Platform']         = $taxonomies['external_system'][0] ?? '';
    $row['Requested']        = date('Y-m-d H:i:s');
    $row['RequestedBy']      = 'SYNCBOT';

    $courseLink = '';
    foreach ($item->children('wp', true)->postmeta as $meta) {
        if ((string)$meta->meta_key === 'course_link') {
            $courseLink = (string)$meta->meta_value;
            break;
        }
    }
    $row['RegistrationLink'] = $courseLink;
    $row['elearning'] = $courseLink;

    // Append to list
    $existingCourses[$title] = $row;
    $newRows[] = $row;
}

// Write updated CSV
if (!empty($newRows)) {
    $output = fopen($csvFile, 'w');
    fputcsv($output, $headers);
    foreach ($existingCourses as $record) {
        $line = [];
        foreach ($headers as $header) {
            $line[] = $record[$header] ?? '';
        }
        fputcsv($output, $line);
    }
    fclose($output);

    // Write log
    $log = fopen($logFile, 'w');
    fputcsv($log, $headers);
    foreach ($newRows as $row) {
        $line = [];
        foreach ($headers as $header) {
            $line[] = $row[$header] ?? '';
        }
        fputcsv($log, $line);
    }
    fclose($log);

    echo count($newRows) . " new course(s) added.\nLog written to: $logFile\n";
} else {
    echo "No new courses found.\n";
}