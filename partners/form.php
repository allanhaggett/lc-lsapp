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

                    <div class="mb-3">
                        <label class="form-label">Partner Name</label>
                        <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($partner["name"]); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" required value="<?php echo htmlspecialchars($partner["slug"]); ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($partner["description"]); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Link</label>
                        <input type="url" name="link" class="form-control" required value="<?php echo htmlspecialchars($partner["link"]); ?>">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="status" class="form-check-input" id="status" value="active" <?php echo $partner["status"] === "active" ? "checked" : ""; ?>>
                        <label class="form-check-label" for="status">Active?</label>
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
                        There is no contact listed for this partner!
                    </div>
                    <?php endif; ?>
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
