<?php 
opcache_reset();
$cats = '';
// 
// First let's process the keywords into an array so that we can merge them 
// into the courses. We do this in two separate files because of how ELM
// produces these queries: 
// The main query comes out with a single line *per category*, and since it 
// would work the same way with the keywords, the query would potentially 
// return too many results (> 5000) and fail. We do one export with categories
// and collapse those into a line per course with comma-separated categories;
// then we do another export with a line per keyword and collapse that into 
// a line per course with comma-separated keywords.
// Finally, we merge the two so there's one list of courses with both cats and
// keywords and write it all to the file, which then becomes the feed.
// 
// #TODO update ELM query to GBC_LEARNINGHUB_SYNC
if (($handle = fopen("data/GBC_ATWORK_CATALOG_KEYWORDS.csv", "r")) !== FALSE) { 
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
    // "Course Code","Course Name","Keyword","Keyword Type ID","Keyword Type"
    $count = 0;
    $lastcode = '';
    $k = '';
    $partnerkey = '';
    $keys = [];
    //echo '<pre>'; print_r($datas); exit;
    // #TODO Comment the following!! 
    foreach($datas as $keyword) {
        
        $code = $keyword['Course Code'];
        $key = $keyword['Keyword'];
        
        if($count > 0) {
            if ($code != $lastcode) {
                $newkey = array($lastcode,$k,$partnerkey);
                array_push($keys,$newkey);
                $k = '';
                $partnerkey = '';
            }
        }
        if($keyword['Keyword Type ID'] == 1039) {
            $partnerkey = $key;
        }
        $k .= $key . ', ';
        $lastcode = $keyword['Course Code'];
        $count++;
        
    }
}
//echo '<pre>';print_r($keys); exit;

//
// Next let's look at the catalog file itself
//
if (($handle = fopen("data/GBC_LEARNINGHUB_SYNC2.csv", "r")) !== FALSE) {
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

    // #TODO add a sort in so that it enforces the grouping of courses by item-code
    // The below algorithm _depends_ on this grouping to work properly. The ELM 
    // query seems to do this by default, but we should make double-sure here.

    // Now that magic "allow us to refer to the column names as key values"
    // thing is done, let's loop through our nifty new array
    // "Course Code","Course Name","Course Description","Delivery Method",
    // "Category","Learner Group","Duration","Available Classes",
    // "Link to ELM Search", "Course Last Modified"
    $newcourses = [];
    $lastcode = '';
    $count = 0;
    
    //echo '<pre>'; print_r($datas); exit;
    foreach($datas as $course) {

        
    // If it's not in the main learner group (or other exceptions), skip it
    if(
        $course['Learner Group'] == 'All Government of British Columbia Learners' || 
        $course['Learner Group'] == 'Excluded Managers' || 
        $course['Learner Group'] == 'Ministries - All'
    ) {

        $currentcode = $course['Course Code'];
        
        // if this is the first loop, then the code won't equal
        // the $lastcode, so we don't want to perform the check
        if($count > 0) {

            // If this line's code doesn't equal the last line's code
            // then we're at a new course, and so we write the _previous_
            // line to the new array 
            // "Course Code","Course Name","Course Description","Delivery Method","Category",
            // "Learner Group","Days","Hours","Minutes","Available Classes","Link to ELM Search",
            // "Course Last Modified","Course Owner Org"
            if($currentcode != $lastcode) {

                $newcourse = array($code,
                                    $name,
                                    $desc,
                                    $method,
                                    $cats,
                                    $learnergroup,
                                    $parsedduration,
                                    $parsedduration,
                                    $parsedduration,
                                    $availclasses,
                                    $linktoelm,
                                    $lastmodified,
                                    $courseowner,
                                    $courseid,
                                    '', // keywords
                                    $group,
                                    $audience,
                                    $topic
                );
                //json_encode($newcourse, JSON_HEX_APOS|JSON_HEX_QUOT);
                array_push($newcourses,$newcourse);
                $cats = '';
                $audience = ''; 
                $group = ''; 
                $topic = '';

            }
        }

        $code = $course['Course Code'];
        
        $name = htmlspecialchars($course['Course Name'], ENT_QUOTES, 'UTF-8');
        $desc = trim_all($course['Course Description']);
        
        $method = $course['Delivery Method'];


        if(strlen($course['Category']) > 0) {
            
            $taxtype = $course['Short Name'];
            if($taxtype == 'Audience') {
                $audience = $course['Category'];
             }
            if($taxtype == 'Group') {
                $group = $course['Category'];
             }
            if($taxtype == 'Topic') {
                $topic = $course['Category'];
             }
            
            $cats = $course['Category'] . ', ' . $cats;
            $cats = rtrim($cats,',');

        } else {

            $cats = '';
            $audience = '';
            $group = '';
            $topic = '';

        }

        $learnergroup = $course['Learner Group'];
        // Default ELM duration gets output as:
        // "0 days, 1 hrs, 30 mins"
        // Here, we parse out the zero values, so the above becomes:
        // "1 hrs 30 mins"
        // #TODO this is broken so I'm commenting it out and setting 
        // a static duration especially since we don't even parse 
        // this on the LearningHUB side
        //
        // if($course['Duration'] != 'Not Listed') {
        //     $dur = explode(',', $course['Duration']);
        //     $parsedduration = '';
        //     foreach($dur as $du) {
        //         $trimmed = trim($du);
        //         if($trimmed[0] != '0') {
        //             $parsedduration .= $du;
        //         }
        //     }
        //     $parsedduration = trim($parsedduration);
        // } else {
        //     $parsedduration = 'Not Listed';
        // }
        $parsedduration = 'Not Listed';
        $availclasses = $course['Available Classes'];
        $lastmodified = $course['Course Last Modified'];
        $linktoelm = $course['Link to ELM Search'];
        $courseowner = $course['Course Owner Org']; // [12]
        $courseid = $course['Course ID'];
        $lastcode = $course['Course Code'];
        $count++;
    
    } // endif learner group == "All Government of British Columbia Learners"
    } // endforeach datas as course
    
} // endif fopen courses.csv

//echo '<pre>'; print_r($newcourses); exit;
// Now we merge $keys into $newcourses so that $newcourses has both categories 
// and keywords associated with each course
$courz = [];
$kwords = '';
$learningpartner = '';
$includecourse = 0;

foreach($newcourses as $c) {
    
    // For every course, loop through the entire array of keywords
    foreach($keys as $k) {
        // compare the item codes
        if($k[0] == $c[0]) {
            // if the learning partner code is present then we want to add
            // the course; if it's not then there's no partner so we skip it
            // as we only add courses that have partnerships.
            if(!empty($k[2])) {
                // start adding keywords to a single string
                $kwords .= $k[1] . ',';
                // ELM keywords have a character limit and some partner names 
                // are too long, so we abbreivate in ELM and manually account 
                // for that here
                if($k[2] == 'EMCR') {
                    $learningpartner = 'Emergency Management and Climate Readiness';
                } elseif($k[2] == 'Priorities & Innovation') {
                    $learningpartner = 'Leadership, Engagement and Priority Initiatives';
                } elseif($k[2] == 'CIRMO') {
                    $learningpartner = 'Corporate Information and Records Management Office';
                } elseif($k[2] == 'DWCS') {
                    $learningpartner = 'Digital Workplace and Collaboration Services Branch';
                } else {
                    $learningpartner = $k[2];
                }
                // set a flag so that next step we can assign values 
                // and move to the next course
                $includecourse = 1;
            }
        }
    }
    if($includecourse) {
        
        if($c[3] == 'Moodle') $c[3] = 'eLearning';
        if($c[3] == 'Virtual') $c[3] = 'Webinar';
        if($c[3] == 'Self-Directed') $c[3] = 'eLearning';
        if($c[3] == 'Scheduled Learning Activities') $c[3] = 'eLearning';
        if($c[3] == 'Self-Paced Learning Activities') $c[3] = 'eLearning';

        $c[12] = $learningpartner;
        $c[14] = rtrim($kwords,', ,');
        $courz[] = $c;
        $kwords = '';
        $learningpartner = '';
        $includecourse = 0;
    }
}

$final_cols = ['Course Code',
                'Course Name',
                'Course Description',
                'Delivery Method',
                'Category',
                'Learner Group',
                'parsedduration',
                'parsedduration',
                'parsedduration',
                'Available Classes',
                'Link to ELM Search',
                'Course Last Modified',
                'Course Owner Org',
                'Course ID',
                'Keywords',
                'Group',
                'Audience',
                'Topic'
            ];
//  echo '<pre>'; print_r($final_cols);
//  echo '<pre>'; print_r($courz); exit;
$fp = fopen('data/courses.csv', 'w');

//echo '<pre>'; print_r($final_cols); exit;
// Add the headers
fputcsv($fp, $final_cols);
// Now loop through the $newelm array created above and write each line to the file
foreach ($courz as $fields) {
	fputcsv($fp, $fields);
}
// Close the file
fclose($fp);
// Redirect 
header('Location: jsonfeed.php');



function trim_all( $str , $what = NULL , $with = ' ' )
{
    if( $what === NULL )
    {
        //  Character      Decimal      Use
        //  "\0"            0           Null Character
        //  "\t"            9           Tab
        //  "\n"           10           New line
        //  "\x0B"         11           Vertical Tab
        //  "\r"           13           New Line in Mac
        //  " "            32           Space
       
        $what   = "\\x00-\\x20";    //all white-spaces and control chars
    }
   
    return trim( preg_replace( "/[".$what."]+/" , $with , $str ) , $what );
}