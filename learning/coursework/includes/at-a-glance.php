<?php
#TODO pull this info from LSApp
$timecommitment = '20 hours (5 modules of 4 hours each)';
$scheduling = '2-hour webinar, plus 2 hours self-directed study per module';
$prepostreq = 'Yes';
$prereq = 'PM 101';
$delivery_method = 'Self-directed online and live webinar';
?>
<!-- #TODO fix icon list alignment. Wider callout? -->
<div class="card border-rounded bg-light">
    <div class="card-header bg-psa text-white h6">At a Glance</div>
    <div class="card-body py-3">
        <div class="card-title">
            <h3 class="fw-light"><?= $deets[2] ?></h3>
        </div>
        <ul class="list-unstyled">
            <li><i class="bi bi-clock-history me-2"></i><strong>Time Commitment</strong>: <?= $timecommitment ?></li>
            <li><i class="bi bi-calendar-week me-2"></i><strong>Scheduling</strong>: <?= $scheduling ?></li>
            <li><i class="bi bi-pencil-square me-2"></i><strong>Pre/Post Session Work Required</strong>: <?= $prepostreq ?></li>
            <li><i class="bi bi-check2-square me-2"></i><strong>Prerequisites</strong>: <?= $prereq ?></li>
            <li><i class="bi bi-person-video3 me-2"></i><strong>Format</strong>: <?= $delivery_method ?></li>
        </ul>
    </div>
</div>