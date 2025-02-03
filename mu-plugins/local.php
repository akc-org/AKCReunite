<?php
/**
 * AKC Local
 *
 * @category  WordPressPlugin
 * @package   AKC-Local
 * @author    AKC Dev <webdev@akc.org>
 * @copyright 2023 AKC
 * @license   GPL-2.0+ https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html
 * @link      https://www.akc.org/
 *
 * @wordpress-plugin
 * Plugin Name: AKC Local
 * Plugin URI:  https://www.akc.org
 * Description: A WordPress plugin to enable dev mode on a local AKC installation.
 * Version:     1.0.0
 * Author:      Web Dev
 * Author URI:  https://www.akc.org
 * Text Domain: akc
 * License:     GPL-2.0+
 **/

// AJAX and chron both seem undependable and buggy on local docker containers.  Try database values for changes made and not
// Prevent direct file access.
if (!defined('ABSPATH') ) {
    exit;
}

/**
 * akc local class
 *
 * @since 1.0
 */
class local {

    private $db_changed = 0;
    /**
	 * The constructor of the object used to trigger actions by adding them to various hooks
	 *
	 * @since 1.0
	 */
    public function __construct() {

        $this->local_akc_add_option_if_none();

        add_action( 'init', [ $this, 'local_akc_local_add_user' ] );
        add_action('init', [ $this, 'local_akc_change_options' ] );
        add_action('init', [ $this, 'local_akc_gigya_handler' ] );
        add_action('wp_head', [ $this, 'local_akc_comment_hide_admin' ] );
        add_action('admin_notices', [ $this, 'general_admin_notice' ] );
        add_action('wp_loaded', [ $this, 'local_akc_add_user_caps' ] );
        add_action( 'admin_enqueue_scripts', array( $this, 'local_akc_enqueue_styles' ) );
        add_action( 'wp_ajax_local_akc_local_commit_button', [ $this, 'local_akc_local_commit_button' ] );
    }

    /**
	 * Function to add the dev mode option if it does not already exist
	 *
	 * @since 1.0
	 */
    public function local_akc_add_option_if_none() {
        $option_name = 'local_akc_dev_mode' ;
        $new_value = 'on' ;

        if ( get_option( $option_name ) !== 'off' ) {
            update_option( $option_name, $new_value );
        } else {
            $deprecated = ' ';
            $autoload = 'no';
            add_option( $option_name, $new_value, $deprecated, $autoload );
        }
    }

    /**
	 * Function to create a local user on this site.
	 *
	 * @since 1.0
	 */
    public function local_akc_local_add_user() {

        $username = 'akc1';
        $password = 'akc1';
        $email = 'akc1@example.com';

        if ( username_exists($username) == null && email_exists($email) == false ) {

            wp_create_user( $username, $password, $email );

        }

    }


    /**
	 * Function used to change the database option.
	 *
	 * @since 1.0
	 */
    public function local_akc_change_options() {

        update_option( 'mo_saml_add_sso_button_wp', 'false', true );
        $this->db_changed = 1;

    }

    /**
	 * Function used to change the database option.
	 *
	 * @since 1.0
	 */
    public function local_akc_add_user_caps() {

        $user_object = wp_get_current_user();
        update_user_meta( $user_object->ID, 'wp_capabilities', array( 'administrator' => 1, 'redirection_admin' => 1 ) );

    }

    /**
	 * Function which print an admin notice/badge onto the WordPress dashboard using their existing UI so it blends in.
	 *
	 * @since 1.0
	 */
    public function general_admin_notice(){

        $current_user = wp_get_current_user();
        $user_string = $current_user->user_login === 'akc1' ? '<span class="green">akc1</span>' : '<span class="red">current_user->user_login</span>';
        $admin_string = current_user_can( 'administrator' ) ? '<span class="green">true</span>' : '<span class="red">false</span>';
        $redirection_admin_string = current_user_can( 'redirection_admin' ) ? '<span class="green">true</span>' : '<span class="red">false</span>';
        $database_changes1_string = $this->db_changed == 1 ? '<span class="green">true</span>' : '<span class="red">false</span>';
        $plugin_count_string = '<span class="blue">' . count( get_option( 'active_plugins' ) ) . '</span>';
        $gigya_disabled_string = is_dir( WP_PLUGIN_DIR . '/gigya-socialize-for-wordpress-disabled' ) ? '<span class="green">true</span>' : '<span class="red">false</span>';
        $init_commented_string = strpos( file_get_contents( get_template_directory() . '/functions/init.php' ), '// Features\Hide_WP_Admin\Register::init();' ) == false ? '<span class="red">false</span>' : '<span class="green">true</span>';
        $local_config_string = file_exists( get_template_directory() . '/_local-config.php') ? '<span class="green">true</span>' : '<span class="red">false</span>';
        $dev_mode_string = get_option( 'local_akc_dev_mode', 'off' ) === 'on' ? '<span class="green">on</span>' : '<span class="red">off</span>';

        echo '<div class="notice notice-success admin-akc-aligned">
            <img
            src="' . WPMU_PLUGIN_URL . '/akc-local/img/logo.jpeg' . '"
            width="48"
            alt=""
            class="admin-akc-logo"
            >
                
            <span class="admin-akc-outter-span"><h3 class="admin-title">Development Mode is <span class="green">active.</span></h3>
            - Site Name: ' . '<span class="blue">' . get_option( 'blogname' ) . '</span><br />
            - Created User <span class="green">akc1</span><br />
            - Current login: ' . $user_string . '<br />
            - Administrator?: ' . $admin_string . '<br />
            - Redirection_admin: ' . $redirection_admin_string . '<br />
            </span>
            <span class="admin-akc-outter-span"><h3 class="admin-title">&nbsp;</h3>
            - Database Altered: ' . $database_changes1_string . '<br />
            - Gigya Disabled: ' . $gigya_disabled_string . '<br />
            - # of Plugins: ' . $plugin_count_string . '<br />
            - Init Commented: ' . $init_commented_string . '<br />
            - Local config file exists: ' . $local_config_string .
            '</span>
            <span class="admin-akc-outter-span">
            - AKC Dev Mode: ' . $dev_mode_string . '<br />
            - Plugin Updates:<br />
            - Theme updates:
            </span>

            <!-- <input type="button" class="akc-dev-btn button button-primary" value="Clean Up Commit"></input> -->
        </div>';

    }

    /**
	 * Function to enqueue stylesheets for this plugin
	 *
	 * @since 1.0
	 */
	public function local_akc_enqueue_styles() {

		wp_enqueue_style( 'local-styles', WPMU_PLUGIN_URL . '/akc-local/css/style.css', array(), '1.0.0' );
        wp_register_script( 'local_script', plugins_url( 'akc-local/js/script.js', __FILE__ ), array( 'jquery' ), '1.0.0', true );

        wp_localize_script( 'local_script', 'local_script', [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce('local_script'),
		] );

		wp_enqueue_script( 'local_script' );

    }

    /**
	 * Function to check if the gigya plugin is renamed or not and if not then the directory is renamed.
	 *
	 * @since 1.0
	 */
    public function local_akc_gigya_handler( $command = 'disable' ) {

        if ( get_option( 'local_akc_dev_mode' ) === 'on' ) {
            if ( is_dir( WP_PLUGIN_DIR . '/gigya-socialize-for-wordpress' ) ) {
                rename( WP_PLUGIN_DIR . '/gigya-socialize-for-wordpress', WP_PLUGIN_DIR . '/gigya-socialize-for-wordpress-disabled'  );
            }
        }
        
        if ( get_option( 'local_akc_dev_mode' ) === 'off' ) {
            if ( is_dir( WP_PLUGIN_DIR . '/gigya-socialize-for-wordpress-disabled' ) ) {
                rename( WP_PLUGIN_DIR . '/gigya-socialize-for-wordpress-disabled', WP_PLUGIN_DIR . '/gigya-socialize-for-wordpress'  );
            }
        }

    }

    /**
	 * Function to find the Hide_Admin line, check if it is commented or not and if not then comment it.
	 *
	 * @since 1.0
	 */
    public function local_akc_comment_hide_admin() {

    
    $handle = fopen(get_template_directory() . '/functions/init.php', "r");
    $new_content = '';

    if ( get_option( 'local_akc_dev_mode' ) === 'on' ) {
         if ($handle) {
            while (($line = fgets($handle)) !== false) {

                $processed_line = $line;

                if ( strpos( $line, 'Hide_WP_Admin' ) !== false ) {
                    if ( strstr($line, 'COMMENT') == false ) {

                        $processed_line = "// Features\Hide_WP_Admin\Register::init(); COMMENT\n";

                    }
                    $new_content .= $processed_line;
                }

                if ( strpos( $line, 'Hide_WP_Admin' ) == false ) {

                    $processed_line = $line;
                    $new_content .= $processed_line;

                }

            }

        }
    }

    if ( get_option( 'local_akc_dev_mode' ) === 'off' ) {
        if ($handle) {
           while (($line = fgets($handle)) !== false) {

               $processed_line = $line;

               if ( strpos( $line, 'Hide_WP_Admin' ) !== false ) {
                   if ( strstr($line, 'COMMENT') !== false ) {

                       $processed_line = "Features\Hide_WP_Admin\Register::init();\n";

                   }
                   $new_content .= $processed_line;
               }

               if ( strpos( $line, 'Hide_WP_Admin' ) == false ) {

                   $processed_line = $line;
                   $new_content .= $processed_line;

               }

           }

       }
   }

        fclose($handle);
        file_put_contents(get_template_directory() . '/functions/init.php', $new_content);

    }

    /**
	 * AJAX function.  Runs PHP on button click
	 *
	 * @since 0.1
	 */
	public function local_akc_local_commit_button() {

        check_ajax_referer( 'local_script', 'nonce' ); // why doesn't the nonce work?

        $this->local_akc_toggle_dev_mode();
        $this->local_akc_gigya_handler();
        $this->local_akc_comment_hide_admin();

		wp_send_json_success( [
			'message' => 'commit button',
		] );

	}

    /**
	 * Function to add the dev mode option if it does not already exist
	 *
	 * @since 1.0
	 */
    public function local_akc_toggle_dev_mode() {


        if ( get_option( 'local_akc_dev_mode', true) === 'on' ) {
            update_option( 'local_akc_dev_mode', 'off' );
            return;
        } else if ( get_option( 'local_akc_dev_mode', true ) === 'off' ) {
           update_option( 'local_akc_dev_mode', 'on' );
            return;
        }
    }
}

new local();


/*
* AJAX requests, nonces and wp cron events are undependable and buggy in docker containers.
*/

// maybe replace the local dev theme file
define( 'DEV_ASSETS', true );