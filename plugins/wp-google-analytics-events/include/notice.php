<?php
/**
 * Notice
 *
 * Notice related functionality goes in this file.
 *
 * @since   1.0.0
 * @package WP
 */
// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

// function endoflife_admin_notice__success() {

//     global $current_user;
//     $user_id = $current_user->ID;
//     /* Check that the user hasn't already clicked to ignore the message */
//     if ( current_user_can( 'manage_options' ) ) {
//         echo '<div class="notice notice-error"><p>';
//         printf(__('
//         <div style="display: block; min-height: 120px;">
//         <div style="display: inline; float: left"><strong>Action Required:<a href="%s"> WP Google Analytics Events</a></strong> is no longer supported with GA4.<p>Instead, please install the <a href="%s" target="_blank"> Goal Tracker for Google Analytics </a> plugin. <p> Here is what you need to do - Head over to the plugins page and disable WP Google Analytics Events.<br>Then (still on the plugins page), click "Add New" and search for Goal Tracker and click Install Now.</div><div style="display: inline; float: left; margin-left: 100px"><img style="max-width:120px;" src="%s"></div></div> '), 'admin.php?page=wp-google-analytics-events-whatsnew&wpgae_whatsnew_notify=1', 'https://wordpress.org/plugins/goal-tracker-ga/',  plugins_url( 'images/icon-256x256.png', dirname(__FILE__)));

//         $active_page = isset( $_GET[ 'page' ] ) ? '&page='.esc_html( $_GET[ 'page' ] ): '';
//         // printf(__('<a href="%s" style="float:right;">Close</a>'), '?wpgae_whatsnew_notify=1'.$active_page);

//         echo "</p></div>";
//     }
// }

/*
<div style="flex: 1; flex-direction: column; margin-bottom: 10px; margin-left:20px;">
<div style="font-size: 24px;line-height: 24px; margin-top: 20px;"><strong>Action Required:</strong></div>
<div style="font-size: 24px;line-height: 28px;">WP Google Analytics Events is Incompatible with GA4</div>
<div style="font-size: 16px; margin-top: 10px; flex: 1;">Google Analytics stopped supporting Universal Tracking.
To keep tracking events:  </div>
<ol style="list-style: none; margin: 0; padding: 0; font-size: 16px; margin-top: 10px;">
<li style="display: block; margin-bottom: 0.5em; display: flex; align-items: center;"><span style="display: flex;justify-content: center;align-items: center;font-size: 16px;padding: 4px;margin-right: 0.5em;background: black;color: white;width: 20px;height: 20px;text-align: center;border-radius: 100px;align-items: center;justify-content: center;display: flex;">1</span><span style="flex: 1;">Install the <a href="https://wordpress.org/plugins/goal-tracker-ga/" target="_blank">Goal Tracker – Custom Event Tracking for GA4</a> plugin. <span></li>
<li style="display: block; margin-bottom: 0.5em; display: flex; align-items: center;"><span style="display: flex;justify-content: center;align-items: center;font-size: 16px;padding: 4px;margin-right: 0.5em;background: black;color: white;width: 20px;height: 20px;text-align: center;border-radius: 100px;align-items: center;justify-content: center;display: flex;">2</span><span style="flex: 1;">Go into the plugin’s settings and connect it with GA4.</span></li>
<li style="display: block; margin-bottom: 0.5em; display: flex; align-items: center;"><span style="display: flex;justify-content: center;align-items: center;font-size: 16px;padding: 4px;margin-right: 0.5em;background: black;color: white;width: 20px;height: 20px;text-align: center;border-radius: 100px;align-items: center;justify-content: center;display: flex;">3</span><span style="flex: 1;">Deactivate the WP Google Analytics Events plugin.</span></li>
</ol>
</div>

 */

function endoflife_admin_notice__success()
{

    global $current_user;
    $user_id = $current_user->ID;
    /* Check that the user hasn't already clicked to ignore the message */
    if (current_user_can('manage_options' && !is_plugin_active('goal-tracker-ga/goal-tracker-ga.php'))) {
        echo '<div class="notice notice-error" style="padding:0; display:flex;">';
        printf(__('
        <div style="display: flex;flex: 1; color: black;">

        <div style="display: flex; float: left; align-items: center;justify-content: center; margin-right: 10px; background: #f2a801;">
          <img style="padding: 20px; max-height: 190px; width: 100%%" src="%s">
        </div>
        <div style="flex: 1; flex-direction: column; margin-bottom: 10px; margin-left:20px;">
          <div style="font-size: 18px;line-height: 24px; margin-top: 20px;"><strong>Migration Required:</strong></div>
          <div style="font-size: 16px; margin-top: 12px; flex: 1;"><strong>WP Google Analytics Events</strong> is no longer compatible with Google Analytics.  </div>
          <div style="font-size: 16px; margin-top: 12px; flex: 1;">Switch to our new plugin, <strong>Goal Tracker for Google Analytics</strong>.  </div>
          <ol style="list-style: none; margin: 0; padding: 0; font-size: 16px; margin-top: 10px;">
          <span></li>

          </ol>
          <a href="' . esc_url(wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=goal-tracker-ga'), 'install-plugin_goal-tracker-ga')) . '" type="button" style="display: inline-flex; align-items: center; margin-top:10px; border-radius: 9999px; border: 0; background-color: #107cd3; color: white; padding: 5px 5px 5px 12px; font-size: 18px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); text-decoration: none; transition: transform 0.2s; margin-bottom: 10px">
          <span style="margin-left: 8px; margin-right: 8px;">Quick-Install Goal Tracker</span>
          <span style="border-radius: 100%%; background: #000; padding: 3px"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 24px; height: 24px;">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
          </svg>
          </span>
      </a>


        </div>

				</div></div> '), plugins_url('images/GoalTracker-Searching.svg', dirname(__FILE__)), 'admin.php?page=wp-google-analytics-events');

        $active_page = isset($_GET[ 'page' ]) ? '&page=' . esc_html($_GET[ 'page' ]) : '';

    }
}

function endoflife_admin_notice__success_2()
{

    global $current_user;
    $user_id = $current_user->ID;
    $percentage = 100;
    /* Check that the user hasn't already clicked to ignore the message */
    if (current_user_can('manage_options')) {
        echo '<div class="notice notice-error" style="padding:0; display:flex;">';
        printf(__('
        <div style="display: flex;flex: 1; color: black;background: #f2a801;">

        <div style="display: flex; float: left; align-items: center;justify-content: center; margin-right: 10px;">
          <img style="padding: 4px; max-height: 100px;" src="%s">
        </div>
        <div style="flex: 1; flex-direction: column; margin-bottom: 10px">
          <div style="font-size: 24px;line-height: 24px; margin-top: 20px;"><strong>Action Required:</strong></div>
          <div style="font-size: 24px;line-height: 28px;">Google Analytics GA4 - is no longer working</div>

          <ol style="list-style: none; margin: 0; padding: 0; font-size: 16px; margin-top: 10px;">
            <li style="display: block; margin-bottom: 0.5em; display: flex; align-items: center;"><span style="display: flex;justify-content: center;align-items: center;font-size: 17px;padding: 5px;margin-right: 0.5em;background: black;color: white;width: 22px;height: 22px;text-align: center;border-radius: 100px;align-items: center;justify-content: center;display: flex;">1</span><span style="flex: 1;">Download the <a href="%s" target="_blank"> Goal Tracker for Google Analytics </a> plugin.</span></li>
            <li style="display: block; margin-bottom: 0.5em; display: flex; align-items: center;"><span style="display: flex;justify-content: center;align-items: center;font-size: 17px;padding: 5px;margin-right: 0.5em;background: black;color: white;width: 22px;height: 22px;text-align: center;border-radius: 100px;align-items: center;justify-content: center;display: flex;">2</span><span style="flex: 1;">Install click "Add New" and search for Goal Tracker and click Install Now.</span></li>
            <li style="display: block; margin-bottom: 0.5em; display: flex; align-items: center;"><span style="display: flex;justify-content: center;align-items: center;font-size: 17px;padding: 5px;margin-right: 0.5em;background: black;color: white;width: 22px;height: 22px;text-align: center;border-radius: 100px;align-items: center;justify-content: center;display: flex;">3</span><span style="flex: 1;">Disable <stron>WP Google Analytics Events</strong> plugin.</span></li>
            </ol>
        </div>

				</div></div> '), 'admin.php?page=wp-google-analytics-events-whatsnew&wpgae_whatsnew_notify=1', 'https://wordpress.org/plugins/goal-tracker-ga/', plugins_url('images/icon-256x256.png', dirname(__FILE__)));

        $active_page = isset($_GET[ 'page' ]) ? '&page=' . esc_html($_GET[ 'page' ]) : '';

        echo "</div>";
    }
}

function whatsnew_admin_notice__success()
{

    global $current_user;
    $user_id = $current_user->ID;
    $was_ignored = get_user_meta($user_id, 'wpgae_whatsnew_ignore_notice', true);
    /* Check that the user hasn't already clicked to ignore the message */
    if (!$was_ignored && current_user_can('manage_options')) {
        echo '<div class="notice notice-success"><p>';
        printf(__('Learn about what\'s new in <strong><a href="%s">WP Google Analytics Events</a></strong>'), 'admin.php?page=wp-google-analytics-events-whatsnew&wpgae_whatsnew_notify=1');

        $active_page = isset($_GET[ 'page' ]) ? '&page=' . esc_html($_GET[ 'page' ]) : '';
        printf(__('<a href="%s" style="float:right;">Close</a>'), '?wpgae_whatsnew_notify=1' . $active_page);

        echo "</p></div>";
    }
}
add_action('admin_notices', 'endoflife_admin_notice__success');

function wpgae_whatsnew_notify()
{
    global $current_user;
    $user_id = $current_user->ID;
    /* If user clicks to ignore the notice, add that to their user meta */
    if (isset($_GET[ 'wpgae_whatsnew_notify' ]) && '1' == $_GET[ 'wpgae_whatsnew_notify' ]) {
        update_user_meta($user_id, 'wpgae_whatsnew_ignore_notice', true);
    }
}
// add_action('admin_init', 'whatsnew_admin_notice__success');

/*
Upon plugin activation, reset 'wpgae_whatsnew_ignore_notice' for all admin users.
 * This functions was called inside ga_events_install funtion.
 */
function wpgae_reactivate_notice()
{

    $args = array(
        'role' => 'administrator',
    );

    $admins = get_users($args);

    foreach ($admins as $user) {
        update_user_meta($user->ID, 'wpgae_whatsnew_ignore_notice', false);
        delete_user_meta($user->ID, 'ga_events_review_dismiss', "true");

    }
}

if (!function_exists('ga_events_review_notice')) {
    // Add an admin notice.
    add_action('admin_notices', 'ga_events_review_notice');

    /**
     *  Admin Notice to Encourage a Review or Donation.
     *
     *  @author Matt Cromwell
     *  @version 1.0.0
     */
    function ga_events_review_notice()
    {
        // Define your Plugin name, review url, and donation url.
        $plugin_name = 'WP Google Analytics Events';
        $review_url = 'https://wordpress.org/support/view/plugin-reviews/wp-google-analytics-events';
        $donate_url = 'https://wpflow.com/upgrade';
        // Get current user.
        global $current_user, $pagenow;
        $user_id = $current_user->ID;
        // Get today's timestamp.
        $today = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $actdate = get_option('ga_events_activation_date', false);
        $installed = (!empty($actdate) ? $actdate : '999999999999999');
        if ($installed <= $today) {
            // Make sure we're on the plugins page.
            // If the user hasn't already dismissed our alert,
            // Output the activation banner.
            $nag_admin_dismiss_url = 'plugins.php?ga_events_review_dismiss=0';
            $user_meta = get_user_meta($user_id, 'ga_events_review_dismiss');
            if (empty($user_meta) && current_user_can('manage_options')) {
                ?>
<div class="update-nag">

  <style>
  div.review {
    position: relative;
    margin-left: 35px;
    height: 80px;
    display: block;
  }

  div.review span.ga-events-icon {
    color: white;
    position: absolute;
    left: -30px;
    /*padding: 9px;*/
    /*top: -8px;*/
  }

  div.review strong {
    color: #66BB6A;
  }

  div.review a.dismiss {
    float: right;
    text-decoration: none;
    color: #000000;
  }

  .review a {
    color: #ED494D;
  }

  .ga-events-notice-text {
    display: inline-block;
    margin-left: 170px;
    margin-top: 24px;
  }
  </style>
  <?php
// For testing purposes
                //echo '<p>Today = ' . $today . '</p>';
                //echo '<p>Installed = ' . $installed . '</p>';
                ?>

  <div class="review">
    <span class="ga-events-icon">
      <img src="<?php echo plugins_url('images/WPGAE_Logo-177x78.png', dirname(__FILE__)) ?>">
    </span>
    <span class="ga-events-notice-text">
      <?php echo wp_kses(sprintf(__('Thank you for using <strong>' . $plugin_name . '</strong>? We would love to hear about <a href="https://wpflow.com/contact">your experience</a> with the plugin. Need more features? <a href="https://wpflow.com/upgrade/?utm_source=wpadmin&utm_medium=banner&utm_campaign=anotice" target="_blank">Upgrade Now</a> to unlock.', 'ga_events_text'), esc_url($donate_url), esc_url($review_url)), array('strong' => array(), 'a' => array('href' => array(), 'target' => array()))); ?>
    </span>
    <a href="<?php echo admin_url($nag_admin_dismiss_url); ?>" class="dismiss"><span
        class="dashicons dashicons-dismiss"></span></a>
  </div>

</div>

<?php
}
        }
    }
}
if (function_exists('ga_events_ignore_review_notice')) {
    // Function to force the Review Admin Notice to stay dismissed correctly.
    add_action('admin_init', 'ga_events_ignore_review_notice');
    /**
     * Ignore review notice.
     *
     * @since  1.0.0
     */
}
function ga_events_ignore_review_notice()
{
    if (isset($_GET[ 'ga_events_review_dismiss' ]) && '0' == $_GET[ 'ga_events_review_dismiss' ]) {
        // Get the global user.
        global $current_user;
        $user_id = $current_user->ID;
        add_user_meta($user_id, 'ga_events_review_dismiss', 'true', true);
    }
}