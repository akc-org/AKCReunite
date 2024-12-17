<?php
defined('ABSPATH') or die('Direct access not allowed!');

class GAESettings
{

    public function upgrade_settings()
    {
        $installed_version = get_option('GA_EVENTS_version');
        if (!isset($installed_version) || $installed_version != GA_EVENTS_VERSION) {
            GAEActivation::start_activation();
        }
    }

    /* ------------------------------------------------------------------------ *
     * Menu Section
     * ------------------------------------------------------------------------ */

    public function ga_events_menu()
    {

        add_menu_page('WP Google Analytics Settings',
            'WP GA Events',
            'manage_options',
            'wp-google-analytics-events',
            array($this, 'ga_events_render_settings_page'), // callback
            GAE_PLUGIN_URL . 'images/menu-icon.png'
        );

        add_submenu_page('wp-google-analytics-events',
            'General Settings',
            'General Settings',
            'manage_options',
            'wp-google-analytics-events',
            array($this, 'ga_events_render_settings_page'));

        add_submenu_page('wp-google-analytics-events',
            'Click Tracking',
            'Click Tracking',
            'manage_options',
            'wp-google-analytics-events-click',
            array($this, 'ga_events_render_settings_page'));

        add_submenu_page('wp-google-analytics-events',
            'Scroll Tracking',
            'Scroll Tracking',
            'manage_options',
            'wp-google-analytics-events-scroll',
            array($this, 'ga_events_render_settings_page'));

        add_submenu_page('wp-google-analytics-events',
            "Getting Started Guide",
            "Getting Started Guide",
            'manage_options',
            'wp-google-analytics-events-getstarted',
            array($this, 'ga_events_render_settings_page'));

        add_submenu_page('wp-google-analytics-events',
            "What's New",
            "What's New",
            'manage_options',
            'wp-google-analytics-events-whatsnew',
            array($this, 'ga_events_render_settings_page'));

        add_submenu_page('wp-google-analytics-events', 'Upgrade', 'Upgrade Now', 'manage_options', 'wp-google-analytics-events-upgrade', 'ga_events_settings_page');

    }

    public function ga_events_render_settings_page()
    {
        ?>
<div class="wrap ga_main">
  <h2>WP Google Analytics Events</h2>
  <?php
// Determine which tab we are on
        $active_page = isset($_GET[ 'page' ]) ? $_GET[ 'page' ] : 'wp-google-analytics-events';
        ?>
  <h2 class="nav-tab-wrapper">
    <!-- General Settings -->
    <a href="?page=wp-google-analytics-events"
      class="nav-tab <?php echo $active_page == 'wp-google-analytics-events' ? 'nav-tab-active' : ''; ?>">General
      Settings</a>

    <!-- Click tracking -->
    <a href="?page=wp-google-analytics-events-click"
      class="nav-tab <?php echo $active_page == 'wp-google-analytics-events-click' ? 'nav-tab-active' : ''; ?>">Click
      Tracking</a>

    <!-- Scroll tracking -->
    <a href="?page=wp-google-analytics-events-scroll"
      class="nav-tab <?php echo $active_page == 'wp-google-analytics-events-scroll' ? 'nav-tab-active' : ''; ?>">Scroll
      Tracking</a>

    <!-- Getting Started Guide -->
    <a href="?page=wp-google-analytics-events-getstarted"
      class="nav-tab <?php echo $active_page == 'wp-google-analytics-events-getstarted' ? 'nav-tab-active' : ''; ?>">Getting
      Started Guide</a>

    <!-- What's new -->
    <a href="?page=wp-google-analytics-events-whatsnew&wpgae_whatsnew_notify=2"
      class="nav-tab <?php echo $active_page == 'wp-google-analytics-events-whatsnew' ? 'nav-tab-active' : ''; ?>">What's
      New</a>

    <!-- Plugin Support -->
    <a href="https://wordpress.org/support/plugin/wp-google-analytics-events/" target="_blank" class="nav-tab">Plugin
      Support</a>
  </h2>


  <?php if ($active_page == 'wp-google-analytics-events') {
            $ga_events_report = "";
            if (isset($_GET[ 'report' ]) && $_GET[ 'report' ] == 'true') {
                $ga_events_report = "ga_events_report";
            }
            echo '
              <div class="bg-white/70 p-4 rounded-md border border-gray-200 my-10 shadow-2xl text-lg flex">
                <img class="h-20 w-auto mr-4 ml-2 mt-16" src="' . GAE_PLUGIN_URL . 'images/Logo-helper.svg"></img>
                <div class="p-4">
                    <div class="text-4xl">Hello there!</div>
                    <div class="my-4 text-lg mb-2">
                        <div>
                         <span>In July 2023 google killed Universal Analytics and launched its new version, GA4. </span>
                        </div>
                        <div class="mt-4">
                        <span>
												WP Google Analytics Events was built for Universal Analytics and <strong>will no longer work</strong>.</span>
												</div>
												<div class="mt-4">
												<span>Instead, you should use our new plugin,</span>
                        <span class="text-brand-primary font-bold mx-1">
                        "Goal Tracker for Google Analytics"
                        </span>
												<span>(it\'s much better!).</span>
                        </div>
                        <div class="mt-2">
                        <span>
												Click the button below to install</span>
                         <span class="text-brand-primary font-bold mx-1">Goal Tracker</span>
                        </div>
                    </div>
                    <div class="flex space-x-1 mt-6 w-full flex-1 items-end flex-col sm:flex-row">
                        <a href="' . esc_url(wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=goal-tracker-ga'), 'install-plugin_goal-tracker-ga')) . '" type="button" class="inline-flex items-center rounded-full border border-transparent bg-brand-primary text-white hover:text-white px-3 py-1 text-xl shadow hover:shadow-xl transform active:scale-95 hover:scale-105 transition-transform hover:ring-2 hover:ring-white hover:ring-offset-0 focus:outline-none focus:ring-0 focus:ring-white focus:ring-offset-2">
                            <span class="mx-2">Install Goal Tracker</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75" />
</svg>
                        </a>
                    </div>
                </div>
          </div>
      ';
            echo "<div class='wpgae-gs-form " . $ga_events_report . "'><form id='ga_events_options' method='post' action='options.php'>";
        }
        ?>

  <?php
settings_fields('ga_events_options_group');

        if ($active_page == 'wp-google-analytics-events-click') {
            if (file_exists(GAE_PLUGIN_PATH . '/templates/click-elements-input-table.php')) {
                include GAE_PLUGIN_PATH . '/templates/click-elements-input-table.php';
            }
        } else if ($active_page == 'wp-google-analytics-events-scroll') {
            if (file_exists(GAE_PLUGIN_PATH . '/templates/scroll-elements-input-table.php')) {
                include GAE_PLUGIN_PATH . '/templates/scroll-elements-input-table.php';
            }
        } else if ($active_page == 'wp-google-analytics-events-getstarted') {
            do_settings_sections('ga_events_getstarted');
        } else if ($active_page == 'wp-google-analytics-events-whatsnew') {
            do_settings_sections('ga_events_whatsnew');
        } else if ($active_page == 'wp-google-analytics-events' && isset($_GET[ 'report' ]) && $_GET[ 'report' ] == 'true') {
            settings_fields('ga_events_general_reports_group');
            do_settings_sections('ga_events_general_reports_group');
        } else {

            do_settings_sections('ga_events_options_group');
        }

        // Save button on Genral Settings tab
        if ($active_page == 'wp-google-analytics-events' && !isset($_GET[ 'report' ])) {
            submit_button();
        }

        // use non-ajax on click tracking page - disabled
        if ($active_page == 'wp-google-analytics-events-click') {
            //submit_button();
        }
        ?>

  <?php echo $active_page == 'wp-google-analytics-events' ? "</form></div>" : '';
        // Save button on Genral Settings tab
        if ($active_page == 'wp-google-analytics-events' && !(isset($_GET[ 'report' ]) && $_GET[ 'report' ] == 'true')) {
            ?>
  <div class="wrap ga_events_banner ga_events_sidebar">
    <!--		 <div class="wrap ga_events_sidebar">-->
    <table class="form-table widefat">
      <thead>
        <th>Need More Features?</th>
      </thead>
      <tbody>
        <tr class="features">
          <td>
            <ul>
              <li><i class="fa fa-check-square-o fa-lg"></i><strong>Link Tracking</strong></li>
              <li title="Dynamic Event Data"><i class="fa fa-check-square-o fa-lg"></i><strong>Placeholders</strong>
              </li>
              <li><i class="fa fa-check-square-o fa-lg"></i><strong>Track your Self-Hosted Media video and
                  audio</strong></li>
              <li><i class="fa fa-check-square-o fa-lg"></i><strong>YouTube Video Tracking</strong></li>
              <li><i class="fa fa-check-square-o fa-lg"></i><strong>Vimeo Video support</strong></li>
              <li><i class="fa fa-check-square-o fa-lg"></i><strong>Allow non-admin users to manage the plugin</strong>
              </li>
              <li><i class="fa fa-check-square-o fa-lg"></i><strong>HTML Tag support</strong></li>
              <li><i class="fa fa-check-square-o fa-lg"></i><strong>Access to our Pro Support Team</strong></li>
            </ul>
          </td>
        </tr>
        <tr class="tfoot">
          <td>
            <div class="wpcta">
              <a class="button-primary button-large" target="_blank"
                href="https://wpflow.com/upgrade/?utm_source=wpadmin&utm_medium=banner&utm_campaign=genreal">
                <span class="btn-title ">
                  Upgrade Now
                </span>
              </a>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

  </div>
</div>
<div class="settings_content">
  <form action="" method="post" enctype="multipart/form-data">
    <a href="#" class="btn_close"><img src="<?php echo GAE_PLUGIN_URL ?>images/close.png"></a>
    <input type="file" name="ga_settings_import">
    <input type="hidden" name="ga_events_action" value="import_settings" />
    <?php
wp_nonce_field('ga_events_import_nonce',
                'ga_events_import_nonce');
            ?>
    <input type="submit" name="set_settings">
  </form>
</div>
<?php
}?>
<?php
}

    /* ------------------------------------------------------------------------ *
     * Setting Registration
     * ------------------------------------------------------------------------ */

    public function ga_events_initialize_plugin_options()
    {

        register_setting('ga_events_options_group',
            'ga_events_options',
            array($this, 'update_settings_callback'));

        register_setting('ga_events_general_reports_group',
            'ga_events_options',
            array($this, 'update_settings_callback'));

        /* ------------------------------------------------------------------------ *
         * Settings sections
         * ------------------------------------------------------------------------ */

        add_settings_section('ga_events_main',
            '',
            array($this, 'ga_events_section_text'),
            'ga_events_options_group');

        add_settings_section('ga_events_main_reports',
            '',
            array($this, 'ga_events_section_text'),
            'ga_events_general_reports_group');

        add_settings_section('ga_events_getstarted_section',
            "",
            array($this, 'ga_events_getstarted_section_content'),
            'ga_events_getstarted');

        add_settings_section('ga_events_whatsnew_section',
            "",
            array($this, 'ga_events_whatsnew_section_content'),
            'ga_events_whatsnew');

        /* ------------------------------------------------------------------------ *
         * Settings Fields
         * ------------------------------------------------------------------------ */

        // GA id field
        add_settings_field('ga_events_id',
            '',
            array($this, 'ga_events_tracking_id_input'), // Rendering callback
            'ga_events_options_group', // page name
            'ga_events_main'); // section name
        // Domain field
        add_settings_field('ga_events_domain',
            '',
            array($this, 'ga_events_setting_domain_input'), // Callback
            'ga_events_options_group',
            'ga_events_main');

        // Anonymize IP checkbox
        add_settings_field('ga_events_anonymizeip',
            '',
            array($this, 'ga_events_setting_anon_input'),
            'ga_events_options_group',
            'ga_events_main');

        // Snippet type select menu input
        add_settings_field('ga_events_snippet_type',
            '',
            array($this, 'ga_events_setting_snippet_type_input'),
            'ga_events_options_group',
            'ga_events_main');

        // GTM ID input
        add_settings_field('ga_events_gtm_id',
            '',
            array($this, 'ga_events_setting_gtm_id_input'),
            'ga_events_options_group',
            'ga_events_main');

        // Download Tracking options checkbox
        add_settings_field('ga_events_download_tracking',
            '',
            array($this, 'ga_events_setting_download_tracking_input'),
            'ga_events_options_group',
            'ga_events_main');

        // Email Links Tracking options checkbox
        add_settings_field('email_link_tracking',
            '',
            array($this, 'ga_events_setting_email_link_tracking_input'),
            'ga_events_options_group',
            'ga_events_main');

//        // Tel Links Tracking options checkbox
//        add_settings_field( 'tel_link_tracking',
//             '',
//             array($this, 'ga_events_setting_tel_link_tracking_input'),
//             'ga_events_options_group',
//             'ga_events_main' );

        // Advanced options checkbox
        add_settings_field('ga_events_advanced',
            '',
            array($this, 'ga_events_setting_adv_input'),
            'ga_events_options_group',
            'ga_events_main');

        // Force Snippet
        add_settings_field('ga_events_force_tracking_code',
            '',
            array($this, 'ga_events_force_tracking_code_input'),
            'ga_events_options_group',
            'ga_events_main');

        // Script debug mode
        add_settings_field('ga_events_script_debug_mode',
            '',
            array($this, 'ga_events_setting_script_debug_mode_input'),
            'ga_events_options_group',
            'ga_events_main');

        // Signout ga GA client
        add_settings_field('ga_events_signout_ga_settings',
            '',
            array($this, 'ga_events_settings_signout_ga'),
            'ga_events_options_group',
            'ga_events_main');

        // Export settings
        add_settings_field('ga_events_download_settings',
            '',
            array($this, 'ga_events_settings_download'),
            'ga_events_options_group',
            'ga_events_main');

        // Import settings
        add_settings_field('ga_events_upload_settings',
            '',
            array($this, 'ga_events_settings_upload'),
            'ga_events_options_group', // needed?
            'ga_events_main');

        // Let settings callback know we're on general settings page
        add_settings_field('ga_events_confirm_general_settings_page',
            '',
            array($this, 'ga_events_settings_confirm_general_settings_page'),
            'ga_events_options_group', // needed?
            'ga_events_main');

        add_settings_field('ga_events_general_reports_section',
            '',
            array($this, 'ga_events_general_reports_section'),
            'ga_events_general_reports_group', 'ga_events_main_reports');

    }

    public function update_settings_callback($input)
    {

//         Very useful for debugging
//        error_log( print_r( $input,
//             true ) );

        $options = get_option('ga_events_options');
        $updated = $options;

        // Loop through each of the incoming options
        foreach ($input as $key => $value) {

            if (isset($input[ $key ])) {

                // Sanitize input
                $updated[ $key ] = $this->strip_tags_deep($input[ $key ]);
            }
        }
        // This code is only run if we are on the general settins page
        // otherwise these settings would get updated by changes on other
        // pages as we only have one callback covering all the settings pages
        if (isset($input[ 'which_page' ]) && $input[ 'which_page' ] == 'general-settings') {

            // The case of the empty checkboxes - anonymize ip
            if (!isset($input[ 'anonymizeip' ])) {
                $updated[ 'anonymizeip' ] = '0';
            }

            // The case of the empty checkboxes - download tracking
            if (!isset($input[ 'download_tracking' ])) {
                $updated[ 'download_tracking' ] = '0';
            }

            // The case of the empty checkboxes - email links tracking
            if (!isset($input[ 'email_link_tracking' ])) {
                $updated[ 'email_link_tracking' ] = '0';
            }

//        // The case of the empty checkboxes - tel links tracking
//        if ( ! isset( $input[ 'tel_link_tracking' ] ) ) {
//            $updated[ 'tel_link_tracking' ] = '0';
//        }

            // The case of the empty checkboxes - advanced mode
            if (!isset($input[ 'advanced' ])) {
                $updated[ 'advanced' ] = '0';
            }

            // The case of the empty checkboxes - script debug mode
            if (!isset($input[ 'script_debug_mode' ])) {
                $updated[ 'script_debug_mode' ] = '0';
            }

        }

        // Remove page identifier
        unset($updated[ 'which_page' ]);

        // Return new modified options array
        return $updated;
    }

    public function ga_events_feedback_form_html()
    {
        $screen = get_current_screen();
        if ($screen->id == "plugins") {
            if (file_exists(GAE_PLUGIN_PATH . '/templates/feedback-modal.php')) {
                include GAE_PLUGIN_PATH . '/templates/feedback-modal.php';
            }
        }
    }

    /* ------------------------------------------------------------------------ *
     * Section Callbacks
     * ------------------------------------------------------------------------ */

    public function ga_events_section_text()
    {
        $active_page = isset($_GET[ 'page' ]) ? $_GET[ 'page' ] : 'wp-google-analytics-events';
        $wpgae_section_title = "General Settings";

        $is_report_tab = isset($_GET[ 'report' ]) && $_GET[ 'report' ] == 'true';
        $report_link = '?page=' . $active_page . '&report=true';
        $settings_link = '?page=' . $active_page;

        $report_class = "";
        if ($is_report_tab) {
            $report_class = "ga_events_report";
        }

        ?>
<div class="ga-main-wrapper <?php echo $is_report_tab ? "" : "ga-main-wrapper-flex" ?>">

  <div class='wpgae-gs-form <?php echo $report_class; ?>'>
    <div class="wpgae-gs-internal">
      <h2><?=$wpgae_section_title?></h2>
      <a href='http://wpflow.com/documentation/'>Need Help?</a>
      <span style='margin-left:8px;'>
        <a href='https://wordpress.org/support/plugin/wp-google-analytics-events/'>Support</a>
      </span>
    </div>
    <div class="ga_main ga_main_settings">
      <h2 class="nav-tab-wrapper">
        <a href="<?=$report_link?>" class="nav-tab <?php if ($is_report_tab) {echo "nav-tab-active";}?>">Reports</a>
        <a href="<?=$settings_link?>" class="nav-tab <?php if (!$is_report_tab) {echo "nav-tab-active";}?>">Settings</a>
      </h2>
    </div>

    <?php
}

    public function ga_events_clicks_section_text()
    {
        ?>
    <p>Click Events</p>
    <?php
}

    public function ga_events_scrolls_section_text()
    {
        ?>
    <p>Scroll Events</p>
    <?php
}

    public function ga_events_getstarted_section_content()
    {
        if (file_exists(GAE_PLUGIN_PATH . '/templates/getting-started-guide-section.php')) {
            include GAE_PLUGIN_PATH . '/templates/getting-started-guide-section.php';
        }
    }

    public function ga_events_whatsnew_section_content()
    {
        if (file_exists(GAE_PLUGIN_PATH . '/templates/whats-new-section-content.php')) {
            include GAE_PLUGIN_PATH . '/templates/whats-new-section-content.php';
        }
    }

    /* ------------------------------------------------------------------------ *
     * Field Callbacks
     * ------------------------------------------------------------------------ */

    // General Settings page begins

    public function ga_events_tracking_id_input()
    {
        // Read the social options collection.
        $options = get_option('ga_events_options');

        // Make sure the element is defined in the options. If not, set an empty string.
        $id = isset($options[ 'tracking_id' ]) ? $options[ 'tracking_id' ] : '';

        // Output HTML
        ?>
    <h4>Google Analytics</h4>
    <label>Google Analytics Tracking ID</label><input class='ga-gs-input' name='ga_events_options[tracking_id]'
      type='text' value="<?php echo $id; ?>" />
    <?php
}

    public function ga_events_setting_domain_input()
    {
        $options = get_option('ga_events_options');
        $domain = isset($options[ 'domain' ]) ? $options[ 'domain' ] : '';
        echo "<label>Domain (optional)</label>";

        echo "<input id='domain' class='ga-gs-input' name='ga_events_options[domain]' type='text' value='$domain' />";
    }

    public function ga_events_setting_anon_input()
    {
        $options = get_option('ga_events_options');
        $value = isset($options[ 'anonymizeip' ]) ? $options[ 'anonymizeip' ] : '0';
        echo "<label>IP Anonymization </i>" . $this->ga_tooltip('Tell Google Analytics not to log IP Addresses. Requires code snippet to be selected (but not GTM container).') . "</label>";
        echo "<label class='toggle-control'>";
        echo "<input id='anonymizeip' name='ga_events_options[anonymizeip]' type='checkbox' value='1' " . checked($value,
            1,
            false) . " />";
        echo "<span class='control'></span>";
        echo "</label>";
    }

    public function ga_events_setting_snippet_type_input()
    {

        $options = get_option('ga_events_options');

        /*
         * TO DO Needs testing with upgrade
         * Should have been set in upgrade function in wp-google-analytics-events.php
         */
        $defaultOption = isset($options[ 'snippet_type' ]) ? $options[ 'snippet_type' ] : 'none';
        echo "<label>Snippet type to add: " . $this->ga_tooltip('The Google Analytics snippet to add if any.') . "</label>";
        ?>
    <select id="snippet_type" class='ga-gs-input' name='ga_events_options[snippet_type]'>
      <option value="gtm" <?php selected($defaultOption, 'gtm')?>>Google Tag Manager Container</option>
      <option value="gst" <?php selected($defaultOption, 'gst')?>>Global Site Tag (gtag.js)</option>
      <option value="universal" <?php selected($defaultOption, 'universal')?>>Universal (analytics.js)</option>
      <option value="legacy" <?php selected($defaultOption, 'legacy')?>>Legacy (ga.js)</option>
      <option value='none' <?php selected($defaultOption, 'none')?>>None</option>
    </select>
    <?php
}

    public function ga_events_setting_gtm_id_input()
    {
        $options = get_option('ga_events_options');
        $gtm_id = isset($options[ 'gtm_id' ]) ? $options[ 'gtm_id' ] : '';
        echo "<label>Google Tag Manager Container ID" . $this->ga_tooltip('Your GTM container ID, which you can get from your GTM account') . "</label>";
        echo "<input id='gtm_id' class='ga-gs-input' name='ga_events_options[gtm_id]' type='text' value='$gtm_id' />";
    }

    public function ga_events_setting_download_tracking_input()
    {
        $options = get_option('ga_events_options');
        $value = isset($options[ 'download_tracking' ]) ? $options[ 'download_tracking' ] : '0';
        echo "<br /><hr /><br /><h4>Download and Email Tracking</h4>";
        echo "<label>Track Downloads (PDF, MP3, PPTX, DOCX): </label>";
        echo "<label class='toggle-control'>";
        echo "<input id='download_tracking' name='ga_events_options[download_tracking]' type='checkbox' value='1'" . checked($value,
            '1',
            false) . " />";
        echo "<span class='control'></span>";
        echo "</label>";
    }

    public function ga_events_setting_email_link_tracking_input()
    {
        $options = get_option('ga_events_options');
        $value = isset($options[ 'email_link_tracking' ]) ? $options[ 'email_link_tracking' ] : '0';
        echo "<label>Track Email Links (mailto:): </label>";
        echo "<label class='toggle-control'>";
        echo "<input id='email_link_tracking' name='ga_events_options[email_link_tracking]' type='checkbox' value='1'" . checked($value,
            '1',
            false) . " />";
        echo "<span class='control'></span>";
        echo "</label>";
    }

    public function ga_events_setting_adv_input()
    {
        $options = get_option('ga_events_options');
        $value = isset($options[ 'advanced' ]) ? $options[ 'advanced' ] : '0';
        echo "<br /><hr /><br /><h4>Advanced Settings</h4>";
        echo "<label>Advanced Mode " . $this->ga_tooltip('Enable Advanced Selectors') . "</label>";
        echo "<label class='toggle-control'>";
        echo "<input id='advanced' name='ga_events_options[advanced]' type='checkbox' value='1' " . checked($value,
            '1',
            false) . " />";
        echo "<span class='control'></span>";
        echo "</label>";
    }

    public function ga_events_force_tracking_code_input()
    {

        $options = get_option('ga_events_options');
        $track = isset($options[ 'advanced' ]) && $options[ 'advanced' ] == true;

        /*
         * TO DO Needs testing with upgrade
         * Should have been set in upgrade function in wp-google-analytics-events.php
         */
        $defaultOption = isset($options[ 'force_snippet' ]) ? $options[ 'force_snippet' ] : 'none';
        echo $track ? "<div id='forcesnopperwrap'>" : "<div style='display:none;' id='forcesnopperwrap'>";

        echo "<label>Force the plugin to use this code snippet: " . $this->ga_tooltip('Please see docs for help with deciding this option') . "</label>";
        ?>
    <select id="force_snippet" name='ga_events_options[force_snippet]'>
      <option value="gtm" <?php selected($defaultOption, 'gtm')?>>Google Tag Manager Container</option>
      <option value="gst" <?php selected($defaultOption, 'gst')?>>Global Site Tag (gtag.js)</option>
      <option value="universal" <?php selected($defaultOption, 'universal')?>>Universal (analytics.js)</option>
      <option value='none' <?php selected($defaultOption, 'none')?>>None</option>
    </select>
  </div>
  <?php
}

    public function ga_events_setting_script_debug_mode_input()
    {
        $options = get_option('ga_events_options');
        $value = isset($options[ 'script_debug_mode' ]) ? $options[ 'script_debug_mode' ] : '0';
        echo "<label>Support Friendly Scripts " . $this->ga_tooltip('Uncheck for script minimization') . "</label>";
        echo "<label class='toggle-control'>";
        echo "<input id='script_debug_mode' name='ga_events_options[script_debug_mode]' type='checkbox' value='1' " . checked($value,
            '1',
            false) . " />";
        echo "<span class='control'></span>";
        echo "</label>";
    }

    public function ga_events_settings_download()
    {
        echo '<br /><hr /><br /><a class="button" style="margin-right: 20px;" href="http://' . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "REQUEST_URI" ] . '&download=1">Export settings</a> <a href="#" class="button btn_upload">Import settings</a>';
    }

    public function ga_events_settings_signout_ga()
    {
        $ajax_nonce = wp_create_nonce("wpflow_ga_disconnect");
        $options = get_option('ga_events_options');
        $advanced = isset($options[ 'advanced' ]) && $options[ 'advanced' ] == true;
        echo $advanced ? '<div id="wpflow_gs_reports_section">' : '<div id="wpflow_gs_reports_section" style="display: none">';
        echo '<br /><hr/><br /><h4>Reports</h4><div><a class="button btn_signout_ga" href="#" data-delete-nonce="' . $ajax_nonce . '">Sign-out of Google Analytics</a></div></div>';
    }

    public function ga_events_settings_upload()
    {
        echo '';
    }

    public function ga_events_settings_confirm_general_settings_page()
    {
        ?>
  <input type="hidden" name='ga_events_options[which_page]' value="general-settings">
  <?php
}

    /*
    Reports Tabs
     */
    public function ga_events_general_reports_section()
    {
        require_once GAE_PLUGIN_PATH . '/templates/reports.php';
    }

    // General settings page ends
    // What's new section
    public function ga_events_setting_whatsnew_input()
    {

        // Currently not used
    }

    // Callback for adding settings link next to listing on plugins page
    public function plugin_add_settings_link($links)
    {
        $upgrade = '<a target="_blank" href="https://wpflow.com/upgrade?utm_source=wpadmin&utm_medium=link&utm_campaign=plugin-settings"">' . __('Upgrade', 'General') . '</a>';
        $settings_link = '<a href="admin.php?page=wp-google-analytics-events">' . __('Settings') . '</a>';
        array_unshift($links,
            $settings_link);
        array_unshift($links,
            $upgrade);
        return $links;
    }

    /* ------------------------------------------------------------------------ *
     * Helpers
     * ------------------------------------------------------------------------ */

    public function ga_events_download_settings()
    {

        if (isset($_GET[ 'download' ]) && isset($_GET[ 'page' ])) {
            if ($_GET[ 'page' ] == 'wp-google-analytics-events') {
                $options = get_option('ga_events_options');
                // the version number currently has its own option in db. Un comment below if needed.
                //$options[ 'GA_EVENTS_version' ]     = get_option( 'GA_EVENTS_version' );
                $settings = json_encode($options);

                nocache_headers();
                header('Content-Type: application/json; charset=utf-8');
                header('Content-Disposition: attachment; filename=ga-events-pro-settings-export-' . date('m-d-Y') . '.json');
                header("Expires: 0");

                echo $settings;
                exit;
            }
        }
    }

    public function ga_events_import_settings()
    {

        if (empty($_POST[ 'ga_events_action' ]) || 'import_settings' != $_POST[ 'ga_events_action' ]) {
            return;
        }

        if (!wp_verify_nonce($_POST[ 'ga_events_import_nonce' ],
            'ga_events_import_nonce')) {
            return;
        }

        $temp = explode('.',
            $_FILES[ 'ga_settings_import' ][ 'name' ]);
        $extension = end($temp);

        if ($extension != 'json') {
            wp_die(__('Please upload a valid .json file'));
        }

        $import_file = $_FILES[ 'ga_settings_import' ][ 'tmp_name' ];

        if (empty($import_file)) {
            wp_die(__('Please upload a file to import'));
        }

        // Retrieve the settings from the file and convert the json object to an array.
        $settings = json_decode(file_get_contents($import_file),
            true);

        WPGAEGClickEvent::delete_all_event_posts();
        WPGAEGScrollEvent::delete_all_event_posts();

        if (isset($settings[ "click_elements" ])) {
            $new_click_elements = $settings[ "click_elements" ];
            foreach ($new_click_elements as &$item) {
                $item[ "selector" ] = $item[ "name" ];
                unset($item[ "name" ]);
            }
            $click_elements = WPGAEGClickEvent::convert_and_save_cached_settings($new_click_elements);
        }

        if (isset($settings[ "scroll_elements" ])) {
            $new_scroll_elements = $settings[ "scroll_elements" ];
            foreach ($new_scroll_elements as &$item) {
                $item[ "selector" ] = $item[ "name" ];
                unset($item[ "name" ]);
            }
            $scroll_elements = WPGAEGScrollEvent::convert_and_save_cached_settings($new_scroll_elements);
        }
//        die(var_dump($settings));
        update_option('ga_events_options',
            $settings);

        wp_safe_redirect(admin_url('admin.php?page=wp-google-analytics-events'));
        exit;
    }

    public function ga_tooltip($content = '')
    {
        $html = '<span class="ga-tooltip" title="' . $content . '"></span>';
        return $html;
    }

    public function createCheckbox($name, $id, $value, $label, $isChecked = false)
    {
        $html = '<input type="checkbox" id="' . $id . '" name="' . $name . '" value="' . $value . '" ';
        $html .= $isChecked ? 'checked>' : '>';
        $html .= $label . '&nbsp&nbsp&nbsp&nbsp';
        return $html;
    }

    public function is_selected($value, $stored)
    {
        if ($stored == $value) {
            return "selected";
        }
        return "";
    }

    public function create_dropdown($name, $id, $options = array(), $selected = 'unknown')
    {
        $html = '';
        if (!empty($options)) {
            $html .= "<select id='$id' name='$name'>";

            foreach ($options as $key => $value) {
                $html .= $selected == $key ? "<option selected value='$key' >$value</option>" : "<option  value='$key' >$value</option>";
            }

            $html .= "</select>";
        }
        return $html;
    }

    public function strip_tags_deep($value)
    {
        $value = is_array($value) ?
        array_map(array($this, 'strip_tags_deep'),
            $value) :
        strip_tags($value);

        return $value;
    }

}