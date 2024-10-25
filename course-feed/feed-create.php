<?php 

opcache_reset();

// #TODO maybe use getCoursesActive here?
if (($handle = fopen("../data/courses.csv", "r")) !== FALSE) {
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
//
//0-CourseID,1-Status,2-CourseName,3-CourseShort,4-ItemCode,5-ClassTimes,6-ClassDays,7-ELM,8-PreWork,9-PostWork,10-CourseOwner,
//11-MinMax,12-CourseNotes,13-Requested,14-RequestedBy,15-EffectiveDate,16-CourseDescription,17-CourseAbstract,18-Prerequisites,
//19-Keywords,20-Categories,21-Method,22-elearning,23-WeShip,24-ProjectNumber,25-Responsibility,26-ServiceLine,
//27-STOB,28-MinEnroll,29-MaxEnroll,30-StartTime,31-EndTime,32-Color
//33-Featured,34-Developer,35-EvaluationsLink,36-LearningHubPartner,37-Alchemer,
//38-Topics,39-Audience,40-Levels,41-Reporting
//42-PathLAN,43-PathStaging,44-PathLive,45-PathNIK,46-PathTeams
// 47-isMoodle,48-TaxProcessed,49-TaxProcessedBy,50-ELMCourseID,51-Modified
// 52-HUBInclude, 53-ExternalSystem

$json = '{' . PHP_EOL;
$json .= '"version": "https://jsonfeed.org/version/1",' . PHP_EOL;
$json .= '"title": "BC Gov Corporate Learning Courses",' . PHP_EOL;
$json .= '"home_page_url": "https://learningcentre.gww.gov.bc.ca/learninghub/",' . PHP_EOL;
$json .= '"feed_url": "https://learn.bcpublicservice.gov.bc.ca/learning-hub/learning-partner-courses.json",' . PHP_EOL;
$json .= '"items": [' . PHP_EOL;
foreach($datas as $course) {
    
        $d = preg_replace( "/\r|\n/", "", htmlentities($course['Course Description']));
        $desc = iconv(mb_detect_encoding($d, mb_detect_order(), true), "UTF-8", $d);

        if($row[1] == 'Active' && !empty($course['HubInclude']) && $course['HubInclude'] > 0) {
            $json .= '{' . PHP_EOL;
            $json .= '"id":"' . $course['ItemCode'] . '",' . PHP_EOL;
            $json .= '"title":"' . $course['CourseName'] . '",' . PHP_EOL;
            $json .= '"summary":"'. $desc . '",' . PHP_EOL;
            $json .= '"content_text":"' . $course['CourseName'] . '",' . PHP_EOL;
            $json .= '"content_html":"<div>' . $course['CourseName'] . '</div>",' . PHP_EOL;
            $json .= '"delivery_method":"' . $course['Method'] . '",' . PHP_EOL;
            // $json .= '"_available_classes":"' . $course['Available Classes'] . '",' . PHP_EOL;
            $json .= '"_course_id":"' . $course['Course ID'] . '",' . PHP_EOL;
            $json .= '"_keywords":"' . $course['Keywords'] . '",' . PHP_EOL;
            $json .= '"_audience":"' . $course['Audience'] . '",' . PHP_EOL;
            $json .= '"_group":"' . $course['Levels'] . '",' . PHP_EOL;
            $json .= '"_topic":"' . $course['Topics'] . '",' . PHP_EOL;
            //$json .= '"duration":"' . $course['Days'] . '",' . PHP_EOL;
            $json .= '"_learning_partner":"' . $course['LearningHubPartner'] . '",' . PHP_EOL;
            $json .= '"_external_system":"' . $course['ExternalSystem'] . '",' . PHP_EOL;
            $json .= '"author":"' . $course['CourseOwner'] . '",' . PHP_EOL;
            $json .= '"date_published":"' . $course['Modified'] . '",' . PHP_EOL;
            $json .= '"date_modified":"' . $course['Modified'] . '",' . PHP_EOL;
            // $json .= '"tags":"' . rtrim(trim($course['Category']),',') . '",' . PHP_EOL;
            // $json .= '"url":"' . $course['Link to ELM Search'] . '"' . PHP_EOL;
            // The provided Link to ELM Search value needs to be parsed to %22 encode the quote values
            $elm_link = '"url":"https://learning.gov.bc.ca/psc/CHIPSPLM/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_CRS_DTL_FL.GBL?Page=LM_CRS_DTL_FL&Action=U&ForceSearch=Y&LM_CI_ID=';
            $elm_link .= $course['CourseID'] . '"' . PHP_EOL;
            $json .= '"url":"'.$elm_link.'"';
            $json .= '},'.PHP_EOL;
        }
    
}
$json .= '{}';
$json .= ']';
$json .= '}';
$jsoname = 'data/corporate-learning-courses.json';
file_put_contents($jsoname, $json);

$newfile = 'E:\WebSites\NonSSOLearning\learning-hub\corporate-learning-courses.json';

if (!copy($jsoname, $newfile)) {
    echo 'Failed to copy $jsoname... contact Allan';
    exit;
}

// header('Location: lhub-course-sync.php');
header('Location: index.php?message=Success');