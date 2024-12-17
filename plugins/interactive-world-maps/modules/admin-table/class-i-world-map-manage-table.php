<?php


/*************************** TABLE CLASS */
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class i_world_map_manage_table extends WP_List_Table {


	function __construct() {
		global $status, $page;

		// Set parent defaults
		parent::__construct(
			array(
				'singular' => 'map',     // singular name of the listed records
				'plural'   => 'maps',    // plural name of the listed records
				'ajax'     => false,        // does this table support ajax?
			)
		);

	}


	function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'shortcode':
				return "[show-map id='" . $item['id'] . "']";

			case 'image':
				if ( isset( $item['image'] ) && $item['image'] !== '' ) {
					return "<img src='" . $item['image'] . "' width='200px'>";
				} else {
					return ''; }

				case 'date':
				return $item['created'];

			default:
				return $item[ $column_name ];
		}
	}


	function column_name( $item ) {

		$edi_url_html = sprintf(
			'<a title="%s" href="?page=iwm_add&action=%s&map=%s">%s</a>',
			__( 'Edit this map', 'interactive-world-maps' ),
			'edit',
			$item['id'],
			__( 'Edit', 'interactive-world-maps' )
		);

		$del_url = wp_nonce_url(
			sprintf( '?page=%s&action=%s&map=%s',
			$_REQUEST['page'],
			'delete',
			$item['id']
			),
			'delete_map'
		);
		$del_url_html = sprintf(
			'<a title="%s" href="%s">%s</a>',
			__( 'Delete Map', 'interactive-world-maps' ),
			$del_url,
			__( 'Delete', 'interactive-world-maps' )
		);

		$dup_url = wp_nonce_url(
			sprintf( '?page=%s&action=%s&map=%s',
			$_REQUEST['page'],
			'duplicate',
			$item['id']
			),
			'duplicate_map'
		);
		$dup_url_html = sprintf(
			'<a title="%s" href="%s">%s</a>',
			__( 'Duplicate this map', 'interactive-world-maps' ),
			$dup_url,
			__( 'Duplicate', 'interactive-world-maps' )
		);

		// Build row actions
		$actions = array(
			'edit'      => $edi_url_html,
			'duplicate' => $dup_url_html,
			'delete'    => $del_url_html,

		);

		// Return the title contents
		return sprintf(
			'%1$s <span style="color:silver">( id:%2$s)</span>%3$s',
			/*$1%s*/ $item['name'],
			/*$2%s*/ $item['id'],
			/*$3%s*/ $this->row_actions( $actions )
		);
	}



	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],  // Let's simply repurpose the table's singular label
			/*$2%s*/ $item['id']                // The value of the checkbox should be the record's id
		);
	}


	function get_columns() {
		$columns = array(
			'cb'          => '<input type="checkbox" />', // Render a checkbox instead of text
			// 'id'     => 'ID',
			'name'        => 'Name',
			'description' => 'Description',
			'shortcode'   => 'Shortcode',
			'image'       => 'Preview',
			'created'     => 'Date',
		);

		$iwmoptions = get_option( 'i-world-map-settings' );
		if ( ! isset( $iwmoptions['image_preview_enabled'] ) ) {
			unset( $columns['image'] );
		}

		return $columns;
	}

	function get_sortable_columns() {
		$sortable_columns = array(
			'name'        => array( 'name', false ),
			'description' => array( 'description', false ),
			'created'     => array( 'created', true ),     // true means its already sorted

		);
		return $sortable_columns;
	}


	function get_bulk_actions() {
		$actions = array(
			'bulk-delete' => 'Delete',
		);
		return $actions;
	}



	function process_bulk_action() {

		// Detect when a bulk action is being triggered...
		if ( 'bulk-delete' === $this->current_action() ) {

			foreach ( $_GET['map'] as $map ) {
				delete_i_world_map( sanitize_key( $map ) );
			}

			$alert = __( 'Map( s) Deleted', 'interactive-world-maps' );
			i_world_map_message( $alert );
		}
	}


	function prepare_items() {

		$per_page = 25;

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		global $wpdb;
		$sql_fields      = i_world_map_get_SQL_fields();
		$table_name_imap = i_world_map_table_name();
		$query = 'SELECT ' . $sql_fields . ' FROM ' . $table_name_imap;
		$data  = $wpdb->get_results( $query, ARRAY_A );

		function usort_reorder( $a, $b ) {
			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'created'; // If no sort, default to title
			$order   = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc'; // If no order, default to asc
			$result  = strcmp( $a[ $orderby ], $b[ $orderby ] ); // Determine sort order
			return ( $order === 'asc' ) ? $result : -$result; // Send final sort direction to usort
		}
		usort( $data, 'usort_reorder' );

		$current_page = $this->get_pagenum();
		$total_items  = count( $data );

		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->items = $data;

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

}

?>