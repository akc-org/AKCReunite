<?php
/**
 *
 * Plugin Name: Interactive World Maps
 * Plugin URI: https://cmoreira.net/interactive-world-maps-demo
 * Description: Create interactive maps and put them anywere on your website, including posts, pages and widgets. You can set the view to the whole world, a continent, a specific country or a US state. You can color full regions or just create markers on specific locations that will have information on hover and can also have actions on click. This plugin uses the Google GeoChart API to render the maps.
 * Author: Carlos Moreira
 * Version: 2.4.5
 * Author URI: https://cmoreira.net
 * Text Domain: interactive-world-maps
 * Domain Path: /lang
 */

// Last Modified: Jan 02 2020.

// Include files.
// Widget code.
require_once dirname( __FILE__ ) . '/modules/widget.php';

// Widget code for Layers.
require_once dirname( __FILE__ ) . '/modules/layers/layers-extension.php';

// Widget code Visual Composer.
require_once dirname( __FILE__ ) . '/modules/visual-composer/class-iwm-vcextendaddonclass.php';

// Element code for Cornerstone.
require_once dirname( __FILE__ ) . '/modules/cornerstone/init.php';

// Table Manage Class.
require_once dirname( __FILE__ ) . '/modules/admin-table/class-i-world-map-manage-table.php';

// Admin Files.
require_once dirname( __FILE__ ) . '/admin/admin.php';
require_once dirname( __FILE__ ) . '/admin/settings.php';
require_once dirname( __FILE__ ) . '/admin/add-edit.php';

// register default settings.
register_activation_hook( __FILE__, 'i_world_map_defaults' );

/**
 * Create globals used by plugin
 *
 * @return void
 */
function i_world_maps_globals() {
	global $iwmcount;
	global $iwmparam_array;

	// increment to serve as unique identifier for each map loaded on a given page.
	$iwmcount = 0;
	// to store different options for different maps on same page if they exist.
	$iwmparam_array = array();
}

add_action( 'init', 'i_world_maps_globals' );

/**
 * Functions to get variables.
 *
 * @return string table name
 */
function i_world_map_table_name() {
	global $wpdb;
	$table_name_imap = $wpdb->prefix . 'i_world_map';
	return $table_name_imap;
}

/**
 * Get plugin version
 *
 * @return string plugin version
 */
function i_world_map_get_version() {

	$plugin_version = '2.4';

	if( function_exists( 'get_plugin_data' ) ){
		$plugin_data    = get_plugin_data( __FILE__ );
		$plugin_version = $plugin_data['Version'];
	}

	return $plugin_version;
}

/**
 * Actions to perform when plugin is enabled for the first time.
 * It will create the database table to store the maps.
 *
 * @return void
 */
function i_world_map_install() {

	global $wpdb;
	$table_name_imap = i_world_map_table_name();
	$iwm_db_version  = 6;

	$charset_collate = '';

	if ( $wpdb->has_cap( 'collation' ) ) {
		$charset_collate = $wpdb->get_charset_collate();
	}

	$sql = "CREATE TABLE $table_name_imap (
					id int(11) NOT NULL AUTO_INCREMENT,
					name varchar(255) DEFAULT NULL,
					description longtext,
					use_defaults int(11) DEFAULT NULL,
					bg_color varchar(100) DEFAULT NULL,
					border_color varchar(100) DEFAULT NULL,
					border_stroke varchar(100) DEFAULT NULL,
					ina_color varchar(100) DEFAULT NULL,
					act_color varchar(100) DEFAULT NULL,
					marker_size int(11) DEFAULT NULL,
					width varchar(100) DEFAULT NULL,
					height varchar(100) DEFAULT NULL,
					aspect_ratio int(11) DEFAULT NULL,
					interactive int(11) DEFAULT '1',
					showtooltip int(11) DEFAULT '1',
					region varchar(100) DEFAULT NULL,
					display_mode varchar(100) DEFAULT NULL,
					map_action varchar(100) DEFAULT NULL,
					places LONGTEXT NULL DEFAULT NULL,
					image LONGTEXT NULL DEFAULT NULL,
					custom_action LONGTEXT NULL DEFAULT NULL,
					custom_css LONGTEXT NULL DEFAULT NULL,
					created timestamp NULL DEFAULT CURRENT_TIMESTAMP,
					UNIQUE KEY id ( id)
    		) $charset_collate;";

	$currentdbversion      = $iwm_db_version;
	$storeddbversion       = false;
	$storeddbversionexists = get_option( 'i_world_map_db_version' );

	// check if table exists.
	global $wpdb;
	$dbexists = false;

	if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name_imap ) ) === $table_name_imap ) {

		$dbexists = true;

	}

	if ( false !== $storeddbversionexists ) {
		$storeddbversion = $storeddbversionexists;
	}

	if ( false !== $storeddbversionexists && $storeddbversionexists !== $currentdbversion ) {

		// upgrade function.
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
		update_option( 'i_world_map_db_version', $currentdbversion );

	}

	if ( false !== $storeddbversionexists && false === $dbexists ) {
		update_option( 'i_world_map_db_version', $currentdbversion );
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	if ( false === $storeddbversionexists ) {
		update_option( 'i_world_map_db_version', $currentdbversion );
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}

// Install Unistall Hook.
register_activation_hook( __FILE__, 'i_world_map_install' );

/**
 * Run the Shortcode to Build Interactive Map
 *
 * @param [array] $atts map attributes.
 * @return string html for the map
 */
function i_world_map_shortcode( $atts ) {

	$html = '';

	if ( isset( $atts['id'] ) ) {
		$id = $atts['id'];
	} else {
		global $wpdb;
		$sql_fields      = i_world_map_get_SQL_fields();
		$table_name_imap = i_world_map_table_name();
		$maps_created    = $wpdb->get_results( $wpdb->prepare( 'SELECT ' . $sql_fields . ' FROM %s', $table_name_imap ), ARRAY_A );
		$id              = $maps_created[0]['id'];
	}

	// if it's AMP functions.
	if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
		return i_world_map_output_image( $id );
	}

	if ( function_exists( 'is_wp_amp' ) && is_wp_amp() ) {
		return i_world_map_output_image( $id );
	}

	// to have another map overlay we created a new parameter.
	if ( isset( $atts['overlay'] ) ) {

		$html .= '<div class="iwm_map_overlay">';
		$html .= i_world_map_build_exec( $id, 'shortcode', 'base', $atts );
		$html .= i_world_map_build_exec( $atts['overlay'], 'shortcode', 'data', $atts );
		$html .= '</div>';

		$html .= '
 		<!-- Map Overlay Styles -->
		<style type="text/css">
		.iwm_map_overlay #map_canvas_' . $atts['overlay'] . ' {
		pointer-events:none;
		}

		.iwm_map_overlay #map_canvas_' . $atts['overlay'] . ' path:not([fill-opacity="0.25"])  {
		display:none;
		}

		.iwm_map_overlay #map_canvas_' . $atts['overlay'] . ' g[clip-path] > * {
		display:block;
		pointer-events:visible;
		}

		.iwm_map_overlay #map_canvas_' . $atts['overlay'] . ' path[fill-opacity="0.25"]  {
		pointer-events:visible;
		}

		.iwm_map_overlay #map_canvas_' . $atts['overlay'] . ' text,
		.iwm_map_overlay #map_canvas_' . $atts['overlay'] . ' circle {
		pointer-events:visible;
		}
		</style>';

		return $html;

	} else {

		return i_world_map_build_exec( $id, 'shortcode', false, $atts );

	}

}

/**
 * Run php comand to Build Interactive Map.
 *
 * @param [int] $id map identifier.
 * @return void
 */
function i_world_map_build( $id ) {

	$atts = null;
	i_world_map_build_exec( $id, 'php', false, $atts );
}

// Add shortcode functionality.
add_shortcode( 'show-map', 'i_world_map_shortcode' );
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_excerpt', 'do_shortcode' );

// Extra Shortcodes.
add_shortcode( 'show-map-list', 'i_world_map_build_i_world_map_list' );
add_shortcode( 'show-map-dropdown', 'i_world_map_build_i_world_map_dropdown' );
add_shortcode( 'show-map-count', 'i_world_map_build_i_world_map_count' );
add_shortcode( 'show-map-image', 'i_world_map_build_map_image' );

/**
 * Output map image
 *
 * @param [array] $atts - attributes for the current map.
 * @return string html code for the image
 */
function i_world_map_build_map_image( $atts ) {

	if ( ! isset( $atts['id'] ) ) {
		return '';
	}

	$id      = $atts['id'];
	$mapdata = i_world_map_get_map_data( $id );

	$image = isset( $mapdata['image'] ) && '' !== $mapdata['image'] ? $mapdata['image'] : '';
	$width = isset( $atts['width'] ) ? 'width="' . $atts['width'] . '"' : '';

	$html = '<img src="' . $image . '" ' . $width . '>';

	return $html;

}

/**
 * Output number of entries in map
 *
 * @param [array] $atts map attributes.
 * @return string number of entries in the map
 */
function i_world_map_build_i_world_map_count( $atts ) {

	global $wpdb;
	$id      = $atts['id'];
	$mapdata = i_world_map_get_map_data( $id );
	$input   = str_replace( array( "\r\n", "\r", "\n" ), ' ', addslashes( $mapdata['places'] ) );
	$places  = explode( ';', $input, -1 );

	$html = count( $places );

	return $html;
}


function i_world_map_get_SQL_fields(){
	return "`id`,`name`,`description`,`use_defaults`,`bg_color`,`border_color`,`border_stroke`,`ina_color`,`act_color`,`marker_size`,`width`,`height`,`aspect_ratio`,`interactive`,`showtooltip`,`region`,`display_mode`,`map_action`,`places`,`image`,`custom_action`,`custom_css`,`created`";
}

/**
 * SQL Query for one map entry
 *
 * @param [int] $id map identifier.
 * @return array map data
 */
function i_world_map_get_map_data( $id ) {
	global $wpdb;
	$sql_fields      = i_world_map_get_SQL_fields();
	$table_name_imap = i_world_map_table_name();
	$mapdata         = $wpdb->get_row( $wpdb->prepare( "SELECT " . $sql_fields . "FROM " . $table_name_imap . " WHERE id = %d", $id ), ARRAY_A );
	return $mapdata;
}

/**
 * Main Function to build the list - BETA
 *
 * @param [array] $atts - map attributes.
 * @return string html with map list
 */
function i_world_map_build_i_world_map_list( $atts ) {

	global $wpdb;
	$id         = $atts['id'];
	$mapdata    = i_world_map_get_map_data( $id );
	$input      = str_replace( array( "\r\n", "\r", "\n" ), ' ', addslashes( $mapdata['places'] ) );
	$places     = explode( ';', $input, -1 );
	$map_action = $mapdata['map_action'];
	$target     = '';

	if ( 'i_map_action_open_url_new' === $map_action ) {
		$target = "target='_blank'";
	}

	$html = '';
	$html = $html . "<ul id='iwm-list-" . $id . "' class='iwm-list'>";

	$orderoptions = array();

		$i = 1;
	foreach ( $places as $place ) {
		$arr             = explode( ',', $place );
		$ttit            = $arr[1];
		$ttool           = $arr[2];
		$ofinal          = array( ',', ';' );
		$oreplace        = array( '&#44', '&#59' );
		$ttitle          = str_replace( $oreplace, $ofinal, $ttit );
		$ttooltip        = str_replace( $oreplace, $ofinal, $ttool );
		$index           = trim( $arr[0] );
		$oaction         = trim( $arr[3] );
		$ofinal          = array( ',', ';' );
		$oreplace        = array( '&#44', '&#59' );
		$formatedactionv = str_replace( $oreplace, $ofinal, $oaction );
		$title           = stripslashes( trim( $ttitle ) );

		if ( isset( $atts['tooltip'] ) && 'true' === $atts['tooltip'] ) {
			$title .= '<br>' . $ttooltip;
		}

		$orderoptions[ $arr[1] ] = "<li><a onmouseover='iwm_setSelection(\"" . $index . '",' . $id . ")' onclick='iwm_select(\"" . $index . '",' . $id . ")' onmouseleave='iwm_clearSelection(" . $id . ")' title='" . $ttitle . "'>" . $title . '</a></li>';

	}

	uksort( $orderoptions, 'i_world_map_compare_words' );

	foreach ( $orderoptions as $key => $value ) {
		$html .= $value;
	}

	$html = $html . '</ul>';
	return $html;
}

/**
 * Function to build dropdown - BETA
 *
 * @param [array] $atts map attributes.
 * @return string html with map entries dropdown
 */
function i_world_map_build_i_world_map_dropdown( $atts ) {

	global $wpdb;
	$id      = $atts['id'];
	$mapdata = i_world_map_get_map_data( $id );
	$input   = str_replace( array( "\r\n", "\r", "\n" ), ' ', addslashes( $mapdata['places'] ) );
	$places  = explode( ';', $input, -1 );

	sort( $places );

	$map_action  = $mapdata['map_action'];
	$displaymode = $mapdata['display_mode'];
	$target      = '';

	$html   = '';
	$before = '';
	$after  = '';

	$html = $before;

	$html = $html . "<select id='imap-dropdown-" . $id . "' onchange='iwm_select(value," . $id . ")'>";
	$html = $html . "<option value='void'>" . __( 'Please Select...', 'interactive-world-maps' ) . '</option>';

	$orderoptions = array();

		$i = 1;
	foreach ( $places as $place ) {
		$arr             = explode( ',', $place );
		$ttit            = $arr[1];
		$ttool           = $arr[2];
		$ofinal          = array( ',', ';' );
		$oreplace        = array( '&#44', '&#59' );
		$ttitle          = str_replace( $oreplace, $ofinal, $ttit );
		$ttooltip        = str_replace( $oreplace, $ofinal, $ttool );
		$index           = trim( $arr[0] );
		$oaction         = trim( $arr[3] );
		$ofinal          = array( ',', ';' );
		$oreplace        = array( '&#44', '&#59' );
		$formatedactionv = str_replace( $oreplace, $ofinal, $oaction );

		$formatedactionv = str_replace( '\"', '{quote}', $formatedactionv );

		if ( '' !== $formatedactionv ) {

			$divid = trim( $index );
			if ( 'markers02' === $displaymode || 'text02' === $displaymode || 'customicon' === $displaymode ) {
				$rcode = explode( ' ', $divid );
				$divid = trim( $rcode[0] );
			}

			$divid                   = str_replace( '.', '', $divid );
			$divid                   = str_replace( '-', '', $divid );
			$divid                   = str_replace( ' ', '', $divid );
			$orderoptions[ $arr[1] ] = "<option id='imap" . $id . '-' . $divid . "' value='" . $index . "' title='" . $ttooltip . "'>" . trim( $ttitle ) . '</option>';

		} else {
			$orderoptions[ $arr[1] ] = "<option value='" . $index . "' title='" . $ttooltip . "'>" . trim( $ttitle ) . '</option>';
		}
	}

	uksort( $orderoptions, 'iwmcompareWords' );

	foreach ( $orderoptions as $key => $value ) {
		$html .= $value;
	}

	$html = $html . '</select>';

	$html = $html . $after;

	return $html;
}

/**
 * Function to order correctly, stripping the accents
 *
 * @param [string] $str string to remove accents
 * @return string without accents
 */
function i_world_map_strip_accents( $str ) {
	return strtr(
		utf8_decode( $str ),
		utf8_decode( 'àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ' ),
		'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY'
	);
}

/**
 * Compare words
 *
 * @param [string] $w1 word 1.
 * @param [string] $w2 word 2.
 * @return int
 */
function i_world_map_compare_words( $w1, $w2 ) {
	return strcasecmp( i_world_map_strip_accents( $w1 ), i_world_map_strip_accents( $w2 ) );
}

add_shortcode( 'show-map-title', 'i_world_map_get_map_title_shortcode' );
add_shortcode( 'show-map-description', 'i_world_map_get_map_description_shortcode' );

/**
 * Get map title shortcode
 *
 * @param [array] $atts map attributes.
 * @return string title of the map
 */
function iwm_get_map_title_shortcode( $atts ) {
	return i_world_map_get_map_title( $atts['id'] );
}

/**
 * Get map description
 *
 * @param [array] $atts
 * @return string map description
 */
function iwm_get_map_description_shortcode( $atts ) {
	return i_world_map_get_map_description( $atts['id'] );
}

/**
 * Function to get map title
 *
 * @param [int]   $id map id.
 * @param boolean $echo if we need to output or returb.
 * @return string ouput or returned.
 */
function i_world_map_get_map_title( $id, $echo = false ) {

	$mapdata = i_world_map_get_map_data( $id );
	if ( $echo ) {
		echo $mapdata['name'];
	} else {
		return $mapdata['name'];
	}
}
/**
 * Function to get map description
 *
 * @param [int]   $id
 * @param boolean $echo
 * @return string return or echo description
 */
function i_world_map_get_map_description( $id, $echo = false ) {
	$mapdata = i_world_map_get_map_data( $id );
	if ( $echo ) {
		echo $mapdata['description'];
	} else {
		return $mapdata['description'];
	}
}

/**
 * Main Function to build the map
 *
 * @param [int] $id
 * @return string html for the map
 */
function i_world_map_output_image( $id ) {

	$mapdata = i_world_map_get_map_data( $id );
	$image   = isset( $mapdata['image'] ) && '' !== $mapdata['image'] ? $mapdata['image'] : '';
	$html    = '';

	if ( '' !== $image ) {
		$html = '<img src="' . $image . '">';
	} else {
		$html = "<div class='iwm_placeholder'><img width='32px' alt='" . $mapdata['name'] . " Placeholder' title='" . $mapdata['name'] . "' src='" . plugins_url( 'imgs/placeholder.png', __FILE__ ) . "'><br>" . $mapdata['name'] . '</div>';
	}
	return $html;

}

/**
 * Build map HTML
 *
 * @param [int]   $id map id
 * @param string  $type shortcode or php function
 * @param boolean $overlay if map is overlay or not
 * @param [array] $atts
 * @return string html for the map
 */
function i_world_map_build_exec( $id, $type = 'shortcode', $overlay = false, $atts ) {

	global $wpdb;
	global $iwmparam_array;
	global $iwmcount;

	$options = get_option( 'i-world-map-settings' );

	if ( false === $options ) {
		i_world_map_defaults();
		$options = get_option( 'i-world-map-settings' );
	}

	$mapdata = i_world_map_get_map_data( $id );
	$input   = apply_filters( 'iwm_input', $mapdata['places'] );
	$id      = $mapdata['id'];

	// Check if custom css for this map exist.
	$styles    = '';
	$overrideh = false;

	if ( '' !== $mapdata['custom_css'] ) {

		$cssarray = json_decode( stripslashes( $mapdata['custom_css'] ), true );

		if ( is_array( $cssarray ) ) {
			$css = array_filter( $cssarray, 'i_world_map_array_empty' );
		} else {
			$css = array();
		}

		$inactivecolor = strtolower( $mapdata['ina_color'] );

		if ( ! empty( $css ) ) {

				$styles  = "<!-- Map Generated CSS --> \n <style>";
				$styles .= "\n.iwm_map_canvas { overflow:hidden; }";

				// set margin left.
			if ( isset( $css['iwm_left'] ) && '' !== $css['iwm_left'] ) {

				$styles .= "\n#map_canvas_" . $id . ' { margin-left: ' . $css['iwm_left'] . '%; }';

			}

				// set margin top.
			if ( isset( $css['iwm_top'] ) && '' !== $css['iwm_top'] ) {

				$styles .= "\n#map_canvas_" . $id . ' { margin-top: ' . $css['iwm_top'] . '%; }';

			}

				// set size %.
			if ( isset( $css['iwm_size'] ) && '' !== $css['iwm_size'] && '100' !== $css['iwm_size'] ) {

				$styles .= "\n#map_canvas_" . $id . ' { width: ' . $css['iwm_size'] . '%; height: ' . $css['iwm_size'] . '%; }';

			}

				// set vertical override size.
			if ( isset( $css['iwm_hsize'] ) && '' !== $css['iwm_hsize'] && '61.7' !== $css['iwm_hsize'] ) {
				$overrideh = true;
				$styles   .= '#iwm_' . $id . ' .iwm_map_canvas:after { padding-top:' . $css['iwm_hsize'] . '%; }';

			}

				// set hovercolor.
			if ( isset( $css['hovercolor'] ) && '' !== $css['hovercolor'] ) {

				if ( 1 === $mapdata['use_defaults'] ) {
					$inactivecolor = strtolower( $options['default_ina_color'] );
				}

				// new way to implement hover.
				$styles .= '#map_canvas_' . $id . " path[stroke-width^='3'] + path { display:none; }";
				$styles .= '#map_canvas_' . $id . " path[stroke-width^='3'] + path + path:not([fill^='" . $inactivecolor . "']) { display:none; }";
				$styles .= '#map_canvas_' . $id . " path[stroke-width^='3'] { fill:" . $css['hovercolor'] . ';  }';
				$styles .= '#map_canvas_' . $id . " path[fill^='" . $inactivecolor . "'] { pointer-events: none; }";
				$styles .= '#map_canvas_' . $id . " path[fill^='none'] { pointer-events: none; }";

				$bw = 1;
				if ( isset( $css['bwidth'] ) && '' !== $css['bwidth'] ) {
					$bw = $css['bwidth'];
				}

				$styles .= '#map_canvas_' . $id . " path:not([fill^='" . $inactivecolor . "']) + path[stroke-width^='3'] { stroke-width:" . $bw . '; stroke-opacity:0; stroke:' . $css['hovercolor'] . '; }';

				// for circle and text.
				$styles .= '#map_canvas_' . $id . " circle[stroke-width='3'] { fill:" . $css['hovercolor'] . ';  }';
				$styles .= '#map_canvas_' . $id . ' text:hover { fill:' . $css['hovercolor'] . ';  }';

			}

				// set cursor.
			if ( isset( $css['showcursor'] ) && '1' === $css['showcursor'] ) {

				$styles .= '#map_canvas_' . $id . ' path:not([fill^="' . $inactivecolor . '"]):hover { cursor:pointer; }';
				$styles .= '#map_canvas_' . $id . ' circle:hover { cursor:pointer; }';
				$styles .= '#map_canvas_' . $id . ' text:hover { cursor:pointer; }';
				$styles .= '#map_canvas_' . $id . ' image:hover { cursor:pointer; }';

			}

				// set border/path colour.
			if ( isset( $css['bcolor'] ) && '' !== $css['bcolor'] ) {

				$styles .= '#map_canvas_' . $id . ' path:not([id]) { stroke:' . $css['bcolor'] . '; }';

			}

				// set border/path width.
			if ( isset( $css['bwidth'] ) && '' !== $css['bwidth'] ) {

				$styles .= '#map_canvas_' . $id . ' path:not([id]) { stroke-width:' . $css['bwidth'] . '; }';

			}

				// set border/path width for inactive regions.
			if ( isset( $css['biwidth'] ) && '' !== $css['biwidth'] ) {

				$styles .= '#map_canvas_' . $id . ' path[fill^="' . $inactivecolor . '"] { stroke-width:' . $css['biwidth'] . '; }';
				$styles .= '#map_canvas_' . $id . ' path[fill^="' . $inactivecolor . '"]:hover { stroke-width:' . $css['biwidth'] . '; }';
				$styles .= '#map_canvas_' . $id . ' path[fill^="none"] { stroke-width:' . $css['biwidth'] . '; stroke-opacity:0; }';

			}

				// set background image.
			if ( isset( $css['bgimage'] ) && '' !== $css['bgimage'] ) {
				$mapdata['bg_color']         = 'transparent';
				$options['default_bg_color'] = 'transparent';
				$styles                     .= '#map_canvas_' . $id . ' { background-image: url("' . $css['bgimage'] . '"); }';

			}

				// set background image repeat.
			if ( isset( $css['bgrepeat'] ) && '' !== $css['bgrepeat'] ) {
				if ( 1 === $css['bgrepeat'] ) {
					$styles .= '#map_canvas_' . $id . ' { background-repeat:repeat; }';
				}
			}
			if ( ! isset( $css['bgrepeat'] ) ) {
				$styles .= '#map_canvas_' . $id . ' { background-repeat:no-repeat; background-size: 100% 100%; }';
			}

				// HTML Tooltips.
			if ( isset( $css['tooltipfontfamily'] ) && '' !== $css['tooltipfontfamily'] ) {
				$styles .= "\n#map_canvas_" . $id . " .google-visualization-tooltip * { font-family:'" . $css['tooltipfontfamily'] . "' !important; }";
			}

			if ( isset( $css['tooltipfontsize'] ) && '' !== $css['tooltipfontsize'] ) {
				$styles .= '#map_canvas_' . $id . ' .google-visualization-tooltip * { font-size:' . $css['tooltipfontsize'] . ' !important; }';
			}

			if ( isset( $css['tooltipbg'] ) && '' !== $css['tooltipbg'] ) {
				$styles .= '#map_canvas_' . $id . ' .google-visualization-tooltip { background:' . $css['tooltipbg'] . '; }';
			}

			if ( isset( $css['tooltipminwidth'] ) && '' !== $css['tooltipminwidth'] ) {
				$styles .= '#map_canvas_' . $id . ' .google-visualization-tooltip { min-width:' . $css['tooltipminwidth'] . '; }';
			}

			if ( isset( $css['tooltiphidetitle'] ) && '' !== $css['tooltiphidetitle'] && 1 === $css['tooltiphidetitle'] ) {
				$styles .= '#map_canvas_' . $id . ' .google-visualization-tooltip-item:first-child { display:none;}';
			}

			if ( isset( $css['mobilemarker'] ) && '' !== $css['mobilemarker'] ) {
				$styles .= '@media handheld, only screen and (max-width: 480px) { #map_canvas_' . $id . ' circle { r:' . $css['mobilemarker'] . ' !important; }}';
				// also for custom images.
				$width   = intval( $css['mobilemarker'] );
				$styles .= '@media handheld, only screen and (max-width: 480px) { #map_canvas_' . $id . ' svg image { width:' . $width . 'px !important; height:' . $width . 'px !important; transform: translate(-' . intval( $width / 2 ) . 'px,-' . intval( $width / 2 ) . 'px) !important;  }}';
			}

			if ( isset( $css['tooltipnowrap'] ) && '' !== $css['tooltipnowrap'] && 1 === $css['tooltipnowrap'] ) {
				$styles .= '#map_canvas_' . $id . ' .google-visualization-tooltip-item { white-space:nowrap; }';
			}

			if ( isset( $css['tooltipbordercolor'] ) && '' !== $css['tooltipbordercolor'] ) {
				$styles .= '#map_canvas_' . $id . ' .google-visualization-tooltip { border-color:' . $css['tooltipbordercolor'] . '; }';
			}

			if ( isset( $css['tooltipborderwidth'] ) && '' !== $css['tooltipborderwidth'] ) {
				$styles .= '#map_canvas_' . $id . ' .google-visualization-tooltip { border-width:' . $css['tooltipborderwidth'] . '; }';
			}

			if ( isset( $css['fontawesomeapply'] ) && '' !== $css['fontawesomeapply'] ) {
				$styles .= '#map_canvas_' . $id . ' text { font-family:fontAwesome; }';
			}

			if ( isset( $css['fontawesomeinclude'] ) && '' !== $css['fontawesomeinclude'] ) {
				i_world_map_include_fontawesome();
			}

				$styles .= '</style>';

		}
	}

	/* Check if any of the entries is a group */
	if ( strpos( $input, 'group:' ) !== false ) {

		// if there's a group, we replicate the group entries.
		$entries = explode( ';', $input );

		$entries = array_slice( $entries, 0, -1 );

		$input = '';

		foreach ( $entries as $entry ) {

			if ( strpos( $entry, 'group:' ) !== false ) {

				$regentry   = explode( ',', $entry );
				$regioncode = $regentry[0];

				$regioncode = str_replace( 'group:', '', $regioncode );

				$newcodes = explode( '|', $regioncode );

				foreach ( $newcodes as $new ) {
					$entry  = $new . ',' . $regentry[1] . ',' . $regentry[2] . ',' . $regentry[3] . ',' . $regentry[4];
					$input .= $entry . ';';
				}
			} else {
				$input .= $entry . ';';
			}
		}
	}

	/* Conditional tag to populate the map automatically, if using taxonomies as source */
	if ( 'taxonomy_count' === $input ) {

		global $post;

		$input = '';

		$tax = 'tshowcase-categories';

		$args      = array(
			'orderby' => 'name',
			'order'   => 'ASC',
			'fields'  => 'all',
		);
		$countries = wp_get_post_terms( $post->ID, $tax, $args );

		foreach ( $countries as $country ) {

			// model: Region Code, Tooltip Title, Tooltip info, Action Value (URL), Color Code;
			$input .= $country->slug . ',' . $country->name . ',,' . get_term_link( $country->slug, $tax ) . ',' . $options['default_act_color'] . ';';

		}
	}

	// Conditional tag to populate the map automatically, if using categories as source.
	if ( 'categories_count' === $input ) {

		$input = '';

		$args = array(
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => 0,
		);

		$categories = get_categories( $args );

		foreach ( $categories as $category ) {

			// model: Region Code, Tooltip Title, Tooltip info, Action Value (URL), Color Code;
			$input .= $category->name . ',' . $category->name . ',' . $category->description . __( 'Number of Posts:', 'interactive-world-maps' ) . $category->count . ',' . get_category_link( $category->term_id ) . ',' . $options['default_act_color'] . ';';

		}
	}

	// Conditional tag to populate the map automatically, if using categories as source.
	if ( 'tags_count' === $input ) {

		$input = '';

		$args = array(
			'orderby'    => 'name',
			'order'      => 'ASC',
			'hide_empty' => 0,
		);

		$tags = get_tags( $args );

		foreach ( $tags as $tag ) {

			// model: Region Code, Tooltip Title, Tooltip info, Action Value (URL), Color Code;
			$input .= $tag->name . ',' . $tag->name . ',' . $tag->description . __( 'Number of Posts:', 'interactive-world-maps' ) . $tag->count . ',' . get_tag_link( $tag->term_id ) . ',' . $options['default_act_color'] . ';';

		}
	}

	// Conditional tag to populate the map automatically, if using CUSTOM POST TYPE as source.
	if ( 'custom_post_type' === $input ) {

		// EDIT HERE.
		$cpt_id           = 'interactive-world-maps';
		$region_code_meta = 'wpcf-regioncode'; // custom meta field name to fetch region code.
		$tooltip_meta     = 'wpcf-tooltip'; // custom meta field name to fetch tooltip info.
		$color_meta       = 'wpcf-color'; // cutom meta field name to fetch color codes.

		// AVOID EDIT BELOW.
		$input = '';

		$args = array(
			'post_type' => $cpt_id,
		);

		$cpt = new WP_Query( $args );

		// The Loop.
		if ( $cpt->have_posts() ) {

			while ( $cpt->have_posts() ) :
				$cpt->the_post();

				$regioncode   = get_post_meta( get_the_ID(), $region_code_meta, true );
				$tooltiptitle = get_the_title();
				$tooltipinfo  = get_post_meta( get_the_ID(), $tooltip_meta, true );

				$actionvalue = do_shortcode( get_the_content() );
				$colorcode   = get_post_meta( get_the_ID(), $color_meta, true );

				// to clean the content from commas (,) and semi-colons (;).
				$oreplace    = array( ',', ';' );
				$ofinal      = array( '&#44', '&#59' );
				$actionvalue = str_replace( $oreplace, $ofinal, $actionvalue );

				// model: Region Code, Tooltip Title, Tooltip info, Action Value (URL), Color Code;
				$input .= $regioncode . ',' . $tooltiptitle . ',' . $tooltipinfo . ',' . $actionvalue . ',' . $colorcode . ';';

				endwhile;

		}

		/* Restore original Post Data */
		wp_reset_postdata();

	}

	// if we're using cornerstone, we add a different class for the preview.
	if ( isset( $atts['cornerstone'] ) ) {

		$placeholder = "<div class='cs_iwm_placeholder'><img width='32px' alt='" . $mapdata['name'] . " Placeholder' title='" . $mapdata['name'] . "' src='" . plugins_url( 'imgs/placeholder.png', __FILE__ ) . "'><br>" . $mapdata['name'] . '</div>';

	} else {

		$placeholder = "<div class='iwm_placeholder'><img width='32px' alt='" . $mapdata['name'] . " Placeholder' title='" . $mapdata['name'] . "' src='" . plugins_url( 'imgs/placeholder.png', __FILE__ ) . "'><br>" . $mapdata['name'] . '</div>';

	}

	// add custom css function.
	add_action( 'wp_footer', 'i_world_map_custom_css_js', 99 );

	if ( false === $options ) {
		i_world_map_defaults();
		$options = get_option( 'i-world-map-settings' );
	}

	$usehtml = ( array_key_exists( 'default_usehtml', $options ) ? $options['default_usehtml'] : '0' );

	if ( '1' === $mapdata['use_defaults'] ) {

		$bg_color      = $options['default_bg_color'];
		$border_color  = $options['default_border_color'];
		$border_stroke = $options['default_border_stroke'];
		$ina_color     = $options['default_ina_color'];
		$act_color     = $options['default_act_color'];
		$marker_size   = $options['default_marker_size'];
		$width         = $options['default_width'];
		$height        = $options['default_height'];
		$aspect_ratio  = isset( $options['default_aspect_ratio'] ) ? $options['default_aspect_ratio'] : '0';

	} else {
		$bg_color      = $mapdata['bg_color'];
		$border_color  = $mapdata['border_color'];
		$border_stroke = $mapdata['border_stroke'];
		$ina_color     = $mapdata['ina_color'];
		$act_color     = $mapdata['act_color'];
		$marker_size   = $mapdata['marker_size'];
		$width         = $mapdata['width'];
		$height        = $mapdata['height'];
		$aspect_ratio  = isset( $mapdata['aspect_ratio'] ) ? $mapdata['aspect_ratio'] : '0';
	}

	if ( isset( $options['default_responsive'] ) && $options['default_responsive'] === '1' ) {
		$width  = '';
		$height = '';
		i_world_map_include_responsive_js();
	}

		$interactive = $mapdata['interactive'];
		$tooltipt    = $mapdata['showtooltip'];

		$diplaym = $mapdata['display_mode'];

	if ( $interactive === 0 || $overlay === 'data' ) {
		$interactive = 'false';
	} else {
		$interactive = 'true';
	}

	if ( intval( $tooltipt ) === 0 ) {
		$tooltipt = 'none';
	} elseif ( intval( $tooltipt ) === 2 ) {
		$tooltipt = 'selection';
	} else {
		$tooltipt = 'focus';
	}

		$display_mode  = $diplaym;
		$areashow      = explode( ',', $mapdata['region'] );
		$region        = $areashow[0];
		$resolution    = isset( $areashow[1] ) ? $areashow[1] : 'regions';
		$map_action    = $mapdata['map_action'];
		$custom_action = $mapdata['custom_action'];

		$projection = ( array_key_exists( 'map_projection', $options ) ? $options['map_projection'] : 'mercator' );
		$projection = isset( $atts['projection'] ) ? $atts['projection'] : $projection;

		$beforediv = '';
		$afterdiv  = '';

	if ( isset( $atts['extras'] ) && $atts['extras'] === 'dropdown' && ( $overlay === 'base' || $overlay === false ) ) {
		$afterdiv .= i_world_map_build_i_world_map_dropdown( $atts );
	}

	if ( $map_action !== 'none' || $map_action !== 'null' ) {

		if ( $map_action === 'i_map_action_content_below' || $map_action === 'i_map_action_content_below_scroll' ) {
			$afterdiv .= "<div id='imap" . $id . "message'>
				" . iwm_render_content( $id, $input, $display_mode ) . '
				</div>';
		}

		if ( 'i_map_action_colorbox_content' ) {
			$afterdiv .= "<div id='imap" . $id . "message' style='display:none;'>
				" . iwm_render_content( $id, $input, $display_mode, '', 'block' ) . '
				</div>';
		}

		if ( $map_action === 'i_map_action_content_above' ) {
			$beforediv = "<div id='imap" . $id . "message'>
				" . iwm_render_content( $id, $input, $display_mode ) . '
				</div>';
		}

		if ( $map_action === 'i_map_action_content_right_1_3' ) {
			$beforediv = "<div class='iwm_2_3_column'>";
			$afterdiv  = "</div><div class='iwm_1_3_column'>
							<div id='imap" . $id . "message'>"
						. iwm_render_content( $id, $input, $display_mode, $mapdata['description'] ) .
						'</div>
							</div>';
			if ( isset( $atts['extras'] ) && $atts['extras'] === 'dropdown' && ( $overlay === 'base' || $overlay === false ) ) {
				$afterdiv .= i_world_map_build_i_world_map_dropdown( $atts );
			}
		}
		if ( $map_action === 'i_map_action_content_right_1_4' ) {
			$beforediv = "<div class='iwm_3_4_column'>";
			$afterdiv  = "</div><div class='iwm_1_4_column'>
							<div id='imap" . $id . "message'>" . iwm_render_content( $id, $input, $display_mode, $mapdata['description'] ) . '</div>
							</div>';
			if ( isset( $atts['extras'] ) && $atts['extras'] === 'dropdown' && ( $overlay === 'base' || $overlay === false ) ) {
				$afterdiv .= i_world_map_build_i_world_map_dropdown( $atts );
			}
		}

		if ( $map_action === 'i_map_action_content_right_1_2' ) {
			$beforediv = "<div class='iwm_1_2_column'>";
			$afterdiv  = "</div><div class='iwm_1_2_column'>
							<div id='imap" . $id . "message'>" . iwm_render_content( $id, $input, $display_mode, $mapdata['description'] ) . '</div>
							</div>';
			if ( isset( $atts['extras'] ) && $atts['extras'] === 'dropdown' && ( $overlay === 'base' || $overlay === false ) ) {
				$afterdiv .= i_world_map_build_i_world_map_dropdown( $atts );
			}
		}

		if ( $map_action === 'i_map_action_colorbox_content' || $map_action === 'i_map_action_colorbox_iframe' || $map_action === 'i_map_action_colorbox_inline' || $map_action === 'i_map_action_colorbox_image' ) {
			i_world_map_enqueue_colorbox();
		}
	}

	$html = '';

	// include custom javascript action
	if ( $map_action === 'i_map_action_custom' ) {

		$old_value  = 'ivalue_' . $id . '[selectedRegion]';
		$new_action = str_replace( $old_value, 'value', $custom_action );

		$html  = '<script type="text/javascript">';
		$html .= 'function iwm_custom_action_' . $id . '(value) {';
		$html .= stripslashes( stripcslashes( $new_action ) );
		$html .= '}</script>';

	}

	$controls         = '';
	$usecontrols      = false;
	$controlsposition = '';
	if ( isset( $atts['controls'] ) ) {

		$ids = $id;
		i_world_map_include_panzoom();
		$usecontrols = true;

		$controlsposition = ( isset( $atts['controls'] ) ) && ( $atts['controls'] === 'top-left' || $atts['controls'] === 'top-right' || $atts['controls'] === 'bottom-left' || $atts['controls'] === 'bottom-right' ) || $atts['controls'] === 'center-left' || $atts['controls'] === 'center-right' ? $atts['controls'] : 'top-left';

	}

	// extra parameters.
	$overlayid = isset( $atts['overlay'] ) ? $atts['overlay'] : false;
	$magglass  = isset( $atts['mag-glass'] ) ? $atts['mag-glass'] : true;
	if ( $magglass === 'off' || $magglass === 'false' ) {
		$magglass = false; }
	$zfactor       = isset( $atts['mag-glass-zoom'] ) ? $atts['mag-glass-zoom'] : '5';
	$widthselector = isset( $atts['width-selector'] ) ? $atts['width-selector'] : false;

	$apikey            = isset( $options['api_key'] ) ? $options['api_key'] : '';
	$imageicon         = isset( $atts['icon-url'] ) ? $atts['icon-url'] : ( isset( $options['imageicon'] ) && $options['imageicon'] !== '' ? $options['imageicon'] : '' );
	$imageiconposition = isset( $atts['icon-position'] ) ? $atts['icon-position'] : ( isset( $options['imageicon_position'] ) && $options['imageicon_position'] !== '' ? $options['imageicon_position'] : 'center' );
	$htmlios           = isset( $atts['html_ios'] ) ? $atts['html_ios'] : ( isset( $options['html_ios'] ) && $options['html_ios'] !== '' ? $options['html_ios'] : '0' );

	// disable mag glass for custom icon display mode.
	if ( $display_mode === 'customicon' ) {
		$magglass = false;
	}

	$new_iwm_array = array(
		'usehtml'            => $usehtml,
		'id'                 => $id,
		'unique_id'          => $iwmcount,
		'bgcolor'            => $bg_color,
		'stroke'             => $border_stroke,
		'bordercolor'        => $border_color,
		'incolor'            => $ina_color,
		'actcolor'           => $act_color,
		'width'              => $width,
		'height'             => $height,
		'aspratio'           => $aspect_ratio,
		'interactive'        => $interactive,
		'tooltip'            => $tooltipt,
		'region'             => $region,
		'resolution'         => $resolution,
		'markersize'         => $marker_size,
		'displaymode'        => $display_mode,
		'placestxt'          => $input,
		'action'             => $map_action,
		'custom_action'      => $custom_action,
		'projection'         => $projection,
		'controls'           => $usecontrols,
		'controls_position'  => $controlsposition,
		'overlay'            => $overlayid,
		'magglass'           => $magglass,
		'magglasszfactor'    => $zfactor,
		'widthselector'      => $widthselector,
		'apikey'             => $apikey,
		'imageicon'          => $imageicon,
		'imageicon_position' => $imageiconposition,
		'htmlios'            => $htmlios,
	);

	array_push( $iwmparam_array, $new_iwm_array );

	// if the theme loads content via ajax, we need to output some js variables in a different way.
	$ajax_enabled = false;
	if ( isset( $options['ajax_enabled'] ) && $options['ajax_enabled'] === '1' ) {
		$ajax_enabled = true;
	}

	// load other scripts.
	i_world_map_scripts( $iwmparam_array );

	$style      = '';
	$customdata = '';

	if ( $overlay === 'base' ) {
		$style .= "style='pointer-events:visible;' ";
	}

	$class  = '';
	$style .= "class='iwm_map_canvas";
	if ( $overlay === 'data' ) {
		$style .= ' iwm_data';
	}

	if ( $display_mode === 'customicon' ) {
		$style     .= ' iwm_custom_icon';
		$customdata = "data-marker-size='" . $marker_size . "' data-icon-url='" . $imageicon . "' data-icon-position='" . $imageiconposition . "'";
	}
	// closing class="" !
	$style .= "'";

	// if the size height is overrided with css, we need extra class.
	if ( $overrideh ) {
		$beforediv .= '<div id="iwm_' . $id . '">';
		$afterdiv   = '</div>' . $afterdiv;
	}

	if ( $type === 'shortcode' ) {
		$finalhtml = '<div id="unique_iwm_' . $iwmcount . '">' . $html . $styles . $beforediv . '<div ' . $style . '>' . $controls . "<div id='map_canvas_" . $id . "' data-map-id='" . $id . "' " . $customdata . " class='i_world_map ' " . $style . '>' . $placeholder . '</div></div>' . $afterdiv . '</div>';
		$iwmcount++;
		return $finalhtml;
	}
	if ( $type === 'php' ) {
		$finalhtml = '<div id="unique_iwm_' . $iwmcount . '">' . $html . $styles . $beforediv . '<div ' . $style . '>' . $controls . "<div id='map_canvas_" . $id . "' data-map-id='" . $id . "' " . $customdata . " class='i_world_map ' " . $style . '>' . $placeholder . '</div></div>' . $afterdiv . '</div>';
		$iwmcount++;
		echo $finalhtml;
	}

	if ( $type === 'nulloutput' ) {

		return;

	}
}

/**
 * Enqueue necessary scripts
 *
 * @param array $iwmparam_array - array of map parameters
 * @return void
 */
function i_world_map_scripts( $iwmparam_array = array() ) {

	$protocol  = is_ssl() ? 'https' : 'http';
	$options   = get_option( 'i-world-map-settings' );
	$key       = isset( $options['api_key'] ) ? $options['api_key'] : '';
	$addscript = isset( $options['scriptadd'] ) && $options['scriptadd'] === '1' ? true : false;
	$version   = i_world_map_get_version();

	if ( $addscript ) {
		wp_deregister_script( 'iwmjsgeo' );
		wp_register_script( 'iwmjsgeo', $protocol . '://maps.googleapis.com/maps/api/js?key=' . $key, array(), $version, false );
		wp_enqueue_script( 'iwmjsgeo' );
	}

	$gurl   = $protocol . '://www.google.com/jsapi';
	$loader = $protocol . '://www.gstatic.com/charts/loader.js';

	wp_deregister_script( 'jsapiloader' );
	wp_register_script( 'jsapiloader', $loader, array(), $version, true );
	wp_enqueue_script( 'jsapiloader' );

	wp_deregister_script( 'jsapifull' );
	wp_register_script( 'jsapifull', $gurl, array( 'jsapiloader' ), $version, true );
	wp_enqueue_script( 'jsapifull' );

	wp_deregister_script( 'iwmjs' );
	wp_register_script( 'iwmjs', plugins_url( '/includes/js/shortcode.js', __FILE__ ), array( 'jsapifull' ), $version, true );
	wp_enqueue_script( 'iwmjs' );

	wp_deregister_style( 'iwm_front_css' );
	wp_register_style( 'iwm_front_css', plugins_url( 'includes/css/styles.css', __FILE__ ), array(), $version, 'all' );
	wp_enqueue_style( 'iwm_front_css' );

	wp_localize_script( 'iwmjs', 'iwmparam', $iwmparam_array );

}


/**
 * Display custom CSS
 *
 * @return string
 */
function i_world_map_custom_css_js() {
	$options = get_option( 'i-world-map-settings' );
	$css     = isset( $options['custom_css'] ) ? $options['custom_css'] : '';
	if ( $css !== '' ) {
		echo '
		<!-- Custom Styles for Interactive World Maps -->
		<style type="text/css">
		' . $css . '
		</style>';
	}

	// display custom js.
	$js = isset( $options['custom_js'] ) ? $options['custom_js'] : '';
	if ( $js !== '' ) {
		echo "
		<!-- Custom Javascript for Interactive World Maps -->
		<script type='text/javascript'>
		" . $js . '
		</script>';
	}

}

// Retina Icons.
add_action( 'admin_head', 'i_world_map_post_type_font_icon' );

/**
 * Output css in admin to render map menu icon
 *
 * @return void
 */
function i_world_map_post_type_font_icon() {
	?>
	<style>
		#adminmenu #toplevel_page_i_world_map_menu div.wp-menu-image:before { content: "\f319"; }
	</style>
	<?php
}

/**
 * filter for css empty
 *
 * @param [string] $var
 * @return boolean
 */
function i_world_map_array_empty( $var ) {
	return ( $var !== null && $var !== false && $var !== '' );
}




add_action( 'init', 'iwm_ajax_workaround' );
/**
 * AJAX WORKAROUND to load all map data in pages
 *
 * @return void
 */
function iwm_ajax_workaround() {

	if ( is_admin() ) {
		return;
	}

	$iwmoptions = get_option( 'i-world-map-settings' );
	if ( isset( $iwmoptions['ajax_enabled'] ) && $iwmoptions['ajax_enabled'] === '1' ) {

		global $wpdb;
		$table_name_imap = i_world_map_table_name();
		$sql             = 'SELECT id FROM ' . $table_name_imap; // no user input, so no need to use ->prepare
		$maps_created    = $wpdb->get_results( $sql, ARRAY_A );

		foreach ( $maps_created as $map ) {
			i_world_map_build_exec( $map['id'], 'nulloutput', false, null );
		}
	}
}


add_action( 'customize_register', 'iwm_admin_styles_custom', 11 );
/**
 * Add the iwm admin styles for the customizer,
 * in case it contains widgets, it will display the correct icon
 *
 * @return void
 */
function iwm_admin_styles_custom() {
		wp_deregister_style( 'iwm-admin-style' );
		wp_register_style( 'iwm-admin-style', plugins_url( 'includes/css/admin.css', __FILE__ ), array(), false, 'all' );
		wp_enqueue_style( 'iwm-admin-style' );
}

/**
 * Render content to be used by map
 *
 * @param [int]    $id map id
 * @param [string] $input content to display
 * @param [string] $displaymode map display mode (markers or regions)
 * @param string   $default
 * @param string   $display
 * @return string html to render
 */
function iwm_render_content( $id, $input, $displaymode, $default = '', $display = 'none' ) {

	$input   = trim( $input );
	$entries = explode( ';', $input );
	$html    = '';

	if ( $default !== '' ) {
		$html .= '<div>' . $default . '</div>';
	}

	array_pop( $entries );

	foreach ( $entries as $key => $entry ) {
		$data = explode( ',', $entry );

		$divid = trim( $data[0] );

		if ( 'markers02' === $displaymode || $displaymode === 'text02' || $displaymode === 'customicon' ) {
			$rcode = explode( ' ', $divid );
			$divid = trim( $rcode[0] );
		}

		$divid = str_replace( '.', '', $divid );
		$divid = str_replace( '-', '', $divid );
		$divid = str_replace( ' ', '', $divid );

		$content = str_replace( '&#59', ';', $data[3] );
		$content = str_replace( '&#44', ',', $content );

		$html .= '<div id="map_' . $id . '_message_' . $divid . '" style="display:' . $display . ';">' . do_shortcode( $content ) . '</div>';
	}
	return $html;
}


/**
 * Enqueue responsive styles and scripts
 *
 * @return void
 */
function i_world_map_include_responsive_js() {

	$version = i_world_map_get_version();

	wp_deregister_script( 'imapresponsive' );
	wp_register_script( 'imapresponsive', plugins_url( 'includes/js/responsive.js', __FILE__ ), array( 'jquery', 'iwmjs' ), $version, true );
	wp_enqueue_script( 'imapresponsive' );

	wp_deregister_style( 'imapresponsivecss' );
	wp_register_style( 'imapresponsivecss', plugins_url( 'includes/css/responsive.css', __FILE__ ), array(), $version, 'all' );
	wp_enqueue_style( 'imapresponsivecss' );

}

/**
 * Enqueue colorbox files
 *
 * @return void
 */
function i_world_map_enqueue_colorbox() {

	$version = i_world_map_get_version();

	wp_deregister_script( 'iwmcolorbox' );
	wp_register_script( 'iwmcolorbox', plugins_url( 'includes/colorbox/jquery.colorbox-min.js', __FILE__ ), array( 'jquery' ), $version, true );
	wp_enqueue_script( 'iwmcolorbox' );

	wp_deregister_style( 'iwmcolorbox' );
	wp_register_style( 'iwmcolorbox', plugins_url( 'includes/colorbox/colorbox.css', __FILE__ ), array(), $version, 'all' );
	wp_enqueue_style( 'iwmcolorbox' );

}

/**
 * Enqueue fontawesome css files
 *
 * @return void
 */
function i_world_map_include_fontawesome() {
	wp_register_style( 'i_world_map_fontawesome', plugins_url( 'includes/font-awesome/css/font-awesome.min.css', __FILE__ ), array(), '1.0.0', 'all' );
	wp_enqueue_style( 'i_world_map_fontawesome' );
}

/**
 * Enqueue zoom and pan script
 *
 * @return void
 */
function i_world_map_include_panzoom() {
		$version = i_world_map_get_version();
		wp_register_script( 'iwmpanzoom', plugins_url( 'includes/js/jquery.panzoom.min.js', __FILE__ ), array( 'jquery' ), $version, true );
		wp_enqueue_script( 'iwmpanzoom' );
}

/**
 * Enqueue default scripts and styles for settings page
 *
 * @return void
 */
function i_world_map_includes_def() {

	$version = i_world_map_get_version();

	/** Register */
	wp_register_style( 'i_world_map_css', plugins_url( 'includes/css/admin.css', __FILE__ ), array(), $version, 'all' );
	wp_register_script( 'jscolor', plugins_url( 'includes/js/jscolor.js', __FILE__ ) );

	/** Enqueue */
	wp_enqueue_style( 'i_world_map_css' );
	wp_enqueue_script( 'jscolor' );

}

/**
 * Enqueue frontend scripts and styles to render the map
 *
 * @return void
 */
function i_world_map_includes_add() {

	$protocol = is_ssl() ? 'https' : 'http';
	$gurl     = $protocol . '://www.google.com/jsapi';
	$loader   = $protocol . '://www.gstatic.com/charts/loader.js';

	$options = get_option( 'i-world-map-settings' );
	$key     = isset( $options['api_key'] ) ? $options['api_key'] : '';

	$geourl  = $protocol . '://maps.googleapis.com/maps/api/js?key=' . $key;
	$version = i_world_map_get_version();

	/** Register */
	wp_register_script( 'iwjsgeo', $geourl, array(), $version, false );

	wp_register_style( 'i_world_map_css', plugins_url( 'includes/css/admin.css', __FILE__ ), array(), $version, 'all' );
	wp_register_style( 'i_world_map_styles_css', plugins_url( 'includes/css/styles.css', __FILE__ ), array(), $version, 'all' );
	wp_register_script( 'iwjsapiloader', $loader, array(), $version, false );
	wp_register_script( 'iwjsapi', $gurl, array( 'iwjsapiloader' ), $version, false );
	wp_register_script( 'iwjscolor', plugins_url( 'includes/js/jscolor.js', __FILE__ ) );
	wp_register_script( 'iwjsadmin', plugins_url( 'includes/js/admin.js', __FILE__ ), array( 'jquery', 'iwjsgeo' ), $version, false );
	wp_register_style( 'i_world_map_fontawesome', plugins_url( 'includes/font-awesome/css/font-awesome.min.css', __FILE__ ), array(), $version, 'all' );

	// hack for tinyMCE.
	$settings = array(
		'teeny'         => false,
		'textarea_rows' => 3,
		'tinymce'       => true,
		'media_buttons' => true,
	);
	wp_editor( 'iwm_hack', 'iwm_hackeditor', $settings );

	$local = array(
		'closeCssBox'                 => __( 'Close Custom CSS Options Box', 'interactive-world-maps' ),
		'expandCssBox'                => __( ' Expand Custom CSS Options Box', 'interactive-world-maps' ),
		'copyToRegionCode'            => __( 'Copy this values to the Region Code field', 'interactive-world-maps' ),
		'categoriesCount'             => __( 'Categories Count', 'interactive-world-maps' ),
		'categoriesCountMessage01'    => __( 'The map will try to fetch the categories of your posts to populate the map automatically.', 'interactive-world-maps' ),
		'categoriesCountMessage02'    => __( 'The preview below will not display anything, but once you apply the shortcode to a page, it will render the map with the categories names and links to the categories archive page.', 'interactive-world-maps' ),
		'useThis'                     => __( 'use this', 'interactive-world-maps' ),
		'errorFinding'                => __( 'Impossible to locate that Address', 'interactive-world-maps' ),
		'editEntry'                   => __( 'Edit Entry', 'interactive-world-maps' ),
		'regionCode'                  => __( 'Region Code', 'interactive-world-maps' ),
		'tooltipTitle'                => __( 'Tooltip Title', 'interactive-world-maps' ),
		'tooltipText'                 => __( 'TooltipText', 'interactive-world-maps' ),
		'actionValue'                 => __( 'Action Value', 'interactive-world-maps' ),
		'color'                       => __( 'Color', 'interactive-world-maps' ),
		'confirmDelete'               => __( 'Are you sure you want to delete this Region entry?', 'interactive-world-maps' ),
		'isoInfo'                     => __( 'To create your interactive regions, when using the "Regions" display mode, use a country name as a string, or an uppercase <a href="http://en.wikipedia.org/wiki/ISO_3166-1">ISO-3166-1</a> code or its English text equivalent (for example, <i>GB</i> or <i>United Kingdom</i>). Check Google\'s <a href="https://developers.google.com/chart/interactive/docs/gallery/geochart#Continent_Hierarchy" target="_blank">Continents and Countries</a> list for aditional resources.', 'interactive-world-maps' ),
		'isoCodes'                    => __( 'To create your interactive regions, use the', 'interactive-world-maps' ),
		'metroCodes'                  => __( 'To create your interactive regions, use these three-digit <a href="https://support.google.com/richmedia/answer/2745487?hl=en" target="_blank">metropolitan area codes</a> as the region codes.', 'interactive-world-maps' ),
		'continents'                  => __( 'Region Codes for Continents (When using Regions Display Mode) <br /> Africa - 002 | Europe - 150 | Americas - 019 | Asia - 142 | Oceania - 009', 'interactive-world-maps' ),
		'subContinents'               => __( 'Region codes for Subcontinents (When using Regions Display Mode): <br />Africa - Northern Africa: 015, Western Africa: 011, Middle Africa: 017, Eastern Africa: 014, Southern Africa: 018;<br />Europe - Northern Europe: 154, Western Europe: 155, Eastern Europe: 151, Southern Europe: 039;<br />Americas - Northern America: 021, Caribbean: 029, Central America: 013, South America: 005;<br />Asia - Central Asia: 143, Eastern Asia: 030, Southern Asia: 034, South-Eastern Asia: 035, Western Asia: 145;<br />Oceania - Australia and New Zealand: 053, Melanesia: 054, Micronesia: 057, Polynesia: 061;', 'interactive-world-maps' ),
		'textMarkers'                 => __( 'Text Markers', 'interactive-world-maps' ),
		'textMarkersInfo'             => __( 'When using the Text Markers display mode, a colored text will be added to the specified region. When this mode is selected you can also use a a specific string address (for example, "1600 Pennsylvania Ave") or Berlin Germany as a Region Code. DO NOT use commas (,) or quotes("").<br /><strong style="color:red"> <i class="fa fa-exclamation-triangle"></i> If you experience slow loading of the Markers, consider using Text Labels - Coordinates Mode and use coordinate values in the Regions Code.</strong>', 'interactive-world-maps' ),
		'roundMarkersCoordinates'     => __( 'Round Markers (Coordinates)', 'interactive-world-maps' ),
		'roundMarkersCoordinatesInfo' => __( 'When using the Markers display mode, a colored bubble will be added to the specified region. When the Coordinates mode is chosen, you should insert the coordinates values in the Region Code, in this format: latitude longitude. <strong>Do not use commas, use a space to separate de values</strong>. Example:34.3071438 -53.7890625', 'interactive-world-maps' ),
		'roundMarkers'                => __( 'Round Markers (Text)', 'interactive-world-maps' ),
		'roundMarkersInfo'            => __( 'When using the Markers display mode, a colored bubble will be added to the specified region. When this mode is selected you can also use a a specific string address (for example, "1600 Pennsylvania Ave") or Berlin Germany as a Region Code. DO NOT use commas (,) or quotes("").<br /><strong style="color:red"><i class="fa fa-exclamation-triangle"></i> If you experience slow loading of the Maps, consider using Markers (Coordinates) Display Mode.</strong>', 'interactive-world-maps' ),
		'textLabels'                  => __( 'Text Labels', 'interactive-world-maps' ),
		'textLabelsInfo'              => __( 'When using the Text Labels display mode, a colored text will be added to the specified region. When the Coordinates mode is chosen, you should insert the coordinates values in the Region Code, in this format: latitude longitude. <strong>Do not use commas, use a space to separate de values</strong>. Example:34.3071438 -53.7890625', 'interactive-world-maps' ),
		'customIcon'                  => __( 'Custom Icon (Coordinates)', 'interactive-world-maps' ),
		'customIconInfo'              => __( 'When using the Custom Icon display mode, the image you have specified in the settings page will be added to the specified location.<br> You should insert the coordinates values in the Region Code, in this format: latitude longitude.<br> <strong>Do not use commas, use a space to separate de values</strong>. Example:34.3071438 -53.7890625 <br><br> <strong>Color Field</strong> will be ignored, but it\'s still mandatory', 'interactive-world-maps' ),
		'urlToOpen'                   => __( 'URL to open.', 'interactive-world-maps' ),
		'actionOpenUrl'               => __( 'Action - Open URL', 'interactive-world-maps' ),
		'actionOpenUrlNewWindow'      => __( 'Action - Open URL (new window)', 'interactive-world-maps' ),
		'actionAlert'                 => __( 'Action - Alert', 'interactive-world-maps' ),
		'actionAlertDescription'      => __( 'An alert message will display with the text you specify in the "Action Value" field.', 'interactive-world-maps'),
		'displayContentBelowMap'      => __( 'Display Content Below Map', 'interactive-world-maps' ),
		'displayContBelowDescription' => __( 'The content of the "Action Value" field will display inside a div under the map. The div will have the id="imapMAPIDmessage" (for example <i>imap1message</i>) and can be customized with CSS.', 'interactive-world-maps'),
		'displayContentLightboxDesc'  => __( 'A lighbox window will open with the content of the "Action Value" field.', 'interactive-world-maps' ),
 		'displayContBelowScrollDesc'  => __( 'The content of the "Action Value" field will display inside a div under the map and the page will scroll to that div to make it visible on the screen. It\'s usefull when the maps are big and take all the screen. The div will have the id="imapMAPIDmessage" (for example <i>imap1message</i>) and can be customized with CSS.', 'interactive-world-maps' ),
		'messageToDisplay'            => __( 'Message to display on alert', 'interactive-world-maps' ),
		'contentToDisplayBelow'       => __( 'Content to display below map. HTML can be used', 'interactive-world-maps' ),
		'displayContBelowScroll'      => __( 'Display Content Below Map & Scroll', 'interactive-world-maps' ),
		'displayContentAbove'         => __( 'Display Content Above Map', 'interactive-world-maps' ),
		'displayContentAboveDesc'     => __( 'The content of the "Action Value" field will display inside a div above the map. The div will have the id="imapMAPIDmessage" (for example <i>imap1message</i>) and can be customized with CSS.', 'interactive-world-maps' ),
		'contentToDisplayAbove'       => __( 'Content to display above map. HTML can be used', 'interactive-world-maps' ),
		'displayContentLightbox'      => __( 'Display Content in Lightbox', 'interactive-world-maps' ),
		'displayUrlLightbox'          => __( 'Display URL in a Lightbox (iframe)', 'interactive-world-maps' ),
		'displayInlineContent'        => __( 'Display inline content', 'interactive-world-maps' ),
		'displayImageLightbox'        => __( 'Display Image in a Lightbox', 'interactive-world-maps' ),
		'customAction'                => __( 'Action - Custom', 'interactive-world-maps' ),
		'displayContentRight'         => __( 'Action - Display content on the right', 'interactive-world-maps' ),
		'contentToDisplayLightbox'    => __( 'Content to display inside lightbox. HTML can be used', 'interactive-world-maps' ),
		'iframeURL'                   => __( 'Full URL to the page you want to open in the iframe.', 'interactive-world-maps' ),
		'inlineHelper'                => __( '#ID or .class selector of the inline element to display.', 'interactive-world-maps' ),
		'fullUrlLightbox'             => __( 'Full URL to the image to open with the lightbox.', 'interactive-world-maps' ),
		'customActionContent'         => __( 'Content to use with your custom action.', 'interactive-world-maps' ),
		'contentRight'                => __( 'Content to display on the right of the map', 'interactive-world-maps' ),
		'contentRightDesc'            => __( 'The content will display to the right of the map. You can use the "description" field to set the default value to display before any region or marker is clicked.', 'interactive-world-maps' ),
		'customActionDesc'            => __( 'Create your custom action.', 'interactive-world-maps' ),
		'imageLightboxDesc'           => __( 'A lighbox window will open with the image you specify in the action value. Use the complete URL to the image.', 'interactive-world-maps' ),
		'inlineDesc'                  => __( 'A lighbox window will open with the content of the inline element you specify in the action value. Use a class selector like ".name" or an id, like "#name".', 'interactive-world-maps' ),
		'urlLightboxDesc'             => __( 'A lighbox window will open with the URL you place in the action value.', 'interactive-world-maps' ),
		'openUrlNewDesc'              => __( 'The URL you specify in the "Action Value" field will open in a new window, after the user clicked on that region.', 'interactive-world-maps' ),
		'openUrlDesc'                 => __( 'The URL you specify in the "Action Value" field will open in the same window, after the user clicked on that region.', 'interactive-world-maps' ),
	);

	wp_localize_script( 'iwjsadmin', 'iwmlocal', $local );

	/** Enqueue */
	wp_enqueue_script( 'iwjsgeo' );
	wp_enqueue_script( 'iwjsapiloader' );
	wp_enqueue_script( 'iwjsapi' );
	wp_enqueue_style( 'i_world_map_css' );
	wp_enqueue_style( 'i_world_map_styles_css' );
	wp_enqueue_script( 'iwjscolor' );
	wp_enqueue_script( 'iwjsadmin' );
	wp_enqueue_style( 'i_world_map_fontawesome' );

}
