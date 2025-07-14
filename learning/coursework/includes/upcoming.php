<?php
/**
 * This will look up the next scheduled class date
 * for this course and output a link to its 
 * webinar platform, and perhaps more details??
 */
$nextclass = getCourseNextClass($deets[0]);
$starttime = explode(' - ', $nextclass[10]);
$today = date('Y-m-d');
if($today == $nextclass[8]):
?>
<!-- START UPCOMING -->
On <?= goodDateShort($nextclass[8]) ?> at <?= $starttime[0] ?><br>
<a href="<?= $nextclass[15] ?>" 
    class="btn btn-lg btn-success" 
    target="_blank"
>
        Launch Webinar
</a>
<?php else: ?>
    <div class="alert alert-success">
        A link to launch the Webinar will appear here on 
        <strong><?= goodDateShort($nextclass[8]) ?></strong>
    </div>
<?php endif ?>
<!-- END UPCOMING -->