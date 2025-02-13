<?php
/**
* AKC OneTrust
*
* @category  WordPressPlugin
* @package   AKC-OneTrust
* @author    AKC Dev <webdev@akc.org>
* @copyright 2024 AKC
* @license   GPL-2.0+ https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
* @link      https://www.akc.org/
*
* @wordpress-plugin
* Plugin Name: AKC OneTrust
* Plugin URI:  https://www.akc.org
* Description: A genius-level solution for all WordPress sites to enable the onetrust SDK to selectively load scripts based on user selection by catching them when they are being loaded and changing their type and class.
* Version:     1.0.0
* Author:      Web Dev
* Author URI:  https://www.akc.org
* Text Domain: akc
* License:     GPL-2.0+
**/

if (!defined('ABSPATH')) {
    exit;
}

// <script> objects that contain a key from this dictionary will be modified, i.e., the type will be set to text/plain and the value will be added as a class
$dictionary = array(
    '<blank>' => 'optanon-category-C0001',
    'googleapis' => 'optanon-category-C0002',
    'google' => 'optanon-category-C0002',
    'gmap' => 'optanon-category-C0002',
    'blueconic' => 'optanon-category-C0002',
    'akc-ga' => 'optanon-category-C0002',
    'facebook' => 'optanon-category-C0002',
    'youtube' => 'optanon-category-C0002',
    'vimeo' => 'optanon-category-C0002',
    'mailchimp' => 'optanon-category-C0002',
    'wp-goal-tracker' => 'optanon-category-C0002',
    'wp-goal-tracker-ga-js-extra' => 'optanon-category-C0002',
    'www-widgetapi-script' => 'optanon-category-C0002',
    'bing' => 'optanon-category-C0002',
    // Add more keys and their corresponding classes as needed
);

function modify_script_attributes() {
    ob_start();
}
add_action('wp_enqueue_scripts', 'modify_script_attributes', 1);

function add_custom_script_attributes() {
    global $dictionary;
    $output = ob_get_clean();

    // Use regex to find all <script> tags
    $output = preg_replace_callback(
        '/<script([^>]*)>(.*?)<\/script>/is',
        function ($matches) use ($dictionary) {
            $attributes = $matches[1];
            $src = '';
            $id = '';

            // Extract the src attribute if it exists
            if (preg_match('/src=[\'"]?([^\'" ]+)[\'"]?/', $attributes, $srcMatch)) {
                $src = $srcMatch[1]; // Get the src value
            }

            // Extract the id attribute if it exists
            if (preg_match('/id=[\'"]?([^\'" ]+)[\'"]?/', $attributes, $idMatch)) {
                $id = $idMatch[1]; // Get the id value
            }

            // Concatenate id to src if both are found
            if ($src && $id) {
                $src .= $id;
            }

            // Check if the src (now including the id) contains any key from the dictionary
            foreach ($dictionary as $key => $value) {
                // if the key appears in the src (which may now include the id)
                if (strpos($src, $key) !== false) {
                    // Set or modify the type attribute
                    if (strpos($attributes, 'type=') === false) {
                        $attributes .= ' type="text/plain"'; // Append if it doesn't exist
                    } else {
                        // Modify existing type attribute
                        $attributes = preg_replace('/type=[\'"]?[^\'" ]*[\'"]?/', 'type="text/plain"', $attributes);
                    }

                    // Add the dictionary value as a class
                    if (strpos($attributes, 'class=') === false) {
                        $attributes .= ' class="' . esc_attr($value) . '"';
                    } else {
                        // Append to existing classes
                        $attributes = preg_replace('/class=[\'"]?([^\'"]*)[\'"]?/', 'class="$1 ' . esc_attr($value) . '"', $attributes);
                    }

                    break; // No need to check other keys once a match is found
                }
            }

            return "<script$attributes>{$matches[2]}</script>";
        },
        $output
    );

    echo $output;
}
add_action('wp_footer', 'add_custom_script_attributes', 99);

function test_load() {
    echo '<script src="https://google.com/cookie-loader.js" id="custom-id"></script>';
}
add_action('wp_enqueue_scripts', 'test_load');
