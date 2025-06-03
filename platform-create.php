<?php 
require('inc/lsapp.php');
if(canAccess()):

    if($_POST):
        $fromform = $_POST;
        
        // Load existing platforms
        $jsonContent = file_get_contents('data/platforms.json');
        $platforms = json_decode($jsonContent, true);
        
        // Create ID from name (lowercase, replace spaces with hyphens)
        $platformId = strtolower(str_replace(' ', '-', $fromform['name']));
        $platformId = preg_replace('/[^a-z0-9\-]/', '', $platformId);
        
        // Create new platform
        $newPlatform = array(
            'id' => $platformId,
            'name' => h($fromform['name']),
            'description' => h($fromform['description']),
            'link' => h($fromform['link'])
        );
        
        // Add to platforms array
        $platforms[] = $newPlatform;
        
        // Save back to JSON file
        file_put_contents('data/platforms.json', json_encode($platforms, JSON_PRETTY_PRINT));
        
        header('Location: /lsapp/platform.php?id=' . $platformId);
        
    else:
?>

<?php getHeader() ?>
<title>Create New Platform</title>
<?php getScripts() ?>
<body>
<?php getNavigation() ?>

<div class="container mb-3">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6 mb-3">

<h1>Create New Platform</h1>

<form method="post" action="platform-create.php" class="mb-3 pb-3">

<div class="form-group">
    <label for="name">Platform Name: <span class="text-danger">*</span></label>
    <input type="text" name="name" id="name" class="form-control" required>
</div>

<div class="form-group">
    <label for="description">Description: <span class="text-danger">*</span></label>
    <textarea name="description" id="description" class="form-control" rows="3" required></textarea>
</div>

<div class="form-group">
    <label for="link">Platform URL:</label>
    <input type="url" name="link" id="link" class="form-control" placeholder="https://example.com">
    <small class="form-text text-muted">Leave blank if no external link is available</small>
</div>

<button type="submit" class="btn btn-primary">Create Platform</button>
<a href="/lsapp/platforms.php" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>
</div>

<?php require('templates/javascript.php') ?>
<?php require('templates/footer.php') ?>

<?php endif ?>

<?php else: ?>

<?php require('templates/noaccess.php') ?>

<?php endif ?>