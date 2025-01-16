<?php
opcache_reset();
$path = '../inc/lsapp.php';
require($path);

// Directory containing the JSON files
$directory = "requests";

// Get all JSON files
$files = glob("{$directory}/*.json");

// Initialize an array to store request data
$changeRequests = [];

foreach ($files as $file) {
    $changeData = json_decode(file_get_contents($file), true);

    if ($changeData) {
        $changeRequests[] = [
            'changeid' => $changeData['changeid'],
            'courseid' => $changeData['courseid'],
            'assign_to' => $changeData['assign_to'],
            'status' => $changeData['status'],
            'urgent' => $changeData['urgent'] ? 'Yes' : 'No',
            'date_created' => date('Y-m-d H:i:s', $changeData['date_created']),
            'date_modified' => date('Y-m-d H:i:s', $changeData['date_modified']),
        ];
    }
}
?>

<?php if(canACcess()): ?>

<?php getHeader() ?>

<title>Change Request Dashboard</title>

<?php getScripts() ?>
</body>
<?php getNavigation() ?>

<div class="container">
    <div class="row justify-content-md-center">
        <div class="col">
        <h1>Change Requests Dashboard</h1>
        <div class="mb-4">Incomplete change requests</div>
        <?php if (!empty($changeRequests)): ?>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Change ID</th>
                        <th>Course</th>
                        <th>Assigned To</th>
                        <th>Status</th>
                        <th>Urgent</th>
                        <th>Date Created</th>
                        <th>Date Modified</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($changeRequests as $request): ?>
                        <?php if($request['status'] !== 'completed'): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($request['changeid']); ?></td>
                            <td>
                                <?php $deets = getCourse($request['courseid']) ?>
                                <a href="/lsapp/course.php?courseid=<?php echo htmlspecialchars($request['courseid']); ?>">
                                    <?php echo $deets[2] ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($request['assign_to']); ?></td>
                            <td><?php echo htmlspecialchars($request['status']); ?></td>
                            <td><?php echo htmlspecialchars($request['urgent']); ?></td>
                            <td><?php echo htmlspecialchars($request['date_created']); ?></td>
                            <td><?php echo htmlspecialchars($request['date_modified']); ?></td>
                            <td>
                                <a href="view.php?courseid=<?php echo htmlspecialchars($request['courseid']); ?>&changeid=<?php echo htmlspecialchars($request['changeid']); ?>" class="btn btn-primary btn-sm">View</a>
                            </td>
                        </tr>
                        <?php endif ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning">No change requests found.</div>
        <?php endif; ?>
    </div>


    <?php endif ?>

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>