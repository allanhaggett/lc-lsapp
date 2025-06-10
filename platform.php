<?php require('inc/lsapp.php') ?>
<?php 
$platformId = (isset($_GET['id'])) ? $_GET['id'] : '';

// Load platforms data
$jsonContent = file_get_contents('data/platforms.json');
$platforms = json_decode($jsonContent, true);

// Find the specific platform
$currentPlatform = null;
foreach ($platforms as $platform) {
    if ($platform['id'] === strtolower($platformId)) {
        $currentPlatform = $platform;
        break;
    }
}
?>

<?php getHeader() ?>
<title><?= $currentPlatform ? htmlspecialchars($currentPlatform['name']) : 'Platform Not Found' ?> - LSApp</title>
<?php getScripts() ?>
<body class="bg-light-subtle">
<?php getNavigation() ?>

<div class="container-fluid">
<div class="row justify-content-md-center mb-3">

<?php if($currentPlatform): ?>
<div class="col-md-6">

<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/lsapp/">Home</a></li>
        <li class="breadcrumb-item"><a href="/lsapp/platforms.php">Platforms</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($currentPlatform['name']) ?></li>
    </ol>
</nav>

<div class="card">
<div class="card-header">
    <div class="float-right">
        <?php if(isAdmin()): ?>
        <a href="platform-update.php?id=<?= urlencode($currentPlatform['id']) ?>" class="btn btn-secondary">Edit</a>
        <?php endif ?>
        <?php if(isSuper()): ?>
        <form method="post" action="platform-delete.php" style="display: inline;">
            <input type="hidden" name="id" value="<?= htmlspecialchars($currentPlatform['id']) ?>">
            <input type="submit" value="Delete" class="btn btn-sm btn-danger del">
        </form>
        <?php endif ?>
    </div>
    <h1 class="card-title"><?= htmlspecialchars($currentPlatform['name']) ?></h1>
</div>
<div class="card-body">
    <p class="lead"><?= htmlspecialchars($currentPlatform['description']) ?></p>
    
    <?php if (!empty($currentPlatform['link'])): ?>
    <div class="mb-4">
        <a href="<?= htmlspecialchars($currentPlatform['link']) ?>" target="_blank">
            Visit Platform Website →
        </a>
    </div>
    <?php endif; ?>
    
    <hr>
    
    <h3>Courses on this Platform</h3>
    
    <?php
    // Get courses that use this platform
    $coursesFile = fopen('data/courses.csv', 'r');
    $headers = fgetcsv($coursesFile);
    $platformIndex = array_search('Platform', $headers);
    $activeCourses = [];
    $inactiveCourses = [];
    
    while ($row = fgetcsv($coursesFile)) {
        if (isset($row[$platformIndex]) && $row[$platformIndex] === $currentPlatform['name']) {
            if ($row[1] === 'Active') {
                $activeCourses[] = $row;
            } else {
                $inactiveCourses[] = $row;
            }
        }
    }
    fclose($coursesFile);
    
    // Reverse arrays to show most recently added first (assuming they're added to the end of the CSV)
    $activeCourses = array_reverse($activeCourses);
    $inactiveCourses = array_reverse($inactiveCourses);
    
    $totalCourses = count($activeCourses) + count($inactiveCourses);
    
    if ($totalCourses > 0):
    ?>
    
    <div id="courselist">
        <input class="search form-control mb-3" placeholder="Search all courses...">
        
        <?php if (count($activeCourses) > 0): ?>
        <h4 class="mt-3" id="active-header">Active Courses <span class="badge badge-success" id="active-count"><?= count($activeCourses) ?></span></h4>
        <?php endif; ?>
        
        <?php if (count($inactiveCourses) > 0): ?>
        <h4 class="mt-3" id="inactive-header" style="display: none;">Inactive Courses <span class="badge badge-secondary" id="inactive-count"><?= count($inactiveCourses) ?></span></h4>
        <?php endif; ?>
        
        <ul class="list-group list">
            <?php 
            $activeIndex = 0;
            foreach ($activeCourses as $course): 
            ?>
            <li class="list-group-item course-item" data-status="active" data-course-type="active">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="name">
                        <a href="/lsapp/course.php?courseid=<?= urlencode($course[0]) ?>">
                            <?= htmlspecialchars($course[2]) ?>
                        </a>
                    </div>
                    <span class="badge badge-success">Active</span>
                </div>
            </li>
            <?php 
            $activeIndex++;
            endforeach; 
            
            $inactiveIndex = 0;
            foreach ($inactiveCourses as $course): 
            ?>
            <li class="list-group-item course-item <?= $inactiveIndex === 0 ? 'first-inactive' : '' ?>" data-status="inactive" data-course-type="inactive">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="name">
                        <a href="/lsapp/course.php?courseid=<?= urlencode($course[0]) ?>">
                            <?= htmlspecialchars($course[2]) ?>
                        </a>
                    </div>
                    <span class="badge badge-secondary">Inactive</span>
                </div>
            </li>
            <?php 
            $inactiveIndex++;
            endforeach; 
            ?>
        </ul>
    </div>
    
    <?php else: ?>
    <div class="alert alert-info">
        No courses are currently using this platform.
    </div>
    <?php endif; ?>
    
</div>
</div>

</div>

<?php else: ?>
<div class="col-md-6">
    <div class="card">
        <div class="card-body">
            <h2>Platform Not Found</h2>
            <p>The requested platform could not be found.</p>
            <a href="/lsapp/platforms.php" class="btn btn-primary">Back to Platforms</a>
        </div>
    </div>
</div>
<?php endif ?>

</div>
</div>

<?php require('templates/javascript.php') ?>

<script>
$(document).ready(function(){
    <?php if($totalCourses > 0): ?>
    var options = {
        valueNames: [ 'name' ]
    };
    var courseList = new List('courselist', options);
    
    // Store original counts
    var totalActive = <?= count($activeCourses) ?>;
    var totalInactive = <?= count($inactiveCourses) ?>;
    
    // Function to manage display based on search
    function updateDisplay() {
        var searchValue = $('.search').val();
        var visibleActive = 0;
        var visibleInactive = 0;
        var hasVisibleInactive = false;
        
        // Count visible courses
        $('.course-item').each(function() {
            if ($(this).is(':visible')) {
                if ($(this).data('course-type') === 'active') {
                    visibleActive++;
                } else if ($(this).data('course-type') === 'inactive') {
                    visibleInactive++;
                    hasVisibleInactive = true;
                }
            }
        });
        
        // Update counts
        $('#active-count').text(visibleActive);
        $('#inactive-count').text(visibleInactive);
        
        // Show/hide headers based on visible items
        if (visibleActive === 0 && searchValue !== '') {
            $('#active-header').hide();
        } else if (totalActive > 0) {
            $('#active-header').show();
        }
        
        if (visibleInactive === 0 && searchValue !== '') {
            $('#inactive-header').hide();
        } else if (totalInactive > 0 && hasVisibleInactive) {
            $('#inactive-header').show();
            // Position it before the first inactive course
            if ($('.first-inactive:visible').length > 0) {
                $('#inactive-header').insertBefore('.first-inactive:visible');
            }
        } else {
            $('#inactive-header').hide();
        }
        
        // Add spacing if we have visible inactive courses
        if (hasVisibleInactive && visibleActive > 0) {
            $('.first-inactive').addClass('mt-4');
        } else {
            $('.first-inactive').removeClass('mt-4');
        }
    }
    
    // Initial positioning of inactive header
    if ($('.first-inactive').length > 0) {
        $('#inactive-header').insertBefore('.first-inactive');
        $('#inactive-header').show();
        $('.first-inactive').addClass('mt-4');
    }
    
    // Update on search events
    courseList.on('searchComplete', updateDisplay);
    courseList.on('filterComplete', updateDisplay);
    
    // Clear search handling
    $('.search').on('keyup', function() {
        setTimeout(updateDisplay, 10);
    });
    
    <?php endif; ?>
});
</script>

<?php require('templates/footer.php') ?>