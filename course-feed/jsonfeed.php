<?php 

opcache_reset();

if (($handle = fopen("data/courses.csv", "r")) !== FALSE) {
    // The following is a little bit of magic that transfers the CSV
    // data into an associative array where you can refer to the values 
    // in each row as the column name.
    $csvs = [];
    while(! feof($handle)) {
       $csvs[] = fgetcsv($handle);
    }
    $datas = [];
    $column_names = [];
    foreach ($csvs[0] as $single_csv) {
        $column_names[] = $single_csv;
    }
    foreach ($csvs as $key => $csv) {
        if ($key === 0) {
            continue;
        }
        foreach ($column_names as $column_key => $column_name) {
            $datas[$key-1][$column_name] = $csv[$column_key];
        }
    }
    fclose($handle);
} // endif fopen courses.csv

//echo '<pre>'; print_r($datas); exit;
// Now that magic "allow us to refer to the column names as key values"
// thing is done, let's loop through our nifty new array and output
// some JSON in nice, easy way.

$json = '{' . PHP_EOL;
$json .= '"version": "https://jsonfeed.org/version/1",' . PHP_EOL;
$json .= '"title": "BC Gov Corporate Learning Courses - Forward!",' . PHP_EOL;
$json .= '"home_page_url": "https://learningcentre.gww.gov.bc.ca/learninghub/",' . PHP_EOL;
$json .= '"feed_url": "https://learn.bcpublicservice.gov.bc.ca/learning-hub/learning-partner-courses.json",' . PHP_EOL;
$json .= '"items": [' . PHP_EOL;
foreach($datas as $course) {
    
        $d = preg_replace( "/\r|\n/", "", htmlentities($course['Course Description']));
        $desc = iconv(mb_detect_encoding($d, mb_detect_order(), true), "UTF-8", $d);
        
        $stupiddate = str_replace('  ', ' ', $course['Course Last Modified']);
        $newDate = date("Y-m-d\TH:i:s", strtotime($stupiddate));

        $json .= '{' . PHP_EOL;
        $json .= '"id":"' . $course['Course Code'] . '",' . PHP_EOL;
        $json .= '"title":"' . $course['Course Name'] . '",' . PHP_EOL;
        $json .= '"summary":"'. $desc . '",' . PHP_EOL;
        $json .= '"content_text":"' . $course['Course Name'] . '",' . PHP_EOL;
        $json .= '"content_html":"<div>' . $course['Course Name'] . '</div>",' . PHP_EOL;
        $json .= '"delivery_method":"' . $course['Delivery Method'] . '",' . PHP_EOL;
        $json .= '"_available_classes":"' . $course['Available Classes'] . '",' . PHP_EOL;
        $json .= '"_course_id":"' . $course['Course ID'] . '",' . PHP_EOL;
        $json .= '"_keywords":"' . $course['Keywords'] . '",' . PHP_EOL;
        $json .= '"_audience":"' . $course['Audience'] . '",' . PHP_EOL;
        $json .= '"_group":"' . $course['Group'] . '",' . PHP_EOL;
        $json .= '"_topic":"' . $course['Topic'] . '",' . PHP_EOL;
        //$json .= '"duration":"' . $course['Days'] . '",' . PHP_EOL;
        $json .= '"_learning_partner":"' . $course['Course Owner Org'] . '",' . PHP_EOL;
        $json .= '"_external_system":"PSA Learning System",' . PHP_EOL;
        $json .= '"author":"' . $course['Course Owner Org'] . '",' . PHP_EOL;
        $json .= '"date_published":"2020-05-13T14:00:00",' . PHP_EOL;
        $json .= '"date_modified":"' . $newDate . '",' . PHP_EOL;
        $json .= '"tags":"' . rtrim(trim($course['Category']),',') . '",' . PHP_EOL;
        // $json .= '"url":"' . $course['Link to ELM Search'] . '"' . PHP_EOL;
        // The provided Link to ELM Search value needs to be parsed to %22 encode the quote values
        $json .= '"url":"https://learning.gov.bc.ca/psc/CHIPSPLM/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_CRS_DTL_FL.GBL?Page=LM_CRS_DTL_FL&Action=U&ForceSearch=Y&LM_CI_ID=';
        $json .= $course['Course ID'] . '"' . PHP_EOL;
        $json .= '},'.PHP_EOL;
    
}
$json .= '{}';
$json .= ']';
$json .= '}';
$jsoname = 'data/learning-partner-courses.json';
file_put_contents($jsoname, $json);

$newfile = 'E:\WebSites\NonSSOLearning\learning-hub\learning-partner-courses.json';

if (!copy($jsoname, $newfile)) {
    echo 'Failed to copy $jsoname... contact Allan';
    exit;
}

header('Location: lhub-course-sync.php');