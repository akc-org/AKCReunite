<?php
/**
 * Functions for the child theme.
 */

// Enqueue parent and child theme styles
function child_theme_enqueue_styles() {
    $parent_style = 'parent-style'; // Style handle for the parent theme

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version') // Use the child theme version
    );
}
add_action( 'wp_enqueue_scripts', 'child_theme_enqueue_styles' );

/*
* ___________________________
*        Theme Support
* _____________________________
*/
require_once __DIR__ . '/functions/init.php';

