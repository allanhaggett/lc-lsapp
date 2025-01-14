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
    $filePath = "requests/course-$courseid-change-$changeid.json";
    if (file_exists($filePath)) {
        $formData = json_decode(file_get_contents($filePath), true);
    } else {
        echo '<div class="alert alert-warning">Warning: Change ID not found. Starting a new form.</div>';
        echo $filePath;
    }
}

// Load categories from the JSON file
$categoriesFile = 'guidance.json';
$categories = [];

if (file_exists($categoriesFile)) {
    $categories = json_decode(file_get_contents($categoriesFile), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Error reading categories.json: " . json_last_error_msg());
    }
}
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
            <h2>Course Change Request <small><?= $formData['changeid'] ?? '' ?></small></h2>
            <?php if(!empty($formData['date_created'])): ?>
            <div>
                Created <?php echo date('Y-m-d H:i:s', $formData['date_created']); ?> 
                by <?= $formData['created_by'] ?? '' ?>
            </div>
            <?php endif ?>
        </div>
    </div>
    
    <div class="row justify-content-md-center">
        <div class="col-md-6">
        
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
            <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select id="category" name="category" class="form-select" required>
                <option value="" disabled>Choose a category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['category']); ?>"
                        <?php echo (isset($formData['category']) && $formData['category'] === $cat['category']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category']); ?>
                    </option>
                <?php endforeach; ?>
                <!-- <option value="open_course" <?php echo $formData['category'] === 'open_course' ? 'selected' : ''; ?>>Open Course</option>
                <option value="close_course" <?php echo $formData['category'] === 'close_course' ? 'selected' : ''; ?>>Close Course</option>
                <option value="course_update" <?php echo $formData['category'] === 'course_update' ? 'selected' : ''; ?>>Course Update</option>
                <option value="other" <?php echo $formData['category'] === 'other' ? 'selected' : ''; ?>>Other</option> -->
            </select>
            <div class="invalid-feedback">Please select a category.</div>
            <div id="category-guidance" class="mt-3 text-muted">
                <p>Select a category to view its guidance.</p>
            </div>
            <script>
                const categories = <?php echo json_encode($categories); ?>;
                const categoryDropdown = document.getElementById('category');
                const guidanceDiv = document.getElementById('category-guidance');
                categoryDropdown.addEventListener('change', function () {
                    const selectedCategory = this.value;
                    const selected = categories.find(cat => cat.category === selectedCategory);
                    if (selected) {
                        guidanceDiv.innerHTML = `<p>${selected.guidance}</p>`;
                    } else {
                        guidanceDiv.innerHTML = `<p>Select a category to view its guidance.</p>`;
                    }
                });
            </script>
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
                <!-- Scope -->

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

            
            <!-- Add New Files -->
            <div class="mb-3">
                <label for="uploaded_files" class="form-label">Upload Files</label>
                <input type="file" id="uploaded_files" name="uploaded_files[]" class="form-control" multiple>
                <small class="text-muted">You can upload multiple files. Max size: 20MB each.</small>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100"><?php echo $changeid ? 'Update' : 'Submit'; ?></button>
        
        
            </div>
            <div class="col-md-6">

            <!-- Add New Comment -->
            <div class="mb-3">
                <label for="new_comment" class="form-label">Add New Comment</label>
                <textarea id="new_comment" name="new_comment" class="form-control" rows="3"></textarea>
                <button type="submit" class="btn btn-primary w-100">Add Comment</button>
            </div>

            
        </form>
        <!-- Existing Files -->
        <div class="mb-3">
                <label for="existing_files" class="form-label">Files</label>
                <ul class="list-group">
                    <?php if (!empty($formData['files'])): ?>
                        <?php foreach ($formData['files'] as $file): ?>
                            <?php
                            // Extract the file name without the ID part
                            $shortFileName = preg_replace("/^course-[a-zA-Z0-9\-]+-change-[a-z0-9]+-/", '', $file);
                            ?>
                            <li class="list-group-item">
                                <form action="delete-file.php" method="post" class="float-end">
                                    <input type="hidden" name="courseid" value="<?php echo htmlspecialchars($formData['courseid']); ?>">
                                    <input type="hidden" name="changeid" value="<?php echo htmlspecialchars($formData['changeid']); ?>">
                                    <input type="hidden" name="file" value="<?php echo htmlspecialchars($file); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">x</button>
                                </form>
                                <a href="requests/files/<?php echo $file; ?>" target="_blank"><?php echo $shortFileName; ?></a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item">No files uploaded yet.</li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php if(!empty($formData['timeline'])): ?>
        <div class="mt-4">
    <h2>Comments</h2>
    <?php
    // Ensure the timeline is sorted in reverse chronological order
    if (!empty($formData['timeline'])) {
        usort($formData['timeline'], function ($a, $b) {
            return $b['changed_at'] <=> $a['changed_at']; // Sort by changed_at in descending order
        });
    }
    ?>
    <ul class="list-group">
    <?php foreach ($formData['timeline'] as $event): ?>
        <?php if ($event['field'] === 'comment'): ?>
            <li class="list-group-item">
                <?php if ($event['changed_by'] === LOGGED_IN_IDIR): ?>
                    <form action="delete-comment.php" method="post" class="float-end">
                        <input type="hidden" name="courseid" value="<?php echo htmlspecialchars($formData['courseid']); ?>">
                        <input type="hidden" name="changeid" value="<?php echo htmlspecialchars($formData['changeid']); ?>">
                        <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($event['comment_id'] ?? ''); ?>">
                        <button type="submit" class="btn btn-danger btn-sm">x</button>
                    </form>
                <?php endif; ?>
                <p><strong>Comment:</strong> <?php echo htmlspecialchars($event['new_value']); ?></p>
                <strong>Commented By:</strong> <?php echo htmlspecialchars($event['changed_by']); ?><br>
                <small class="text-muted">At: <?php echo date('Y-m-d H:i:s', $event['changed_at']); ?></small>
            </li>
            <?php endif; ?>
    <?php endforeach; ?>
    </ul>
</div>
<?php endif; // $formData['timeline'] check ?>
        
</div>
</div>
<hr class="mt-5">
<div class="row">
<div class="col">
<h2 class="mt-5">Timeline</h2>
<table class="table table-striped">
    <tr>
        <th>Field Changed</th> 
        <th width="400">Previous Value</th> 
        <th width="400">New Value</th> 
        <th>Changed By</th> 
        <th>When</th>
    </tr>
<?php foreach ($formData['timeline'] as $event): ?>
<?php if ($event['field'] !== 'comment'): ?>
    <tr>
        <!-- <form action="delete-comment.php" method="post" class="float-end">
            <input type="hidden" name="courseid" value="<?php echo htmlspecialchars($formData['courseid']); ?>">
            <input type="hidden" name="changeid" value="<?php echo htmlspecialchars($formData['changeid']); ?>">
            <input type="hidden" name="comment_id" value="<?php echo htmlspecialchars($event['comment_id'] ?? ''); ?>">
            <button type="submit" class="btn btn-danger btn-sm">x</button>
        </form> -->
        <td><?php echo htmlspecialchars($event['field']); ?></td>
        <td><?php echo htmlspecialchars($event['previous_value'] ?? 'N/A'); ?></td>
        <td><?php echo htmlspecialchars($event['new_value'] ?? ''); ?></td>
        <td><?php echo htmlspecialchars($event['changed_by'] ?? ''); ?></td>
        <td><?php echo date('Y-m-d H:i:s', $event['changed_at'] ?? ''); ?></td>

    </tr>
<?php endif; // not a comment ?>
<?php endforeach; ?>
</table>

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