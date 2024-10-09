


<script src="/lsapp/js/jquery-3.4.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script src="/lsapp/js/list.min.js"></script>
<script src="/lsapp/js/rome.min.js"></script>
<script src="/lsapp/js/jquery.csv.min.js"></script>

<script>
$('document').ready(function(){

	$('.del').on('click',function(e){
		if (confirm('Are you sure? You cannot undo this action.')) {
			
		} else {
			e.preventDefault();
			return false;
		}
	});

	$('.showcourse').on('click',function(e){
		e.preventDefault();
		//alert('hey');
		$(this).next('div.course-details').toggle();
	});
	
});
</script>

