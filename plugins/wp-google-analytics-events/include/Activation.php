<?php
require_once("Settings.php");
class GAEActivation
{

  // intial values for plugin options. NB strings used for boolean values
  // for good reason
  private static $default_options = array(
    'link_clicks_delay' => '120',
    'gtm' => '0',
    'anonymizeip' => '0',
    'advanced' => '0',
    'script_debug_mode' => '0',
    'snippet_type' => 'gst',
    'tracking_id' => '',
    'gtm_id' => '',
    'domain' => '',
    'tel_link_tracking' => '0',
    'email_link_tracking' => '0',
    'download_tracking' => '0',
    'download_tracking_type' => array("pdf", "mp3", "pptx", "docx"),
    'force_snippet' => "none",
      // Roles for which tracking is disabled
    'scroll_elements' => array(),
    'click_elements' => array(),
  );

  public static function start_activation()
  {
    // Decide whether to upgrade from previous version
    if (GA_EVENTS_VERSION !== get_option('GA_EVENTS_version') && false !== get_option('ga_events_options')) {
      self::GA_EVENTS_upgrade();
    } else {
      self::initialize_options();
    }
  }

  private static function initialize_options()
  {

    update_option('GA_EVENTS_version',
      GA_EVENTS_VERSION);

    if (!get_option('ga_events_options')) {
      update_option('ga_events_options',
        self::$default_options);
    }

    $triggerreview = mktime(0, 0, 0, date('m')  , date('d') + 10 , date('Y'));
    update_option( 'ga_events_activation_date', $triggerreview, '', 'yes' );

    wpgae_reactivate_notice();
  }

  public static function GA_EVENTS_upgrade()
  {
    $existing_values = get_option('ga_events_options');
    $installed_version = get_option('GA_EVENTS_version');
    if (!isset($installed_version)) {
      $installed_version == "0.0.0";
    }


    if (version_compare(GA_EVENTS_VERSION, $installed_version) > 0) {
      if (version_compare("2.6.0", $installed_version) > 0) {
        $updated = static::version_2_6_0_upgrade($existing_values);
      }
    }


    if (version_compare(GA_EVENTS_VERSION, $installed_version) > 0) {
      if (version_compare("2.6.1", $installed_version) > 0) {
          if (isset($updated)) {
              $updated = static::version_2_6_1_upgrade($updated);
          } else {
              $updated = static::version_2_6_1_upgrade($existing_values);
          }
      }
    }


      // last value in array_merge overwrites others
    if (isset($updated)) {
      // last value in array_merge overwrites others
      update_option('ga_events_options',
        $updated);
    }
    update_option('GA_EVENTS_version',
      GA_EVENTS_VERSION);
  }

  private static function version_2_6_1_upgrade($existing_values) {
    $existing_values['download_tracking_type'] = array("pdf", "mp3", "pptx", "docx");
    return $existing_values;
  }

  private static function version_2_6_0_upgrade($existing_values)
  {
    // Conditionally determine the desired snippet type based on existing
    // values
    $default_snippet = 'universal';

    if (array_key_exists("exclude_snippet", $existing_values) && $existing_values['exclude_snippet'] == 1) { // The "include_snippet" value is misleading
      $default_snippet = 'none';
    }

    if (array_key_exists("gst", $existing_values) && $existing_values['gst'] == 1) {
      $default_snippet = 'none';
    }

    if (array_key_exists("gtm", $existing_values) && $existing_values['gtm'] == 1) {
      $default_snippet = 'none';
    }

    if ((!array_key_exists("universal", $existing_values) || $existing_values['universal'] == 0) && (!array_key_exists("include_snippet", $existing_values) || $existing_values['include_snippet'] == 0) && (!array_key_exists("gst", $existing_values) || $existing_values['gst']) == 0 && (!array_key_exists("gtm", $existing_values) || $existing_values['gtm'] == 0)) {
      $default_snippet = 'legacy';
    }

    // New key=>value pairs for options array
    $new_options = array(
      'snippet_type' => $default_snippet,
      'gtm_id' => '',
    );

    // We need to modify click and scroll options to associative array on update
    $keys = array('selector', 'type', 'category', 'action', 'label', 'bounce', 'value');
    if (array_key_exists("click", $existing_values)) {
      foreach ($existing_values['click'] as &$click_item) {
        // We need a way to migrate the old settings which are sub optimal.
        // That is why we are doing the migration this way
        $new_click_array = array();
        for ($i = 0; $i < sizeof($keys); $i++) {
          if (isset($click_item[$i])) {
            $new_click_array[$keys[$i]] = $click_item[$i];
          }
        }
        $click_item = $new_click_array;
      }
    }

    if (array_key_exists("divs", $existing_values)) {
      foreach ($existing_values['divs'] as &$scroll_item) {
        $new_scroll_array = array();
        for ($i = 0; $i < sizeof($keys); $i++) {
          if (isset($scroll_item[$i])) {
            $new_scroll_array[$keys[$i]] = $scroll_item[$i];
          }
        }
        $scroll_item = $new_scroll_array;
      }
    }

    $updated = array(
      'anonymizeip' => static::assign_if_set('anonymizeip', $existing_values),
      'advanced' => static::assign_if_set('advanced', $existing_values),
      'snippet_type' => $default_snippet,
      'tracking_id' => static::assign_if_set('id', $existing_values),
      'gtm_id' => '',
      'domain' => static::assign_if_set('domain', $existing_values),
      'click_elements' => array(),
      'scroll_elements' => array(),
      'script_debug_mode' => '1'
    );

    $updated["link_clicks_delay"] = array_key_exists("link_clicks_delay", $existing_values) ? $existing_values["link_clicks_delay"] : 120;


    if (isset($existing_values["click"])) {
      $click_events = WPGAEGClickEvent::convert_and_save_cached_settings($existing_values["click"]);
      if (isset($click_events)) {
        $updated["click_elements"] = WPGAEGClickEvent::prepare_cache_settings();
      }
    }

    if (isset($existing_values["divs"])) {
      $scroll_elements = WPGAEGScrollEvent::convert_and_save_cached_settings($existing_values["divs"]);
      if (isset($scroll_elements)) {
        $updated["scroll_elements"] = WPGAEGScrollEvent::prepare_cache_settings();
      }
    }

    return $updated;
  }

  protected static function assign_if_set($param, $the_array)
  {
    return array_key_exists($param, $the_array) && !empty($the_array[$param]) ? $the_array[$param] : "";
  }
}