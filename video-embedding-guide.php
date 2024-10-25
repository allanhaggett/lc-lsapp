<?php 
require('inc/lsapp.php');
$idir = LOGGED_IN_IDIR;
$person = getPerson($idir);
$keplerpeople = getKeplerPeople();
$istorepeople = getiStoreDesignees();
?>

<?php getHeader() ?>

<title>Video Embedding Guide</title>

<?php getScripts() ?>

<?php getNavigation() ?>

<div class="container">
<div class="row justify-content-md-center mb-3">

<div class="col-md-6">

<h1>Video Embedding Guide</h1>
<?php
$directory = '/';  // You can specify any directory
$free_space = disk_free_space($directory);

echo '<div class="alert alert-success">Available disk space: ' . formatSizeUnits($free_space) . '</div>';

function formatSizeUnits($bytes) {
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}
?>
<p>Our document server called "<a href="/lsapp/kepler.php">Kepler</a>" is used to host video files.</p>
<p>Kepler is split into two partitions, one is IDIR protected, the other is open to the public:</p>
<ul>
    <li>IDIR-protected: https://gww.bcpublicservice.gov.bc.ca/learning/
    <li><abbr title="Non-IDIR Kepler">NIK</abbr> (Non-IDIR Kepler) is fully public:
 https://learn.bcpublicservice.gov.bc.ca
</ul>
 <p>In order to host a video on Kepler, you'll require a few things. This is more work than publishing
    to other platforms, but it's worth the effort.</p>
<h2>You'll need</h2>
<ol>
    <li>Video file:
        <ul>
            <li>Ensure an appropriate mix of AV quality and file size.
            <li>Inquire for assistance in transcoding/compressing your files.
            <li>If your video file is greater than 500 MB in size, please take steps
                to compress it. If you are downloading from MS Stream, use the 480p download.
                A lot depends on the context, but an hour-long video shouldn't be much larger than 
                500MB.
        </ul>
    <li>Caption file. The "VTT" format is most widely recognized. There are other formats, but prefer VTT.
    <li>Poster frame. A small JPEG image that is a still from the video (if not a promotional image). 
        File size should not exceed 100KB; should be about 480p dimensions.
    <li>HTML file to embed into. Please do not link directly to a video file. We are required to support
        captions on all videos we publish, and the only way to do that in this context is to embed the 
        video file together with the caption file within an HTML file (or other web page i.e. Moodle; Wordpress).
    <li>A content review to determine if public access is an option.
</ol>
<h2>Steps</h2>
<ol>
    <li>Upload your video and caption files to an appropriate folder depending on your use-case.
        <li>View the file(s) directly in your browser so you know you've got the right link(s)
    <li>Come here and paste your links into the form below and get your embed code.
    <li>Paste the embed code into an HTML page (hosted on Kepler/NIK or elsewhere).
    <li>Profit.
</ol>


</div>
<div class="col-md-6">
<h2>Embed Code Tool</h2>
<div class="card p-4 shadow-sm">
    <form id="videoForm">
        <div class="mb-3">
            <label for="videoUrl" class="form-label">Video URL</label>
            <input type="url" class="form-control" id="videoUrl" name="videoUrl" placeholder="https://example.com/video.mp4" required>
        </div>

        <div class="mb-3">
            <label for="posterUrl" class="form-label">Poster URL</label>
            <input type="url" class="form-control" id="posterUrl" name="posterUrl" placeholder="https://example.com/poster.jpg">
        </div>

        <div class="mb-3">
            <label for="captionUrl" class="form-label">Caption URL</label>
            <input type="url" class="form-control" id="captionUrl" name="captionUrl" placeholder="https://example.com/captions.vtt">
        </div>

        <div class="mb-3">
            <label for="embedCode" class="form-label">Embed Code (will populate when you click the button below)</label>
            <textarea class="form-control" id="embedCode" name="embedCode" rows="6" readonly></textarea>
        </div>

        <div class="text-center">
            <button type="button" class="btn btn-primary" onclick="generateEmbedCode()">Generate Embed Code</button>
        </div>
    </form>
</div>

<script>
    function generateEmbedCode() {
        const videoUrl = document.getElementById("videoUrl").value;
        const posterUrl = document.getElementById("posterUrl").value;
        const captionUrl = document.getElementById("captionUrl").value;

        let embedCode = `<video controls${posterUrl ? ` poster="${posterUrl}"` : ''}>\n`;
        embedCode += `  <source src="${videoUrl}" type="video/mp4">\n`;

        if (captionUrl) {
            embedCode += `  <track src="${captionUrl}" kind="captions" srclang="en" label="English">\n`;
        }

        embedCode += `  Your browser does not support the video tag.\n</video>`;

        document.getElementById("embedCode").value = embedCode;
    }
</script>


</div>
</div>
</div>


<?php require('templates/javascript.php') ?>
<?php require('templates/footer.php') ?>
	