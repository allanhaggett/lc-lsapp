<?php require('inc/lsapp.php') ?>
<?php getHeader() ?>
<title>The Learning Centre | PSA | Learning Support Application</title>
<?php getScripts() ?>
<?php getNavigation() ?>


<?php if(isAdmin()): ?>
<div class="container">
<div class="row justify-content-md-center">
<div class="col-md-12">

<div id="mapthat" style="height: 480px"></div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
     integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
     crossorigin=""/>

      <!-- Make sure you put this AFTER Leaflet's CSS -->
 <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
     integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
     crossorigin=""></script>
<script>
var map = L.map('mapthat').setView([51, -123], 6);
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 100,
    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
}).addTo(map);
var marker = L.marker([48.407326,-123.329773]).addTo(map);


</script>



</div>
</div>
</div>
<?php else: // if canAccess() ?>
<?php include('templates/noaccess.php') ?>
<?php endif ?>


<?php require('templates/javascript.php') ?>


<?php require('templates/footer.php'); ?>