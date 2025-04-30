<?php opcache_reset() ?>
<?php require('../inc/lsapp.php') ?>

<?php if(canACcess()): ?>

<?php getHeader() ?>

<title>Manage Learning Partner</title>

<?php getScripts() ?>

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
</head>
<body>
<?php getNavigation() ?>


<div class="container-lg p-lg-5 p-4 bg-light-subtle">
    <div class="row">
       
        <div class="col-md-6">
            <h2>Partner Requests</h2>
            <p>New partnership requests.</p>
<?php
$pendingPartners = array_filter($partnerData, function($partner) {
    return !in_array(strtolower($partner['status']), ['active', 'inactive']);
});
if (!empty($pendingPartners)):
?>
<ul class="list-group mb-4">
    <?php foreach ($pendingPartners as $partner): ?>
        <li class="list-group-item">
            <a href="partners/view.php?slug=<?php echo urlencode($partner['slug']); ?>"><?= htmlspecialchars($partner['name']) ?></a>
            <!-- <span class="badge bg-secondary"><?= htmlspecialchars($partner['status']) ?></span> -->
        </li>
    <?php endforeach; ?>
</ul>
<?php else: ?>
<p class="text-muted">No pending partner records at this time.</p>
<?php endif; ?>
</div>
<div class="col-md-6">
            <h2>Partner Contacts</h2>
            <p>Partner contacts to be added to an existing partner.</p>
<?php
$contactRequestsFile = '../data/partner_contact_requests.json';
if (file_exists($contactRequestsFile)):
    $contactRequests = json_decode(file_get_contents($contactRequestsFile), true);
    if (!empty($contactRequests)):
?>
    <ul class="list-group mb-4">
        <?php foreach ($contactRequests as $request): ?>
            <li class="list-group-item">
                <strong><?= htmlspecialchars($request['name']) ?></strong> (<?= htmlspecialchars($request['idir']) ?>) 
                requested to be added as <strong><?= htmlspecialchars($request['role']) ?></strong> 
                to <strong><?= htmlspecialchars($request['partner_name']) ?></strong>.
                <br>
                <small class="text-muted"><?= date('F j, Y, g:i a', strtotime($request['timestamp'])) ?></small>
                <form action="contact-approve.php" method="POST" class="mt-2">
                    <?php foreach (['partner_slug', 'partner_name', 'name', 'email', 'idir', 'title', 'role'] as $field): ?>
                        <input type="hidden" name="<?= $field ?>" value="<?= htmlspecialchars($request[$field]) ?>">
                    <?php endforeach; ?>
                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
<?php
    else:
        echo "<p class='text-muted'>No contact requests at this time.</p>";
    endif;
else:
    echo "<p class='text-muted'>No contact requests found.</p>";
endif;
?>
</div>
<div class="col-m-12">
            <h2>Course Requests</h2>
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
            <td><a href="../course.php?courseid=<?php echo urlencode($course[0]); ?>"><?php echo htmlspecialchars($course[2]); ?></a></td>
            <td><a href="view.php?slug=<?php echo urlencode($partnerMap[$course[36]]); ?>"><?php echo htmlspecialchars($course[36]); ?></a></td>
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

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>

<?php endif ?>