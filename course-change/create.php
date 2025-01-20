<?php 
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
$cat = urldecode($_GET['cat']) ?? '';
$categoriesFile = 'guidance.json';
$guidance = getGuidanceByCategory($cat, $categoriesFile);

?>

<?php if(canACcess()): ?>

<?php getHeader() ?>

<title><?= $deets[2] ?> Change Request</title>

<?php getScripts() ?>
</body>
<?php getNavigation() ?>

<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-md-12">
            <h1 class=""><a href="/lsapp/course.php?courseid=<?= $deets[0] ?>"><?= $deets[2] ?></a></h1>
            <h2><?= $cat ?> Request <small><?= $formData['changeid'] ?? '' ?></small></h2>
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
            <input type="hidden" name="category" value="<?php echo urlencode($cat); ?>">
   
            <div class="row">
            
            <div class="col">
                <!-- Scope -->
                <label for="scope" class="form-label visually-hidden">Scope</label>
                <select id="scope" name="scope" class="form-select" required>
                    <option value="" disabled>Choose a scope</option>
                    <option value="minor" <?php echo $formData['scope'] === 'minor' ? 'selected' : ''; ?>>Minor Change (1-2 hours)</option>
                    <option value="moderate" <?php echo $formData['scope'] === 'moderate' ? 'selected' : ''; ?>>Moderate Change (2-24 hours)</option>
                    <option value="major" <?php echo $formData['scope'] === 'major' ? 'selected' : ''; ?>>Major Change (&gt;24 hours)</option>
                </select>
                <div class="invalid-feedback">Please select the scope of the request.</div>
            </div>
            <div class="col align-self-end">
            <div class="form-check">
                <input type="checkbox" id="urgent" name="urgent" class="form-check-input" value="yes" <?php echo $formData['urgent'] ? 'checked' : ''; ?>>
                <label for="urgent" class="form-check-label">Urgent?</label>
            </div>
            </div>


            <!-- Description -->
            <div class="my-3">

                <div id="toolbar" class="btn-group">
                    <button class="btn btn-sm bg-light-subtle" type="button" onclick="applyMarkdown('###', '')">Header</button>
                    <button class="btn btn-sm bg-light-subtle" type="button" onclick="applyMarkdown('**', '**')">Bold</button>
                    <button class="btn btn-sm bg-light-subtle" type="button" onclick="applyMarkdown('_', '_')">Italic</button>
                    <button class="btn btn-sm bg-light-subtle" type="button" onclick="applyLink()">Link</button>
                    <button class="btn btn-sm bg-light-subtle" type="button" onclick="applyList('unordered')">Unordered List</button>
                    <button class="btn btn-sm bg-light-subtle" type="button" onclick="applyList('ordered')">Ordered List</button>
                </div>
                <textarea id="description" name="description" class="form-control" rows="4" required placeholder="Detailed description"><?php echo $formData['description']; ?></textarea>
                <div class="invalid-feedback">Please provide a description of the request.</div>
                <script>
                    function applyMarkdown(before, after) {
                    const textarea = document.getElementById("description");
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const selectedText = textarea.value.slice(start, end);

                    // Apply Markdown symbols
                    const newText = before + selectedText + after;
                    textarea.setRangeText(newText);

                    // Re-select the newly formatted text
                    textarea.setSelectionRange(start + before.length, end + before.length);
                    textarea.focus();
                    }

                    function applyLink() {
                    const textarea = document.getElementById("description");
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const selectedText = textarea.value.slice(start, end);

                    // Default link format
                    const newText = `[${selectedText || 'text'}](http://example.com)`;
                    textarea.setRangeText(newText);

                    // Re-select the text to allow easy editing of the link
                    if (!selectedText) {
                        textarea.setSelectionRange(start + 1, start + 5); // Select 'text'
                    } else {
                        textarea.setSelectionRange(start + newText.length - 19, start + newText.length - 1); // Select 'http://example.com'
                    }
                    textarea.focus();
                    }

                    function applyList(type) {
                    const textarea = document.getElementById("description");
                    const start = textarea.selectionStart;
                    const end = textarea.selectionEnd;
                    const selectedText = textarea.value.slice(start, end);

                    // Split selected text by lines
                    const lines = selectedText.split('\n');
                    
                    // Prefix each line with list markdown symbols
                    const newLines = lines.map((line, index) => {
                        if (type === 'unordered') {
                        return `- ${line}`;
                        } else if (type === 'ordered') {
                        return `${index + 1}. ${line}`;
                        }
                        return line;
                    });
                    
                    const newText = newLines.join('\n');
                    textarea.setRangeText(newText);

                    // Re-select the newly formatted text
                    textarea.setSelectionRange(start, start + newText.length);
                    textarea.focus();
                    }
                    </script>
            </div>
            <!-- Approval Status -->
            <div class="col-md-6">
                <label for="approval_status" class="form-label">Approval Status</label>
                <select id="approval_status" name="approval_status" class="form-select" required>
                    <option value="approved" <?php echo $formData['approval_status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="pending" <?php echo $formData['approval_status'] === 'pending' ? 'selected' : ''; ?>>Pending Approval</option>
                    <option value="denied" <?php echo $formData['approval_status'] === 'denied' ? 'selected' : ''; ?>>Denied</option>
                    <option value="on_hold" <?php echo $formData['approval_status'] === 'on_hold' ? 'selected' : ''; ?>>On Hold</option>
                </select>
                <div class="invalid-feedback">Please select the approval status.</div>
            </div>
            <!-- Status -->
            <div class="col">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select" required>
                    <option value="not_started" <?php echo $formData['status'] === 'not_started' ? 'selected' : ''; ?>>Not Started</option>
                    <option value="in_progress" <?php echo $formData['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="completed" <?php echo $formData['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                </select>
                <div class="invalid-feedback">Please select the status.</div>
            </div>

            
            </div>

            <div class="row my-3">
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


            
            <!-- Add New Files -->
            <div class="mb-3">
                <label for="uploaded_files" class="form-label">Upload Files</label>
                <input type="file" id="uploaded_files" name="uploaded_files[]" class="form-control" multiple>
                <small class="text-muted">You can upload multiple files. Max size: 20MB each.</small>
            </div>

            <!-- Add Hyperlinks with Descriptions -->
            <div class="mb-3" id="hyperlinks-section">
                <label for="hyperlink_1" class="form-label">Hyperlinks</label>
                <div class="input-group mb-2" id="hyperlink-group-1">
                    <input type="url" id="hyperlink_1" name="hyperlinks[]" class="form-control" placeholder="Enter hyperlink (e.g., https://example.com)">
                    <input type="text" id="description_1" name="descriptions[]" class="form-control" placeholder="Enter title (optional)">
                    <button type="button" class="btn btn-success" onclick="addHyperlinkField()" title="Add another hyperlink">+</button>
                </div>
                <small class="text-muted">Add one or more hyperlinks with optional descriptions. Click "+" to add additional fields.</small>
            </div>

            <script>
                let hyperlinkCount = 1; // Counter for hyperlink fields

                function addHyperlinkField() {
                    hyperlinkCount++;
                    const section = document.getElementById("hyperlinks-section");

                    // Create a new input group
                    const newInputGroup = document.createElement("div");
                    newInputGroup.className = "input-group mb-2";
                    newInputGroup.id = `hyperlink-group-${hyperlinkCount}`;

                    // Create the hyperlink input field
                    const newHyperlinkInput = document.createElement("input");
                    newHyperlinkInput.type = "url";
                    newHyperlinkInput.id = `hyperlink_${hyperlinkCount}`;
                    newHyperlinkInput.name = "hyperlinks[]";
                    newHyperlinkInput.className = "form-control";
                    newHyperlinkInput.placeholder = "Enter hyperlink (e.g., https://example.com)";

                    // Create the description input field
                    const newDescriptionInput = document.createElement("input");
                    newDescriptionInput.type = "text";
                    newDescriptionInput.id = `description_${hyperlinkCount}`;
                    newDescriptionInput.name = "descriptions[]";
                    newDescriptionInput.className = "form-control";
                    newDescriptionInput.placeholder = "Enter description (optional)";

                    // Create the remove button
                    const removeButton = document.createElement("button");
                    removeButton.type = "button";
                    removeButton.className = "btn btn-danger";
                    removeButton.title = "Remove this hyperlink";
                    removeButton.innerHTML = "−";
                    removeButton.onclick = function () {
                        section.removeChild(newInputGroup);
                    };

                    // Append the input fields and button to the input group
                    newInputGroup.appendChild(newHyperlinkInput);
                    newInputGroup.appendChild(newDescriptionInput);
                    newInputGroup.appendChild(removeButton);

                    // Append the input group to the hyperlinks section
                    section.appendChild(newInputGroup);
                }
            </script>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary w-100"><?php echo $changeid ? 'Update' : 'Submit'; ?></button>
        
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