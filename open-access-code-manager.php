<?php 
opcache_reset();
$path = 'inc/lsapp.php';
require($path); 

$jsonPath = __DIR__ . '/data/open-access-code.json';
$data = json_decode(file_get_contents($jsonPath), true);

$currentCode = $data[0]['code'] ?? 'N/A';
$currentCreated = $data[0]['created'] ?? 'Unknown';
$history = $data[1]['history'] ?? [];
?>

<?php if(canACcess()): ?>

<?php getHeader() ?>

<title>PSALS Course Catalog Feed Generator</title>

<?php getScripts() ?>
</body>
<?php getNavigation() ?>

<div class="container-fluid">
<div class="row justify-content-md-center">
<div class="col-md-8 col-xl-6">

<h1>Manage Open Access Code</h1>
    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">Current Access Code</h5>
            <p class="display-2"><?= htmlspecialchars($currentCode) ?></p>
            <p><strong>Created:</strong> <?= htmlspecialchars($currentCreated) ?></p>
            <form method="post" action="open-access-rotate-code.php">
                <button type="submit" class="btn btn-danger">Invalidate and Generate New Code</button>
            </form>
            <p class="mt-3">In order for a new code to take effect, the synchronization process needs 
                to happen. <a href="/lsapp/course-feed/">Sync now</a>, or wait until the automated
                sync happens at 7am, 12pm, and 4pm daily.</p>
        </div>
    </div>
    <?php if (!empty($history)): ?>
        <hr>
        <h5>Archived Codes</h5>
        <ul class="list-group">
            <?php foreach (array_reverse($history) as $entry): ?>
                <li class="list-group-item">
                    <strong>Code:</strong> <?= htmlspecialchars($entry['code']) ?><br>
                    <strong>Created:</strong> <?= htmlspecialchars($entry['created']) ?><br>
                    <strong>By:</strong> <?= htmlspecialchars($entry['createdby']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
</div>
</div>

<?php endif ?>

<?php require('templates/javascript.php') ?>
<?php require('templates/footer.php') ?>