<?php
opcache_reset();
$path = '../inc/lsapp.php';
require($path); 
$partnersFile = "partners.json";
$partners = file_exists($partnersFile) ? json_decode(file_get_contents($partnersFile), true) : [];
?>

<?php if(canACcess()): ?>

<?php getHeader() ?>

<title>Manage Learning Partner</title>
    <script>
        function editPartner(partner) {
            localStorage.setItem("editPartner", JSON.stringify(partner));
            window.location.href = "index.php";
        }
    </script>


<?php getScripts() ?>
<script>
        function addContactField(contact = {}) {
            let container = document.getElementById("contacts-container");
            let index = document.querySelectorAll(".contact-group").length;

            let contactHtml = `
                <div class="contact-group border rounded p-3 mb-2">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="contacts[${index}][name]" class="form-control" value="${contact.name || ''}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="contacts[${index}][email]" class="form-control" value="${contact.email || ''}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">IDIR</label>
                            <input type="text" name="contacts[${index}][idir]" class="form-control" value="${contact.idir || ''}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Title</label>
                            <input type="text" name="contacts[${index}][title]" class="form-control" value="${contact.title || ''}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Role</label>
                            <input type="text" name="contacts[${index}][role]" class="form-control" value="${contact.role || ''}">
                        </div>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm mt-2" onclick="removeContactField(this)">Remove Contact</button>
                </div>`;
            container.insertAdjacentHTML('beforeend', contactHtml);
        }

        function removeContactField(button) {
            button.parentElement.remove();
        }

        function loadPartnerData(partner) {
            document.querySelector('input[name="name"]').value = partner.name || '';
            document.querySelector('input[name="slug"]').value = partner.slug || '';
            document.querySelector('textarea[name="description"]').value = partner.description || '';
            document.querySelector('input[name="link"]').value = partner.link || '';

            partner.contacts.forEach(contact => addContactField(contact));
        }

        document.addEventListener("DOMContentLoaded", function () {
            let partnerData = JSON.parse(localStorage.getItem("editPartner"));
            if (partnerData) {
                loadPartnerData(partnerData);
                localStorage.removeItem("editPartner");
            }
        });
    </script>
<body>
<?php getNavigation() ?>

<div class="container-fluid">
<div class="row justify-content-md-center">
<div class="col-md-10 col-xl-8">

    
            <div class="card-body">
                <form action="process.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label">Partner Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Link</label>
                        <input type="url" name="link" class="form-control" required>
                    </div>

                    <h4>Contacts</h4>
                    <div id="contacts-container"></div>
                    <button type="button" class="btn btn-success btn-sm mb-3" onclick="addContactField()">Add Contact</button>

                    <br>
                    <button type="submit" class="btn btn-primary">Save Partner</button>
                    <a href="list.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <?php endif ?>

<?php require('../templates/javascript.php') ?>
<?php require('../templates/footer.php') ?>