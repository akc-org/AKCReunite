<?php


// VISUAL COMPOSER CLASS
class iwm_VCExtendAddonClass {
	function __construct() {
		// We safely integrate with VC with this hook
		add_action( 'init', array( $this, 'integrateWithVC' ) );

	}

	public function integrateWithVC() {
		// Check if Visual Composer is installed
		if ( ! defined( 'WPB_VC_VERSION' ) || ! function_exists( 'vc_map' ) ) {
			// Display notice that Visual Compser is required
			// add_action( 'admin_notices', array( $this, 'showVcVersionNotice' ));
			return;
		}

		global $wpdb;
		$table_name_imap = i_world_map_table_name();

		$maps_created = $wpdb->get_results( "SELECT `id`, `name` FROM $table_name_imap", ARRAY_A );

		$maps = array();

		$maps[ __( 'Please Select...', 'interactive-world-maps' ) ] = 0;

		foreach ( $maps_created as $map ) {
			$maps[ $map['name'] ] = $map['id'];
		}

		$manage_url = get_admin_url() . 'admin.php?page=i_world_map_menu';

		if ( function_exists( 'vc_map' ) ) {

			vc_map(
				array(
					'name'             => __( 'Interactive Map', 'interactive-world-maps' ),
					'description'      => __( 'Insert map previously created', 'interactive-world-maps' ),
					'base'             => 'show-map',
					'class'            => '',
					// "front_enqueue_css" => plugins_url( 'includes/visual_composer.css', __FILE__),
					'front_enqueue_js' => plugins_url( 'includes/js/visual_composer.js', __FILE__ ),
					'icon'             => plugins_url( 'imgs/icon-32.png', __FILE__ ),
					'category'         => __( 'Content', 'interactive-world-maps' ),
					'params'           => array(
						array(
							'admin_label' => true,
							'type'        => 'dropdown',
							'holder'      => 'hidden',
							'class'       => '',
							'heading'     => __( 'Map to display', 'interactive-world-maps' ),
							'param_name'  => 'id',
							'value'       => $maps,
							'description' => __( "Choose one of the previously created maps. <br> <a href='" . $manage_url . "' target='_blank'>Click here to go to your Manage Maps page</a>", 'interactive-world-maps' ),
						),
					),

				)
			);

		}

	}
}
// Finally initialize code
new iwm_VCExtendAddonClass();

?>