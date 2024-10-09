<?php 
require('inc/lsapp.php');
if(canACcess()):

require('inc/Parsedown.php');
$Parsedown = new Parsedown();

$changeid = (isset($_GET['changeid'])) ? $_GET['changeid'] : 0;

$chgs = getCourseChangesAll();
// echo '<pre>';print_r($chgs); exit;
// 0-creqID,1-CourseID,2-CourseName,3-DateRequested,4-RequestedBy,5-Status,6-CompletedBy,
// 7-CompletedDate,8-Request,9-RequestType,10-AssignedTo,11-Urgency

?>
<?php echo '<?'; ?>xml version="1.0" encoding="utf-8"<?php echo '?>' ?>
<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	
	xmlns:georss="http://www.georss.org/georss"
	xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
	>

  <channel>
	<title>Course Request Changes</title>
	<link>https://gww.bcpublicservice.gov.bc.ca/lsapp/course-changes.php</link>
  <atom:link href="https://gww.bcpublicservice.gov.bc.ca/lsapp/course-changes-feed.php" rel="self" type="application/rss+xml" />
	<description>LSApp Course Change Requests.</description>
	<lastBuildDate>Sun, 10 Sep 2023 13:19:30 +0000</lastBuildDate>
	<language>en-US</language>
  	<sy:updatePeriod>hourly</sy:updatePeriod>
	<sy:updateFrequency>1</sy:updateFrequency>




<?php foreach($chgs as $chg): ?>
<item>
    <title><?= $chg[3] ?></title>
    <link>https://gww.bcpublicservice.gov.bc.ca/lsapp/course-change-view.php?changeid=<?= $chg[0] ?></link>
    <guid isPermaLink="false">https://gww.bcpublicservice.gov.bc.ca/lsapp/course-change-view.php?changeid=<?= $chg[0] ?></guid>
    <description><![CDATA[<?= $chg[3] ?>]]></description>
    <dc:creator><![CDATA[Curator]]></dc:creator>
		<pubDate><?= date('r', strtotime($p->created)) ?></pubDate>
    <category><![CDATA[curator]]></category>
    <content:encoded>
    <![CDATA[

		<div>
			<?php 
			if($chg[5] == 'Pending'):
				$statbadge = 'primary';
			elseif($chg[5] == 'Completed'):
				$statbadge = 'success';
			endif; 
			?>
			<span class="badge text-bg-<?= $statbadge ?>"><?= h($chg[5]) ?></span>
		</div>
		<div>

		<?php 
		if($chg[11] == 'Backlog'):
			$urgencybadge = 'dark';
		elseif($chg[11] == 'NotUrgent'):
			$urgencybadge = 'warning';
		elseif($chg[11] == 'ASAP'):
			$urgencybadge = 'warning';
		elseif($chg[11] == 'HighPriority'):
			$urgencybadge = 'danger';
		endif; 
		?>
		<span class="badge text-bg-<?= $urgencybadge ?>"><?= h($chg[11]) ?></span>

		</div>
		<div>
			<?= h($chg[3]) ?>
		</div>
		<div>
			<a href="/lsapp/person.php?idir=<?= h($chg[4]) ?>"><?= h($chg[4]) ?></a>
		</div>

		<div>

			<a href="course.php?courseid=<?= h($chg[1]) ?>">
				<?= h($chg[2]) ?>
			</a>
		</div>

		<div>

			<span> <span class="badge text-bg-secondary"><?= h($chg[9]) ?></span></span>

		</div>
		<div>

		<span class="badge text-bg-secondary"><?= h($chg[10]) ?></span>
	
    ]]>
    </content:encoded>
  </item>


<?php endforeach ?>

<?php endif ?>
</channel>
</rss>
