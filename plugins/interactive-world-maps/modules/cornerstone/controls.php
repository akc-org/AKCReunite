<?php

/**
 * Element Controls
 */

global $wpdb;
$table_name_imap = i_world_map_table_name();

$sql_fields       = i_world_map_get_SQL_fields();
$iwm_maps_created = $wpdb->get_results( 'SELECT ' . $sql_fields . ' FROM ' . $table_name_imap, ARRAY_A );

if ( count( $iwm_maps_created ) === 0 ) {
	$iwm_maps = array(
		array(
			'value' => '',
			'label' => __( '-- Please create a map first --', 'interactive-world-maps' ),
		),
	);

} else {

	$iwm_maps = array(
		array(
			'value' => '',
			'label' => __( '-- Please Select --', 'interactive-world-maps' ),
		),
	);

}

foreach ( $iwm_maps_created as $map ) {
	$temp = array(
		'value' => $map['id'],
		'label' => $map['name'],
	);
	array_push( $iwm_maps, $temp );
}

return array(

	'heading'       => array(
		'type'    => 'text',
		'ui'      => array(
			'title'   => __( 'Title', 'my-extension' ),
			'tooltip' => __( 'Add a title above the map', 'interactive-world-maps' ),
		),
		'context' => 'content',
	),

	'heading_color' => array(
		'type' => 'color',
		'ui'   => array(
			'title' => __( 'Title Color', 'interactive-world-maps' ),
		),
	),

	'align'         => array(
		'type'    => 'choose',
		'ui'      => array(
			'title' => __( 'Title Alignment', 'interactive-world-maps' ),
		),
		'options' => array(
			'columns' => '3',
			'choices' => array(
				array(
					'value' => 'left',
					'label' => __( 'Left', 'cornerstone' ),
					'icon'  => fa_entity( 'align-left' ),
				),
				array(
					'value' => 'center',
					'label' => __( 'Center', 'cornerstone' ),
					'icon'  => fa_entity( 'align-center' ),
				),
				array(
					'value' => 'right',
					'label' => __( 'Right', 'cornerstone' ),
					'icon'  => fa_entity( 'align-right' ),
				),
			),
		),
	),

	'iwmid'         => array(
		'type'    => 'select',
		'ui'      => array(
			'title'   => __( 'Choose Map', 'interactive-world-maps' ),
			'tooltip' => __( 'Choose which previously created map to display. Will only be visible on live site.', 'interactive-world-maps' ),
		),
		'options' => array(
			'choices' => $iwm_maps,
		),
	),
	'map_padding'   => array(
		'type' => 'dimensions',
		'ui'   => array(
			'title' => __( 'Map Padding', 'interactive-world-maps' ),
		),
	),


);
