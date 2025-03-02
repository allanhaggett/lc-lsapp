<?php
opcache_reset();
$path = '../inc/lsapp.php';
require($path);
$partnersFile = "partners.json";
$partners = file_exists($partnersFile) ? json_decode(file_get_contents($partnersFile), true) : [];
$partnerId = $_GET['id'] ?? null;
$partner = null;

if ($partnerId) {
    foreach ($partners as $p) {
        if ($p['id'] == $partnerId) {
            $partner = $p;
            break;
        }
    }
}

$pcourses = $partner ? getCoursesByPartnerName($partner["name"]) : [];
?>

<?php if(canACcess() && $partner): ?>

<?php getHeader() ?>
<title><?php echo htmlspecialchars($partner["name"]); ?> - Partner Details</title>

<?php getScripts() ?>
<body>
<?php getNavigation() ?>

<div class="container mt-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Partners</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($partner["name"]); ?></li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h2 class="mb-0"> <?php echo htmlspecialchars($partner["name"]); ?> </h2>
            <a href="form.php?id=<?php echo $partner['id']; ?>" class="btn btn-warning">Edit</a>
        </div>
        <div class="card-body">
            <h5>Description:</h5>
            <p><?php echo nl2br(htmlspecialchars($partner["description"])); ?></p>

            <h5>Contacts:</h5>
            <ul class="list-group">
                <?php foreach ($partner["contacts"] as $contact): ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($contact["name"]); ?></strong>
                        <br><a href="mailto:<?php echo htmlspecialchars($contact["email"]); ?>"><?php echo htmlspecialchars($contact["email"]); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h5 class="mt-4">External Link:</h5>
            <a href="<?php echo htmlspecialchars($partner["link"]); ?>" class="btn btn-primary" target="_blank">Visit Partner Website</a>
        </div>
    </div>

    <?php if (!empty($pcourses)): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5>Courses Offered</h5>
            </div>
            <div class="card-body">
                <ul class="list-group">
                    <?php foreach ($pcourses as $course): ?>
                        <li class="list-group-item">
                            <strong><?php echo htmlspecialchars($course["title"] ?? 'Untitled Course'); ?></strong>
                            <br><?php echo htmlspecialchars($course["description"] ?? 'No description available.'); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php endif ?>

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>