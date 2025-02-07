<div class="row justify-content-md-center">
<div class="col-md-4">
	<div class="my-3 p-3">
		<h2>Welcome to LSApp</h2>
		<p>The Learning Support Application, or LSApp (pronounced "el-sap") is an administrative tool 
			used by the Corporate Learning Branch (CLB) to coordinate much of the data associated with 
			the courses that CLB manages alonside its 
			<a href="https://learningcentre.gww.gov.bc.ca/learninghub/corporate-learning-partners/"
				target="_blank" rel="noopener">
				Corporate Learning Partners
			</a>.</p>
		<p>LSApp manages numerous functions for the CLB. A complete feature list is coming soon.</p>
		<div class="alert alert-warning">
			LSApp is restricted to pre-approved folks. 
			If you need access, please fill out the form below.
			Submitting the form will open up your email client with a message
			to the CLB admin inbox pre-composed with your details. Just click send, and a support
			person will process your request.
		</div>
		<form method="post" class="newuser card p-4 shadow-sm">

            <div class="mb-3">
                <label for="idir" class="form-label">IDIR</label>
                <input type="text" name="idir" id="idir" class="form-control" value="<?= LOGGED_IN_IDIR ?>" required disabled>
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name" required>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Enter Title" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter Phone Number" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="full.name@gov.bc.ca" required>
            </div>

            <div class="mb-3">
                <label for="Pronouns" class="form-label">Personal Pronouns</label>
                <input type="text" name="Pronouns" id="Pronouns" class="form-control" placeholder="e.g. She/Hers/They" required>
            </div>

            <div class="text-center">
                <input type="submit" class="btn btn-primary w-100" value="Email Request">
            </div>
        </form>

	</div>
</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelector(".newuser").addEventListener("submit", function (event) {
        event.preventDefault(); // Prevent actual form submission

        // Collect form data
        // const role = document.getElementById("role").value;
        const idir = document.getElementById("idir").value;
        const name = document.getElementById("name").value;
        const title = document.getElementById("title").value;
        const phone = document.getElementById("phone").value;
        const email = document.getElementById("email").value;
        const pronouns = document.getElementById("Pronouns").value;

        // Construct email content
        const subject = `New LSApp user request: ${name}`;
        const body = `
        A new user requests access to LSApp with the following details:

        - IDIR: ${idir}
        - Name: ${name}
        - Title: ${title}
        - Phone: ${phone}
        - Email: ${email}
        - Pronouns: ${pronouns}

        Please review and take necessary action.
		
		https://gww.bcpublicservice.gov.bc.ca/lsapp/
        `;

        // Create mailto link
        const mailtoLink = `mailto:Learning.Centre.Admin@gov.bc.ca?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;

        // Open the default email client
        window.location.href = mailtoLink;
    });
});
</script>