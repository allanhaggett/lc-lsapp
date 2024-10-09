	$('.facilitatorclaim').on('submit',function(e){
		e.preventDefault();
		var form = $(this);
		var url = form.attr('action');
		var action = e.originalEvent.explicitOriginalTarget.value;
		
		if(action == 'x') {
			var confirmessage = 'This will remove the facilitator of this class. Proceed?';
			var formdata = form.serialize() + '&unclaim=unclaim';
		} else {
			var confirmessage = 'This will assign you as a facilitator of this class. Proceed?';
			var formdata = form.serialize();
		}
		
		if (confirm(confirmessage)) {

			
			$.ajax({
				type: "GET",
				url: url,
				data: formdata,
				success: function(data)
				{
					console.log(data);
					if(data == 'Unknown') {
						$('.claimed').html(' ');
						$('.facclaim').val('Claim');
						$('.unclaimed').html('Unknown');
					} else {
						$('.unclaimed').html(' ');
						$('.facclaim').val('x');
						$('.claimed').html('<a href="person.php?idir='+data+'">'+data+'</a>');
						
					}
				},
				statusCode: 
				{
					403: function() {
						form.after('<div class="alert alert-warning">You must be logged in.</div>');
					}
				}});
			e.preventDefault();	
			
		} else {
			e.preventDefault();
			return false;
		}


	});