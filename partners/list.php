<?php
opcache_reset();
$path = '../inc/lsapp.php';
require($path); 
$partnersFile = "partners.json";
$partners = file_exists($partnersFile) ? json_decode(file_get_contents($partnersFile), true) : [];
?>

<?php if(canACcess()): ?>

<?php getHeader() ?>

    <title>Learning Partners</title>
    <script>
        function editPartner(partner) {
            localStorage.setItem("editPartner", JSON.stringify(partner));
            window.location.href = "index.php";
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
<div class="col-md-12 col-xl-12">

        <a href="index.php" class="btn btn-primary mb-3">Add New Partner</a>


        <div class="row">
            <?php foreach ($partners as $partner): ?>

                    <div class="col-md-6">
                    <div class="card mt-2">
                        <div class="card-header">
                            <h5 class="mb-0"><?php echo htmlspecialchars($partner["name"]); ?></h5>
                        </div>
                        <div class="card-body">
                            <details>
                                <summary class="text-primary mb-2">View Description</summary>
                                <p><?php echo nl2br(htmlspecialchars($partner["description"])); ?></p>
                            </details>

                            <h6>Contacts:</h6>
                            <ul class="list-unstyled">
                                <?php foreach ($partner["contacts"] as $contact): ?>
                                    <li><strong><?php echo htmlspecialchars($contact["name"]); ?></strong> 
                                        (<?php echo htmlspecialchars($contact["email"]); ?>)</li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="d-flex justify-content-between mt-3">
                                <a href="<?php echo htmlspecialchars($partner["link"]); ?>" class="" target="_blank">View Partner</a>
                                <button class="btn btn-secondary" onclick='editPartner(<?php echo json_encode($partner, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>Edit</button>
                                <button class="btn btn-danger" onclick="deletePartner(<?php echo $partner['id']; ?>, '<?php echo htmlspecialchars($partner['name']); ?>')">Delete</button>
                            </div>
                        </div>
                    </div>
                    </div>

            <?php endforeach; ?>

    </div>
    </div>

    
</div>
</div>
</div>

<?php endif ?>

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>