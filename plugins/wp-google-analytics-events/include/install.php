<?php
function ga_events_install() {

	    // intial values for plugin options
	    $ga_events_options = array(
		'link_clicks_delay'	 => 120,
		'gtm'			 => false,
		'anonymizeip'		 => false,
		'advanced'		 => false,
		'snippet_type'		 => 'universal',
		'id'			 => '',
		'status'		 => '',
		'gtm_id'		 => '',
		'domain'		 => '',
        'force_snippet'	 => "none",
        // Roles for which tracking is disabled
		'scroll_track_elements'	 => array(),
		'click_track_elements'	 => array(),
		// Roles with permission to modify plugin options
		'roles'			 => array( 'administrator' ),
	    );
	}