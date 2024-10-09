<?php require('inc/lsapp.php') ?>
<?php opcache_reset(); ?>
<?php 
if(canAccess()):
if($_POST):
    
    $course = getCourse($_POST['CourseID']);

    //print_r($course); exit;

    $f = fopen('data/courses.csv','r');
    $temp_table = fopen('data/courses-temp.csv','w');
    // pop the headers off the source file and start the new file with those headers
    $headers = fgetcsv($f);
    fputcsv($temp_table,$headers);


    $course[21] = h($_POST['Delivery']);
    $course[38] = h($_POST['Topics']);
    $course[39] = h($_POST['Audience']);
    $course[40] = h($_POST['Levels']);
    $course[41] = h($_POST['Reporting']);
    $course[48] = 1;

               
    while (($data = fgetcsv($f)) !== FALSE){
        
        if($data[0] == $_POST['CourseID']) {
            fputcsv($temp_table,$course);
        } else {
            fputcsv($temp_table,$data);
        }
    }
    fclose($f);
    fclose($temp_table);

    rename('data/courses-temp.csv','data/courses.csv');
    $go = 'Location: ' . $_SERVER['HTTP_REFERER'];
    //header($go);
    echo 'OK!';

endif;
endif;
