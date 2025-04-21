<?php 
opcache_reset();
$path = 'inc/lsapp.php';
require($path); 

$jsonPath = __DIR__ . '/data/open-access-code.json';
$data = json_decode(file_get_contents($jsonPath), true);

$currentCode = $data[0]['code'] ?? 'N/A';
$currentCreated = $data[0]['created'] ?? 'Unknown';
$history = $data[1]['history'] ?? [];

$allCourses = array_filter(getCourses(), function($c) {
    return !empty($c[57]) && (strtolower($c[57]) === 'true' || strtolower($c[57]) === 'on');
});
usort($allCourses, fn($a, $b) => strcmp($a[2], $b[2]));
$sortedCourses = $allCourses;
?>

<?php if(canACcess()): ?>

<?php getHeader() ?>

<title>PSALS Course Catalog Feed Generator</title>

<?php getScripts() ?>
<body>
<?php getNavigation() ?>

<div class="container">
<div class="row justify-content-md-center">
<div class="col-md-12">
    <h1>Manage Open Access Code</h1>
</div>
<div class="col-md-6">

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Current Access Code</h5>
            <p class="display-2"><?= htmlspecialchars($currentCode) ?></p>
            <p><strong>Created:</strong> <?= htmlspecialchars($currentCreated) ?></p>
            <p>This is a global code that is used by all Open Access courses. Every URL that we provide
                to our partners has this code embedded in it, and that link is what they distribute to their
                learners.</p>
            <dl>
                <dt>An example link:</dt>
                <dd>https://learn.bcpublicservice.gov.bc.ca/openaccess/chngmgmtf.php?accesscode=98mgfvbrhx</dd>
            </dl>
            <div class="alert alert-danger mt-3">
                <strong>Warning:</strong> Regenerating the code will cut off access to <strong>all Open Access courses</strong>. 
                Only do this if you know what you're doing and can communicate the change back to our partners.
            </div>
            <form class="mb-3" method="post" action="open-access-rotate-code.php">
                <button type="submit" 
                        class="btn btn-danger" 
                        onclick="return confirm('Are you sure you want to re-generate this code? It will cut access off to ALL OPEN ACCESS COURSES! Only do this if you know what you\'re doing and can communicate the change back to our partners.')">
                            Invalidate and Generate New Code
                </button>
            </form>
            <p class="mt-3">In order for a new code to take effect, the synchronization process needs 
                to happen. <a href="/lsapp/course-feed/">Sync now</a>, or wait until the automated
                sync happens at 7am, 12pm, and 4pm daily.</p>
        </div>
    </div>
    <?php if (!empty($history)): ?>
        <hr>
        <h5>Archived Codes</h5>
        <ul class="list-group">
            <?php foreach (array_reverse($history) as $entry): ?>
                <li class="list-group-item">
                    <strong>Code:</strong> <?= htmlspecialchars($entry['code']) ?><br>
                    <strong>Created:</strong> <?= htmlspecialchars($entry['created']) ?><br>
                    <strong>By:</strong> <?= htmlspecialchars($entry['createdby']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
<div class="col-md-6" id="openaccesscourses">
    <h2>Open Access Courses</h2>
    <input class="search form-control mb-3" placeholder="Search courses" />
    <ul class="list-group list" id="courseList">
        <?php foreach ($sortedCourses as $course): ?>
            <li class="list-group-item">
                <span class="coursename">
                    <a href="/lsapp/course.php?courseid=<?= urlencode($course[0]) ?>">
                        <?= htmlspecialchars($course[2]) ?>
                    </a>
                    (<a href="https://learn.bcpublicservice.gov.bc.ca/openaccess/<?= str_replace(' ', '-', strtolower($course[3])) ?>.php?accesscode=<?= h($currentCode) ?>" 
                        target="_blank">OpenAccess</a>)
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
</div>
</div>

<?php endif ?>

<?php require('templates/javascript.php') ?>
<script src="/lsapp/js/list.min.js"></script>
<script>
    new List('openaccesscourses', {
        valueNames: ['coursename']
    });
</script>
<?php require('templates/footer.php') ?>