<?php

class WPGAEGeneralEvent {
  protected static $my_options;
  protected static $wpgae_post_type;
  protected static $wpgae_post_columns;
  protected $event;


  public function create_event() {
    $postarr = array(
      'post_type' => $this->post_type,
      'post_status' => 'publish'
    );

    $post_id = wp_insert_post($postarr);
    foreach($this->event as $key=> $value) {
      if (array_key_exists("value", $value)) {
        update_post_meta(
          $post_id,
          $key,
          $value["value"]
        );
      } else if ($value["type"] == "checkbox") {
        update_post_meta(
          $post_id,
          $key,
          false
        );
      }
    }

  }
  public static function get_wpgae_post_type() {
    return static::$wpgae_post_type;
  }
  public static function  get_wpgae_post_columns() {
    return static::$wpgae_post_columns;
  }

  public static function get_posts() {
    return get_posts(array(
      'post_type' => static::get_wpgae_post_type(),
      'post_status' => 'publish',
      'numberposts' => -1
      // 'order'    => 'ASC'
    ));
  }
  public static function save_event($post_id) {
    foreach(static::get_wpgae_post_columns() as $key=> $value) {
      if (array_key_exists($key, $_POST)) {
        update_post_meta(
          $post_id,
          $key,
          $_POST[$key]
        );
      } else if ($value["type"] == "checkbox") {
        update_post_meta(
          $post_id,
          $key,
          false
        );
      }
    }
    static::set_cache_settings();
  }


  public static function update_cached_options($new_settings) {
    $plugin_options = get_option( 'ga_events_options' );
    $plugin_options[static::$my_options] = $new_settings;
    update_option('ga_events_options', $plugin_options);
  }

  public static function prepare_cache_settings() {
    $cached = array();
    $posts =       $tracking_entries = static::get_posts();

    foreach($posts as $post) {
      $event = array();
      $post_id = $post->ID;
      $post_meta = get_post_meta($post_id);
      foreach(static::get_wpgae_post_columns() as $key=> $value) {
        if (array_key_exists("settings", $value)) {
          $setting = $value["settings"];
          if (array_key_exists($key, $post_meta)) {
            $event[$setting] = $post_meta[$key][0];
          }
        }
      }
      if (array_key_exists("name", $event)) {
        array_push($cached, $event);
      }
    }
    return $cached;
  }

  public static function set_cache_settings() {
    $cached = static::prepare_cache_settings();
    if (sizeof($cached) >= 0 ) {
      static::update_cached_options($cached);
    }
  }

  static public function deletePostsById($post_ids)
  {
    $posts_to_delete = array_map('sanitize_key', $post_ids);
    // Todo: Make sure that we have permisssion to do so
    foreach($posts_to_delete as $post) {
      wp_delete_post($post, true);
    }
    static::set_cache_settings();
  }

  static public function delete_all_event_posts() {
    $posts = static::get_posts();
    $post_ids = array();
    foreach($posts as $post) {
      array_push($post_ids, $post->ID);
    }
    static::deletePostsById($post_ids);
  }

  static public function update_cache_by_post_type($post_type)
  {
    foreach (get_declared_classes() as $class) {
      if (is_subclass_of($class, WPGAEGeneralEvent::class)) {
        if ($post_type == $class::get_wpgae_post_type()) {
          $class::set_cache_settings();
        }
      }
    }
  }
}


class WPGAEGClickEvent extends WPGAEGeneralEvent{
  protected $event;
  protected static $keys = array('selector', 'type', 'category', 'action', 'label', 'bounce', 'value');
  protected static $my_options = "click_elements";
  protected static $wpgae_post_type = "wpgae_click_event";
  protected static $wpgae_post_columns = array (
    '_wpgae_click_selector_meta_key' => array('title' => 'Selector', 'type' => 'input', "settings"=> "name"),
    '_wpgae_click_type_meta_key'     => array('title' => 'Type', 'type' => 'input', "settings"=> "type"),
    '_wpgae_click_category_meta_key' => array('title' => 'Event Category', 'type' => 'input', "settings"=> "category"),
    '_wpgae_click_action_meta_key' => array('title' => 'Event Action', 'type' => 'input', "settings"=> "action"),
    '_wpgae_click_label_meta_key' => array('title' => 'Event Label', 'type' => 'input', "settings"=> "label"),
    '_wpgae_click_value_meta_key' => array('title' => 'Event Value', 'type' => 'input', "settings"=> "value"),
    '_wpgae_click_bounce_meta_key' => array('title' => 'Non-Interaction', 'type' => 'input', "settings"=> "bounce"),
  );

  function __construct($event_array)
  {
    // Create an instance of the class based off of the post columns static object.
    $this->post_type = static::$wpgae_post_type;
    $this->event = static::$wpgae_post_columns;
    if (array_key_exists("selector", $event_array) && isset($event_array["selector"])) {
      $this->event["_wpgae_click_selector_meta_key"]["value"] = $event_array["selector"];
    }
    if (array_key_exists("type", $event_array) && isset($event_array["type"])) {
      $this->event["_wpgae_click_type_meta_key"]["value"] = $event_array["type"];
    }
    if (array_key_exists("category", $event_array) && isset($event_array["category"])) {
      $this->event["_wpgae_click_category_meta_key"]["value"] = $event_array["category"];
    }
    if (array_key_exists("action", $event_array) && isset($event_array["action"])) {
      $this->event["_wpgae_click_action_meta_key"]["value"] = $event_array["action"];
    }
    if (array_key_exists("label", $event_array) && isset($event_array["label"])) {
      $this->event["_wpgae_click_label_meta_key"]["value"] = $event_array["label"];
    }
    if (array_key_exists("value", $event_array) && isset($event_array["value"])) {
      $this->event["_wpgae_click_value_meta_key"]["value"] = $event_array["value"];
    }
    if (array_key_exists("bounce", $event_array) && isset($event_array["bounce"])) {
      $this->event["_wpgae_click_bounce_meta_key"]["value"] = $event_array["bounce"];
    }

  }

   static function convert_and_save_cached_settings($cached) {
     if (sizeof($cached) > 0)	{
       foreach($cached as $event) {
         if (array_key_exists("selector", $event) && isset($event["selector"]) && $event["selector"] != "") {
           if (strpos($event['selector'], "&#039;") !== false) {
             $event['selector'] = str_replace("&#039;", '"', $event['selector']);
           }
           $new_event = new WPGAEGClickEvent($event);
           $new_event->create_event();
         }
       }
       return static::prepare_cache_settings();
     }
   }

  static function wpgae_register_click_event_post_type()
  {
    $labels = array(
      'name' => 'Click Tracking',
      'singular_name' => 'Click Event',
      'add_new' => 'Add New',
      'add_new_item' => 'Add New Click Event',
      'edit_item' => 'Edit Click Event',
      'new_item' => 'New Click Event',
      'all_items' => 'Click Tracking',
      'view_item' => 'View Click Event',
      'search_items' => 'Search Click Events',
      'not_found' => 'No Click Events found',
      'not_found_in_trash' => 'No Click Events found in Trash',
      'parent_item_colon' => '',
      'menu_name' => 'Click Events'
    );

    $args = array(
      'labels' => $labels,
      'public' => false,
      'publicly_queryable' => false,
      'show_ui' => false,
      'show_in_menu' => false,
      'query_var' => true,
      'rewrite' => array('slug' => 'click-event'),
      'capability_type' => 'post',
      'has_archive' => true,
      'hierarchical' => false,
      'menu_position' => null,
      'supports' => false
    );
    register_post_type('wpgae_click_event', $args);

    add_action('save_post_wpgae_click_event', 'WPGAEGClickEvent::save_event');
  }
}

class WPGAEGScrollEvent extends WPGAEGeneralEvent{
  protected $event;
  protected static $keys = array('selector', 'type', 'category', 'action', 'label', 'bounce', 'value');
  protected static $my_options = "scroll_elements";
  protected static $wpgae_post_type = "wpgae_scroll_event";
  protected static $wpgae_post_columns = array (
    '_wpgae_scroll_selector_meta_key' => array('title' => 'Selector', 'type' => 'input', "settings"=> "name"),
    '_wpgae_scroll_type_meta_key'     => array('title' => 'Type', 'type' => 'input', "settings"=> "type"),
    '_wpgae_scroll_category_meta_key' => array('title' => 'Event Category', 'type' => 'input', "settings"=> "category"),
    '_wpgae_scroll_action_meta_key' => array('title' => 'Event Action', 'type' => 'input', "settings"=> "action"),
    '_wpgae_scroll_label_meta_key' => array('title' => 'Event Label', 'type' => 'input', "settings"=> "label"),
    '_wpgae_scroll_value_meta_key' => array('title' => 'Event Value', 'type' => 'input', "settings"=> "value"),
    '_wpgae_scroll_bounce_meta_key' =>  array('title' => 'Non-Interaction', 'type' => 'input', "settings"=> "bounce"),
  );

  function __construct($event_array)
  {
    // Create an instance of the class based off of the post columns static object.
    $this->post_type = static::$wpgae_post_type;
    $this->event = static::$wpgae_post_columns;
    if (array_key_exists("selector", $event_array) && isset($event_array["selector"])) {
      $this->event["_wpgae_scroll_selector_meta_key"]["value"] = $event_array["selector"];
    }
    if (array_key_exists("type", $event_array) && isset($event_array["type"])) {
      $this->event["_wpgae_scroll_type_meta_key"]["value"] = $event_array["type"];
    }
    if (array_key_exists("category", $event_array) && isset($event_array["category"])) {
      $this->event["_wpgae_scroll_category_meta_key"]["value"] = $event_array["category"];
    }
    if (array_key_exists("action", $event_array) && isset($event_array["action"])) {
      $this->event["_wpgae_scroll_action_meta_key"]["value"] = $event_array["action"];
    }
    if (array_key_exists("label", $event_array) && isset($event_array["label"])) {
      $this->event["_wpgae_scroll_label_meta_key"]["value"] = $event_array["label"];
    }
    if (array_key_exists("value", $event_array) && isset($event_array["value"])) {
      $this->event["_wpgae_scroll_value_meta_key"]["value"] = $event_array["value"];
    }
    if (array_key_exists("bounce", $event_array) && isset($event_array["bounce"])) {
      $this->event["_wpgae_scroll_bounce_meta_key"]["value"] = $event_array["bounce"];
    }

  }
  static function convert_and_save_cached_settings($cached) {
    if (sizeof($cached) > 0)	{
      foreach($cached as $event) {
        $new_event = new WPGAEGScrollEvent($event);
        $new_event->create_event();
      }
      return static::prepare_cache_settings();
    }
  }

  static function wpgae_register_scroll_event_post_type()
  {
    $labels = array(
      'name' => 'Scroll Tracking',
      'singular_name' => 'Scroll Event',
      'add_new' => 'Add New',
      'add_new_item' => 'Add New Scroll Event',
      'edit_item' => 'Edit Scroll Event',
      'new_item' => 'New Scroll Event',
      'all_items' => 'Scroll Tracking',
      'view_item' => 'View Scroll Event',
      'search_items' => 'Search Scroll Events',
      'not_found' => 'No Scroll Events found',
      'not_found_in_trash' => 'No Scroll Events found in Trash',
      'parent_item_colon' => '',
      'menu_name' => 'Scroll Events'
    );

    $args = array(
      'labels' => $labels,
      'public' => false,
      'publicly_queryable' => false,
      'show_ui' => false,
      'show_in_menu' => false,
      'query_var' => true,
      'rewrite' => array('slug' => 'scroll-event'),
      'capability_type' => 'post',
      'has_archive' => true,
      'hierarchical' => false,
      'menu_position' => null,
      'supports' => false
    );
    register_post_type('wpgae_scroll_event', $args);
    add_action('save_post_wpgae_scroll_event', 'WPGAEGScrollEvent::save_event');
  }
}