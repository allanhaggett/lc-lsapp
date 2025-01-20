<?php 
// Reset the OPcache
opcache_reset();

$path = '../inc/lsapp.php';
require($path); 
require('../inc/Parsedown.php');
$Parsedown = new Parsedown();

// Get parameters from the URL
$courseid = isset($_GET['courseid']) ? htmlspecialchars($_GET['courseid']) : null;
$changeid = isset($_GET['changeid']) ? htmlspecialchars($_GET['changeid']) : null;

if (!$courseid) {
    echo '<div class="alert alert-danger">Error: Course ID is required.</div>';
    exit;
}

$deets = getCourse($courseid);

// Default form data
$formData = [
    'assign_to' => '',
    'crm_ticket_reference' => '',
    'category' => '',
    'description' => '',
    'scope' => '',
    'approval_status' => '',
    'urgent' => false,
    'comments' => '',
    'status' => '',
    'last_assigned_at' => null,
    'date_created' => null,
    'created_by' => ''
];

// Prefill data if updating an existing change
if ($changeid) {
    $filePath = "requests/course-$courseid-change-$changeid.json";
    if (file_exists($filePath)) {
        $fileContent = file_get_contents($filePath);
        $formData = json_decode($fileContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo '<div class="alert alert-danger">Error: Unable to parse change request details.</div>';
        }
    } else {
        echo '<div class="alert alert-warning">Warning: Change ID not found. Starting a new form.</div>';
    }
}

function getGuidanceByCategory($cat, $categoriesFile) {
    // Check if the JSON file exists
    if (!file_exists($categoriesFile)) {
        return "Error: Categories file not found.";
    }

    // Decode the JSON file into an associative array
    $categories = json_decode(file_get_contents($categoriesFile), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return "Error decoding JSON: " . json_last_error_msg();
    }

    // Search for the category and return the guidance
    foreach ($categories as $category) {
        if (isset($category['category']) && $category['category'] === $cat) {
            return $category['guidance'] ?? "Guidance not found.";
        }
    }

    // Return a message if the category was not found
    return "Category not found.";
}

// Example usage
$cat = urldecode($formData['category']) ?? '';
$categoriesFile = 'guidance.json';
$guidance = getGuidanceByCategory($cat, $categoriesFile);

?>

<?php if(canACcess()): ?>

<?php getHeader() ?>

<title><?= htmlspecialchars($deets[2]) ?> Change Request</title>

<?php getScripts() ?>
</body>
<?php getNavigation() ?>

<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-md-12">
            <a href="view.php?courseid=<?= $courseid ?>&changeid=<?= $changeid ?>" class="btn btn-primary mb-4 float-end">View request</a>
            <form class="float-end" action="request-delete.php" method="post" onsubmit="return confirm('Are you sure you want to delete this request?');">
                <!-- Hidden Fields -->
                <input type="hidden" name="courseid" value="<?= htmlspecialchars($courseid) ?>">
                <input type="hidden" name="changeid" value="<?= htmlspecialchars($changeid) ?>">

                <!-- Delete Button -->
                <button type="submit" class="btn btn-danger">Delete Request</button>
            </form>
            <h1 class=""><a href="/lsapp/course.php?courseid=<?= htmlspecialchars($deets[0]) ?>"><?= htmlspecialchars($deets[2]) ?></a></h1>
            <h2><?= htmlspecialchars($formData['category']) ?> Request <small><?= htmlspecialchars($changeid ?? '') ?></small></h2>
            <?php if(!empty($formData['date_created'])): ?>
            <div>
                Created <?= date('Y-m-d H:i:s', $formData['date_created']) ?> 
                by <?= htmlspecialchars($formData['created_by'] ?? '') ?>
            </div>
            <?php endif ?>
        </div>
    </div>
    
    <div class="row justify-content-md-center">
    <div class="col-md-6">

        <form action="controller.php" method="post" enctype="multipart/form-data" class="needs-validation mt-3" novalidate>

            <!-- Hidden Fields -->
            <input type="hidden" name="courseid" value="<?= htmlspecialchars($courseid) ?>">
            <input type="hidden" name="changeid" value="<?= htmlspecialchars($changeid) ?>">
            <input type="hidden" name="category" value="<?= urlencode($formData['category']) ?>">

            <!-- Form Fields (Scope, Urgent, etc.) -->
            <div class="row">
                <div class="col">
                    <label for="scope" class="form-label visually-hidden">Scope</label>
                    <select id="scope" name="scope" class="form-select" required>
                        <option value="" disabled>Choose a scope</option>
                        <option value="minor" <?= $formData['scope'] === 'minor' ? 'selected' : '' ?>>Minor Change (1-2 hours)</option>
                        <option value="moderate" <?= $formData['scope'] === 'moderate' ? 'selected' : '' ?>>Moderate Change (2-24 hours)</option>
                        <option value="major" <?= $formData['scope'] === 'major' ? 'selected' : '' ?>>Major Change (&gt;24 hours)</option>
                    </select>
                    <div class="invalid-feedback">Please select the scope of the request.</div>
                </div>
                <div class="col align-self-end">
                    <div class="form-check">
                        <input type="checkbox" id="urgent" name="urgent" class="form-check-input" value="yes" <?= $formData['urgent'] ? 'checked' : '' ?>>
                        <label for="urgent" class="form-check-label">Urgent?</label>
                    </div>
                </div>
            </div>

            <div class="row my-3">
                <div class="col">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4" required><?= htmlspecialchars($formData['description']) ?></textarea>
                    <div class="invalid-feedback">Please provide a description of the request.</div>
                </div>
            </div>

            <div class="row my-3">
                <div class="col">
                    <label for="assign_to" class="form-label">Assigned To</label>
                    <input list="people" name="assign_to" id="assign_to" class="form-control" placeholder="Select a person" value="<?= htmlspecialchars($formData['assign_to'] ?? '') ?>">
                    <datalist id="people">
                        <?php
                        foreach ($people as $person) {
                            if (!empty($person[0]) && !empty($person[2])) {
                                $value = htmlspecialchars($person[0]);
                                $label = htmlspecialchars($person[2]);
                                echo "<option value=\"{$value}\" label=\"{$label}\"></option>";
                            }
                        }
                        ?>
                    </datalist>
                    <?php if (!empty($formData['last_assigned_at'])): ?>
                        Assigned on <?= date('Y-m-d H:i:s', $formData['last_assigned_at']) ?>
                    <?php endif ?>
                </div>

                <div class="col">
                    <label for="crm_ticket_reference" class="form-label">CRM Ticket #</label>
                    <input type="text" id="crm_ticket_reference" name="crm_ticket_reference" class="form-control" value="<?= htmlspecialchars($formData['crm_ticket_reference']) ?>">
                </div>
            </div>

            <div class="row my-3">
                <div class="col">
                    <label for="approval_status" class="form-label">Approval Status</label>
                    <select id="approval_status" name="approval_status" class="form-select" required>
                        <option value="approved" <?= $formData['approval_status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                        <option value="pending" <?= $formData['approval_status'] === 'pending' ? 'selected' : '' ?>>Pending Approval</option>
                        <option value="denied" <?= $formData['approval_status'] === 'denied' ? 'selected' : '' ?>>Denied</option>
                        <option value="on_hold" <?= $formData['approval_status'] === 'on_hold' ? 'selected' : '' ?>>On Hold</option>
                    </select>
                    <div class="invalid-feedback">Please select the approval status.</div>
                </div>

                <div class="col">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" name="status" class="form-select" required>
                        <option value="not_started" <?= $formData['status'] === 'not_started' ? 'selected' : '' ?>>Not Started</option>
                        <option value="in_progress" <?= $formData['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="completed" <?= $formData['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                    </select>
                    <div class="invalid-feedback">Please select the status.</div>
                </div>
            </div>


            <!-- Add Hyperlinks with Descriptions -->
            <div class="mb-3" id="hyperlinks-section">
                <label for="hyperlink_1" class="form-label">Hyperlinks</label>
                <?php if (!empty($formData['links'])): ?>
                    <?php foreach ($formData['links'] as $index => $link): ?>
                        <div class="input-group mb-2" id="hyperlink-group-<?= $index + 1 ?>">
                            <input type="hidden" name="link_ids[]" value="<?= $index ?>">
                            <input type="url" id="hyperlink_<?= $index + 1 ?>" name="hyperlinks[]" class="form-control" value="<?= htmlspecialchars($link['url']) ?>" placeholder="Enter hyperlink (e.g., https://example.com)">
                            <input type="text" id="description_<?= $index + 1 ?>" name="descriptions[]" class="form-control" value="<?= htmlspecialchars($link['description'] ?? '') ?>" placeholder="Enter description (optional)">
                            <button type="button" class="btn btn-danger" onclick="removeHyperlinkField(<?= $index + 1 ?>)" title="Remove this hyperlink">−</button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Template for new hyperlinks -->
                <div class="input-group mb-2" id="hyperlink-group-template" style="display: none;">
                    <input type="url" name="hyperlinks[]" class="form-control" placeholder="Enter hyperlink (e.g., https://example.com)">
                    <input type="text" name="descriptions[]" class="form-control" placeholder="Enter description (optional)">
                    <button type="button" class="btn btn-danger" onclick="removeHyperlinkField(this)" title="Remove this hyperlink">−</button>
                </div>

                <!-- Add Button -->
                <button type="button" class="btn btn-success mt-2" onclick="addHyperlinkField()" title="Add another hyperlink">+</button>
                <small class="text-muted d-block mt-2">Add one or more hyperlinks with optional descriptions. Click "+" to add additional fields.</small>
            </div>

            <script>
                let hyperlinkCount = <?= !empty($formData['links']) ? count($formData['links']) : 0 ?>;

                function addHyperlinkField() {
                    hyperlinkCount++;
                    const template = document.getElementById("hyperlink-group-template");
                    const clone = template.cloneNode(true);
                    clone.style.display = "flex";
                    clone.id = `hyperlink-group-${hyperlinkCount}`;

                    const inputs = clone.querySelectorAll("input");
                    inputs[0].id = `hyperlink_${hyperlinkCount}`;
                    inputs[1].id = `description_${hyperlinkCount}`;

                    template.parentNode.insertBefore(clone, template);
                }

                function removeHyperlinkField(element) {
                    const group = typeof element === 'number' 
                        ? document.getElementById(`hyperlink-group-${element}`)
                        : element.closest(".input-group");
                    group.remove();

                    // Mark the link for deletion
                    const removedLinksInput = document.getElementById("removed_links");
                    if (!removedLinksInput) {
                        const input = document.createElement("input");
                        input.type = "hidden";
                        input.name = "removed_links[]";
                        input.id = "removed_links";
                        document.getElementById("hyperlinks-section").appendChild(input);
                    }
                    const linkId = group.querySelector("input[name='link_ids[]']").value;
                    document.getElementById("removed_links").value += linkId + ",";
                }
            </script>



            <div class="mb-3">
                <label for="uploaded_files" class="form-label">Upload Files</label>
                <input type="file" id="uploaded_files" name="uploaded_files[]" class="form-control" multiple>
                <small class="text-muted">You can upload multiple files. Max size: 20MB each.</small>
            </div>
            

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100"><?= $changeid ? 'Update' : 'Submit' ?></button>
        </form>

    </div>
    <div class="col-md-6">
    <div><a href="#">Process documentation</a></div>
    <details>
        <summary><?= $cat ?> guidance</summary>
        <?= $Parsedown->text($guidance) ?>
    </details>
    <details>
    <summary>Scope guidance</summary>
        <div class="p-3">
            <h3>Minor Change</h3>
            <div><strong>1-2 hours </strong></div>
            <p>Small revisions to existing content that don’t significantly change the 
                meaning/consultation with the business owner is not required (e.g., typos, 
                updating links to existing or new versions of small assets (e.g., images), 
                minor big fixes that don’t significantly alter the user experience, changes 
                that don’t require extensive testing, small adjustments to quiz questions 
                in Moodle or HTML).</p>
        </div>
        <div class="p-3">
            <h3>Moderate </h3>
            <div><strong>2 hours – 24 hours </strong></div>
            <p>Moderate changes to content (needing business owner approval), updating or 
                reorganizing content in multiple lessons or modules, adding/updating evaluation 
                surveys, adjustments to quizzes built in Storyline, updating videos/interactive 
                activities, adding new activities/quizzes, multiple changes from an annual 
                review, or changes that require more than one person (e.g., developer). </p>
        </div>
        <div class="p-3">
            <h3>Major</h3>
            <div><strong>> 24 hours </strong></div>
            <p>Course overhauls or complete reorganization of existing content, revising learning 
                objectives, creating videos, simulations, requires extensive consultation with 
                business owners.</p>
        </div>   
    
    </details>
</div>
    </div>
</div>
<?php endif ?>

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>