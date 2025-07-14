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
 * These pages are designed to work within a directory that
 * is named accordingly to the courses short code within LSAPP
 * and should be named index.php e.g. 
 * https://gww.bcpublicservice.gov.bc.ca/learning/coursework/courses/cac101/
 * 
 * Note: do not link directly to the index.php file; use the above example
 * 
 */

$path = $_SERVER['DOCUMENT_ROOT'] . '\lsapp\inc\lsapp.php';
require($path);
$getfromdir = getcwd();
$code = explode('\learning\coursework\courses\\',$getfromdir);
$courseid = $code[1];
$deets = getCourseByAbbreviation($courseid);

$includespath = $_SERVER['DOCUMENT_ROOT'] . '\learning\coursework\includes\\';
$header = $includespath . '\header.php';
include($header);

?>

<!-- 

    - You need to manually copy assets into the approrpiate
        directory structure within the folder that this file
        resides in.
    - You may leverage numerous existing common component 
        includes, such as:
        - Upcoming class info (currently just a launch button)
        - Zoom Tips
        - Video Embedding
        - More coming soon
-->

    <!-- intro, course description and at a glance -->
    <div class="container-lg p-5">
        <section id="intro">
            <div class="row">
                <div class="col-lg-8">
                    <h2>Course description</h2>
                    <p class="lead">
                        <?= $deets[16] ?>
                    </p>
                    <ul>
                        <li>Learning Outcomes?</li>
                        <li>Take-away skills?</li>
                        <li>Who should take?</li>
                        <li>Next session?</li>
                    </ul>
                    <a class="btn btn-success" 
                        href="https://learning.gov.bc.ca/psc/CHIPSPLM_6/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_FND_LRN_FL.GBL?Page=LM_FND_LRN_RSLT_FL&Action=U&KWRD=%22<?= $deets[4] ?>%22" 
                        target="_blank" 
                        title="View this course code in the PSA Learning System">View in PSA Learning System </a>
                    <?php if(canAccess()): ?>
                    <a class="btn btn-secondary" 
                        href="https://gww.bcpublicservice.gov.bc.ca/lsapp/course.php?courseid=<?= $deets[0] ?>"
                        target="_blank"
                        title="Launch LSApp course page"
                    >
                        View in LSApp
                    </a>
                    <?php endif ?>
                    <?php
$upcoming = $includespath . '\upcoming.php';
include($upcoming);
?>

<?php
$zoomtips = $includespath . '\zoom-tips.php';
include($zoomtips);
?>

<?php
$video_title = 'An Example\'s Video Title';
$video_description = 'Provide a brief description of the video contents here.';
$video_file_name = 'videos/video-filename-here.mp4';
$running_time = '10m 46s';
$captions_file = 'videos/video-filename-for-captions.vtt';
$speakers = [
['Tony Pesklevits', 'Strategic Adviser, BC Wildfire Service'],
['Tamara Leonard-Vail', 'Program Lead, Managing in the BCPS']
];
$video_embed = $includespath . '\video-embed.php';
include($video_embed);
?>
                </div>
                <div class="col-lg-4 my-3 my-lg-0">
                <?php 
                $at_a_glance = $includespath . '\at-a-glance.php';
                include($at_a_glance); 
                ?>
                </div>
            </div>
        </section>
        <!-- session details tabs/accordions -->
        <section id="details">
            <!-- todo tab styling -->
            <!-- todo accordions at smaller screen sizes -->
            <div class="accordion d-block d-md-none mt-3" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne"> Before the Session </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo"> During the Session </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree"> After the Session </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse plugin adds the appropriate classes that we use to style each element. These classes control the overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-none d-md-block">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="detailsTabs" role="tablist">
                    <li class="nav-item me-1" role="presentation">
                        <button class="nav-link active" id="before-tab" data-bs-toggle="tab" data-bs-target="#before" type="button" role="tab" aria-controls="before" aria-selected="true">Before the Session</button>
                    </li>
                    <li class="nav-item me-1" role="presentation">
                        <button class="nav-link" id="during-tab" data-bs-toggle="tab" data-bs-target="#during" type="button" role="tab" aria-controls="during" aria-selected="false">During the Session</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="after-tab" data-bs-toggle="tab" data-bs-target="#after" type="button" role="tab" aria-controls="after" aria-selected="false">After the Session</button>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content p-3">
                    <div class="tab-pane active" id="before" role="tabpanel" aria-labelledby="before-tab">
                        <h2>Before the Session</h2>
                        <div class="row">
                            <div class="col-lg-8">
                                <h3>Prerequisites</h3>
                                <p>To prepare for this course, please complete the following before you enrol in PM 200.</p>
                                <h4><a href="https://learning.gov.bc.ca/psc/CHIPSPLM/EMPLOYEE/ELM/c/LM_OD_EMPLOYEE_FL.LM_FND_LRN_FL.GBL?Page=LM_FND_LRN_RSLT_FL&Action=U&KWRD=ITEM-416" target="_blank">PM 101: Project Foundations</a> (eLearning)</h4>
                                <p>Please review the course prior to our first session. We will be building on core project management concepts and processes introduced in this eLearning module.</p>
                                <h3>Preparation</h3>
                                <ul>
                                    <li> Required work (with time needed to complete) </li>
                                    <li> Document links/embedded videos etc. </li>
                                    <li> instructions </li>
                                    <li>Optional work/resource</li>
                                </ul>
                                <h3>Webinar Readiness/Virtual Etiquette</h3>
                                <ul>
                                    <li>Tech info/tips</li>
                                    <li>Tech requirements - camera, audio, multiple screens, mobile device etc.</li>
                                    <li>Online learning expectations (also during session category?)</li>
                                    <li>What to bring</li>
                                    <li>Accommodations/questions </li>
                                    <li>What to expect in the session: format</li>
                                </ul>
                            </div>
                            <div class="col-lg-4 mt-3 mt-lg-0">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h3 class="card-title">Accommodation</h3>
                                        <p>Please let us know if you require course materials in alternate formats and if you require other accommodation supports to successfully attend a course. We will provide reasonable accommodation if possible. </p>
                                        <p><a href="#" class="text-white fw-bold">Request accommodation</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="during" role="tabpanel" aria-labelledby="during-tab">
                        <h2>During the Session</h2>
                        <h3>How to launch the session</h3>
                        <h3>Online learning expectations reminder</h3>
                        <h3>Course Materials</h3>
                        <h3>Help during the session</h3>
                        <h3>Self-care</h3>
                    </div>
                    <div class="tab-pane" id="after" role="tabpanel" aria-labelledby="after-tab">
                        <h2>After the Session</h2>
                        <h3>Completion requirements</h3>
                        <h3>Homework/Practice</h3>
                        <h3>Next Steps</h3>
                        <ul>
                            <li>Courses</li>
                            <li>CoP</li>
                            <li>Other resources</li>
                        </ul>
                        <h3>Feedback/Assistance</h3>
                        <ul>
                            <li>Feedback survey/other ways to give feedback</li>
                            <li>Care/support/assistance needed?</li>
                        </ul>
                    </div>
                </div>
            </div>
    </div>
    </section>



<?php 
$footer = $includespath . '\footer.php';
include($footer); 
?>