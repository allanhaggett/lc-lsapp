<?php
require('inc/lsapp.php');

if (canAccess()) {
    if ($_POST) {

        $courseid = $_POST['courseid'];
        $categoryid = $_POST['categoryid'];
        if ($courseid && $categoryid === 'Course Change') {
            header("Location: /lsapp/course-change/create.php?courseid={$courseid}");
        } elseif ($courseid && $categoryid === 'New Class Date') {
            header("Location: /lsapp/class-bulk-insert.php?courseid={$courseid}");
        } else {
            echo "An error occured";
        }
    }
}