<?php 
opcache_reset();
$path = '../inc/lsapp.php';
require($path); 
require('../inc/Parsedown.php');
$Parsedown = new Parsedown();

$courseid = isset($_GET['courseid']) ? htmlspecialchars($_GET['courseid']) : null;
$changeid = isset($_GET['changeid']) ? htmlspecialchars($_GET['changeid']) : null;

if (!$courseid) {
    echo '<div class="alert alert-danger">Error: Course ID is required.</div>';
    exit;
}

$course_deets = getCourse($courseid);
$course_steward = getPerson($course_deets[10]);
$course_developer = getPerson($course_deets[34]);

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
        echo '<div class="alert alert-warning">Warning: Change ID not found.</div>';
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

$assignedtoemail = getPerson($formData['assign_to']);

$email_addresses = [
                    'steward' => $course_steward[3],
                    'developer' => $course_developer[3], 
                    'assigned' => $assignedtoemail[3]
                    ];

function generateMailtoLink($formData, $courseid, $changeid, $course_deets, $email_addresses) {
    $subject = '';
    if($formData['urgent']) {
        $subject .= '[URGENT] ';    
    }
    $subject .= $course_deets[2] . ' - ' . htmlspecialchars($formData['category'] ?? 'N/A') . ' request';

    $body = "This is just a notification. Please reply via the request page on LSApp.\n";
    // Add a link back to the request
    $requestLink = "https://gww.bcpublicservice.gov.bc.ca/lsapp/course-change/view.php?courseid=$courseid&changeid=$changeid";
    $body .= "\nView the full request here: $requestLink\n\n";

    // Build the body of the email
    $body .= "Change Request Details:\n\n";
    $body .= "Course ID: $courseid\n";
    $body .= "Change ID: $changeid\n";
    $body .= "Category: " . htmlspecialchars($formData['category'] ?? 'N/A') . "\n";
    $body .= "Scope: " . htmlspecialchars($formData['scope'] ?? 'N/A') . "\n";
    $body .= "Assigned To: " . htmlspecialchars($formData['assign_to'] ?? 'N/A') . "\n";
    $body .= "Approval Status: " . htmlspecialchars($formData['approval_status'] ?? 'N/A') . "\n";
    $body .= "Progress: " . htmlspecialchars($formData['status'] ?? 'N/A') . "\n";
    $body .= "Description: \n" . strip_tags($formData['description'] ?? 'N/A') . "\n"; // Remove HTML tags

    if (!empty($formData['crm_ticket_reference'])) {
        $body .= "CRM Ticket Reference: " . htmlspecialchars($formData['crm_ticket_reference']) . "\n";
    }

    // Add links section
    if (!empty($formData['links'])) {
        $body .= "\nLinks:\n";
        foreach ($formData['links'] as $link) {
            $url = htmlspecialchars($link['url'] ?? 'N/A');
            $description = htmlspecialchars($link['description'] ?? $url);
            $body .= "- $description: $url\n";
        }
    }

    // Add files section
    if (!empty($formData['files'])) {
        $body .= "\nUploaded Files:\n";
        foreach ($formData['files'] as $file) {
            $shortFileName = preg_replace("/^course-[a-zA-Z0-9\-]+-change-[a-z0-9]+-/", '', $file);
            $fileUrl = "https://gww.bcpublicservice.gov.bc.ca/lsapp/requests/files/" . urlencode($file);
            $body .= "- $shortFileName: $fileUrl\n";
        }
    }

    // Add comments section
    if (!empty($formData['timeline'])) {
        $body .= "\nComments:\n";
        foreach ($formData['timeline'] as $event) {
            if ($event['field'] === 'comment') {
                $comment = htmlspecialchars_decode($event['new_value'] ?? ''); // Decode special characters
                $body .= "- " . htmlspecialchars($event['changed_by'] ?? 'Unknown') . " at " . date('Y-m-d H:i:s', $event['changed_at'] ?? 0) . ":\n";
                $body .= "  $comment\n";
            }
        }
    }


    // Encode the subject and body for use in a mailto link
    $mailto = 'mailto:' . $email_addresses['steward'] . ';' . $email_addresses['assigned'];
    $mailto .= "?subject=" . rawurlencode($subject); // Use rawurlencode for proper space encoding
    $mailto .= "&body=" . rawurlencode($body);

    // Add course developer as CC
    if (!empty($email_addresses['developer'])) {
        $mailto .= "&cc=" . rawurlencode($email_addresses['developer']);
    }
    return $mailto;
}

?>

<?php if (canACcess()): ?>

<?php getHeader(); ?>

<title><?= $course_deets[2] ?> Change Request</title>

<?php getScripts(); ?>

<body>

<?php getNavigation(); ?>

<div class="container mt-4">
    <div class="row justify-content-md-center">
        <div class="col">
            <a href="edit.php?courseid=<?= $courseid ?>&changeid=<?= $changeid ?>" class="btn btn-primary mb-4 float-end">Edit request</a>
            <h1 class="mb-3">
                <a href="/lsapp/course.php?courseid=<?= $course_deets[0] ?>" class="text-decoration-none"><?= $course_deets[2] ?></a>
            </h1>
            <div class="">
            <?php if ($formData['urgent']): ?>
            <span class="badge bg-danger">
                <strong>Urgent</strong>
            </span>
            <?php endif; ?>
            <span class="badge bg-success"><?= htmlspecialchars($formData['approval_status']) ?></span>
            </div>
            <h2 class="my-2"><?= htmlspecialchars($formData['category']) ?> Request <small class="text-muted"><?= $formData['changeid'] ?? '' ?></small></h2>
            
        </div>
    </div>
    <div class="row">
    <div class="col-md-6">
    
    <!-- Description Section -->
    <div>
        <strong>Scope:</strong> <?= htmlspecialchars($formData['scope']) ?>
        <strong>Progress:</strong> <?= htmlspecialchars($formData['status']) ?>
        <strong>Assigned To:</strong> <?= htmlspecialchars($formData['assign_to']) ?>
    </div>
            <div class="my-1 p-3 bg-dark-subtle rounded-3">
            <?= $Parsedown->text($formData['description']) ?>
            </div>
            <?php if ($formData['crm_ticket_reference']): ?>
            <div class="mb-2">
                <strong><a href="https://rightnow.gov.bc.ca" target="_blank" rel="noopener">CRM Ticket</a> #:</strong> <?= htmlspecialchars($formData['crm_ticket_reference'] ?? 'N/A') ?>
            </div>
            <?php endif; ?>

            <?php $mailtoLink = generateMailtoLink($formData, $courseid, $changeid, $course_deets, $email_addresses); ?>
            <div><a href="<?= $mailtoLink ?>" class="mb-1 btn btn-sm btn-primary">Email this request</a></div>

            <?php
            // Assuming $data['links'] contains the hyperlinks and descriptions
            if (!empty($formData['links'])): ?>
                <h4>Links</h4>
                <ul class="list-group mb-3">
                    <?php foreach ($formData['links'] as $link): ?>
                        <?php 
                            $url = htmlspecialchars($link['url']);
                            $description = !empty($link['description']) ? htmlspecialchars($link['description']) : $url;
                        ?>
                        <li class="list-group-item">
                            <a href="<?= $url ?>" target="_blank" rel="noopener noreferrer">
                                <?= $description ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

            <?php endif; ?>


    <!-- Files Section -->
    <?php if (!empty($formData['files'])): ?>

                <h4>Uploaded Files</h4>
                <ul class="list-group">
                    <?php foreach ($formData['files'] as $file): ?>
                        <?php $shortFileName = preg_replace("/^course-[a-zA-Z0-9\-]+-change-[a-z0-9]+-/", '', $file); ?>
                        <li class="list-group-item">
                            <a href="requests/files/<?= htmlspecialchars($file) ?>" target="_blank"><?= htmlspecialchars($shortFileName) ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>

    <?php endif; ?>
    

        </div>
        
        <div class="col-md-6">
            <h3>Guidance</h3>
            <div class="p-3 rounded-3 bg-light-subtle">

            <div><a href="#">Process documentation</a></div>
            <details>
                <summary><?= $cat ?> guidance</summary>
                <?= $Parsedown->text($guidance) ?>
            </details>
            <details>
            <summary>Scope guidance</summary>
                <div class="p-3">
                    <h3>Minor Change</h3>
                    <div><strong>Less than 2 hours </strong></div>
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


            <h4 class="fs-5 mt-5">Comments</h4>
            <details class="">
        <summary>Add a comment</summary>
    <!-- Comments Section -->
    <form action="comment-add.php" method="post" class="mt-4">
    <!-- Hidden Fields -->
    <input type="hidden" name="courseid" value="<?= $courseid ?>">
    <input type="hidden" name="changeid" value="<?= $changeid ?>">

    <!-- Comment Field -->
    <div class="mb-3">
        <label for="new_comment" class="form-label visually-hidden">Add Comment</label>
        <textarea id="new_comment" name="new_comment" class="form-control" rows="4" placeholder="Enter your comment here..." required></textarea>
    </div>

    <!-- Submit Button -->
    <button type="submit" class="btn btn-primary">Submit Comment</button>
    </form>
    </details>
    <?php if (!empty($formData['timeline'])): ?>

    <ul class="list-group">
        <?php foreach ($formData['timeline'] as $event): ?>
            <?php if ($event['field'] === 'comment'): ?>
                <li class="list-group-item bg-dark-subtle">
                    <strong><?= htmlspecialchars($event['changed_by']) ?>:</strong> 
                    <?= nl2br(htmlspecialchars($event['new_value'])) ?>
                    <br><small class="text-muted">At: <?= date('Y-m-d H:i:s', $event['changed_at']) ?></small>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>

    <?php endif; ?>





        </div>
    </div>

    <div class="mt-3">
        <strong>Created:</strong> <?= date('Y-m-d H:i:s', $formData['date_created']) ?> 
        by <?= htmlspecialchars($formData['created_by'] ?? '') ?>
    </div>
    <div class="mb-3">
        <strong>Last modified:</strong> <?= date('Y-m-d H:i:s', $formData['date_modified']) ?> 
        by <?= htmlspecialchars($formData['created_by'] ?? '') ?>
    </div>

    <!-- Timeline Section -->
    <?php if (!empty($formData['timeline'])): ?>
        
            <details>
                <summary>Timeline</summary>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Field</th>
                            <th>Previous Value</th>
                            <th>New Value</th>
                            <th>Changed By</th>
                            <th>Changed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($formData['timeline'] as $event): ?>
                            <?php if ($event['field'] !== 'comment'): ?>
                                <tr>
                                    <td><?= htmlspecialchars($event['field']) ?></td>
                                    <td>
                                    <?php if($event['field'] === 'link_updated'): ?>
                                        <?= $event['previous_value']['description'] ?? '' ?> - 
                                        <?= $event['previous_value']['url'] ?>
                                    <?php else: ?>
                                        <?= $event['previous_value'] ?? '' ?>
                                    <?php endif ?>    
                                    </td>
                                    <td>
                                    <?php if($event['field'] === 'link_added' || $event['field'] === 'link_updated'): ?>
                                        <?= $event['new_value']['description'] ?? '' ?> - 
                                        <?= $event['new_value']['url'] ?>
                                    <?php else: ?>
                                        <?= $event['new_value'] ?? '' ?>
                                    <?php endif ?>
                                    </td>
                                    <td><?= htmlspecialchars($event['changed_by'] ?? '') ?></td>
                                    <td><?= date('Y-m-d H:i:s', $event['changed_at'] ?? 0) ?></td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </details>

    <?php endif; ?>
</div>

<?php endif; ?>

<?php require('../templates/javascript.php'); ?>
<?php require('../templates/footer.php'); ?>