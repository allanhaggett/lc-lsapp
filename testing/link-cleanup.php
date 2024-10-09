<?php 
require('../inc/lsapp.php');
?>

<?php if(canAccess()): ?>
<?php

opcache_reset();
?>
<?php getHeader() ?>

<title>URL Clean-up</title>

<?php getScripts() ?>


<body class="bg-body-tertiary">
<?php getNavigation() ?>


<div class="container">
  <div class="row justify-content-md-center mb-3">
    <div class="col-lg-12">
      <div class="p-2 mb-1 bg-light-subtle border border-secondary-subtle rounded-3">
        <h1>Link</h1>
        <form class="row row-cols-lg-auto g-3 align-items-center">
            <label for="input-url" class="col-sm-2 col-form-label">Input</label>
            <div class="col-lg-8">
                <input id="input-url" name="input-url" class="form-control" placeholder="Enter link" />
            </div>
            <div class="col-sm-2">
                <button id="link-submit" type="button" class="btn btn-primary" title="Submit">Go</button>
            </div>
        </form>
        <div class="row row-cols-lg-auto g-3 mt-2 align-items-center">
            <label for="output-url" class="col-sm-2 col-form-label">Output</label>
            <div class="col-lg-8">
                <input id="output-url" name="output-url" class="form-control" placeholder="Output" value="" />
            </div>    
        </div>

        <?php
            // Decoded: https://teams.microsoft.com/l/meetup-join/19:meeting_MDhkMmUwOGMtZmI3YS00NGU2LWIwMTktZWU2NjFlOTViMTFi@thread.v2/0?context={"Tid":"6fdb5200-3d0d-4a8a-b036-d3685e359adc","Oid":"a82778e0-8d0b-4490-ba5d-15647f55ff6b"}
            $testLink = "https://teams.microsoft.com/l/meetup-join/19%3ameeting_MDhkMmUwOGMtZmI3YS00NGU2LWIwMTktZWU2NjFlOTViMTFi%40thread.v2/0?context=%7b%22Tid%22%3a%226fdb5200-3d0d-4a8a-b036-d3685e359adc%22%2c%22Oid%22%3a%22a82778e0-8d0b-4490-ba5d-15647f55ff6b%22%7d";
            
            $cleanedUrl = parse_url(urldecode($testLink)); // returns array of link components, including query
            $jsonObj = str_replace("context=", "", $cleanedUrl["query"]); // remove first part of string so we're left with json object
            $jsonObjDecoded = json_decode($jsonObj); // creates stdClass object (object(stdClass)#1)
            $organizerId = $jsonObjDecoded->{'Oid'}; // isolate Oid value
            echo $organizerId; // returns "a82778e0-8d0b-4490-ba5d-15647f55ff6b"
            
        ?>


      </div>
    </div>
  </div>
</div>

<!-- Example link for testing -->
<!-- https://can01.safelinks.protection.outlook.com/ap/t-59584e83/?url=https%3A%2F%2Fteams.microsoft.com%2Fl%2Fmeetup-join%2F19%253ameeting_NjgzODRiYmYtOTU1NC00MmQ5LTliZTgtYTgxZWJiY2NjMjZh%2540thread.v2%2F0%3Fcontext%3D%257b%2522Tid%2522%253a%25226fdb5200-3d0d-4a8a-b036-d3685e359adc%2522%252c%2522Oid%2522%253a%25223126fd85-12c1-4fae-b240-e6797bde7d3c%2522%257d&data=05%7C02%7CKelly.Donald%40gov.bc.ca%7Cd48a38df3cc942e5d21008dca5db190a%7C6fdb52003d0d4a8ab036d3685e359adc%7C0%7C0%7C638567607634520668%7CUnknown%7CTWFpbGZsb3d8eyJWIjoiMC4wLjAwMDAiLCJQIjoiV2luMzIiLCJBTiI6Ik1haWwiLCJXVCI6Mn0%3D%7C0%7C%7C%7C&sdata=PFizWW2wtoRSJKsRIo4YSFVWwEu7MnC5JOaaq4mfuWc%3D&reserved=0 -->







<?php else: ?>

<?php require('../templates/noaccess.php'); ?>

<?php endif ?>


<?php require('../templates/javascript.php') ?>

<script>

function processLink() {
    const inputField = document.getElementById("input-url");
    const outputField = document.getElementById("output-url");
    try {
        const url = new URL(inputField.value); 
        if (URL.canParse(url)) {
            outputField.value = url.searchParams.get('url');
        } else {
            outputField.value = 'Improper link format';
        }
    } catch(error) {
        console.log(error.message);
        outputField.value = error.message;
    }
}


$(document).ready(function(){
	
$('.claimform').on('submit',function(e){

	var form = $(this);
	var url = form.attr('action');

	//form.nextAll('.alert').first().fadeOut().remove();
	
	$.ajax({
		type: "GET",
		url: url,
		data: form.serialize(),
		success: function(data)
		{
			userlink = '<a href="person.php?idir='+data+'">'+data+'</a>';
			console.log(userlink);
			form.after(userlink);
			form.remove();
			//form.closest('tr').fadeOut().remove();
			
		},
		statusCode: 
		{
			403: function() {
				form.after('<div class="alert alert-warning">You must be logged in.</div>');
			}
		}});
	e.preventDefault();

});

// Link input
const button = document.getElementById("link-submit");
button.addEventListener("click", processLink);
	
// TODO add event listener for on-change

});
</script>
<?php include('../templates/footer.php') ?>