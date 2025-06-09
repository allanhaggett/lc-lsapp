<?php 
ob_start();
require('inc/lsapp.php');
require('inc/Parsedown.php');
$Parsedown = new Parsedown();
opcache_reset();

if(!isAdmin()) {
    header('Location: /lsapp/');
    exit;
}

// Handle form submission
if($_POST) {
    // Validate and sanitize inputs
    $courseid = filter_var($_POST['CourseID'], FILTER_VALIDATE_INT);
    if(!$courseid) {
        die("Invalid course ID");
    }
    
    // Ensure HTTPS for pre/post work links
    $prework = sanitize($_POST['PreWork']);
    $postwork = sanitize($_POST['PostWork']);
    
    if($prework) {
        $scheme = parse_url($prework, PHP_URL_SCHEME);
        if($scheme != 'https') {
            $prework = str_replace('http://', 'https://', $prework);
        }
    }
    
    if($postwork) {
        $scheme = parse_url($postwork, PHP_URL_SCHEME);
        if($scheme != 'https') {
            $postwork = str_replace('http://', 'https://', $postwork);
        }
    }
    
    // Process checkboxes
    $weship = isset($_POST['WeShip']) ? 'Yes' : 'No';
    $alchemer = isset($_POST['Alchemer']) ? 'Yes' : 'No';
    $hubInclude = isset($_POST['HUBInclude']) ? 'Yes' : 'No';
    $featured = isset($_POST['Featured']) ? 'Yes' : 'No';
    $isMoodle = isset($_POST['isMoodle']) ? 'Yes' : 'No';
    $openAccessOptin = isset($_POST['OpenAccessOptin']) ? 'Yes' : 'No';
    
    // Combine times
    $combinedtimes = sanitize($_POST['StartTime']) . ' - ' . sanitize($_POST['EndTime']);
    
    // Clean LAN path
    $lanpath = ltrim(trim($_POST['PathLAN']),'\\');
    $lanpath = rtrim($lanpath,'\\');
    
    // Create slug
    $slug = createSlug($_POST['CourseName']);
    
    // Get current timestamp
    $now = date('Y-m-d\TH:i:s');
    
    // Build course data array
    $course = [
        $_POST['CourseID'],
        sanitize($_POST['Status']),
        sanitize($_POST['CourseName']),
        sanitize($_POST['CourseShort']),
        sanitize($_POST['ItemCode']),
        $combinedtimes,
        sanitize($_POST['ClassDays']),
        sanitize($_POST['ELM']),
        $prework,
        $postwork,
        sanitize($_POST['CourseOwner'] ?? ''),
        '', // old minmax field
        sanitize($_POST['CourseNotes']),
        sanitize($_POST['Requested']),
        sanitize($_POST['RequestedBy']),
        sanitize($_POST['EffectiveDate']),
        sanitize($_POST['CourseDescription']),
        sanitize($_POST['CourseAbstract']),
        sanitize($_POST['Prerequisites']),
        sanitize($_POST['Keywords']),
        '', // old category field
        sanitize($_POST['Method']),
        sanitize($_POST['elearning']),
        $weship,
        sanitize($_POST['ProjectNumber']),
        sanitize($_POST['Responsibility']),
        sanitize($_POST['ServiceLine']),
        sanitize($_POST['STOB']),
        sanitize($_POST['MinEnroll']),
        sanitize($_POST['MaxEnroll']),
        sanitize($_POST['StartTime']),
        sanitize($_POST['EndTime']),
        sanitize($_POST['CourseColor']),
        $featured,
        sanitize($_POST['Developer'] ?? ''),
        sanitize($_POST['EvaluationsLink']),
        sanitize($_POST['LearningHubPartner']),
        $alchemer,
        sanitize($_POST['Topics']),
        sanitize($_POST['Audience'] ?? ''),
        sanitize($_POST['Levels'] ?? ''),
        sanitize($_POST['Reporting'] ?? ''),
        $lanpath,
        sanitize($_POST['PathStaging']),
        sanitize($_POST['PathLive']),
        sanitize($_POST['PathNIK']),
        sanitize($_POST['PathTeams']),
        $isMoodle,
        sanitize($_POST['TaxonomyProcessed'] ?? ''),
        sanitize($_POST['TaxonomyProcessedBy'] ?? ''),
        sanitize($_POST['ELMCourseID']),
        $now,
        sanitize($_POST['Platform']),
        $hubInclude,
        sanitize($_POST['RegistrationLink']),
        $slug,
        sanitize($_POST['HubExpirationDate']),
        $openAccessOptin
    ];
    
    // Update courses.csv
    $f = fopen('data/courses.csv','r');
    $temp_table = fopen('data/courses-temp.csv','w');
    
    // Copy headers
    $headers = fgetcsv($f);
    fputcsv($temp_table, $headers);
    
    // Process rows
    $coursesteward = '';
    $coursedeveloper = '';
    
    while (($data = fgetcsv($f)) !== FALSE) {
        if($data[0] == $courseid) {
            $coursesteward = $data[10];
            $coursedeveloper = $data[34];
            fputcsv($temp_table, $course);
        } else {
            fputcsv($temp_table, $data);
        }
    }
    
    fclose($f);
    fclose($temp_table);
    
    rename('data/courses-temp.csv', 'data/courses.csv');
    
    // Update course-people.csv if steward or developer changed
    $peoplefp = fopen('data/course-people.csv', 'a+');
    
    if(($_POST['CourseOwner'] ?? '') != $coursesteward && !empty($_POST['CourseOwner'])) {
        $stew = [$courseid, 'steward', $_POST['CourseOwner'], $now];
        fputcsv($peoplefp, $stew);
    }
    
    if(isset($_POST['Developer']) && $_POST['Developer'] != $coursedeveloper && !empty($_POST['Developer'])) {
        $dev = [$courseid, 'dev', $_POST['Developer'], $now];
        fputcsv($peoplefp, $dev);
    }
    
    fclose($peoplefp);
    
    header('Location: course.php?courseid=' . $courseid);
    exit;
}

// Display form
$courseid = (isset($_GET['courseid'])) ? $_GET['courseid'] : 0;
$deets = getCourse($courseid);

if(!$deets) {
    header('Location: /lsapp/');
    exit;
}

// Load partners and platforms from JSON files
$partnersJson = file_get_contents('data/partners.json');
$partners = json_decode($partnersJson, true);

$platformsJson = file_get_contents('data/platforms.json');
$platforms = json_decode($platformsJson, true);

// Get taxonomy options
$topics = getAllTopics();
$audience = getAllAudiences();
$deliverymethods = getDeliveryMethods();
$levels = getLevels();
$reportinglist = getReportingList();

?>
<?php getHeader() ?>

<title>Update <?= sanitize($deets[2]) ?></title>
<style>
.form-section {
    background-color: var(--bs-light-bg-subtle);
    border: 1px solid var(--bs-border-color);
    border-radius: 0.375rem;
    padding: 1rem;
    margin-bottom: 1.5rem;
}
.form-section-title {
    font-weight: bold;
    text-transform: uppercase;
    font-size: 0.875rem;
    margin-bottom: 1rem;
}
</style>

<?php getScripts() ?>
<body>
<?php getNavigation() ?>

<div class="container mb-5">
<div class="row justify-content-md-center">
<div class="col-md-10">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1>Update: <?= sanitize($deets[2]) ?></h1>
    <a href="course.php?courseid=<?= $courseid ?>" class="btn btn-light">Cancel</a>
</div>

<form method="post" action="course-update.php" class="mb-3" id="courseupdateform">
    
    <!-- Hidden fields -->
    <input type="hidden" name="CourseID" value="<?= sanitize($deets[0]) ?>">
    <input type="hidden" name="Requested" value="<?= sanitize($deets[13]) ?>">
    <input type="hidden" name="RequestedBy" value="<?= sanitize($deets[14]) ?>">
    <input type="hidden" name="TaxonomyProcessed" value="<?= sanitize($deets[48]) ?>">
    <input type="hidden" name="TaxonomyProcessedBy" value="<?= sanitize($deets[49]) ?>">
    
    <!-- Basic Information Section -->
    <div class="form-section">
        <div class="form-section-title">Basic Information</div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="Status" class="form-label">Status</label>
                <select name="Status" id="Status" class="form-select" required>
                    <option value="" disabled>Select one</option>
                    <?php $statuses = ['Requested','Active','Inactive'] ?>
                    <?php foreach($statuses as $s): ?>
                        <option value="<?= $s ?>" <?= ($s == $deets[1]) ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="CourseShort" class="form-label">Short Name</label>
                <small class="d-block text-muted">Max 10 chars, no spaces</small>
                <input type="text" name="CourseShort" id="CourseShort" class="form-control" value="<?= sanitize($deets[3]) ?>" maxlength="10">
            </div>
            <div class="col-md-3 mb-3">
                <label for="ItemCode" class="form-label">Item Code</label>
                <input type="text" name="ItemCode" id="ItemCode" class="form-control" value="<?= sanitize($deets[4]) ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label for="Method" class="form-label">Delivery Method</label>
                <select name="Method" id="Method" class="form-select" required>
                    <option value="" disabled>Select one</option>
                    <?php $methods = ['Classroom','eLearning','Blended','Webinar'] ?>
                    <?php foreach($methods as $method): ?>
                        <option value="<?= $method ?>" <?= ($method == $deets[21]) ? 'selected' : '' ?>><?= $method ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="LearningHubPartner" class="form-label">Learning Hub Partner</label>
                <select name="LearningHubPartner" id="LearningHubPartner" class="form-select" required>
                    <option value="" disabled <?= empty($deets[36]) ? 'selected' : '' ?>>Select one</option>
                    <?php foreach($partners as $partner): ?>
                        <option value="<?= sanitize($partner['name']) ?>" <?= ($partner['name'] == $deets[36]) ? 'selected' : '' ?>>
                            <?= sanitize($partner['name']) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="Platform" class="form-label">Platform</label>
                <select name="Platform" id="Platform" class="form-select" required>
                    <option value="" disabled <?= empty($deets[52]) ? 'selected' : '' ?>>Select one</option>
                    <?php foreach($platforms as $platform): ?>
                        <option value="<?= sanitize($platform['name']) ?>" <?= ($platform['name'] == $deets[52]) ? 'selected' : '' ?>>
                            <?= sanitize($platform['name']) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="HUBInclude" id="HUBInclude" 
                           <?= ($deets[53] == 'Yes' || $deets[53] == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="HUBInclude">Include in LearningHUB?</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="OpenAccessOptin" id="OpenAccessOptin" 
                           <?= ($deets[57] == 'Yes' || $deets[57] == 1) ? 'checked' : '' ?>
                           <?= empty($deets[3]) ? 'disabled' : '' ?>>
                    <label class="form-check-label" for="OpenAccessOptin">OpenAccess Publish?</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="Featured" id="Featured" 
                           <?= ($deets[33] == 'Yes' || $deets[33] == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="Featured">Featured?</label>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="WeShip" id="WeShip" 
                           <?= ($deets[23] == 'Yes' || $deets[23] == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="WeShip">We Ship?</label>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Course Details Section -->
    <div class="form-section">
        <div class="form-section-title">Course Details</div>
        
        <div class="mb-3">
            <label for="CourseName" class="form-label">Course Name (Long)</label>
            <small class="d-block text-muted">Max 200 characters - Full/Complete title of the course</small>
            <input type="text" name="CourseName" id="CourseName" class="form-control" required value="<?= sanitize($deets[2]) ?>" maxlength="200">
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="ELMCourseID" class="form-label">ELM Course ID</label>
                <input type="text" name="ELMCourseID" id="ELMCourseID" class="form-control" value="<?= sanitize($deets[50]) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="CourseColor" class="form-label">Color</label>
                <input type="text" name="CourseColor" id="CourseColor" class="form-control" value="<?= sanitize($deets[32]) ?>">
            </div>
        </div>
        
        <div class="mb-3">
            <label for="CourseDescription" class="form-label">Course Description</label>
            <small class="d-block text-muted">Max 254 characters - Overall purpose in 2-3 sentences including: duration, target learners, delivery method</small>
            <textarea name="CourseDescription" id="CourseDescription" class="form-control" rows="3" required maxlength="254"><?= sanitize($deets[16]) ?></textarea>
        </div>
        
        <div class="mb-3">
            <label for="CourseAbstract" class="form-label">Course Abstract</label>
            <small class="d-block text-muted">Max 4000 characters - Detailed elaboration including background, objectives, benefits, structure</small>
            <textarea name="CourseAbstract" id="CourseAbstract" class="form-control" rows="6"><?= sanitize($deets[17]) ?></textarea>
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="Prerequisites" class="form-label">Prerequisites</label>
                <input type="text" name="Prerequisites" id="Prerequisites" class="form-control" value="<?= sanitize($deets[18]) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="Keywords" class="form-label">Keywords</label>
                <small class="d-block text-muted">Comma-separated search terms</small>
                <input type="text" name="Keywords" id="Keywords" class="form-control" value="<?= sanitize($deets[19]) ?>">
            </div>
        </div>
        
        <div class="mb-3">
            <label for="CourseNotes" class="form-label">Notes</label>
            <textarea name="CourseNotes" id="CourseNotes" class="form-control" rows="3"><?= sanitize($deets[12]) ?></textarea>
        </div>
    </div>
    
    <!-- Taxonomies Section -->
    <div class="form-section">
        <div class="form-section-title">Taxonomies</div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="Topics" class="form-label">Topic</label>
                <select name="Topics" id="Topics" class="form-select" required>
                    <option value="" disabled <?= empty($deets[38]) ? 'selected' : '' ?>>Select one</option>
                    <?php foreach($topics as $t): ?>
                        <option value="<?= $t ?>" <?= ($deets[38] == $t) ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="Audience" class="form-label">Audience</label>
                <select name="Audience" id="Audience" class="form-select" required>
                    <option value="" disabled <?= empty($deets[39]) ? 'selected' : '' ?>>Select one</option>
                    <?php foreach($audience as $a): ?>
                        <option value="<?= $a ?>" <?= ($deets[39] == $a) ? 'selected' : '' ?>><?= $a ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="Levels" class="form-label">Group</label>
                <select name="Levels" id="Levels" class="form-select" required>
                    <option value="" disabled <?= empty($deets[40]) ? 'selected' : '' ?>>Select one</option>
                    <?php foreach($levels as $l): ?>
                        <option value="<?= $l ?>" <?= ($deets[40] == $l) ? 'selected' : '' ?>><?= $l ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="Reporting" class="form-label">Reporting</label>
                <select name="Reporting" id="Reporting" class="form-select">
                    <option value="" disabled <?= empty($deets[41]) ? 'selected' : '' ?>>Select one</option>
                    <?php foreach($reportinglist as $r): ?>
                        <option value="<?= $r ?>" <?= ($deets[41] == $r) ? 'selected' : '' ?>><?= $r ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    </div>
    
    <!-- People Section -->
    <div class="form-section">
        <div class="form-section-title">People</div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="CourseOwner" class="form-label">Steward</label>
                <small class="d-block text-muted">Responsible for delivery</small>
                <select name="CourseOwner" id="CourseOwner" class="form-select">
                    <option value="">Unassigned</option>
                    <?php getPeople($deets[10]) ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="Developer" class="form-label">Developer</label>
                <small class="d-block text-muted">Responsible for materials creation/revisions</small>
                <select name="Developer" id="Developer" class="form-select">
                    <option value="">Unassigned</option>
                    <?php getPeople($deets[34]) ?>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Administrative Details Section -->
    <div class="form-section">
        <div class="form-section-title">Administrative Details</div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="EffectiveDate" class="form-label">Effective Date</label>
                <small class="d-block text-muted">Date the course should be visible to learners</small>
                <input type="date" name="EffectiveDate" id="EffectiveDate" class="form-control" value="<?= sanitize($deets[15]) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check mt-4">
                    <input type="checkbox" class="form-check-input" name="Alchemer" id="Alchemer" 
                           <?= ($deets[37] == 'Yes' || $deets[37] == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="Alchemer">Alchemer?</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="isMoodle" id="isMoodle" 
                           <?= ($deets[47] == 'Yes' || $deets[47] == 1) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="isMoodle">Moodle Course?</label>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="MinEnroll" class="form-label">Min Participants</label>
                <input type="number" name="MinEnroll" id="MinEnroll" class="form-control" value="<?= sanitize($deets[28]) ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label for="MaxEnroll" class="form-label">Max Participants</label>
                <input type="number" name="MaxEnroll" id="MaxEnroll" class="form-control" value="<?= sanitize($deets[29]) ?>">
            </div>
            <div class="col-md-2 mb-3">
                <label for="ClassDays" class="form-label">Days</label>
                <input type="text" name="ClassDays" id="ClassDays" class="form-control" value="<?= sanitize($deets[6]) ?>">
            </div>
            <div class="col-md-2 mb-3">
                <label for="StartTime" class="form-label">Start Time</label>
                <input type="text" name="StartTime" id="StartTime" class="form-control starttime" value="<?= sanitize($deets[30]) ?>">
            </div>
            <div class="col-md-2 mb-3">
                <label for="EndTime" class="form-label">End Time</label>
                <input type="text" name="EndTime" id="EndTime" class="form-control endtime" value="<?= sanitize($deets[31]) ?>">
            </div>
        </div>
        
        <div id="notelm" class="<?= ($deets[52] == 'PSA Learning System') ? 'd-none' : '' ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="RegistrationLink" class="form-label">Registration Link</label>
                    <small class="d-block text-muted">If not in Learning System, where to register?</small>
                    <input type="url" name="RegistrationLink" id="RegistrationLink" class="form-control" value="<?= sanitize($deets[54]) ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="HubExpirationDate" class="form-label">Expiration Date</label>
                    <small class="d-block text-muted">Date to remove from search results</small>
                    <input type="date" name="HubExpirationDate" id="HubExpirationDate" class="form-control" value="<?= sanitize($deets[56]) ?>">
                </div>
            </div>
        </div>
    </div>
    
    <!-- Links Section -->
    <div class="form-section">
        <div class="form-section-title">Links & Resources</div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="ELM" class="form-label">ELM Link</label>
                <input type="url" name="ELM" id="ELM" class="form-control" value="<?= sanitize($deets[7]) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="elearning" class="form-label">eLearning Course URL</label>
                <input type="url" name="elearning" id="elearning" class="form-control" value="<?= sanitize($deets[22]) ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="PreWork" class="form-label">Pre-work Link</label>
                <input type="url" name="PreWork" id="PreWork" class="form-control" value="<?= sanitize($deets[8]) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="PostWork" class="form-label">Post-work Link</label>
                <input type="url" name="PostWork" id="PostWork" class="form-control" value="<?= sanitize($deets[9]) ?>">
            </div>
        </div>
        <div class="mb-3">
            <label for="EvaluationsLink" class="form-label">Evaluation Link</label>
            <input type="url" name="EvaluationsLink" id="EvaluationsLink" class="form-control" value="<?= sanitize($deets[35]) ?>">
        </div>
    </div>
    
    <!-- Print Details Section -->
    <div class="form-section">
        <div class="form-section-title">Print Materials Operating Codes</div>
        <div class="row">
            <div class="col-md-3 mb-3">
                <label for="ProjectNumber" class="form-label">Project Number</label>
                <input type="text" name="ProjectNumber" id="ProjectNumber" class="form-control" value="<?= sanitize($deets[24]) ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label for="Responsibility" class="form-label">Responsibility</label>
                <input type="text" name="Responsibility" id="Responsibility" class="form-control" value="<?= sanitize($deets[25]) ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label for="ServiceLine" class="form-label">Service Line</label>
                <input type="text" name="ServiceLine" id="ServiceLine" class="form-control" value="<?= sanitize($deets[26]) ?>">
            </div>
            <div class="col-md-3 mb-3">
                <label for="STOB" class="form-label">STOB</label>
                <input type="text" name="STOB" id="STOB" class="form-control" value="<?= sanitize($deets[27]) ?>">
            </div>
        </div>
    </div>
    
    <!-- Developer File Paths Section -->
    <div class="form-section">
        <div class="form-section-title">Developer File Paths</div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="PathLAN" class="form-label">LAN Path</label>
                <input type="text" name="PathLAN" id="PathLAN" class="form-control" value="<?= sanitize($deets[42]) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="PathStaging" class="form-label">Staging Path</label>
                <input type="text" name="PathStaging" id="PathStaging" class="form-control" value="<?= sanitize($deets[43]) ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="PathLive" class="form-label">Live Path</label>
                <input type="text" name="PathLive" id="PathLive" class="form-control" value="<?= sanitize($deets[44]) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="PathNIK" class="form-label">NIK Path</label>
                <input type="text" name="PathNIK" id="PathNIK" class="form-control" value="<?= sanitize($deets[45]) ?>">
            </div>
        </div>
        <div class="mb-3">
            <label for="PathTeams" class="form-label">Teams Path</label>
            <input type="text" name="PathTeams" id="PathTeams" class="form-control" value="<?= sanitize($deets[46]) ?>">
        </div>
    </div>
    
    <div class="d-flex justify-content-between">
        <button type="submit" class="btn btn-primary">Save Course Info</button>
        <a href="course.php?courseid=<?= $courseid ?>" class="btn btn-secondary">Cancel</a>
    </div>
</form>

</div>
</div>
</div>

<?php require('templates/javascript.php') ?>
<script>
$(document).ready(function(){
    // Platform-based field visibility
    $('#Platform').on('change', function() {
        if($(this).val() === 'PSA Learning System') {
            $('#notelm').addClass('d-none');
        } else {
            $('#notelm').removeClass('d-none');
        }
    });
    
    // Time picker setup
    var moment = rome.moment;
    var endtime = rome(document.querySelector('.endtime'), { 
        date: false,
        timeValidator: function (d) {
            var m = moment(d);
            var start = m.clone().hour(7).minute(59).second(59);
            var end = m.clone().hour(16).minute(30).second(1);
            return m.isAfter(start) && m.isBefore(end);
        }
    });
    var starttime = rome(document.querySelector('.starttime'), { 
        date: false,
        timeValidator: function (d) {
            var m = moment(d);
            var start = m.clone().hour(7).minute(59).second(59);
            var end = m.clone().hour(16).minute(0).second(1);
            return m.isAfter(start) && m.isBefore(end);
        }
    });
    
    // Form validation
    $('#courseupdateform').on('submit', function(e) {
        var isValid = true;
        
        // Check required selects
        $(this).find('select[required]').each(function() {
            if(!$(this).val() || $(this).val() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if(!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
    
    // Remove invalid class on change
    $('select[required]').on('change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
<?php require('templates/footer.php') ?>