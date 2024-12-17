<?php

/*
Plugin Name: WP Google Analytics Events
Plugin URI: http://wpflow.com
Description: No-Code Custom Event Tracking for Google Analytics.
Version: 2.8.2
Author: PineWise
Author URI: http://wpflow.com
License: GPLv2
 */

defined('ABSPATH') or die('Direct access not allowed!');

/* ------------------------------------------------------------------------ *
 * Setup
 * ------------------------------------------------------------------------ */

// Script debugging
define('GAE_SCRIPT_DEBUG',
    false);

// Paths
define('GAE_PLUGIN_PATH',
    plugin_dir_path(__FILE__));
define('GAE_PLUGIN_URL',
    plugin_dir_url(__FILE__));

// Current version number
if (!defined('GA_EVENTS_VERSION')) {
    define('GA_EVENTS_VERSION',
        '2.7.1');
}

// Dependencies

require_once GAE_PLUGIN_PATH . '/include/Activation.php';

// Activation hook
register_activation_hook(__FILE__,
    array('GAEActivation', 'start_activation'));

/* ------------------------------------------------------------------------ *
 * Main plugin class
 * ------------------------------------------------------------------------ */
if (!class_exists('GaEvents')) {

    class GAEvents
    {

        public function __construct()
        {
            if (is_admin()) {
                $this->init_hooks();
                $this->do_settings();
                $this->load_admin_scripts();
                $this->load_admin_styles();
                include dirname(__FILE__) . '/include/notice.php';

            }
            require_once GAE_PLUGIN_PATH . '/include/Snippets.php';
            $this->include_snippet();
            $this->load_front_end_scripts();
        }

        public function init_hooks()
        {
            require_once GAE_PLUGIN_PATH . '/include/EventClasses.php';
            require_once GAE_PLUGIN_PATH . '/include/Settings.php';

            add_action('init', 'WPGAEGClickEvent::wpgae_register_click_event_post_type');
            add_action('init', 'WPGAEGScrollEvent::wpgae_register_scroll_event_post_type');

            add_action('wp_ajax_wpflow_add_event', 'wpflow_event_ajax_handler');
            add_action('wp_ajax_wpflow_edit_event', 'wpflow_event_ajax_handler');
            add_action('wp_ajax_wpflow_delete_event', 'wpflow_event_ajax_handler');
            add_action('wp_ajax_wpflow_delete_event', 'wpflow_event_ajax_handler');
            add_action('wp_ajax_wpflow_save_view', 'wpflow_event_ajax_handler');
            add_action('wp_ajax_wpflow_ga_disconnect', 'wpflow_ga_disconnect_event_ajax_handler');

            function wpflow_event_ajax_handler()
            {
                /**
                 * The Ajax Handler for the plugin.
                 * Note that all metadata updates through the save_post hook and not showing here.
                 */
                // Handle the ajax request

                if (isset($_POST[ "action" ]) && $_POST[ "action" ] == "wpflow_save_view") {
                    check_ajax_referer('wpflow_save_view', "ajax_nonce", true);
                } else {
                    check_ajax_referer('wpflow_add_event', "ajax_nonce", true);
                }

                if (isset($_POST[ "action" ]) && $_POST[ "action" ] == "wpflow_add_event") {
                    if (!isset($_POST[ 'wpgae_type' ])) {
                        wp_die("wpgae_type cannot be empty");
                    }

                    $post_type = sanitize_text_field($_POST[ 'wpgae_type' ]);
                    $postarr = array(
                        'post_type' => $post_type,
                        'post_status' => 'publish',
                    );

                    $post_id = wp_insert_post($postarr);
                } else if (isset($_POST[ "action" ]) && $_POST[ "action" ] == "wpflow_delete_event") {
                    $post = wp_delete_post(intval($_POST[ "event_id" ]), true);
                    WPGAEGeneralEvent::update_cache_by_post_type($post->post_type);
                } else if (isset($_POST[ "action" ]) && $_POST[ "action" ] == "wpflow_edit_event") {
                    if (isset($_POST[ "event_id" ]) && $_POST[ "event_id" ]) {
                        $post_id = get_post(intval($_POST[ "event_id" ]));
                        wp_update_post($post_id);
                    }
                } else if (isset($_POST[ "action" ]) && $_POST[ "action" ] == "wpflow_save_view") {
                    if (isset($_POST[ 'viewId' ]) && $_POST[ 'viewId' ]) {
                        $view_id = $_POST[ 'viewId' ];
                        $options = get_option('ga_events_options');
                        $options[ 'ga_view_id' ] = $view_id;
                        update_option('ga_events_options', $options);
                    }
                }
                wp_die(); // All ajax handlers die when finished
            }

            function validateClickAndScrollForm($post_data)
            {
                /**
                 * Event Tracking rules -
                 * 1. Selector and Type cannot be empty - plugin restriction
                 * 2. Event Category and action cannot be empty - Google Analytics restrictions
                 * 3. Value can either be set to a number or not set at all
                 * 4. If the selector is not of type advanced, no spaces or . or # are allowed
                 */
                $form_issues = array();

                if (array_key_exists("wpgae_event_selector", $post_data) && $post_data[ "wpgae_event_selector" ] != "") {
                    if (!array_key_exists("wpgae_event_type", $post_data) || $post_data[ "wpgae_event_type" ] != "advanced") {
                        if ($post_data[ "wpgae_event_selector" ] != sanitize_html_class($post_data[ "wpgae_event_selector" ])) {
                            array_push($form_issues, 'Cannot use non alphabetical or numerical values other than "_" and "-" when
						Selector is set to Class or Id');
                        }
                    }
                } else {
                    array_push($form_issues, "Selector cannot be empty");
                }
                if (sizeof($form_issues) > 0) {
                    wp_send_json_error($form_issues);
                    wp_die();
                }
            }

            add_action('wp_ajax_wpflow_get_event_json', 'wpflow_get_event_json_callback');

            function wpflow_get_event_json_callback()
            {

                // retrieve post_id, and sanitize it to enhance security
                $post_id = intval($_POST[ 'post_id' ]);

                // Check if the input was a valid integer
                if ($post_id == 0) {

                    $response[ 'error' ] = 'true';
                    $response[ 'result' ] = 'Invalid Input';

                } else {

                    // get the post
                    $thispost = get_post($post_id);
                    // check if post exists
                    if (!is_object($thispost)) {

                        $response[ 'error' ] = 'true';
                        $response[ 'result' ] = 'There is no post with the ID ' . $post_id;

                    } else {

                        $post_meta = get_post_meta($thispost->ID);
                        $response[ "meta" ] = $post_meta;
                        $response[ 'error' ] = 'false';
                        $response[ 'result' ] = wpautop($thispost->post_content);

                    }

                }

                wp_send_json($response);

            }

        }

        public function do_settings()
        {
            $settings_instance = new GAESettings();

            // Handle import options button from general settings
            add_action('admin_init',
                array($settings_instance, 'upgrade_settings'));

            // Handle import options button from general settings
            add_action('admin_init',
                array($settings_instance, 'ga_events_import_settings'));

            // Handle export options button from general settings
            add_action('admin_init',
                array($settings_instance, 'ga_events_download_settings'));

            // add plugin menu
            add_action('admin_menu',
                array($settings_instance, 'ga_events_menu'));

            // Initialise plugin options
            add_action('admin_init',
                array($settings_instance, 'ga_events_initialize_plugin_options'));

            add_filter("admin_footer", array($settings_instance, 'ga_events_feedback_form_html'));

            // Display settings link next to plugin listing on plugins page
            $plugin = plugin_basename(__FILE__);
            add_filter("plugin_action_links_$plugin",
                array($settings_instance, 'plugin_add_settings_link'));

            // Add Ajax functionality for the admin UI
            add_action('wp_ajax_wpflow_update', 'wpflow_ajax_update_handler');

            add_action('wp_ajax_wpflow_save_view', 'wpflow_event_ajax_handler');
            add_action('wp_ajax_wpflow_ga_disconnect', 'wpflow_ga_disconnect_event_ajax_handler');

            function wpflow_ga_disconnect_event_ajax_handler()
            {

                if (isset($_POST[ "action" ]) && $_POST[ "action" ] == "wpflow_ga_disconnect") {
                    check_ajax_referer('wpflow_ga_disconnect', "ajax_nonce", true);
                    $options = get_option('ga_events_options');
                    $update_options = $options;
                    if (isset($options[ 'ga_view_id' ])) {
                        $update_options[ 'ga_view_id' ] = -1;
                        $update = update_option('ga_events_options', $update_options);
                    }
                }
                wp_die(); // All ajax handlers die when finished
            }

            function wpflow_ajax_update_handler()
            {
                // Handle the ajax request
                check_ajax_referer('wpflow-nounce');
                wp_die(); // All ajax handlers die when finished
            }
        }

        public function load_admin_scripts()
        {

            $ga_options = get_option('ga_events_options');
            $debug_mode = isset($ga_options[ 'script_debug_mode' ]) ? $ga_options[ 'script_debug_mode' ] : '0';

            // This condition need to be checked in localize_placeholder function too!
            if (GAE_SCRIPT_DEBUG || $debug_mode) {
                add_action('admin_enqueue_scripts',
                    array($this, 'enqueue_admin_scripts_debug'));
            } else {
                add_action('admin_enqueue_scripts',
                    array($this, 'enqueue_admin_scripts'));
            }
        }

        public function load_front_end_scripts()
        {

            $ga_options = get_option('ga_events_options');
            $debug_mode = isset($ga_options[ 'script_debug_mode' ]) ? $ga_options[ 'script_debug_mode' ] : '0';

            if (GAE_SCRIPT_DEBUG || $debug_mode == '1') {
                add_action('wp_enqueue_scripts',
                    array($this, 'enqueue_front_end_scripts_debug'));
            } else {
                add_action('wp_enqueue_scripts',
                    array($this, 'enqueue_front_end_scripts'));
            }
        }

        public function load_admin_styles()
        {

            add_action('admin_enqueue_scripts',
                array($this, 'enqueue_admin_styles'));
        }

        /* --------------------------------------------------------------------- *
         * Custom Post Types
         * --------------------------------------------------------------------- */

        /* --------------------------------------------------------------------- *
         * Register and enqueue admin scripts
         * --------------------------------------------------------------------- */

        public function enqueue_admin_scripts($hook)
        {
            $screen = get_current_screen();

            if (strpos($hook, "wp-google-analytics-events") == false && $screen->id !== "plugins") {
                return;
            }
            wp_register_script('tailwindcss', 'https://cdn.tailwindcss.com', null, null, true);

            wp_enqueue_script('tailwindcss');
            wp_register_script('admin_bundle_libs',
                GAE_PLUGIN_URL . 'js/dist/admin-scripts-libs.js',
                array('jquery', 'tailwindcss'),
                true);

            wp_register_script('admin_bundle',
                GAE_PLUGIN_URL . 'js/dist/admin-scripts.min.js',
                array('jquery', 'tailwindcss', 'jquery-ui-tooltip', 'admin_bundle_libs'),
                true);

            // Localize plugin options in database for use in main JS script
            wp_localize_script('admin_bundle',
                'ga_options',
                get_option('ga_events_options'));

            wp_enqueue_script('admin_bundle');

            $wpflow_nonce = wp_create_nonce('wpflow-nounce');
            wp_localize_script('admin_bundle', 'wpflow_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => $wpflow_nonce, // It is common practice to comma after
            ));

            $options = get_option('ga_events_options');
            if (isset($options[ 'ga_view_id' ]) && $options[ 'ga_view_id' ] != -1) {
                wp_localize_script('admin_bundle', 'ga_reports', $this->get_ga_reports_localization());
            }
        }

        /* --------------------------------------------------------------------- *
         * Register and enqueue admin scripts - DEBUGGING!
         * --------------------------------------------------------------------- */

        public function enqueue_admin_scripts_debug($hook)
        {
            $screen = get_current_screen();

            if (strpos($hook, "wp-google-analytics-events") == false && $screen->id != "plugins") {
                return;
            }
            // Localize plugin options in database for use in main JS script
            wp_localize_script('gae_mapper',
                'ga_options',
                get_option('ga_events_options'));

            // Needed for the admin UI
            wp_enqueue_script('jquery_modal',
                GAE_PLUGIN_URL . 'js/third-party/jquery.modal.min.js',
                array('jquery'),
                false);

//      wp_enqueue_script('jquery_form_validator',
//        GAE_PLUGIN_URL . 'js/third-party/jquery.form-validator.min.js',
//        array('jquery'),
//        false);

            // Admin JS

            wp_register_script('tailwindcss', 'https://cdn.tailwindcss.com', null, null, true);

            wp_enqueue_script('tailwindcss');

            wp_enqueue_script('reports_js',
                GAE_PLUGIN_URL . 'js/reports.js',
                array('jquery', 'tailwindcss', 'admin_bundle_libs'),
                '1.0',
                true);

            wp_enqueue_script('admin_js',
                GAE_PLUGIN_URL . 'js/admin.js',
                array('jquery', 'tailwindcss', 'jquery-ui-tooltip'),
                '1.0',
                true);

            wp_register_script('admin_bundle_libs',
                GAE_PLUGIN_URL . 'js/dist/admin-scripts-libs.js', array('jquery'),
                false);

            // Ajax for settings
            wp_enqueue_script('ajax_settings_js',
                GAE_PLUGIN_URL . 'js/ajax-settings.js',
                array('jquery'),
                '1.0',
                false);

            $wpflow_nonce = wp_create_nonce('wpflow-nounce');

            wp_localize_script('ajax_settings_js', 'wpflow_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => $wpflow_nonce, // It is common practice to comma after
            ));

            if (isset($options[ 'ga_view_id' ]) && $options[ 'ga_view_id' ] != -1) {
                wp_localize_script('admin_js', 'ga_reports', $this->get_ga_reports_localization());
            }
        }

        public function get_ga_reports_localization()
        {
            $options = get_option('ga_events_options');
            $click_categories = "";
            $click_labels = "";
            $click_actions = "";
            $i = 0;
            foreach ($options[ 'click_elements' ] as $click_event) {
                if ($i > 0) {
                    if (!empty($click_categories)) {$click_categories = $click_categories . ',';}
                    if (!empty($click_labels)) {$click_labels = $click_labels . ',';}
                    if (!empty($click_actions)) {$click_actions = $click_actions . ',';}
                }
                if (!empty($click_event[ 'category' ])) {$click_categories = $click_categories . 'ga:eventCategory==' . $click_event[ 'category' ];}
                if (!empty($click_event[ 'label' ])) {$click_labels = $click_labels . 'ga:eventLabel==' . $click_event[ 'label' ];}
                if (!empty($click_event[ 'action' ])) {$click_actions = $click_actions . 'ga:eventAction==' . $click_event[ 'action' ];}
                $i++;
            }
            $scroll_categories = "";
            $scroll_labels = "";
            $scroll_actions = "";
            $i = 0;
            foreach ($options[ 'scroll_elements' ] as $scroll_event) {
                if ($i > 0) {
                    if (!empty($scroll_categories)) {$scroll_categories = $scroll_categories . ',';}
                    if (!empty($scroll_labels)) {$scroll_labels = $scroll_labels . ',';}
                    if (!empty($scroll_actions)) {$scroll_actions = $scroll_actions . ',';}
                }
                if (!empty($scroll_event[ 'category' ])) {$scroll_categories = $scroll_categories . 'ga:eventCategory==' . $scroll_event[ 'category' ];}
                if (!empty($scroll_event[ 'label' ])) {$scroll_labels = $scroll_labels . 'ga:eventLabel==' . $scroll_event[ 'label' ];}
                if (!empty($scroll_event[ 'action' ])) {$scroll_actions = $scroll_actions . 'ga:eventAction==' . $scroll_event[ 'action' ];}
                $i++;
            }

            return array(
                'ga_view_id' => $options[ 'ga_view_id' ],
                'clicksCategories' => $click_categories,
                'clicksLabels' => $click_labels,
                'clicksActions' => $click_actions,
                'scrollCategories' => $scroll_categories,
                'scrollLabels' => $scroll_labels,
                'scrollActions' => $scroll_actions,
            );
        }

        public function enqueue_front_end_scripts()
        {

            // Get plugin options from the database. Needed for conditional enqueueing
            $ga_options = get_option('ga_events_options');

            /* --------------------------------------------------------------- *
             * Register frontend scripts
             * ---------------------------------------------------------------- */

            wp_register_script('ga_events_frontend_bundle',
                GAE_PLUGIN_URL . 'js/dist/frontend-scripts.min.js',
                array('jquery'),
                '1.0',
                true);

            // Localize plugin options in database for use in main JS script
            wp_localize_script('ga_events_frontend_bundle',
                'ga_options',
                prepare_ga_options_for_frontend());

            $this->localize_placeholders();
            /* ------------------------------------------------------------- *
             * Enqueue frontend scripts
             * -------------------------------------------------------------- */

            wp_enqueue_script('ga_events_frontend_bundle');
        }

        public function enqueue_front_end_scripts_debug()
        {

            // Get plugin options from the database. Needed for conditional enqueueing
            $ga_options = get_option('ga_events_options');

            /* --------------------------------------------------------------------- *
             * Register frontend scripts
             * --------------------------------------------------------------------- */

            wp_register_script('ga_events_main_script',
                GAE_PLUGIN_URL . 'js/main.js',
                array('jquery'),
                '1.0',
                false);

            // Localize plugin options in database for use in main JS script
            wp_localize_script('ga_events_main_script',
                'ga_options',
                prepare_ga_options_for_frontend());

            $this->localize_placeholders();

            /* --------------------------------------------------------------------- *
             * Enqueue frontend scripts
             * --------------------------------------------------------------------- */

            // Tracking is disabled for selected roles so we load tracking
            wp_enqueue_script('ga_events_main_script');
        }

        public function enqueue_admin_styles($hook)
        {
            if (strpos($hook, "wp-google-analytics-events") == false) {
                return;
            }
            wp_enqueue_style('wpgae_font_awesome',
                '//maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css');

            wp_enqueue_style('wpgae_admin_styles',
                GAE_PLUGIN_URL . 'css/style.css',
                array('wpgae_font_awesome'));

            wp_enqueue_style('wpgae_jquery_modal_styles',
                GAE_PLUGIN_URL . 'css/third-party/jquery.modal.min.css');

            wp_enqueue_style('wpgae_jquery_ui_styles',
                GAE_PLUGIN_URL . 'css/third-party/jquery-ui.min.css');

            wp_enqueue_style('wpgae_jquery_daterangepicker_styles',
                GAE_PLUGIN_URL . 'css/third-party/jquery.comiseo.daterangepicker.css');

        }

        public function include_snippet()
        {
            add_action('wp_head',
                array('GAESnippets', 'add_snippet_to_header'),
                0);
        }

        public function localize_placeholders()
        {

            global $wp_query;

            $page_title = !empty($wp_query->post->post_title) ? $wp_query->post->post_title : '';

            $translation_array = array(
                // home page is home page not page title
                'is_front_page' => is_front_page(),
                'page_title' => $page_title,
            );

            $ga_options = get_option('ga_events_options');
            $debug_mode = isset($ga_options[ 'script_debug_mode' ]) ? $ga_options[ 'script_debug_mode' ] : '0';

            // These need to be conditional on GAE_SCRIPT_DEBUG
            if (GAE_SCRIPT_DEBUG || $debug_mode) {
                // gae_mapper is bundled with admin and frontend scripts
                wp_localize_script('ga_events_main_script',
                    'gaePlaceholders',
                    $translation_array);
            } else {
                wp_localize_script('admin_bundle',
                    'gaePlaceholders',
                    $translation_array);

                wp_localize_script('ga_events_frontend_bundle',
                    'gaePlaceholders',
                    $translation_array);
            }
        }

        public function isAdvancedModeOn()
        {
            $wpgae_options = get_option('ga_events_options');
            if (isset($wpgae_options[ 'advanced' ]) && $wpgae_options[ "advanced" ] == 1) {
                return true;
            }
            return false;

        }
    }

    /*
     * Remove sensitive data from the frontend
     */
    function prepare_ga_options_for_frontend()
    {
        $sensitive_options = array("script_debug_mode");
        $ga_options = get_option('ga_events_options');
        foreach ($sensitive_options as $key_to_remove) {
            if (array_key_exists($key_to_remove, $ga_options)) {
                unset($ga_options[ $key_to_remove ]);
            }
        }
        if (!array_key_exists("force_snippet", $ga_options)) {
            $ga_options[ 'force_snippet' ] = "none";
        }
        return $ga_options;
    }

}

$wpgae = new GaEvents();
