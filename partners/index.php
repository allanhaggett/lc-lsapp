<?php
opcache_reset();
$path = '../inc/lsapp.php';
require($path); 
$partnersFile = "../data/partners.json";
$partners = file_exists($partnersFile) ? json_decode(file_get_contents($partnersFile), true) : [];
?>

<?php if(canACcess()): ?>

<?php getHeader() ?>
    <title>Learning Partners</title>
    <script src="/lsapp/js/list.min.js"></script>
    <script>
        function editPartner(partner) {
            localStorage.setItem("editPartner", JSON.stringify(partner));
            window.location.href = "form.php";
        }
        function deletePartner(id, name) {
            if (confirm(`Are you sure you want to delete '${name}'? This cannot be undone.`)) {
                fetch('process.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `delete_id=${id}`
                }).then(response => response.text()).then(data => {
                    alert(data);
                    window.location.reload();
                }).catch(error => console.error('Error:', error));
            }
        }
    </script>

<?php getScripts() ?>
<body>
<?php getNavigation() ?>

<div class="container-fluid">
<div class="row justify-content-md-center">
<div class="col-md-6">

        <h1>Corporate Learning Partners</h1>

        <a href="form.php" class="btn btn-primary mb-3">Add New Partner</a>
        
        
        <div id="partner-list">
            <input class="search form-control mb-3" placeholder="Search partners...">
            <div class="list-group list">
                <?php foreach ($partners as $partner): ?>
                    <details class="list-group-item">
                        <summary class="name">
                            <?php echo htmlspecialchars($partner["name"]); ?>
                        </summary>
                        <p class="mt-2">
                            <?php echo nl2br(htmlspecialchars($partner["description"])); ?>
                        </p>

                        <h6>Contacts:</h6>
                        <ul class="list-unstyled">
                            <?php foreach ($partner["contacts"] as $contact): ?>
                                <li class="contact"><strong><?php echo htmlspecialchars($contact["name"]); ?></strong> 
                                    (<?php echo htmlspecialchars($contact["email"]); ?>)</li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="d-flex gap-2 mt-2">
                            <a href="<?php echo htmlspecialchars($partner["link"]); ?>" class="btn btn-success btn-sm" target="_blank">View on LearningHUB</a>
                            <a href="/lsapp/partners/view.php?slug=<?php echo htmlspecialchars($partner["slug"]); ?>" class="btn btn-success btn-sm">View in LSApp</a>
                            <a href="form.php?id=<?php echo $partner['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <form action="process.php" method="POST" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $partner['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this partner?')">Delete</button>
                            </form>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<script>
    var options = {
        valueNames: ['name', 'contact']
    };
    var partnerList = new List('partner-list', options);
</script>

<?php endif ?>

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>
