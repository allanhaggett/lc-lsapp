<?php 
opcache_reset();
$path = '../inc/lsapp.php';
require($path); 
 // Get parameters from the URL
 $courseid = isset($_GET['courseid']) ? htmlspecialchars($_GET['courseid']) : null;
 $changeid = isset($_GET['changeid']) ? htmlspecialchars($_GET['changeid']) : null;

 if (!$courseid) {
     echo '<div class="alert alert-danger">Error: Course ID is required.</div>';
     exit;
 }
 $deets = getCourse($courseid);
?>

<?php if(canACcess()): ?>

<?php getHeader() ?>

<title><?= $deets[2] ?> Change Request</title>

<?php getScripts() ?>
</body>
<?php getNavigation() ?>

<div class="container">
    <div class="row justify-content-md-center">
        <div class="col">
            <h1 class=""><a href="/lsapp/course.php?courseid=<?= $deets[0] ?>"><?= $deets[2] ?></a></h1>
            <h2>Course Change Request</h2>
        </div>
    </div>
    
    <!-- <div><a href="change.php?courseid=<?= $courseid ?>&changeid=<?= $changeid ?>" class="btn btn-sm btn-secondary mt-2">View</a></div> -->
        <a class="btn btn-primary" data-bs-toggle="collapse" href="#otherchanges" role="button" aria-expanded="false" aria-controls="otherchanges">
            Other Changes
        </a>
        <div id="otherchanges" class="collapse">
        <?php
        // Fetch all matching request files for the course ID
        $files = glob("requests/course-{$courseid}-*.json");
        if (empty($files)) {
            echo '<p>No requests found for this course.</p>';
        } else {
            echo '<ul class="list-group mb-4">';
            foreach ($files as $file) {
                $request = json_decode(file_get_contents($file), true);
                $filenameParts = explode('-', basename($file, '.json')); // Parse file name
                $chid = $filenameParts[2]; // Extract change ID (second part of the name)
                echo '<li class="list-group-item">';
                echo "<strong>Request ID:</strong> {$changeid}<br>";
                echo "<strong>Assigned To:</strong> {$request['assign_to']}<br>";
                echo "<strong>Status:</strong> {$request['status']}<br>";
                echo "<strong>Last Assigned:</strong> " . date('Y-m-d H:i:s', $request['last_assigned_at'] ?? time()) . "<br>";
                echo "<strong>Description:</strong> {$request['description']}<br>";
                echo "<a href='?courseid={$courseid}&changeid={$chid}' class='btn btn-sm btn-primary mt-2'>Edit</a>";
                echo '</li>';
            }
            echo '</ul>';
        }
        ?>

        </div>


        <div class="row justify-content-md-center">
        <div class="col-md-5">
        <?php
       

        // Prefill data if updating an existing change
        $formData = [
            'assign_to' => '',
            'crm_ticket_reference' => '',
            'category' => '',
            'description' => '',
            'scope' => '',
            'approval_status' => '',
            'urgent' => false,
            'comments' => '',
            'status' => ''
        ];

        if ($changeid) {
            $filePath = "requests/course-$courseid-$changeid.json";
            if (file_exists($filePath)) {
                $formData = json_decode(file_get_contents($filePath), true);
            } else {
                echo '<div class="alert alert-warning">Warning: Change ID not found. Starting a new form.</div>';
            }
        }
        ?>
        
        <form action="controller.php" method="post" enctype="multipart/form-data" class="needs-validation mt-3" novalidate>

            <!-- Hidden Fields -->
            <input type="hidden" name="courseid" value="<?php echo $courseid; ?>">
            <input type="hidden" name="changeid" value="<?php echo $changeid; ?>">
            <div class="alert alert-secondary mb-3">
            <div class="form-check">
                <input type="checkbox" id="urgent" name="urgent" class="form-check-input" value="yes" <?php echo $formData['urgent'] ? 'checked' : ''; ?>>
                <label for="urgent" class="form-check-label">Urgent</label>
            </div>
            </div>
            <div class="row">
            <div class="col">
            <!-- Scope -->
            <div class="mb-3">
                <label for="scope" class="form-label">Scope</label>
                <select id="scope" name="scope" class="form-select" required>
                    <option value="" disabled>Choose a scope</option>
                    <option value="minor" <?php echo $formData['scope'] === 'minor' ? 'selected' : ''; ?>>Minor Change (1-2 hours)</option>
                    <option value="moderate" <?php echo $formData['scope'] === 'moderate' ? 'selected' : ''; ?>>Moderate Change (2-24 hours)</option>
                    <option value="major" <?php echo $formData['scope'] === 'major' ? 'selected' : ''; ?>>Major Change (&gt;24 hours)</option>
                </select>
                <div class="invalid-feedback">Please select the scope of the request.</div>
            </div>
            </div>
            <!-- Approval Status -->
            <div class="col">
                <label for="approval_status" class="form-label">Approval Status</label>
                <select id="approval_status" name="approval_status" class="form-select" required>
                    <option value="" disabled>Choose approval status</option>
                    <option value="approved" <?php echo $formData['approval_status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="pending" <?php echo $formData['approval_status'] === 'pending' ? 'selected' : ''; ?>>Pending Approval</option>
                    <option value="denied" <?php echo $formData['approval_status'] === 'denied' ? 'selected' : ''; ?>>Denied</option>
                    <option value="on_hold" <?php echo $formData['approval_status'] === 'on_hold' ? 'selected' : ''; ?>>On Hold</option>
                </select>
                <div class="invalid-feedback">Please select the approval status.</div>
            </div>
            </div>
            <div class="row mb-3">
            <!-- Status -->
            <div class="col">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select" required>
                    <option value="" disabled>Choose a status</option>
                    <option value="not_started" <?php echo $formData['status'] === 'not_started' ? 'selected' : ''; ?>>Not Started</option>
                    <option value="in_progress" <?php echo $formData['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $formData['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
                <div class="invalid-feedback">Please select the status.</div>
            </div>

            <div class="col">
                <label for="category" class="form-label">Category</label>
                <select id="category" name="category" class="form-select" required>
                    <option value="" disabled>Choose a category</option>
                    <option value="open_course" <?php echo $formData['category'] === 'open_course' ? 'selected' : ''; ?>>Open Course</option>
                    <option value="close_course" <?php echo $formData['category'] === 'close_course' ? 'selected' : ''; ?>>Close Course</option>
                    <option value="course_update" <?php echo $formData['category'] === 'course_update' ? 'selected' : ''; ?>>Course Update</option>
                    <option value="other" <?php echo $formData['category'] === 'other' ? 'selected' : ''; ?>>Other</option>
                </select>
                <div class="invalid-feedback">Please select a category.</div>
            </div>
            </div>

            <div class="row mb-3">
            <div class="col">
                <label for="assign_to" class="form-label">Assigned To</label>
                <!-- <input type="text" id="assign_to" name="assign_to" class="form-control" value="<?php echo $formData['assign_to']; ?>" required> -->
                <div class="invalid-feedback">Please provide the assignee.</div>
                <input list="people" 
                        name="assign_to" 
                        id="assign_to" 
                        class="form-control" 
                        placeholder="Select a person"
                        value="<?php echo htmlspecialchars($formData['assign_to'] ?? ''); ?>"
                >
                <datalist id="people">
                    <?php
                    // Generate the datalist options
                    foreach ($people as $person) {
                        // Ensure the array has at least 3 elements for IDIR and Name
                        if (!empty($person[0]) && !empty($person[2])) {
                            $value = htmlspecialchars($person[0]); // Use IDIR (index 0) as the value
                            $label = htmlspecialchars($person[2]); // Use Name (index 2) as the displayed label
                            echo "<option value=\"{$value}\" label=\"{$label}\"></option>";
                        }
                    }
                    ?>
                </datalist>
                <?php if(!empty($formData['last_assigned_at'])): ?>
                Assigned on <?php echo date('Y-m-d H:i:s', $formData['last_assigned_at']); ?>
                <?php endif ?>
            </div>
            <!-- CRM Ticket Reference -->
            <div class="col">
                <label for="crm_ticket_reference" class="form-label">CRM Ticket #</label>
                <input type="text" id="crm_ticket_reference" name="crm_ticket_reference" class="form-control" value="<?php echo $formData['crm_ticket_reference']; ?>">
            </div>
            </div>
            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4" required><?php echo $formData['description']; ?></textarea>
                <div class="invalid-feedback">Please provide a description of the request.</div>
            </div>
            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100"><?php echo $changeid ? 'Update' : 'Submit'; ?></button>
            </div>
            <div class="col-md-3">
            <!-- Existing Files -->
            <div class="mb-3">
                <label for="existing_files" class="form-label">Files</label>
                <ul class="list-group">
                    <?php if (!empty($formData['files'])): ?>
                        <?php foreach ($formData['files'] as $file): ?>
                            <?php
                            // Extract the file name without the ID part
                            $shortFileName = preg_replace("/^course-\d+-change-[a-z0-9]+-/", '', $file);
                            ?>
                            <li class="list-group-item">
                                <a href="requests/files/<?php echo $file; ?>" target="_blank"><?php echo $shortFileName; ?></a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item">No files uploaded yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- Add New Files -->
            <div class="mb-3">
                <label for="uploaded_files" class="form-label">Upload Files</label>
                <input type="file" id="uploaded_files" name="uploaded_files[]" class="form-control" multiple>
                <small class="text-muted">You can upload multiple files. Max size: 20MB each.</small>
            </div>


            </div>
            <div class="col-md-4">

            <!-- Add New Comment -->
            <div class="mb-3">
                <label for="new_comment" class="form-label">Add New Comment</label>
                <textarea id="new_comment" name="new_comment" class="form-control" rows="3"></textarea>
                <button type="submit" class="btn btn-primary w-100">Add Comment</button>
            </div>

            
        </form>
        <?php
// Merge comments, status history, and assignment history into a unified timeline
$timeline = [];

// Add comments to the timeline
if (!empty($formData['comments'])) {
    foreach ($formData['comments'] as $comment) {
        $timeline[] = [
            'type' => 'comment',
            'id' => $comment['id'], // Include the unique ID
            'commented_by' => $comment['commented_by'],
            'commented_at' => $comment['commented_at'],
            'comment' => $comment['comment'],
        ];
    }
}

// Add status history to the timeline
if (!empty($formData['status_history'])) {
    foreach ($formData['status_history'] as $status) {
        $timeline[] = [
            'type' => 'status',
            'id' => $status['id'], // Include the unique ID
            'previous_status' => $status['previous_status'],
            'new_status' => $status['new_status'],
            'changed_at' => $status['changed_at'],
        ];
    }
}

// Add assignment history to the timeline
if (!empty($formData['assign_to_history'])) {
    foreach ($formData['assign_to_history'] as $assignment) {
        $timeline[] = [
            'type' => 'assignment',
            'id' => $assignment['id'], // Include the unique ID
            'assigned_to' => $assignment['name'],
            'assigned_at' => $assignment['assigned_at'],
        ];
    }
}

// Sort the timeline by timestamp in reverse chronological order
usort($timeline, function ($a, $b) {
    $aTime = $a['commented_at'] ?? $a['changed_at'] ?? $a['assigned_at'] ?? 0;
    $bTime = $b['commented_at'] ?? $b['changed_at'] ?? $b['assigned_at'] ?? 0;
    return $aTime <=> $bTime;
});
?>

<div class="mt-4">
    <h2>Timeline</h2>
    <ul class="list-group">
        <?php foreach ($timeline as $event): ?>
            <li class="list-group-item">
                <?php if ($event['type'] === 'comment'): ?>
                    <strong>Commented By:</strong> <?php echo htmlspecialchars($event['commented_by'] ?? ''); ?><br>
                    <small class="text-muted"><?php echo date('Y-m-d H:i:s', $event['commented_at'] ?? ''); ?></small>
                    <p><?php echo htmlspecialchars($event['comment'] ?? ''); ?></p>
                    <?php if ($event['commented_by'] === LOGGED_IN_IDIR): ?>
                        <form action="delete-comment.php" method="post" class="mt-2">
                            <input type="hidden" name="courseid" value="<?php echo htmlspecialchars($courseid ?? ''); ?>">
                            <input type="hidden" name="changeid" value="<?php echo htmlspecialchars($changeid ?? ''); ?>">
                            <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($event['id'] ?? ''); ?>">
                            <button type="submit" class="btn btn-secondary btn-sm">Delete</button>
                        </form>
                    <?php endif; ?>

                <?php elseif ($event['type'] === 'status'): ?>
                    <strong>Status Changed:</strong><br>
                    <strong>From:</strong> <?php echo htmlspecialchars($event['previous_status']); ?><br>
                    <strong>To:</strong> <?php echo htmlspecialchars($event['new_status']); ?><br>
                    <small class="text-muted"><?php echo date('Y-m-d H:i:s', $event['changed_at']); ?></small>

                <?php elseif ($event['type'] === 'assignment'): ?>
                    <strong>Assigned To:</strong> <?php echo htmlspecialchars($event['assigned_to']); ?><br>
                    <small class="text-muted"><?php echo date('Y-m-d H:i:s', $event['assigned_at']); ?></small>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
    </div>
    </div>
    


    <!-- Form Validation -->
    <script>
        (function () {
            'use strict';
            const forms = document.querySelectorAll('.needs-validation');
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</div>
</div>
</div>

<?php endif ?>

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>