<?php
require('inc/lsapp.php');
if(canAccess()):
// Path to the JSON file
$filePath = 'data/learning_partners.json';

// Read the JSON data
$data = json_decode(file_get_contents($filePath), true);

// Initialize form fields with empty values or defaults
$id = $name = $description = $link = $slug = "";
$admin_idir = "ahaggett";
$admin_email = "allan.haggett@gov.bc.ca";
$admin_name = "Allan Haggett";

// Check if an 'id' is provided in the query string for editing
if (isset($_GET['id'])) {
    $recordId = (int)$_GET['id'];
    // Find the record with the matching ID
    foreach ($data as $record) {
        if ($record['id'] === $recordId) {
            $id = $record['id'];
            $name = $record['name'];
            $description = $record['description'];
            $link = $record['link'];
            $slug = $record['slug'];
            $admin_idir = $record['admin_idir'];
            $admin_email = $record['admin_email'];
            $admin_name = $record['admin_name'];
            break;
        }
    }
}
?>
<?php getHeader() ?>
<?php getScripts() ?>
<body>
<?php getNavigation() ?>
<div class="container">
<div class="row">
<div class="col-md-6">
    <h2>Learning Partner Form</h2>
    <form action="learning-hub-partner-update.php" method="post">
        <div class="mb-3">
            <label for="id" class="form-label">ID</label>
            <input type="number" class="form-control" id="id" name="id" placeholder="Record ID" value="<?php echo htmlspecialchars($id); ?>" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($description); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="link" class="form-label">Link</label>
            <input type="url" class="form-control" id="link" name="link" value="<?php echo htmlspecialchars($link); ?>" required>
        </div>
        <div class="mb-3">
            <label for="slug" class="form-label">Slug</label>
            <input type="text" class="form-control" id="slug" name="slug" value="<?php echo htmlspecialchars($slug); ?>" required>
        </div>
        <div class="mb-3">
            <label for="admin_idir" class="form-label">Admin IDIR</label>
            <input type="text" class="form-control" id="admin_idir" name="admin_idir" value="<?php echo htmlspecialchars($admin_idir); ?>" required>
        </div>
        <div class="mb-3">
            <label for="admin_email" class="form-label">Admin Email</label>
            <input type="email" class="form-control" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($admin_email); ?>" required>
        </div>
        <div class="mb-3">
            <label for="admin_name" class="form-label">Admin Name</label>
            <input type="text" class="form-control" id="admin_name" name="admin_name" value="<?php echo htmlspecialchars($admin_name); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
</div>
</div>
</div>
<?php require('templates/footer.php') ?>
<?php endif; // canAccess()?? ?>