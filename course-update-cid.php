<?php require('inc/lsapp.php') ?>
<?php opcache_reset(); ?>
<?php 
if(canAccess()):


    $path = $_SERVER['DOCUMENT_ROOT'] . '\lsapp\course-feed\data\courses.csv';
    $hc = fopen($path, 'r');
    fgetcsv($hc);
    $hubcourses = [];
    while ($row = fgetcsv($hc)) {
        array_push($hubcourses,$row);
    }
    fclose($hc);

    foreach($hubcourses as $hubc) {

    
        $f = fopen('data/courses.csv','r');
        $temp_table = fopen('data/courses-temp.csv','w');
        // pop the headers off the source file and start the new file with those headers
        $headers = fgetcsv($f);
        fputcsv($temp_table,$headers);
                
        while (($data = fgetcsv($f)) !== FALSE){
            
            if(strtolower($hubc[0]) == strtolower($data[4])) { 
                $data[50] = $hubc[13];
                fputcsv($temp_table,$data);
            } else {
                fputcsv($temp_table,$data);
            }
        }
        fclose($f);
        fclose($temp_table);

        rename('data/courses-temp.csv','data/courses.csv');
        //$go = 'Location: ' . $_SERVER['HTTP_REFERER'];
        //header($go);
        //echo 'OK!';
        //usleep(5000);
    }


endif;
