<?php 
require('inc/lsapp.php');

// Get the full list of partners
$partners = getPartners();

?>
<?php getHeader() ?>
<title>Learning Hub Partners</title>
<?php getScripts() ?>
<?php getNavigation() ?>
<?php if(canAccess()): ?>
<div class="container">
<div class="row justify-content-md-center mb-3">

<div class="col-md-8">
<h1>Learning Hub Partners <span class="badge bg-light-subtle"><?php echo count($partners) ?></span></h1>

<div id="partnerlist">
<input class="search form-control  mb-3" placeholder="search">
<div class="list">
<?php foreach($partners as $p): ?>
	<details class="partner p-2 mb-1 bg-secondary-subtle rounded-3">
		<summary class="fw-bold">
			<div class="float-end"><a href="learning-hub-partner-manage.php?id=<?= $p->id ?>">Edit</a></div>
			<?= $p->name ?>
		</summary>
		<div class="p-3 pt-0 mt-2">
			<div><?= $p->description ?></div>
			<div class="p-3 mt-3 bg-light-subtle rounded-3">Admin: <a href="mailto:<?= $p->admin_email ?>"><?= $p->admin_name ?></a></div>
			<?php $pcourses = getCoursesByPartnerName($p->name) ?>
			<details class="my-3">
				<summary class="mb-3"><?= count($pcourses) ?> Courses</summary>
				<?php foreach($pcourses as $c): ?>
					<div class="mb-2 p-2 bg-light-subtle rounded-3">
						<a href="/lsapp/course.php?courseid=<?= $c[0] ?>"><?= $c[2] ?></a>
					</div>
				<?php endforeach ?>
			</details>
		</div>
	</details>

<?php endforeach ?>
</div>
</div> <!-- /#partnerlist -->
</div> <!-- /.col -->
</div> <!-- /.row -->

<?php else: ?>

<?php require('templates/noaccess.php') ?>

<?php endif ?>

<?php require('templates/javascript.php') ?>

<script>

document.querySelector('.search').addEventListener('input', function (e) {
  const query = e.target.value.toLowerCase();
  document.querySelectorAll('.partner').forEach(card => {
    const text = card.innerText.toLowerCase();
    if (text.includes(query)) {
      card.style.display = 'block';
    } else {
      card.style.display = 'none';
    }
  });
});

</script>
<?php include('templates/footer.php') ?>