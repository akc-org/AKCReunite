<?php
/**
 * AKC OneTrust
 *
 * @category  WordPressPlugin
 * @package   AKC-OneTrust
 * Author:    Web Dev
 * License:   GPL-2.0+
 * Text Domain: akc
 *
 * Plugin Name: AKC OneTrust
 * Description: A solution for all WordPress sites to enable the OneTrust SDK to selectively load scripts based on user response to the cookie banner.
 * Version:     1.1.1
 */

if (!defined('ABSPATH')) {
    exit;
}

// Dictionary to check against enqueued handles
$handle_dictionary = array(
    '<blank>'                                     => 'optanon-category-C0001',
    'googleapis'                                  => 'optanon-category-C0004',
    'google'                                      => 'optanon-category-C0002',
    'gmap'                                        => 'optanon-category-C0004',
    'blueconic'                                   => 'optanon-category-C0004',
    'akc-ga'                                   => 'optanon-category-C0004',
    'facebook'                                    => 'optanon-category-C0005',
    'youtube'                                     => 'optanon-category-C0005',
    // Add more keys and their corresponding classes as needed
);

function customize_script_tags($tag, $handle) {
    global $handle_dictionary;

    // Loop over dictionary keys and check if the handle contains the key
    foreach ($handle_dictionary as $key => $class) {
        if (strpos($handle, $key) !== false) {
            // Add the class
            $tag = str_replace('<script', '<script class="' . esc_attr($class) . '"', $tag);
            // Add or replace type
            if (strpos($tag, 'type=') !== false) {
                $tag = preg_replace('/type=["\'][^"\']*["\']/', 'type="text/plain"', $tag);
            } else {
                $tag = str_replace('<script', '<script type="text/plain"', $tag);
            }
            break; // Stop checking once a match is found
        }
    }

    return $tag;
}
add_filter('script_loader_tag', 'customize_script_tags', 10, 2);