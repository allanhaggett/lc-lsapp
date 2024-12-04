<?php require('inc/lsapp.php') ?>
<?php getHeader() ?>
<?php getScripts() ?>
<?php getNavigation() ?>

<?php
// URL of the JSON file
$url = "https://raw.githubusercontent.com/bcgov/ministry-org-names/refs/heads/main/ministry-names.json";

// Fetch the JSON content
$jsonContent = file_get_contents($url);

// Decode the JSON into an associative array
$ministries = json_decode($jsonContent, true);

if ($ministries === null) {
    echo '<div class="alert alert-danger" role="alert">Failed to load ministry data. Please try again later.</div>';
    // exit;
}
?>

<div class="container mt-4">
	<h1 class="mb-4">Ministry Names</h1>
	<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
		<?php foreach ($ministries as $ministry): ?>
			<div class="col">
				<div class="card h-100">
					<div class="card-body">
						<h5 class="card-title"><?php echo htmlspecialchars($ministry['name']); ?></h5>
						<p class="card-text">
							<strong>Abbreviation:</strong> <?php echo htmlspecialchars($ministry['abbreviation'] ?? 'N/A'); ?>
						</p>
						<p class="card-text">
							<strong>Full Name:</strong> <?php echo htmlspecialchars($ministry['full_name'] ?? 'N/A'); ?>
						</p>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>
    


<?php require_once 'templates/footer.php' ?>
