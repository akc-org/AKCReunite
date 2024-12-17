 <?php

if( ! class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class WPGAE_Tracking_Table extends WP_List_Table {

  protected $wpgae_post_type;
  protected $wpgae_post_columns;
  protected $wpgae_first_column;


  function get_columns(){
    $columns = array();
    $columns['cb'] = '<input type="checkbox" />';
    foreach($this->wpgae_post_columns as $key => $value) {
      $columns[$key] = $value["title"];
    }
    return $columns;
  }

  function getData() {
    if (isset($_POST["s"]) && $_POST["s"] != "") {
      $meta_query = array(
        'relation' => 'OR'
      );

      foreach($this->wpgae_post_columns as $key => $value) {
        $entry  = array(
          'key' => $key,
          'value' => $_POST["s"],
          'compare' => 'LIKE'
        );
        array_push($meta_query, $entry);
      }

      $tracking_entries = get_posts(array(
        'post_type' => $this->wpgae_post_type,
        'post_status' => 'publish',
        'numberposts' => -1,
        'meta_query' => $meta_query
        // 'order'    => 'ASC'
      ));


      if (isset($tracking_entries)) {
        return $tracking_entries;
      }
    }
    else {
      $tracking_entries = get_posts(array(
        'post_type' => $this->wpgae_post_type,
        'post_status' => 'publish',
        'numberposts' => -1
        // 'order'    => 'ASC'
      ));

      if (isset($tracking_entries)) {
        return $tracking_entries;
      }
    }
  }

  function getColumnData($post, $column_name) {
    if ($column_name == "cb") {
      return column_cb($post);
    }
    // Todo - can we optimize this so we query the post meta just once?
    $post_meta = get_post_meta($post->ID);
    if (isset($post_meta[$column_name]) && isset($post_meta[$column_name][0])) {
      $column_data = $post_meta[$column_name][0];
      $column_type = $this->wpgae_post_columns[$column_name]["type"];
      if ($column_type == "checkbox") {
        return $column_data ? "<i class='far fa-check-square' style='font-style: normal;font-family:FontAwesome'></i>": "";
      }
      return $column_data;
    } else {
      return "";
    }
  }

  function column_cb($item) {
    return sprintf(
      '<input type="checkbox" name="wpgae_event[]" value="%s" />', $item->ID
    );
  }

  function prepare_items() {
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = array();
    $this->_column_headers = array($columns, $hidden, $sortable, $this->wpgae_first_column);
    $this->items = $this->getData();

    $this->process_bulk_action();
  }

  function column_default( $item, $column_name ) {
    if (array_key_exists($column_name, $this->wpgae_post_columns)) {
      return $this->getColumnData($item, $column_name);
    }
    return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
  }
//
  function get_bulk_actions() {
    $actions = array(
      'delete'    => 'Delete'
    );
    return $actions;
  }

  function process_bulk_action() {
    if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {
      $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
      $action = 'bulk-' . $this->_args['plural'];

      if ( ! wp_verify_nonce( $nonce, $action ) )
        wp_die( 'Nope, not this time' );
    }
    $action = $this->current_action();

    if ($action == "delete") {
      $posts = isset( $_POST['wpgae_event'] ) ? (array) $_POST['wpgae_event'] : array();
      global $wpgae;
      foreach (get_declared_classes() as $class) {
        if (is_subclass_of($class, WPGAEGeneralEvent::class)) {
          if ($this->wpgae_post_type == $class::get_wpgae_post_type() ) {
            $class::deletePostsById($posts);
            break;
          }
        }
      }
    }

  }
}