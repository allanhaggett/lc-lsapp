<?php 
require('inc/lsapp.php');

// Initialize filter variables
$filters = [
    'topic' => $_GET['topic'] ?? '',
    'audience' => $_GET['audience'] ?? '',
    'level' => $_GET['level'] ?? '',
    'category' => $_GET['category'] ?? '',
    'delivery' => $_GET['delivery'] ?? '',
    'platform' => $_GET['platform'] ?? '',
    'status' => $_GET['status'] ?? '',
    'processed' => $_GET['processed'] ?? '',
    'openaccess' => $_GET['openaccess'] ?? '',
    'hubonly' => $_GET['hubonly'] ?? '',
    'sort' => $_GET['sort'] ?? ''
];

// Decode URL-encoded values
foreach (['topic', 'audience', 'level', 'category', 'platform'] as $key) {
    if ($filters[$key]) {
        $filters[$key] = urldecode($filters[$key]);
    }
}

// Get all courses and remove header row
$courses = getCourses();
array_shift($courses);

// Set up sorting
$sortField = 2; // Course name by default
$sortDir = SORT_ASC;
if ($filters['sort'] === 'dateadded') {
    $sortField = 13;
    $sortDir = SORT_DESC;
}

// Sort function
function sortCourses($a, $b) {
    global $sortDir, $sortField;
    return $sortDir == SORT_ASC 
        ? strcmp($a[$sortField], $b[$sortField])
        : strcmp($b[$sortField], $a[$sortField]);
}

// Separate active and inactive courses
$activeCourses = [];
$inactiveCourses = [];
foreach ($courses as $course) {
    if ($course[1] === 'Inactive') {
        $inactiveCourses[] = $course;
    } else {
        $activeCourses[] = $course;
    }
}

// Sort both arrays
usort($activeCourses, 'sortCourses');
usort($inactiveCourses, 'sortCourses');

// Combine based on status filter
$sortedCourses = [];
if ($filters['status'] === 'active') {
    $sortedCourses = $activeCourses;
} elseif ($filters['status'] === 'inactive') {
    $sortedCourses = $inactiveCourses;
} else {
    $sortedCourses = array_merge($activeCourses, $inactiveCourses);
}

// Apply filters
$filteredCourses = [];
$activeCount = 0;

foreach ($sortedCourses as $course) {
    // Check each filter
    if ($filters['level'] && $filters['level'] !== $course[40]) continue;
    if ($filters['audience'] && $filters['audience'] !== $course[39]) continue;
    if ($filters['topic'] && $filters['topic'] !== $course[38]) continue;
    if ($filters['delivery'] && $filters['delivery'] !== $course[21]) continue;
    if ($filters['platform'] && $filters['platform'] !== $course[52]) continue;
    if ($filters['processed'] && $course[48] == $filters['processed']) continue;
    if ($filters['openaccess'] && !($course[57] === 'true' || $course[57] === 'on')) continue;
    if ($filters['hubonly'] && strtolower($filters['hubonly']) === 'true' && strtolower($course[53]) !== 'yes') continue;
    
    // Count active courses
    if ($course[1] === 'Active') {
        $activeCount++;
    }
    
    $filteredCourses[] = $course;
}

// Get filter options
$deliveryMethods = getDeliveryMethods();
$topics = getAllTopics();
$audiences = getAllAudiences();
$levels = getLevels();

// Get unique platforms from courses
$platforms = [];
foreach ($courses as $course) {
    if (!empty($course[52]) && !in_array($course[52], $platforms)) {
        $platforms[] = $course[52];
    }
}
sort($platforms);

// Helper function to build query string
function buildQueryString($filters, $exclude = []) {
    $query = [];
    foreach ($filters as $key => $value) {
        if ($value && !in_array($key, $exclude)) {
            $query[$key] = $value;
        }
    }
    return http_build_query($query);
}

// Helper function to generate filter link
function getFilterLink($filters, $key, $value = null) {
    if ($value === null) {
        // Remove filter
        return 'courses.php?' . buildQueryString($filters, [$key]);
    } else {
        // Add/update filter
        $newFilters = $filters;
        $newFilters[$key] = $value;
        return 'courses.php?' . buildQueryString($newFilters);
    }
}

?>
<?php getHeader() ?>
<title>Learning Centre Course Catalog</title>
<?php getScripts() ?>

<body>
<?php getNavigation() ?>

<div id="courses">
<div class="container-fluid">
<div class="row justify-content-md-center">

<div class="col-md-8">
    <?php if (!empty($filteredCourses)): ?>
        <?php if ($filters['status'] === 'inactive'): ?>
            <h1><span class="badge bg-secondary"><?= count($inactiveCourses) ?></span> Inactive Courses</h1>
        <?php else: ?>
            <h1><span class="badge bg-primary"><?= $activeCount ?></span> Active Courses</h1>
        <?php endif; ?>
    <?php else: ?>
        <h1><span class="badge bg-primary"><?= count($activeCourses) ?></span> Courses</h1>
    <?php endif; ?>
</div>
</div>

<div class="row justify-content-md-center">
<div class="col-md-4 col-xl-3">

    <input class="search form-control mb-2" placeholder="search">
    
    <!-- Quick filters -->
    <div class="mb-2">
        <a class="badge bg-light-subtle text-primary-emphasis" href="courses.php">All Alphabetically</a> 
        <a class="badge bg-light-subtle text-primary-emphasis" href="courses.php?sort=dateadded">All Recent</a>
        <a class="badge bg-light-subtle text-primary-emphasis" href="courses.php?status=active">All Active</a>
        <a class="badge bg-light-subtle text-primary-emphasis" href="courses.php?status=inactive">All Inactive</a>
    </div>
    
    <!-- Special filters -->
    <div class="mb-3">
        <?php if ($filters['openaccess']): ?>
            <a href="<?= getFilterLink($filters, 'openaccess') ?>" class="badge bg-dark-subtle text-primary-emphasis">&times; Open Access</a>
        <?php else: ?>
            <a href="<?= getFilterLink($filters, 'openaccess', 'true') ?>" class="badge bg-light-subtle text-primary-emphasis">Open Access</a>
        <?php endif; ?>
        
        <?php if ($filters['hubonly'] === 'true'): ?>
            <a href="<?= getFilterLink($filters, 'hubonly') ?>" class="badge bg-dark-subtle text-primary-emphasis">&times; LearningHUB</a>
        <?php else: ?>
            <a href="<?= getFilterLink($filters, 'hubonly', 'true') ?>" class="badge bg-light-subtle text-primary-emphasis">LearningHUB</a>
        <?php endif; ?>
    </div>
    
    <!-- Delivery Method Filter -->
    <div class="mb-3">
        <div class="fw-bold">Delivery Method</div>
        <?php foreach ($deliveryMethods as $method): ?>
            <?php if ($filters['delivery'] === $method): ?>
                <a href="<?= getFilterLink($filters, 'delivery') ?>" class="badge bg-dark-subtle text-primary-emphasis">&times; <?= $method ?></a>
            <?php else: ?>
                <a href="<?= getFilterLink($filters, 'delivery', $method) ?>" class="badge bg-light-subtle text-primary-emphasis"><?= $method ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    
    <!-- Audience Filter -->
    <div class="mb-3">
        <div class="fw-bold">Audience</div>
        <?php foreach ($audiences as $audience): ?>
            <?php if ($filters['audience'] === $audience): ?>
                <a href="<?= getFilterLink($filters, 'audience') ?>" class="badge bg-dark-subtle text-primary-emphasis">&times; <?= $audience ?></a>
            <?php else: ?>
                <a href="<?= getFilterLink($filters, 'audience', $audience) ?>" class="badge bg-light-subtle text-primary-emphasis"><?= $audience ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    
    <!-- Topics Filter -->
    <div class="mb-3">
        <div class="fw-bold">Topics</div>
        <?php foreach ($topics as $topic): ?>
            <?php if ($filters['topic'] === $topic): ?>
                <a href="<?= getFilterLink($filters, 'topic') ?>" class="badge bg-dark-subtle text-primary-emphasis">&times; <?= $topic ?></a>
            <?php else: ?>
                <a href="<?= getFilterLink($filters, 'topic', $topic) ?>" class="badge bg-light-subtle text-primary-emphasis"><?= $topic ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <!-- Platform Filter -->
    <div class="mb-3">
        <div class="fw-bold">Platform</div>
        <?php foreach ($platforms as $platform): ?>
            <?php if ($filters['platform'] === $platform): ?>
                <a href="<?= getFilterLink($filters, 'platform') ?>" class="badge bg-dark-subtle text-primary-emphasis">&times; <?= $platform ?></a>
            <?php else: ?>
                <a href="<?= getFilterLink($filters, 'platform', $platform) ?>" class="badge bg-light-subtle text-primary-emphasis"><?= $platform ?></a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

</div>

<div class="col-md-5">
    <div class="list">
    <?php foreach ($filteredCourses as $course): ?>
        <div class="mb-2 p-3 bg-light-subtle border border-secondary-subtle rounded-3">
            <div>
                <div class="float-end pl-3 pb-3">
                    <?php $statusBg = ($course[1] === 'Inactive') ? 'secondary' : 'primary'; ?>
                    <span class="badge text-light-subtle bg-<?= $statusBg ?>"><?= $course[1] ?></span> 
                </div>
                
                <a class="badge bg-body text-primary-emphasis" href="<?= getFilterLink($filters, 'delivery', $course[21]) ?>">
                    <?= $course[21] ?>
                </a>
                <?php if (!empty($course[39])): ?>
                    <a class="badge bg-body text-primary-emphasis" href="<?= getFilterLink($filters, 'audience', $course[39]) ?>"><?= $course[39] ?></a>
                <?php endif; ?>
                <?php if (!empty($course[38])): ?>
                    <a class="badge bg-body text-primary-emphasis" href="<?= getFilterLink($filters, 'topic', $course[38]) ?>"><?= $course[38] ?></a>
                <?php endif; ?>
                <?php if ($course[53] == 'Yes' || $course[53] == 1): ?>
                    <span class="badge bg-body">Learning<strong>HUB</strong></span>
                <?php endif; ?>
            </div>
            
            <div class="name" style="font-size: 1.3em">
                <a href="/lsapp/course.php?courseid=<?= $course[0] ?>"><?= h($course[2]) ?></a>
            </div>
            
            <div class="mb-3">
                <?php if (!empty($course[4])): ?>
                    <a class="badge bg-light-subtle text-primary-emphasis" 
                       title="Find course in ELM by ITEM-code" 
                       target="_blank" 
                       href="https://learning.gov.bc.ca/psc/CHIPSPLM_6/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_FND_LRN_FL.GBL?Page=LM_FND_LRN_RSLT_FL&Action=U&KWRD=%22<?= h($course[4]) ?>%22">
                       <?= $course[4] ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($course[50])): ?>
                    <a class="badge bg-light-subtle text-primary-emphasis" 
                       title="Edit course in ELM" 
                       target="_blank" 
                       href="https://learning.gov.bc.ca/psp/CHIPSPLM/EMPLOYEE/ELM/c/LM_COURSESTRUCTURE.LM_CI_LA_CMP.GBL?LM_CI_ID=<?= h($course[50]) ?>">
                       <?= $course[50] ?>
                    </a>
                <?php endif; ?>
            </div>
            
            <div class="row bg-light-subtle">
                <div class="col-md-6">
                    <div class="p-2">
                        Platform: 
                        <?php if (!empty($course[52])): ?>
                            <?php 
                            $platformId = strtolower(str_replace(' ', '-', preg_replace('/[^a-z0-9\s-]/i', '', $course[52])));
                            ?>
                            <a href="/lsapp/platform.php?id=<?= urlencode($platformId) ?>"><?= h($course[52]) ?></a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-2">
                        Corp. Partner: 
                        <?php if (!empty($course[36])): ?>
                            <?php 
                            $partnerSlug = strtolower(preg_replace('/[^a-z0-9\s-]/i', '', str_replace(' ', '-', $course[36])));
                            ?>
                            <a href="partners/view.php?slug=<?= $partnerSlug ?>"><?= h($course[36]) ?></a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>

</div>
</div>
</div>

<?php require('templates/javascript.php') ?>

<script>
var options = {
    valueNames: ['name', 'delivery', 'category']
};
var courses = new List('courses', options);
</script>

<?php require('templates/footer.php') ?>