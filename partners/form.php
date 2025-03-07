<?php
opcache_reset();
$path = '../inc/lsapp.php';
require($path); 
$partnersFile = "../data/partners.json";
$partners = file_exists($partnersFile) ? json_decode(file_get_contents($partnersFile), true) : [];
$partner = ["id" => "", "name" => "", "slug" => "", "description" => "", "link" => "", "contacts" => []];

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
            button.parentElement.remove();
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
                                    <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeContactField(this)">Remove</button>
                                </div>
                            <?php endforeach; ?>
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

