<?php 
opcache_reset();
$path = '../inc/lsapp.php';
require($path); 
?>

<?php if(canACcess()): ?>

<?php getHeader() ?>

<title>PSALS Course Catalog Feed Generator</title>

<?php getScripts() ?>
</body>
<?php getNavigation() ?>

<div class="container-fluid">
<div class="row justify-content-md-center">
<div class="col-md-6 col-xl-4">

        <h1 class="text-center mb-4">Course Change Request Form</h1>

        <?php
        // Get parameters from the URL
        $courseid = isset($_GET['courseid']) ? htmlspecialchars($_GET['courseid']) : null;
        $changeid = isset($_GET['changeid']) ? htmlspecialchars($_GET['changeid']) : null;

        if (!$courseid) {
            echo '<div class="alert alert-danger">Error: Course ID is required.</div>';
            exit;
        }

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
            $filePath = "requests/$changeid.json";
            if (file_exists($filePath)) {
                $formData = json_decode(file_get_contents($filePath), true);
            } else {
                echo '<div class="alert alert-warning">Warning: Change ID not found. Starting a new form.</div>';
            }
        }
        ?>

        <form action="addupdate.php" method="post" class="needs-validation" novalidate>
            <!-- Hidden Fields -->
            <input type="hidden" name="courseid" value="<?php echo $courseid; ?>">
            <input type="hidden" name="changeid" value="<?php echo $changeid; ?>">

            <!-- Assign To -->
            <div class="mb-3">
                <label for="assign_to" class="form-label">Assign To</label>
                <input type="text" id="assign_to" name="assign_to" class="form-control" value="<?php echo $formData['assign_to']; ?>" required>
                <div class="invalid-feedback">Please provide the assignee.</div>
            </div>

            <!-- CRM Ticket Reference -->
            <div class="mb-3">
                <label for="crm_ticket_reference" class="form-label">CRM Ticket Reference #</label>
                <input type="text" id="crm_ticket_reference" name="crm_ticket_reference" class="form-control" value="<?php echo $formData['crm_ticket_reference']; ?>">
            </div>

            <!-- Category -->
            <div class="mb-3">
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

            <!-- Description -->
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4" required><?php echo $formData['description']; ?></textarea>
                <div class="invalid-feedback">Please provide a description of the request.</div>
            </div>

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

            <!-- Approval Status -->
            <div class="mb-3">
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

            <!-- Urgency -->
            <div class="mb-3 form-check">
                <input type="checkbox" id="urgent" name="urgent" class="form-check-input" value="yes" <?php echo $formData['urgent'] ? 'checked' : ''; ?>>
                <label for="urgent" class="form-check-label">Urgent</label>
            </div>

            <!-- Comments -->
            <div class="mb-3">
                <label for="comments" class="form-label">Comments</label>
                <textarea id="comments" name="comments" class="form-control" rows="4"><?php echo $formData['comments']; ?></textarea>
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select" required>
                    <option value="" disabled>Choose a status</option>
                    <option value="not_started" <?php echo $formData['status'] === 'not_started' ? 'selected' : ''; ?>>Not Started</option>
                    <option value="in_progress" <?php echo $formData['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $formData['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
                <div class="invalid-feedback">Please select the status.</div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100"><?php echo $changeid ? 'Update' : 'Submit'; ?></button>
        </form>



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