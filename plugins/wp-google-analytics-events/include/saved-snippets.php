<?php



function ga_events_get_settings(){
	$options = get_option('ga_events_options');
	$current = json_encode($options);

	return $current;
}

/*
 * Define shortcode for scroll events
 */

function ga_events_short_scroll( $attr, $content ) {

    $cat	 = isset( $attr[ 'cat' ] ) ? $attr[ 'cat' ] : 'Default Category';
    $action	 = isset( $attr[ 'action' ] ) ? $attr[ 'action' ] : 'Default Action';
    $label	 = isset( $attr[ 'label' ] ) ? $attr[ 'label' ] : 'Default Label';
    $bounce	 = isset( $attr[ 'bounce' ] ) ? $attr[ 'bounce' ] : 'false';
    $evalue	 = isset( $attr[ 'evalue' ] ) ? $attr[ 'evalue' ] : '';

    if ( ! empty( $content ) ) {
	$new_content = add_class_to_element( $content, 'scrollevent', $cat, $action, $label, $bounce, $evalue );
	return $new_content;
    }
}

add_shortcode( 'scrollevent', 'ga_events_short_scroll' );

/*
 * Define shortcode for click events
 */

function ga_events_short_click( $attr, $content ) {

    $cat	 = isset( $attr[ 'cat' ] ) ? $attr[ 'cat' ] : 'Default Category';
    $action	 = isset( $attr[ 'action' ] ) ? $attr[ 'action' ] : 'Default Action';
    $label	 = isset( $attr[ 'label' ] ) ? $attr[ 'label' ] : 'Default Label';
    $bounce	 = isset( $attr[ 'bounce' ] ) ? $attr[ 'bounce' ] : 'false';
    $evalue	 = isset( $attr[ 'evalue' ] ) ? $attr[ 'evalue' ] : '';



    if ( ! empty( $content ) ) {
	$new_content = add_class_to_element( $content, 'clickevent', $cat, $action, $label, $bounce, $evalue );
	return $new_content;
    }
}

add_shortcode( 'clickevent', 'ga_events_short_click' );

;