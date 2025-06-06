</div>
</div>
</div>
<div class="container">
    <div class="row justify-content-md-center">
        <div class="col-md-8">
            <div><?php echo isset($title) ? htmlspecialchars($title) : 'Default Title'; ?></div>
            <div>Brought to you by PSA Corporate Learning Branch</div>
        </div>
    </div>
</div>
<script>
    // <!-- Snowplow starts plowing - Standalone vE.2.14.0 -->
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
    window.snowplow('enableActivityTracking', 30, 30); // Ping every 30 seconds after 30 seconds
    window.snowplow('enableLinkClickTracking');
    window.snowplow('trackPageView');
    // <!-- Snowplow stops plowing -->
</script>
</body>
</html>