<?php
if (! class_exists ( 'WP_List_Table' )) {
	require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
class mo_landing_page_list_table extends WP_List_Table {
	function __construct() {
		global $status, $page;
		// Set parent defaults
		parent::__construct ( array (
				'singular' => 'Landing Page', // singular name of the listed records
				'plural' => 'Landing Pages', // plural name of the listed records
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
		}
	}
	function column_title($item) {
		$last_reset = get_post_meta ( $item ['ID'], 'mo_last_stat_reset', true ) ? 'Last Reset: ' . date ( 'n/j/y h:ia', get_post_meta ( $item ['ID'], 'mo_last_stat_reset', true ) ) : 'Last Reset: ' . 'Never';
		$actions = array (
                        'edit' => sprintf ( '<a href="post.php?action=%s&post=%s">Edit</a>', 'edit', $item ['ID'] ),
                        'view' => '<a href="/' . get_permalink ( $item ['ID'] ) . '">View</a>',
                        'duplicate' => '<a href="admin.php?action=mo_duplicate_variation&post_id=' . $item ['ID'] . '">Duplicate</a>',
                        'mo_reset_ab_stats' => sprintf ( '<a href="admin.php?action=%s&post=%s">Reset All Stats</a> <i>(' . $last_reset, 'mo_reset_ab_stats', $item ['ID'] ) . ')</i>' 
                );
		
		
		return sprintf ( '%1$s <span style="color:silver">(id:%2$s)</span>%3$s',
                $item ['post_title'],
                $item ['ID'],
                $this->row_actions ( $actions ) );
	}
	function column_cb($item) {
		return sprintf ( '<input type="checkbox" name="%1$s[]" value="%2$s" />',
                        $this->_args ['singular'], 		
                        $item ['ID'] 
                );
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
				)  
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
		if ('delete' === $this->current_action ()) {
			wp_die ( 'Items deleted (or they would be if we had items to delete)!' );
		}
	}
	function prepare_items() {
		global $wpdb, $blog_id; 
		
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
			$orderby = (! empty ( $_REQUEST ['orderby'] )) ? $_REQUEST ['orderby'] : 'title'; 
			$order = (! empty ( $_REQUEST ['order'] )) ? $_REQUEST ['order'] : 'asc';
			$result = strcmp ( $a [$orderby], $b [$orderby] ); 
			return ($order === 'asc') ? $result : - $result; 
		}
		usort ( $data, 'usort_reorder' );
		
		$current_page = $this->get_pagenum ();
		
		$total_items = count ( $data );
		
		$data = array_slice ( $data, (($current_page - 1) * $per_page), $per_page );
		
		$this->items = $data;
		
		$this->set_pagination_args ( array (
				'total_items' => $total_items, 
				'per_page' => $per_page, 
				'total_pages' => ceil ( $total_items / $per_page )  
				) );
	}
}
function mo_lp_add_menu_items() {
	add_submenu_page ( __ ( mo_landing_pages::plugin_name . '-settings', EMU2_I18N_DOMAIN ), 'Landing-Pages', 'Landing-Pages', 'manage_options', 'edit.php?post_type=landing_page', 'mo_lp_render_list_page' );
}
if (get_option ( 'mo_variation_pages' ) == 'true') {
	add_action ( 'admin_menu', 'mo_lp_add_menu_items' );
}
function mo_lp_render_list_page() {
	
	$moABListTable = new mo_landing_page_list_table ();
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
	<h2 style="width: 25%; float: left;">Landing Pages</h2>
	<p style="float: left;">
	<form action="admin.php?action=mo_create_experiment" method="post">
		<label for="mo_variation_parent">Select Control Page: </label><?php wp_dropdown_pages( $args ); ?><input
			type="submit" value="Add New Experiment" class="button-primary" />
	</form>
	<form id="movies-filter" method="get">
		<input type="hidden" name="page"
			value="<?php echo $_REQUEST['page'] ?>" />
	    <?php $moABListTable->display()?>
        </form>
</div>
<?php
}