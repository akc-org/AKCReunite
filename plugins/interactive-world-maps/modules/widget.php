<?php

/**
 * Widget
 */
class IWM_Widget extends WP_Widget

{
	public

	function __construct()
	{

		$widget_ops = array(
			'classname' => 'iwm_widget',
			'description' => __('Display a previously created Map','interactive-world-maps')
		);
		parent::__construct( 'iwm_widget', __('Interactive Map','interactive-world-maps'), $widget_ops);
	}

	public

	function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters( 'widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

		$id = isset($instance['id']) ? $instance['id'] : '1';

		$return = '';

		$return .= $before_widget;
		if (!empty($title)) $return .= $before_title . $title . $after_title;

		if($id !== '0') {
			$return .= do_shortcode('[show-map id="'.$id.'"]');
		}
		else {
			$return .= '<!-- Empty Map Container -->';
		}

		$return .= $after_widget;

		echo $return;

	}

	public

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['id'] = $new_instance['id'];
		return $instance;
	}

	public

	function form($instance)
	{
		$instance = wp_parse_args((array)$instance, array(
			'title' => '',
			'id' => '1'
		));
		$title = strip_tags($instance['title']);
		$id = isset($instance['id']) ? $instance['id'] : '';

		echo '<p><label for="'.$this->get_field_id( 'title' ).'">Title:</label>
        	  <input class="widefat" id="'.$this->get_field_id( 'title' ).'" name="'.$this->get_field_name( 'title' ).'" type="text" value="'.esc_attr($title).'">
        	  </p>';

        global $wpdb;
		$table_name_imap = i_world_map_table_name();

		$sql_fields   = i_world_map_get_SQL_fields();
		$maps_created = $wpdb->get_results( "SELECT " . $sql_fields . " FROM " . $table_name_imap, ARRAY_A);

		$maps = array();

		if(count($maps_created) >= 1) {

		echo '
		<p>
        <label for="'.$this->get_field_id( 'id' ).'">Map to display:</label>

        <select id="'.$this->get_field_id( 'id' ).'" name="'.$this->get_field_name( 'id' ).'">';

        echo "<option value='0' ".selected($id, '0' )."> -- Select -- </option>";

			foreach ($maps_created as $map) {

				echo "<option value='".$map['id']."' ".selected($id, $map['id'] )."> ".$map['name']."</option>";

				}

		echo '</select>';

		} else {

			echo '<p>'.__('Please create a map first','interactive-world-maps').'</p>';

		}

	}
}

add_action( 'widgets_init', 'register_iwm_widget' );
/**
 * Register widget
 *
 * This functions is attached to the 'widgets_init' action hook.
 */

function register_iwm_widget()
{
	if( 'layerswp' !== get_template() ) {

		register_widget( 'IWM_Widget' );

	}

}

?>