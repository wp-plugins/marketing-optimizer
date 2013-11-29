<?php
if (! class_exists ( 'WP_List_Table' )) {
	require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class MO_List_Table extends WP_List_Table {
	function __construct() {
		global $status, $page;
		
		// Set parent defaults
		parent::__construct ( array (
				'singular' => 'Experiments', // singular name of the listed records
				'plural' => 'Experiment', // plural name of the listed records
				'ajax' => false,
				'screen' => true  // does this table support ajax?
				) );
	}
	function column_default($item, $column_name) {
		switch ($column_name) {
			case 'title' :
				return $item ['post_' . $column_name];
				break;
			case 'stats' :
				echo mo_get_variation_page_stats_table ( $item ['ID'] );
				break;
			default :
			// return print_r($item,true); //Show the whole array for troubleshooting purposes
		}
	}
	function column_title($item) {
		$last_reset = get_post_meta ( $item ['ID'], 'mo_last_stat_reset', true ) ? 'Last Reset: ' . date ( 'n/j/y h:ia', get_post_meta ( $item ['ID'], 'mo_last_stat_reset', true ) ) : 'Last Reset: ' . 'Never';
		// Build row actions
		$actions = array (
				'edit' => sprintf ( '<a href="post.php?action=%s&post=%s">Edit</a>', 'edit', $item ['ID'] ),
				// 'delete' => sprintf('<a href="post.php?action=%s&post=%s">Delete</a>','delete',$item['ID']),
				'view' => '<a href="' . get_permalink ( $item ['ID'] ) . '">View</a>',
				'duplicate' => '<a href="admin.php?action=mo_duplicate_variation&post_id=' . $item ['ID'] . '">Duplicate</a>',
				'mo_reset_ab_stats' => sprintf ( '<a href="admin.php?action=%s&post=%s">Reset All Stats</a> <i>(' . $last_reset, 'mo_reset_ab_stats', $item ['ID'] ) . ')</i>' 
		);
		
		// Return the title contents
		return sprintf ( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
            /*$1%s*/ $item ['post_title'],
            /*$2%s*/ $item ['ID'],
            /*$3%s*/ $this->row_actions ( $actions ) );
	}
	function column_cb($item) {
		return sprintf ( '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args ['singular'], 		// Let's simply repurpose the table's singular label ("movie")
		/* $2%s */
		$item ['ID'] )		// The value of the checkbox should be the record's id
		;
	}
	function get_columns() {
		$columns = array (
				"cb" => "&lt;input type=\"checkbox\" /&gt;",
				"title" => "Title",
				"stats" => "Experiment Stats" 
		);
		return $columns;
	}
	function get_sortable_columns() {
		$sortable_columns = array (
				'title' => array (
						'title',
						false 
				)  // true means it's already sorted
				);
		return $sortable_columns;
	}
	function get_bulk_actions() {
		$actions = array (
				'delete' => 'Delete' 
		);
		return $actions;
	}
	function process_bulk_action() {
		
		// Detect when a bulk action is being triggered...
		if ('delete' === $this->current_action ()) {
			wp_die ( 'Items deleted (or they would be if we had items to delete)!' );
		}
	}
	function prepare_items() {
		global $wpdb, $blog_id; // This is used only if making any database queries
		
		$per_page = 10;
		
		$columns = $this->get_columns ();
		$hidden = array ();
		$sortable = $this->get_sortable_columns ();
		
		$this->_column_headers = array (
				$columns,
				$hidden,
				$sortable 
		);
		
		$this->process_bulk_action ();
		if ((is_multisite () && $blog_id == 1) || ! is_multisite ()) {
			$parent_pages = $wpdb->get_col ( 'SELECT meta_value FROM ' . $wpdb->base_prefix . 'postmeta WHERE meta_key = \'mo_variation_parent\' AND meta_value IS NOT NULL AND meta_value != "" GROUP BY meta_value' );
		} else {
			$parent_pages = $wpdb->get_col ( 'SELECT meta_value FROM ' . $wpdb->base_prefix . $blog_id . '_postmeta WHERE meta_key = \'mo_variation_parent\' AND meta_value IS NOT NULL AND meta_value != "" GROUP BY meta_value' );
		}
		if (count ( $parent_pages )) {
			$where_clause = 'AND ';
			$array_last = end ( $parent_pages );
			reset ( $parent_pages );
			foreach ( $parent_pages as $id ) {
				if ($id != $array_last || !count($parent_pages) == 1) {
					$where_clause .= ' ID = ' . $id . ' OR';
				} else {
					$where_clause .= ' ID = ' . $id;
				}
			}
		} else {
			$where_clause = '';
		}
		if ((is_multisite () && $blog_id == 1) || ! is_multisite ()) {
			$data = $wpdb->get_results ( 'SELECT * FROM ' . $wpdb->base_prefix . 'posts WHERE post_type = \'page\' AND post_status = \'publish\' ' . $where_clause, ARRAY_A );
		} else {
			$data = $wpdb->get_results ( 'SELECT * FROM ' . $wpdb->base_prefix . $blog_id . '_posts WHERE post_type = \'page\' AND post_status = \'publish\' ' . $where_clause, ARRAY_A );
		}
		function usort_reorder($a, $b) {
			$orderby = (! empty ( $_REQUEST ['orderby'] )) ? $_REQUEST ['orderby'] : 'title'; // If no sort, default to title
			$order = (! empty ( $_REQUEST ['order'] )) ? $_REQUEST ['order'] : 'asc'; // If no order, default to asc
			$result = strcmp ( $a [$orderby], $b [$orderby] ); // Determine sort order
			return ($order === 'asc') ? $result : - $result; // Send final sort direction to usort
		}
		usort ( $data, 'usort_reorder' );
		
		$current_page = $this->get_pagenum ();
		
		$total_items = count ( $data );
		
		$data = array_slice ( $data, (($current_page - 1) * $per_page), $per_page );
		
		$this->items = $data;
		
		$this->set_pagination_args ( array (
				'total_items' => $total_items, // WE have to calculate the total number of items
				'per_page' => $per_page, // WE have to determine how many items to show on a page
				'total_pages' => ceil ( $total_items / $per_page )  // WE have to calculate the total number of pages
				) );
	}
}
function mo_add_menu_items() {
	add_submenu_page ( __ ( MO_PLUGIN_DIRECTORY . '/mo_settings_page.php', EMU2_I18N_DOMAIN ), 'Experiments', 'Experiments', 'manage_options', 'edit.php?post_type=variation-page', 'mo_render_list_page' );
	add_submenu_page ( __ ( MO_PLUGIN_DIRECTORY . '/mo_settings_page.php', EMU2_I18N_DOMAIN ), 'Variations', 'Variations', 'manage_options', 'edit.php?post_type=variation-page&view=true' );
}
if (get_option ( 'mo_variation_pages' ) == 'true') {
	add_action ( 'admin_menu', 'mo_add_menu_items' );
}
function mo_render_list_page() {
	
	// Create an instance of our package class...
	$moABListTable = new MO_List_Table ();
	// Fetch, prepare, sort, and filter our data...
	$moABListTable->prepare_items ();
	$args = array (
			'name' => 'mo_variation_parent',
			'show_option_none' => 'None',
			'option_none_value' => 0 
	);
	?>
<div class="wrap">

	<div id="icon-users" class="icon32">
		<br />
	</div>
	<h2 style="width: 25%; float: left;">Experiments</h2>
	<p style="float: left;">
	
	
	<form action="admin.php?action=mo_create_experiment" method="post">
		<label for="mo_variation_parent">Select Control Page: </label><?php wp_dropdown_pages( $args ); ?><input
			type="submit" value="Add New Experiment" class="button-primary" />
	</form>
	<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
	<form id="movies-filter" method="get">
		<!-- For plugins, we also need to ensure that the form posts back to our current page -->
		<input type="hidden" name="page"
			value="<?php echo $_REQUEST['page'] ?>" />
		<!-- Now we can render the completed list table -->
            <?php $moABListTable->display()?>
        </form>

</div>
<?php
}