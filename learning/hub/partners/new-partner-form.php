<?php
opcache_reset();
date_default_timezone_set('America/Vancouver');
$path = '../../../lsapp/inc/lsapp.php';
require($path); 
$partnersFile = "../../../lsapp/data/partners.json";
$partners = file_exists($partnersFile) ? json_decode(file_get_contents($partnersFile), true) : [];
$partner = ["id" => "", "name" => "", "slug" => "", "description" => "", "link" => "", "employee_facing_contact" => "", "contacts" => [], "status" => "inactive"];

// Check if logged in user has a pending request
$hasRequestedPartner = false;
if (defined('LOGGED_IN_IDIR') && LOGGED_IN_IDIR) {
    foreach ($partners as $p) {
        if ($p["status"] === "requested" || $p["status"] === "Requested") {
            // Check requested_idir first (more reliable)
            if (isset($p["requested_idir"]) && strtolower($p["requested_idir"]) === strtolower(LOGGED_IN_IDIR)) {
                $hasRequestedPartner = true;
                break;
            }
            // Also check contacts for backward compatibility
            if (!empty($p["contacts"])) {
                foreach ($p["contacts"] as $contact) {
                    if (isset($contact["idir"]) && strtolower($contact["idir"]) === strtolower(LOGGED_IN_IDIR)) {
                        $hasRequestedPartner = true;
                        break 2;
                    }
                }
            }
        }
    }
}

// Load existing partner if editing
if (isset($_GET["id"])) {
    foreach ($partners as $p) {
        if ($p["id"] == $_GET["id"]) {
            $partner = $p;
            break;
        }
    }
}
?>

<?php include('../templates/header.php') ?>

    <script>
        function setCRM() {
            document.getElementById('employee_facing_contact').value = 'CRM';
        }
        
        function addContactField() {
            let container = document.getElementById("contacts-container");
            let index = document.querySelectorAll(".contact-group").length;

            let contactHtml = `
                <div class="contact-group border rounded p-3 mb-2">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="contacts[${index}][name]" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="contacts[${index}][email]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IDIR</label>
                            <input type="text" name="contacts[${index}][idir]" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Title</label>
                            <input type="text" name="contacts[${index}][title]" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Role</label>
                            <input type="text" name="contacts[${index}][role]" class="form-control">
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeContactField(this)">Remove</button>
                </div>`;
            container.insertAdjacentHTML('beforeend', contactHtml);
        }

        function removeContactField(button) {
            let contactGroup = button.closest('.contact-group');
            if (contactGroup) {
                contactGroup.remove();
            }
        }
    </script>
<div class="d-flex p-4 p-md-5 align-items-center bg-gov-green bg-gradient" style="height: 12vh; min-height: 100px;">
    <div class="container-lg py-4 py-md-5">
        <h1 class="text-white"><?php echo $partner["id"] ? "Edit" : "New"; ?> Learning Partner</h1>
    </div>
</div>

    <div class="container my-5">
    <?php if ($hasRequestedPartner): ?>
    <div class="alert alert-primary" role="alert">
        Thank you for your request. We'll process it as soon as possible.
    </div>
    <?php endif; ?>
    <div class="row justify-content-md-center">
    <div class="col-md-4">
        <h2>Welcome</h2>
        <p>In the BC Public Service, corporate learning is a shared space. <a href="https://corporatelearning.gww.gov.bc.ca/learninghub/corporate-learning-partners/">Corporate learning partners</a> are all committed to offering learning, development and growth opportunities for all our employees.</p>
        <p>To be a corporate learning partner, you must have developed or designed a course that is aligned with the <a href="https://corporatelearning.gww.gov.bc.ca/learninghub/what-is-corp-learning-framework/">Corporate Learning Framework</a> and beneficial to all BC Public Service employees regardless of their ministry.</p>
        <p>If you're ready, fill out the form to the right and we'll get back to you with next steps.</p>
    </div>
    <div class="col-md-6">
    <div class="p-4 bg-light-subtle rounded-3">

        
                <form action="process.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($partner["id"]); ?>">
                    <input type="hidden" name="slug" value="<?php echo htmlspecialchars($partner["slug"]); ?>">

                    <?php if (!empty($partner["id"])): ?>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="requested" <?php echo $partner["status"] === "requested" ? "selected" : ""; ?>>Requested</option>
                            <option value="active" <?php echo $partner["status"] === "active" ? "selected" : ""; ?>>Active</option>
                            <option value="inactive" <?php echo $partner["status"] === "inactive" ? "selected" : ""; ?>>Inactive</option>
                        </select>
                    </div>
                    <?php else: ?>
                        <input type="hidden" name="status" value="requested">
                    <?php endif; ?>
                    
                    <div class="mb-3">
                        <label class="form-label">Partner Name</label>
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($partner["name"]); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($partner["description"]); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Link</label>
                        <input type="url" name="link" class="form-control" required value="<?php echo htmlspecialchars($partner["link"]); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Employee Facing Contact</label>
                        <div class="input-group">
                            <input type="text" name="employee_facing_contact" class="form-control" 
                                   placeholder="Enter email address or 'CRM'" 
                                   value="<?php echo htmlspecialchars($partner["employee_facing_contact"] ?? ''); ?>"
                                   id="employee_facing_contact">
                            <button class="btn btn-outline-secondary" type="button" onclick="setCRM()">Set to CRM</button>
                        </div>
                        <small class="text-muted">Enter an email address or select 'CRM' for CRM-based contact</small>
                    </div>

                    <h4>Contacts</h4>
                    <p><strong>For internal use only.</strong> These contacts are not public, but let us know who to contact for queries about your courses. You can list multiple people in multiple roles for different kinds of issues if you like.</p>
                    <div id="contacts-container">
                    <?php if (!empty($partner["contacts"])): ?>
                    <?php foreach ($partner["contacts"] as $index => $contact): ?>
                        <div class="contact-group border rounded p-3 mb-2">
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label">Name</label>
                                    <input type="text" name="contacts[<?php echo $index; ?>][name]" class="form-control" required value="<?php echo htmlspecialchars($contact["name"]); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="contacts[<?php echo $index; ?>][email]" class="form-control" required value="<?php echo htmlspecialchars($contact["email"]); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">IDIR</label>
                                    <input type="text" name="contacts[<?php echo $index; ?>][idir]" class="form-control" required value="<?php echo htmlspecialchars($contact["idir"]); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Title</label>
                                    <input type="text" name="contacts[<?php echo $index; ?>][title]" class="form-control" value="<?php echo htmlspecialchars($contact["title"]); ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Role</label>
                                    <input type="text" name="contacts[<?php echo $index; ?>][role]" class="form-control" value="<?php echo htmlspecialchars($contact["role"]); ?>">
                                </div>
                            </div>
                            <input type="hidden" name="contacts[<?php echo $index; ?>][added_at]" value="<?php echo htmlspecialchars($contact["added_at"] ?? ''); ?>">

                            <!-- "Retire" Button inside <details> -->
                            <details class="mt-2">
                                <summary>Retire</summary>
                                <p>If this person is no longer in this role, we need to "retire" them. Their information is still available as part of the record of this partnership.</p>
                                <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeContactField(this)">Retire</button>
                            </details>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php if (isset($_GET["id"])) : ?>
                    <div class="alert alert-warning">
                        There is no contact listed for this partner! A blank contact field has been added.
                    </div>
                    <?php endif; ?>
                    <div class="contact-group border rounded p-3 mb-2">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="contacts[0][name]" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="contacts[0][email]" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">IDIR</label>
                                <input type="text" name="contacts[0][idir]" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Title</label>
                                <input type="text" name="contacts[0][title]" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Role</label>
                                <input type="text" name="contacts[0][role]" class="form-control">
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                        <?php if (!empty($partner["contact_history"])): ?>
                        <details>
                            <summary>Contact History</summary>
                            <?php foreach ($partner["contact_history"] as $index => $contact): ?>
                            <div class="mb-2 p-3 bg-light-subtle rounded-3">
                            <div>
                                <?php echo htmlspecialchars($contact["name"]); ?> 
                                &lt;<?php echo htmlspecialchars($contact["email"]); ?>&gt;
                                (<?php echo htmlspecialchars($contact["idir"]); ?>)
                            </div>
                            <div>
                                Title: <?php echo htmlspecialchars($contact["title"]); ?>
                            </div>
                            <div>
                                Role: <?php echo htmlspecialchars($contact["role"]); ?>
                            </div>
                            <div>Added: <?php echo htmlspecialchars($contact["added_at"]); ?></div>
                            <div>Retired: <?php echo htmlspecialchars($contact["removed_at"]); ?></div>
                            </div>
                            <?php endforeach; ?>
                        </details>
                        <?php endif; ?>
                    </div>

                    <button type="button" class="btn btn-success btn-sm mt-2" onclick="addContactField()">Add Contact</button>

                    <br><br>
                    <button type="submit" class="btn btn-primary">Request Partnership</button>

                </form>

        
    </div>
    
    </div>
    </div>
    </div>

<?php include('../templates/footer.php') ?>