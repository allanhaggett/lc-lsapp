<?php
opcache_reset();
$path = '../inc/lsapp.php';
require($path);
$partnersFile = "../data/partners.json";
$partners = file_exists($partnersFile) ? json_decode(file_get_contents($partnersFile), true) : [];
$partnerSlug = $_GET['slug'] ?? null;
$partner = null;

if ($partnerSlug) {
    foreach ($partners as $p) {
        if ($p['slug'] == $partnerSlug) {
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
            <li class="breadcrumb-item"><a href="index.php">Corp. Learning Partners</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($partner["name"]); ?></li>
        </ol>
    </nav>

    <div class="row">
    <div class="col-md-6">
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
            <div class="mt-3">
                <a href="<?php echo htmlspecialchars($partner["link"]); ?>" class="btn btn-primary" target="_blank">LearningHUB</a>
            </div>

        </div>
    </div>
    </div>

    <?php if (!empty($pcourses)): ?>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Courses Offered</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <?php foreach ($pcourses as $course): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($course[1]); ?>
                        </td>
                        <td>
                            <a href="/lsapp/course.php?courseid=<?php echo htmlspecialchars($course[0]); ?>">
                                <?php echo htmlspecialchars($course[2] ?? 'Untitled Course'); ?>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
    </div>
</div>

<?php endif ?>

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>
