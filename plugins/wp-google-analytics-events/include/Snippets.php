<?php

class GAESnippets {

    static function add_google_tag_manager_container()
		{
			$options = get_option('ga_events_options');
			$gtm_id = isset($options['gtm_id']) ? $options['gtm_id'] : '';
			if (isset($gtm_id) && $gtm_id != "") {

				echo <<<EOF
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','$gtm_id');</script>
<!-- End Google Tag Manager -->
    
EOF;
				// TODO discuss this implementtion with Yuval as it requires theme modification
				add_action('wp_after_body',
					'add_google_tag_manager_noscript');
			}
		}

    static function add_google_tag_manager_noscript() {
			$options = get_option('ga_events_options');
			$gtm_id = isset($options['gtm_id']) ? $options['gtm_id'] : '';
	// This part is for running GA without JS on page. However this plugin requires JS, so the snippet is strictly redundant, although GA detetcts its absence
	echo <<<EOF
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=$gtm_id"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
EOF;
    }

    static function add_global_site_tag() {
	$options = get_option( 'ga_events_options' );
	$id	 = $options[ 'tracking_id' ];
			if (isset($id) && $id != "") {
				$anonymize_ip = $options['anonymizeip'];
				$anonymize_ip_string = (isset($anonymize_ip) && $anonymize_ip) ? "{ 'anonymize_ip': true }" : "{ 'anonymize_ip': false }";
				echo <<<EOF
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=$id"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', '$id', $anonymize_ip_string );
</script>

EOF;
			}
    }

    static function add_universal_tag()
		{
			$options = get_option('ga_events_options');

			$id = $options['tracking_id'];
			if (isset($id) && $id != "") {
				if (isset($options['domain']) && $options['domain'] != "") {
					$domain = $options['domain'];
				} else {
					$domain = 'auto';
				}
				$ga_create_call = (isset($options['anonymizeip']) && $options['anonymizeip']) ? "ga('create','$id', '$domain', {anonymizeIp: true});" : "ga('create','$id', '$domain');";

				echo <<<EOF
<script>
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
$ga_create_call
ga('send', 'pageview');
</script>

EOF;
			}
    }

    static function add_legacy_tag() {
    // Legacy at time of writing means the ga.js api
	$options = get_option( 'ga_events_options' );

	$id = $options[ 'tracking_id' ];
			if (isset($id) && $id != "") {

				if (isset($options['domain']) && $options['domain'] != "") {
					$domain = $options['domain'];
				} else {
					$domain = 'auto';
				}
				if ($domain == 'auto') {
					$domain = $_SERVER['SERVER_NAME'];
				}
				$anon = (isset($options['anonymizeip']) && $options['anonymizeip']) ? "_gaq.push (['_gat._anonymizeIp'])" : "";

				echo <<<EOF
<script type='text/javascript'>
var _gaq = _gaq || [];
_gaq.push(['_setAccount', '$id']);
$anon
_gaq.push(['_setAllowLinker', true]);
_gaq.push(['_trackPageview']);

(function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>

EOF;
			}
    }

    /*
     * Insert tracking snippet of type chosen by user
     */

    static function add_snippet_to_header() {
	$options	 = get_option( 'ga_events_options' );
	$snippet_type	 = $options[ 'snippet_type' ];

	// Add snippet based on snippet_type in options
	switch ( $snippet_type ) {
	    case 'gtm':
		self::add_google_tag_manager_container();
		break;
	    case 'gst':
		self::add_global_site_tag();
		break;
	    case 'universal':
		self::add_universal_tag();
		break;
	    case 'legacy':
		self::add_legacy_tag();
		break;
	}
    }

}
