<?php
/**
 * Welcome to a PSA Coursework Landing Page.
 * 
 * This file has been copied from the template page and 
 * uses file includes to render the header, footer, and 
 * depending on your content, other common elements like
 * easier video embedding, or messages around different
 * meeting platforms.
 * 
 * These pages are designed to work within a directory that
 * is named accordingly to the courses short code within LSAPP
 * and should be named index.php e.g. 
 * https://gww.bcpublicservice.gov.bc.ca/learning/coursework/courses/cac101/
 * 
 * Note: do not link directly to the index.php file; use the above example
 * 
 */

$includespath = $_SERVER['DOCUMENT_ROOT'] . '\learning\coursework\includes\\';
$header = $includespath . '\header.php';
include($header);

?>




<!-- 
    LANDING PAGE CONTENT GOES HERE :) 
    - Use TailwindCSS?? Bootstrap perhaps?
    - You can just write raw HTML here
    - You need to manually copy assets into the approrpiate
        directory structure within the folder that this file
        resides in.
    - You may leverage numerous existing common component 
        includes, such as:
        - Upcoming class info (currently just a launch button)
        - Zoom Tips
        - Video Embedding
        - More coming soon

-->




<?php 
$footer = $includespath . '\footer.php';
include($footer); 
?>