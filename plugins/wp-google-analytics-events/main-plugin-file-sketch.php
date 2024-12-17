<?php

defined( 'ABSPATH' ) or die( 'Direct access not allowed!' );

if ( ! class_exists( 'GaEvents' ) ) {

    class GaEvents {

	function ga_events_install() {

	    // intial values for plugin options
	    $ga_events_options = array(
		'link_clicks_delay'	 => 0,
		'gtm'			 => false,
		'anonymizeip'		 => false,
		'advanced'		 => false,
		'snippet_type'		 => 'universal',
		'id'			 => '',
		'license'		 => '',
		'status'		 => '',
		'gtm_id'		 => '',
		'domain'		 => '',
		// Roles for which tracking is disabled
		'excluded_roles'	 => array(),
		'nofollow_links'	 => array(),
		'scroll_track_elements'	 => array(),
		'click_track_elements'	 => array(),
		'youtube_videos'	 => array(),
		'vimeo_videos'		 => array(),
		// Roles with permission to modify plugin options
		'roles'			 => array( 'administrator' ),
		'link_tracking'		 => array(
		    'track'	 => false,
		    'type'		 => "all",
		    'class'		 => ''
		),
	    );
	}

    }
}
