<?php
opcache_reset();
$path = '../inc/lsapp.php';
require($path); 
?>

<?php if(isAdmin()): ?>

<?php getHeader() ?>

<title>Partner Course Manager</title>
   
<?php getScripts() ?>

<body>

<?php getNavigation() ?>

<?php 
$courses = getCourses();
$filteredCourses = array_filter($courses, function($course) {
    return $course[1] === 'Draft' || $course[1] === 'Requested';
});

// Load partner data
$partnerData = json_decode(file_get_contents('../data/partners.json'), true);
$partnerMap = [];
foreach ($partnerData as $partner) {
    $partnerMap[$partner['name']] = $partner['slug'];
}
?>
<div class="container">
<div class="row">
<div class="col">
<h1>New Course Requests</h1>
<p>Courses submitted by learning partners for review and publishing on the 
    LearningHUB.</p>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Course Name</th>
            <th>Learning Partner</th>
            <th>Status</th>
            <th>Requested By</th>
            <th>Created Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($filteredCourses as $course): ?>
        <tr>
            <td><a href="/lsapp/course.php?courseid=<?php echo urlencode($course[0]); ?>"><?php echo htmlspecialchars($course[2]); ?></a></td>
            <td><a href="/lsapp/partners/view.php?slug=<?php echo urlencode($partnerMap[$course[36]]); ?>"><?php echo htmlspecialchars($course[36]); ?></a></td>
            <td><?php echo htmlspecialchars($course[1]); ?></td>
            <td><?php echo htmlspecialchars($course[14]); ?></td>
            <td><?php echo htmlspecialchars($course[15]); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>
</div>
</div>
<?php endif ?>

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>
