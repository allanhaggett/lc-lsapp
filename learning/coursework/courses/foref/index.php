<?php
/**
 * Welcome to a PSA Coursework Landing Page.
 * 
 * This file has been copied from the template page and 
 * uses file includes to render the header, footer, and 
 * depending on your content, other common elements like
 * easier video embedding, or messages around different
 * meeting platforms.
 * 
 */
$includespath = $_SERVER['DOCUMENT_ROOT'] . '\learning\coursework\includes\\';
$header = $includespath . '\header.php';
include($header) 
?>


<div class="container mt-3">
<div class="row justify-content-md-center">

<div class="col-md-6">


<?php include('../../includes/zoom-collection-notice.php') ?>

<h2>Key Terms</h2>
<dl>
  <dt class="bg-light">Circle of Courage:</dt>
  <dd class="bg-light mb-3">The
      <a href="https://starr.org/circle-of-courage/" 
          target="_blank" 
          rel="noopener">
            Circle of Courage
      </a>
      is a model of positive youth development from the 
      book Reclaiming Youth at Risk, co-authored by Dr. Martin Brokenleg, 
      Dr. Larry Brendtro, and Dr. Steve Van Bockern. The model is based on 
      the idea that each human being has four essential needs: belonging, 
      mastery, generosity and independence.  </dd>
  <dt>Frame of Reference:</dt>
    <dd class="mb-3">In physics, a frame of reference is a set of axes which enable an 
        observer to measure the position and motion of all bodies in some 
        system relative to the reference frame.</dd>
  <dt class="bg-light">Cultural Frame of Reference:</dt>
    <dd class="bg-light mb-3">Cultural frame of reference is a complex set of assumptions and 
    attitudes which we use to filter perceptions to create meaning. The 
    frame can include beliefs, schemas, preferences, values, culture and 
    other identity factors which we bias our understanding and judgment. We
     often make judgments and assumptions according to our cultural frame of 
     reference. When something fails to satisfy these assumptions, we form a 
     negative impression</dd>
  <dt>Social Location:</dt>
    <dd class="mb-3">The groups that we belong to or identity with includes, race, religion,
     age, family, culture and other characteristics.  Some identify factors 
     are considered "subjugated" or disadvantaged while some are considered 
     "privileged" or advantageous.</dd>
  <dt class="bg-light"><a href="https://youtu.be/tqz7UcCgbLA" 
          target="_blank" 
          rel="noopener">
            Perspective Taking:
      </a>
  </dt>	
    <dd class="bg-light mb-3">Perspective taking is the conscious action of seeing the world from 
    another person or persons point of view.  It is one of the strongest 
    methods to demonstrate empathy and broaden your personal view. </dd>
  <dt>Life Chances:</dt>
    <dd class="mb-3">The chances and opportunities an individual has to improve their life 
    based on their social location.</dd>

  <dt class="bg-light">Words Matter:</dt>
    <dd class="bg-light">A 28-page guideline on using inclusive language in the workplace 
    covering culture & ancestry, political belief, religion, marital or family 
    status, disability, sexual orientation & gender identity or expression, 
    and age.</dd>
</dl>
	
</div>
<div class="col-md-6">

<h2>Pre-work</h2>
<h3>Part 1</h3>
<p>Learn more about the Circle of Courage by watching the video 
First Nations Principles of Learning:</p>
<iframe 
        width="100%" 
        height="300" 
        src="https://www.youtube.com/embed/0PgrfCVCt_A" 
        title="First Nations Principles of Learning" 
        frameborder="0" 
        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
        allowfullscreen>
</iframe>
<div>
  <small>
    <a href="https://www.youtube.com/watch?v=0PgrfCVCt_A"
    target="_blank" 
    rel="noopener">
      https://www.youtube.com/watch?v=0PgrfCVCt_A
    </a>
  </small>
</div>
<div class="alert alert-success">
<p>For a discussion during the 
Frame of Reference workshop, <strong>write down what resonated for you</strong> in this video, 
and why it resonated for you.</p>
</div>
<h3>Part 2</h3>
<p>Different identity factors make up our social location and our social location 
makes up or informs the lens in which we see the world which is our 
frame of reference.</p>
<div class="mb-3">
<a href="img/frame-reference-flow-chart.png"
    target="_blank" 
    rel="noopener">
  <img src="img/frame-reference-flow-chart.png" alt="Frame of Reference flowchart">
</a>
</div>
<div class="alert alert-success">
<p><strong>What are some of your identity factors?</strong>  Make a list and be prepared to share 
with others what some of those identity factors are. </p>
</div>
<p><strong>Here are some examples</strong>:</p>
<div class="mb-5">
<a href="img/frame-reference-word-cloud.png" 
    target="_blank" 
    rel="noopener">
      <img src="img/frame-reference-word-cloud.png" 
            alt="word cloud including the largest words of mother, feminine, settler, friend, father, friend">
</a>
</div> 

</div>
</div>
</div>


<?php 
$footer = $includespath . '\footer.php';
include($footer); 
?>