<?php 
require('inc/lsapp.php');
if(canAccess()):

if($_POST):

    $fromform = $_POST;
    $platformId = $fromform['id'];
    
    // Load existing platforms
    $jsonContent = file_get_contents('data/platforms.json');
    $platforms = json_decode($jsonContent, true);
    
    // Find and update the platform
    foreach ($platforms as &$platform) {
        if ($platform['id'] === $platformId) {
            $platform['name'] = h($fromform['name']);
            $platform['description'] = h($fromform['description']);
            $platform['link'] = h($fromform['link']);
            break;
        }
    }
    
    // Save back to JSON file
    file_put_contents('data/platforms.json', json_encode($platforms, JSON_PRETTY_PRINT));
    
    header('Location: platform.php?id=' . $platformId);
    
else: ?>

<?php 
$platformId = $_GET['id'];

// Load platforms data
$jsonContent = file_get_contents('data/platforms.json');
$platforms = json_decode($jsonContent, true);

// Find the specific platform
$currentPlatform = null;
foreach ($platforms as $platform) {
    if ($platform['id'] === $platformId) {
        $currentPlatform = $platform;
        break;
    }
}

if (!$currentPlatform) {
    header('Location: platforms.php');
    exit;
}
?>

<?php getHeader() ?>
<title>Edit <?= htmlspecialchars($currentPlatform['name']) ?></title>
<?php getScripts() ?>
<body>
<?php getNavigation() ?>

<div class="container mb-3">
<div class="row justify-content-md-center mb-3">
<div class="col-md-6 mb-3">

<h1>Edit <?= htmlspecialchars($currentPlatform['name']) ?></h1>

<form method="post" action="platform-update.php" class="mb-3 pb-3">

<input type="hidden" name="id" value="<?= htmlspecialchars($currentPlatform['id']) ?>">

<div class="form-group">
    <label for="name">Platform Name: <span class="text-danger">*</span></label>
    <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($currentPlatform['name']) ?>" required>
</div>

<div class="form-group">
    <label for="description">Description: <span class="text-danger">*</span></label>
    <textarea name="description" id="description" class="form-control" rows="3" required><?= htmlspecialchars($currentPlatform['description']) ?></textarea>
</div>

<div class="form-group">
    <label for="link">Platform URL:</label>
    <input type="url" name="link" id="link" class="form-control" value="<?= htmlspecialchars($currentPlatform['link']) ?>" placeholder="https://example.com">
    <small class="form-text text-muted">Leave blank if no external link is available</small>
</div>

<button type="submit" class="btn btn-primary">Save Changes</button>
<a href="/lsapp/platform.php?id=<?= htmlspecialchars($currentPlatform['id']) ?>" class="btn btn-secondary">Cancel</a>

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