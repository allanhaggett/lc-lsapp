<?php
opcache_reset();
date_default_timezone_set('America/Vancouver');
$path = '../inc/lsapp.php';
require($path); 
$partnersFile = "../data/partners.json";
$partners = file_exists($partnersFile) ? json_decode(file_get_contents($partnersFile), true) : [];
$partner = ["id" => "", "name" => "", "slug" => "", "description" => "", "link" => "", "contacts" => [], "status" => "inactive"];

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

<?php if(canACcess()): ?>

<?php getHeader() ?>

<title>Manage Learning Partner</title>

<?php getScripts() ?>

    <script>
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
                            <label class="form-label">IDIR <small>(not required, but highly recommended)</small></label>
                            <input type="text" name="contacts[${index}][idir]" class="form-control">
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
        
        function deleteContact(button, contactIndex, contactName) {
            if (confirm(`Are you sure you want to permanently delete "${contactName}" from this partner?\n\nThis action cannot be undone and will not preserve the contact in history.`)) {
                // Add a hidden input to mark this contact for deletion
                let form = button.closest('form');
                let deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'delete_contact[]';
                deleteInput.value = contactIndex;
                form.appendChild(deleteInput);
                
                // Apply visual changes to the contact group
                let contactGroup = button.closest('.contact-group');
                if (contactGroup) {
                    // Add Bootstrap classes for dark mode compatibility
                    contactGroup.classList.add('border-danger', 'bg-danger-subtle');
                    contactGroup.style.opacity = '0.7';
                    
                    // Strike through only the input fields, not the entire content
                    let inputs = contactGroup.querySelectorAll('input[type="text"], input[type="email"]');
                    inputs.forEach(input => {
                        input.style.textDecoration = 'line-through';
                        input.classList.add('bg-danger-subtle', 'text-danger-emphasis');
                        input.disabled = true;
                    });
                    
                    // Add a visual indicator using Bootstrap alert classes
                    let deletedLabel = document.createElement('div');
                    deletedLabel.className = 'alert alert-danger mt-2';
                    deletedLabel.innerHTML = '<strong>Marked for deletion:</strong> This contact will be permanently removed when you save.';
                    contactGroup.appendChild(deletedLabel);
                    
                    // Disable the delete button
                    button.disabled = true;
                    button.textContent = 'Marked for Deletion';
                    button.className = 'btn btn-secondary btn-sm';
                }
            }
        }
    </script>
</head>
<body>
<?php getNavigation() ?>

    <div class="container mt-4">
    <div class="row justify-content-md-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header">
                <h2 class="mb-0"><?php echo $partner["id"] ? "Edit" : "Add"; ?> Learning Partner</h2>
            </div>
            <div class="card-body">
                <form action="process.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($partner["id"]); ?>">
                    <input type="hidden" name="slug" value="<?php echo htmlspecialchars($partner["slug"]); ?>">

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="requested" <?php echo $partner["status"] === "requested" ? "selected" : ""; ?>>Requested</option>
                            <option value="active" <?php echo $partner["status"] === "active" ? "selected" : ""; ?>>Active</option>
                            <option value="inactive" <?php echo $partner["status"] === "inactive" ? "selected" : ""; ?>>Inactive</option>
                        </select>
                    </div>
                    
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

                    <h4>Contacts</h4>
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
                                    <label class="form-label">IDIR <small>(not required, but highly recommended)</small></label>
                                    <input type="text" name="contacts[<?php echo $index; ?>][idir]" class="form-control" value="<?php echo htmlspecialchars($contact["idir"]); ?>">
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
                            
                            <?php if (isAdmin()): ?>
                            <!-- "Delete" Button for Admins only -->
                            <details class="mt-2">
                                <summary class="text-danger">Delete</summary>
                                <div class="alert alert-danger">
                                    <strong>⚠️ Warning:</strong> This will permanently delete this contact from the partner. This action cannot be undone and the contact will not be moved to the contact history.
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteContact(this, <?php echo $index; ?>, '<?php echo htmlspecialchars($contact['name']); ?>')">
                                    <i class="bi bi-trash"></i> Permanently Delete Contact
                                </button>
                            </details>
                            <?php endif; ?>
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
                                <label class="form-label">IDIR <small>(not required, but highly recommended)</small></label>
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
                    <button type="submit" class="btn btn-primary">Save Partner</button>
                    <a href="list.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
    </div>
    </div>

<?php endif ?>

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>
