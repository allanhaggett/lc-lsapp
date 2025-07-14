<?php 
/**
 * Easy Video Embed with Captions
 * When you include this file, you need to provide the following
 * variable values along with the include file name.
 */

// $video_title = 'One Public Servant\'s View on Coaching';
// $video_description = 'Join Tamara Leonard-Vail, Program Lead for Managing in 
// the BCPS and Tony Pesklevits, Strategic Advisor with the BC Wildfire Service 
// as they talk about various aspects of the Coaching Approach to conversations.';
// $video_file_name = 'coaching-approach-one-public-servant-20201229.mp4';
// $running_time = '10m 46s';
// $captions_file = 'coaching-approach-one-public-servant-captions.vtt';
// 
// $speakers = [
//     ['Tony Pesklevits', 'Strategic Adviser, BC Wildfire Service'],
//     ['Tamara Leonard-Vail', 'Program Lead, Managing in the BCPS']
// ];

?>
    <!-- START VIDEO EMBED -->
    <div class="badge badge-primary">WATCH NOW</div>
    <h1><?= $video_title ?></h1>
    <div class="my-3"><?= $video_description ?></div>
    <video id="video1" width="100%" controls class="shadow-lg">
        <source src="<?= $video_file_name ?>" 
                type="video/mp4">
        <track label="English" kind="subtitles" srclang="en" src="<?= $captions_file ?>" default>
            Your browser does not support the video tag.
    </video>

    <div class="alert alert-light bg-light mt-3">
        <div>Speakers:</div>
        <?php foreach($speakers as $s): ?>
        <div class="mb-2"><strong><?= $s[0] ?></strong>, <?= $s[1] ?></div>
        <?php endforeach ?>
        <dl>
            <dt>Running time</dt>
                <dd><?= $running_time ?></dd>
            <dt>Closed Captions</dt>
                <dd>
                    <a href="<?= $captions_file ?>">Download</a>
                </dd>

        </dl>
    </div>
    <!-- END VIDEO EMBED -->
