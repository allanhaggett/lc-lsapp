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
        <div class="card-header">
            <div class="mb-0">
            <?php 
            $statustype = 'primary';
            if($partner["status"] != 'active') $statustype = 'warning'; 
            ?>
                <span class="badge bg-<?= $statustype ?>">
                    <?php echo htmlspecialchars($partner["status"]); ?>
                </span>
            </div>
            <h2 class="mb-0"> <?php echo htmlspecialchars($partner["name"]); ?> </h2>
        </div>
        <div class="card-body">
            <h5>Description:</h5>
            <p><?php echo nl2br(htmlspecialchars($partner["description"])); ?></p>
            <div class="my-3">
                <a href="<?php echo htmlspecialchars($partner["link"]); ?>" class="" target="_blank">LearningHUB</a>
            </div>
            <h5>Contacts:</h5>
            <?php if (!empty($partner["contacts"])): ?>
            <ul class="list-group">
                <?php foreach ($partner["contacts"] as $contact): ?>
                    <li class="list-group-item">

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
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php else: ?>
                <div class="alert alert-warning">
                    There is no contact listed for this partner!
                </div>
            <?php endif ?>
            
            <?php if (!empty($partner["contact_history"])): ?>
            <details class="mt-3">
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
            
            <div class="mt-3">
                <a href="form.php?id=<?php echo $partner['id']; ?>" class="btn btn-dark">
                    Edit partner info
                </a>
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
