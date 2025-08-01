<?php require('inc/lsapp.php') ?>

<?php if(canACcess()): ?>

<?php getHeader() ?>

<title>Snowplow Analytics</title>
  <style>

    button.copy-btn {
      position: absolute;
      top: 0.5em;
      right: 0.5em;
      cursor: pointer;
      font-size: 0.8em;
    }
  </style>

<?php getScripts() ?>
<?php getNavigation() ?>

<div class="container">
<div class="row justify-content-md-center mb-3">
<div class="col-md-8">

	<h1>Snowplow Analytics</h1>
	<p>More formally known as <abbr title="Government Digital Experience">G.D.X.</abbr> Dashboards, Snowplow Analytics
		is a front-end, javascript-based analytics platform that allows us to track <strong>anonymous</strong> usage data for our websites. We can gather and view
		statistics on how many people are visiting what pages, when, and from where; along with detailed information about the devices being used. We <strong>cannot</strong>
		tie this information to any particular user or demographic group.</p>

	<p><a href="https://intranet.qa.gov.bc.ca/analytics" target="_blank">GDX Analytics Dashboards</a> is the primary way to access our dashboards. Access 
		documentation and training resources on that website. 
		<strong><a href="https://citz-gdx.atlassian.net/servicedesk/customer/portal/1/group/5" target="_blank">You have to request access</a></strong> 
		to the dashboards to view them. Talk to your supervisor if you think you need access and don't have it before making a request.</p>

	<p>For Kepler, Snowplow is NOT implemented at the server level, but we do have dashboards set up for both sides/domains of it:</p>
	<ul>
		<li><a href="https://gww.bcpublicservice.gov.bc.ca/" target="_blank">https://gww.bcpublicservice.gov.bc.ca/</a> ("normal" IDIR-protected side)
		<li><a href="https://learn.bcpublicservice.gov.bc.ca/" target="_blank">https://learn.bcpublicservice.gov.bc.ca/</a> (Public, "Non-IDIR Kepler" or <abbr title="Non-IDIR Kepler">NIK</abbr>)
	</ul>

	<p>This means that, if you want a page to be registered and tracked for sites/pages on Kepler (either side), you must <strong>manually</strong> place the javascript snippet below 
		into the source code of each page. If your web site is static HTML, you need to update every page with the code.
		If you're using a dynamic scripting language like PHP, you should be able to use an include to add it to your pages.</p>

	<p>Snowplow IS enabled at the server level in our PSA Moodle. <strong>You do NOT need to include the javascript snippet in your PSA Moodle courses.</strong></p>

	<h2>The Code Snippet</h2>
	<p>Place the following javascript on your HTML pages, either in the &lt;head&gt; section, or (Allan recommends) place it at the very 
		bottom of the source code, just above the closing &lt;/body&gt; tag (for performance reasons).</p>
	<div style="position: relative;">
	<button class="btn btn-success copy-btn" onclick="copyCode()">Copy</button>
<pre class="bg-dark-subtle p-5 rounded-3"><code id="codeblock">&lt;script&gt;
;(function(p,l,o,w,i,n,g){if(!p[i]){p.GlobalSnowplowNamespace=p.GlobalSnowplowNamespace||[];
p.GlobalSnowplowNamespace.push(i);p[i]=function(){(p[i].q=p[i].q||[]).push(arguments)
};p[i].q=p[i].q||[];n=l.createElement(o);g=l.getElementsByTagName(o)[0];n.async=1;
n.src=w;g.parentNode.insertBefore(n,g)}}(window,document,"script","https://www2.gov.bc.ca/StaticWebResources/static/sp/sp-2-14-0.js","snowplow"));

var collector = 'spt.apps.gov.bc.ca';
window.snowplow('newTracker','rt',collector, {
	appId: 'Snowplow_standalone_PSA',
	cookieLifetime: 86400 * 548,
	platform: 'web',
	post: true,
	forceSecureTracker: true,
	contexts: {
	webPage: true,
	performanceTiming: true
	}
});
window.snowplow('enableActivityTracking', 30, 30);
window.snowplow('enableLinkClickTracking');
window.snowplow('trackPageView');
&lt;/script&gt;</code></pre>
	</div>

	<script>
	function copyCode() {
		const code = document.getElementById("codeblock").innerText;
		navigator.clipboard.writeText(code).then(() => {
		alert("Copied to clipboard!");
		}, () => {
		alert("Failed to copy.");
		});
	}
	</script>


</div>
</div>
</div>



<?php require('templates/javascript.php') ?>

<?php require('templates/footer.php') ?>

<?php else: ?>


<?php require('templates/noaccess.php') ?>

<?php endif ?>