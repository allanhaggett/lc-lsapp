<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta name="author" content="Allan Haggett <allan.haggett@gov.bc.ca>">
<meta name="description" content="Coursework pages for Learning Centre courses.">
<title>PSA Coursework Landing Pages</title>
<link rel="stylesheet" 
      href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" 
      integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" 
      crossorigin="anonymous">
      
<style>
/** You can also use .img-fluid on images, but we almost never want a different
    behaviour, so set responsive images (and iframes) globally */
iframe,
img {
  height: auto;
  max-width: 100%;
}
</style>
</head>
<body>
<nav class="site-header py-3 text-white" style="background-color: #003366;">
  <div class="container d-flex justify-content-between">
    <span class="navbar-brand d-inline-block mt-2" style="display: inline-block; font-size: 1.6em; padding-top: 15px">PSA Coursework Landing Pages</span>
    <img alt="Where Ideas Work logo" 
          class="d-none d-md-block" 
          src="https://learn.bcpublicservice.gov.bc.ca/common-components/where-ideas-work-whitetext.svg" 
          width="300">
  </div>
</nav>

<div class="container-fluid bg-light">
<div class="row justify-content-md-center">
<div class="col-md-6">

<div class="my-5 p-5 bg-white rounded-3 shadow-lg" id="highlight">
  <h1>PSA Coursework Landing Pages</h1>
  <p>PSA Coursework Landing Pages for Learning Centre-managed courses. 
    A course will have a page or pages, managed by the templates
    set out here.
  </p>
  <p>This system is based on a template file in this directory named
    coursework-index-template.php which includes a common header and footer.
    Besides establishing a common template for the look-and-feel, the header 
    file also automatically pulls course information from 
    <a href="https://gww.bcpublicservice.gov.bc.ca/lsapp/">LSApp</a> 
    to show the course title, description, and link to the PSA Learning System. 
    <em>If you have access to LSApp</em>, it will also provide a link to the 
    course page there.
  </p>
  <p>A developer with a new course to create could access Kepler and
    navigate to this folder. Starting by creating a folder witnin 
    the 'courses' folder named after the course shortcode (as named 
    within LSApp), they would then copy the coursework-index-template.php
    file into that new directory and start developing the content from 
    that file.
  </p> 
  <p>As well as providing a header and footer, there are several modules that 
    a developer can add to the page with a few lines of copypasta from below.
  </p>
  <div class="alert alert-warning">
    Special Note: if you are migrating a course from an older page here on Kepler
    the URL to the page will obviously change, so please <strong>don't forget</strong>
    to update course within the PSA Learning System with the new link!!
  </div>


  <h2>Current Courses</h2>
  <ul class="list-group mb-3">
  <?php $dir = new DirectoryIterator('courses'); ?>
  <?php foreach ($dir as $fileinfo) : ?>
  <?php if ($fileinfo->isDir() && !$fileinfo->isDot()) : ?>
  <li class="list-group-item">
    <a href="courses/<?= $fileinfo->getFilename(); ?>">
      <?= $fileinfo->getFilename(); ?>
    </a>
  </li>
  <?php endif ?>
  <?php endforeach ?>

  
  </ul>
  <h2>Available Modules</h2>
  <p>When you compose a Coursework Landing Page for a course, you 
    can choose from several different components to include in the 
    page that provide common functionality/messaging across courses.
  </p>
  <h3>Upcoming Class Launch Webinar Link</h3>
  <p>Show the webinar link to for the next scheduled class,
    but only show it on the day of that class (a notice that link will be shown
    on the day is present until the day itself):</p>
  <div class="alert alert-secondary mt-3">
    <div>
      &lt;?php<br>
      $upcoming = $includespath . '\upcoming.php';<br>
      include($upcoming); <br>
      ?&gt;
    </div>
  </div>

  <h3>Zoom Tips</h3>
  <p>Show Zoom tips button and collection notice:</p>
  <div class="alert alert-secondary mt-3">
    <div>
      &lt;?php<br>
      $zoomtips = $includespath . '\zoom-tips.php';<br>
      include($zoomtips); <br>
      ?&gt;
    </div>
  </div>

  <h3>Embed a Video <small><em>(with captions)</em></small></h3>
  <p>Provide some details and have a video file embedded into the page;
    include the VTT captions file as well, and have that just work:</p>
  <div class="alert alert-secondary mt-3">
    <div>
      &lt;?php<br>
      $video_title = 'An Example\'s Video Title';<br>
      $video_description = 'Provide a brief description of the video contents here.';<br>
      $video_file_name = 'videos/video-filename-here.mp4';<br>
      $running_time = '10m 46s';<br>
      $captions_file = 'videos/video-filename-for-captions.vtt';<br>
      $speakers = [<br>
          ['Tony Pesklevits', 'Strategic Adviser, BC Wildfire Service'],<br>
          ['Tamara Leonard-Vail', 'Program Lead, Managing in the BCPS']<br>
      ];<br>
      $video_embed = $includespath . '\video-embed.php';<br>
      include($video_embed); <br>
      ?&gt;
    </div>
  </div>

</div> <!-- /#highlight -->
 


</div>
</div>
</div>

<div class="container-fluid bg-light">
<div class="row justify-content-md-center">
<div class="col-md-6">
<div class="bg-white my-5 p-5 text-center rounded-lg shadow-sm">
<img alt="Where Ideas Work" 
      src="https://learn.bcpublicservice.gov.bc.ca/common-components/where-ideas-work.svg" 
      width="420">
</div>
</div>
<div class="col-md-6">
<div class="bg-white my-5 p-5 text-center rounded-lg shadow-sm">
Brought to you by:<br>
<a href="https://learningcentre.gww.gov.bc.ca/"
    target="_blank" 
    rel="noopener">
      <img alt="Brought to you by the Learning Centre" 
            height="100" 
            src="https://learn.bcpublicservice.gov.bc.ca/common-components/learning-centre-logo-wordmark.svg" 
            width="300">
</a>
</div>
</div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" 
          integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" 
          crossorigin="anonymous"></script>
<script>window.jQuery || document.write('<script src="/docs/4.6/assets/js/vendor/jquery.slim.min.js"><\/script>')</script>
<script src="/docs/4.6/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" 
        crossorigin="anonymous"></script>


</body>
</html>
