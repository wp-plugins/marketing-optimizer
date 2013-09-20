<?php
define ( 'VARIATION_PAGE_URLPATH', WP_PLUGIN_URL . '/' . plugin_basename ( dirname ( __FILE__ ) ) . '/' );
define ( 'VARIATIONPAGES_PATH', WP_PLUGIN_DIR . '/' . plugin_basename ( dirname ( __FILE__ ) ) . '/' );
// Variation pages
add_action ( 'init', 'variation_page_register' );
function variation_page_register() {
	$slug = 'variation';
	
	$labels = array (
			'name' => _x ( 'Variations', 'post type general name' ),
			'singular_name' => _x ( 'Variation', 'post type singular name' ),
			'add_new' => _x ( 'Add New', 'Variation' ),
			'add_new_item' => __ ( 'Add New Variation' ),
			'edit_item' => __ ( 'Edit Variation' ),
			'new_item' => __ ( 'New Variation' ),
			'view_item' => __ ( 'View Variation' ),
			'search_items' => __ ( 'Search Variation' ),
			'not_found' => __ ( 'Nothing found' ),
			'not_found_in_trash' => __ ( 'Nothing found in Trash' ),
			'parent_item_colon' => '' 
	);
	
	$args = array (
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_ui_nav_menus' => false,
			'show_in_menu' => false,
			'query_var' => true,
			'menu_icon' => VARIATION_PAGE_URLPATH . 'images/plus.gif',
			'rewrite' => array (
					"slug" => "$slug" 
			),
			'capability_type' => 'page',
			'hierarchical' => false,
			'menu_position' => false,
			'supports' => array (
					'title',
					'editor',
					'custom-fields',
					'thumbnail',
					'excerpt',
					'page-attributes' 
			) 
	);
	
	register_post_type ( 'variation-page', $args );
	
	register_taxonomy ( 'variation_page_category', 'variation-page', array (
			'hierarchical' => true,
			'label' => "Categories",
			'singular_label' => "Variation Page Category",
			'show_ui' => true,
			'query_var' => true,
			"rewrite" => true 
	) );
	register_post_status ( 'variation', array (
			'label' => _x ( 'Variation', 'variation-page' ),
			'public' => true,
			'exclude_from_search' => true,
			'show_in_admin_all_list' => true,
			'show_in_admin_status_list' => true,
			'label_count' => _n_noop ( 'Variation <span class="count">(%s)</span>', 'Variation <span class="count">(%s)</span>' ) 
	) );
}

add_action ( "admin_init", "admin_init_variation_page_meta_boxes" );
function admin_init_variation_page_meta_boxes() {
	add_meta_box ( "variation_page_settings", "Markting Optimizer Variation Settings", "variation_page_settings", "page", "normal", "high" );
	add_meta_box ( "variation_settings", "Markting Optimizer Variation Settings", "variation_settings", "variation-page", "normal", "high" );
	add_meta_box ( 'postparentdiv', __ ( 'Page Template' ), 'post_template_meta_box', 'variation-page', 'side', 'core' );
}
function variation_settings() {
	global $post;
	$custom = get_post_custom ( $post->ID );
	$variation_name = isset ( $custom ["mo_variation_name"] [0] ) ? $custom ["mo_variation_name"] [0] : '';
	$variation_id = isset ( $custom ["mo_variation_id"] [0] ) ? $custom ["mo_variation_id"] [0] : '';
	$variation_parent = isset ( $custom ['mo_variation_parent'] [0] ) ? $custom ['mo_variation_parent'] [0] : '';
	$args = array (
			'selected' => $variation_parent,
			'name' => 'mo_variation_parent' 
	);
	?>
<table>
	<tr>
		<td><label>Variation Description:</label></td>
		<td><input name="mo_variation_name"
			value="<?php echo $variation_name; ?>" /></td>
	</tr>
	<tr>
		<td><label>Marketing Optimizer Variation ID:</label></td>
		<td><input name="mo_variation_id" value="<?php echo $variation_id; ?>" /></td>
	</tr>
	<tr>
		<td><label>Control:</label></td>
		<td><?php wp_dropdown_pages( $args ); ?></td>
	</tr>
</table>
<?php
}
function variation_page_settings() {
	global $post;
	$custom = get_post_custom ( $post->ID );
	$variation_id = isset ( $custom ["mo_variation_id"] [0] ) ? $custom ["mo_variation_id"] [0] : '';
	$variation_name = isset ( $custom ["mo_variation_name"] [0] ) ? $custom ["mo_variation_name"] [0] : '';
	?>
<table>
	<tr>
		<td><label>Marketing Optimizer Variation ID:</label></td>
		<td><input name="mo_variation_id" value="<?php echo $variation_id; ?>" /></td>
	</tr>
	<tr>
		<td><label>Variation Description:</label></td>
		<td><input name="mo_variation_name"
			value="<?php echo $variation_name; ?>" /></td>
	</tr>
</table>
<?php
}
add_action ( 'admin_head', 'mo_experiments_column_width' );
function mo_experiments_column_width() {
	echo '<style type="text/css">
        .column-title { text-align: left; width:20% !important; overflow:hidden }
        .column-stats { text-align: left; width:80% !important; overflow:hidden }
    </style>';
}
add_filter ( 'wp_insert_post_data', 'mo_save_variation_settings', 10, 2 );
function mo_save_variation_settings($data, $postArr) {
	global $post;
	// Check it's not an auto save routine
	if (defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || $postArr ['post_type'] == 'revision')
		
		return $data;
	
	if (isset ( $postArr ['ID'] ) && $postArr ['ID']) {
		
		// Perform permission checks! For example:
		if (! current_user_can ( 'edit_post', $post->ID ))
			return;
		
		if ($post->post_type == 'variation-page') {
			update_post_meta ( $post->ID, "mo_variation_name", $postArr ['mo_variation_name'] );
			update_post_meta ( $post->ID, "mo_variation_id", $postArr ['mo_variation_id'] );
			update_post_meta ( $post->ID, "mo_variation_parent", $postArr ['mo_variation_parent'] );
			update_post_meta ( $post->ID, "mo_variation_active", 'true' );
		} else {
			update_post_meta ( $post->ID, "mo_variation_id", $postArr ['mo_variation_id'] );
			update_post_meta ( $post->ID, "mo_variation_name", $postArr ['mo_variation_name'] );
		}
		
		if ($post->post_type == 'variation-page' && ! empty ( $postArr ['mo_post_template'] )) {
			update_post_meta ( $post->ID, '_post_template', $postArr ['mo_post_template'] );
		}
		// If calling wp_update_post, unhook this function so it doesn't loop infinitely
		remove_filter ( 'wp_insert_post_data', 'mo_save_variation_settings' );
		if ($post->post_type == 'variation-page' && $_GET ['action'] != 'trash') {
			$postArr ['post_status'] = 'variation';
		}
		wp_update_post ( $postArr );
		add_filter ( 'wp_insert_post_data', 'mo_save_variation_settings', 10, 2 );
	} else {
		return $data;
	}
}
function post_template_meta_box($post) {
	if ('variation-page' == $post->post_type && 0 != count ( get_page_templates () )) {
		$template = get_post_meta ( $post->ID, '_post_template', true );
		?>
<label class="screen-reader-text" for="post_template"><?php _e('Post Template') ?></label>
<select name="mo_post_template" id="post_template">
	<option value='default'><?php _e('Default Template'); ?></option>
<?php page_template_dropdown($template); ?>
</select>
<?php
	}
	?>
<?php
}
function post_template_dropdown($default = '') {
	$templates = get_page_templates ();
	ksort ( $templates );
	foreach ( array_keys ( $templates ) as $template ) :
		if ($default == $templates [$template])
			$selected = " selected='selected'";
		else
			$selected = '';
		echo "\n\t<option value='" . $templates [$template] . "' $selected>$template</option>";
	endforeach
	;
}
function get_post_template_for_template_loader($template) {
	global $post;
	
	if ($post && $post->post_type == 'variation-page') {
		$post_template = get_post_meta ( $post->ID, '_post_template', true );
		$parent_post_template = get_post_meta ( get_post_meta ( $post->ID, 'mo_variation_parent', true ), '_post_template', true );
		if (empty ( $post_template ) && (! $post_template || $post_template == 'default')) {
			if ($parent_post_template && file_exists ( get_template_directory () . '/' . $parent_post_template )) {
				$template = get_template_directory () . '/' . $parent_post_template;
			} elseif (file_exists ( get_template_directory () . '/page-' . $post->post_name . '.php' )) {
				$template = get_template_directory () . '/page-' . $post->post_name . '.php';
			} elseif (file_exists ( get_template_directory () . '/page-' . $post->ID . '.php' )) {
				$template = get_template_directory () . '/page-' . $post->ID . '.php';
			} elseif (file_exists ( get_template_directory () . '/page.php' )) {
				$template = get_template_directory () . '/page.php';
			} elseif (file_exists ( get_template_directory () . 'index.php' )) {
				$template = get_template_directory () . '/index.php';
			}
			return ($template);
		}
		if (! empty ( $post_template ) && $post_template != 'default' && file_exists ( get_template_directory () . "/{$post_template}" ))
			$template = get_template_directory () . "/{$post_template}";
		return ($template);
	}
	return $template;
}

add_filter ( 'template_include', 'get_post_template_for_template_loader' );

add_action ( 'wp', 'mo_make_variation_page' );
function mo_make_variation_page() {
	global $wp_query, $post;
	$parent_post_id = get_post_meta ( $post->ID, 'mo_variation_parent', true );
	if ($post->post_type == 'variation-page') {
		$wp_query->is_page = true;
		$wp_query->is_single = false;
	}
}
function get_variation_template_for_template_loader() {
	global $post, $variation_post_id;
	if (is_object ( $post ) && $post->ID && mo_is_experiment ( $post->ID )) {
		
		$variationMetaDataArr = mo_get_variation_meta_data ( $post->ID );
		// $variationMetaDataArr [$post->ID] = get_post_meta ( $post->ID );
		if (is_array ( $variationMetaDataArr ) && count ( $variationMetaDataArr ) > 0) {
			$totalPageViews = 0;
			foreach ( $variationMetaDataArr as $post_id => $metaDataArr ) {
				if (isset ( $metaDataArr ['mo_page_views_count'] [0] )) {
					$totalPageViews = ( int ) $totalPageViews + ( int ) $metaDataArr ['mo_page_views_count'] [0];
				} else {
					update_post_meta ( $post_id, 'mo_page_views_count', 0 );
				}
			}
			// if ($totalPageViews > 0) {
			if (isset ( $_GET ['v'] ) && $_GET ['v']) {
				$variationPostId = $_GET ['v'];
			} else {
				$variationPostId = mo_get_variation_to_show ( $post->ID, $variationMetaDataArr );
			}
			$variation_post_id = $variationPostId;
			update_post_meta ( $variationPostId, 'mo_page_views_count', $variationMetaDataArr [$variationPostId] ['mo_page_views_count'] [0] + 1 );
			if (mo_is_experiment ( $post->ID ) && get_option ( 'mo_cache_compatible' ) && ! $_GET ['t'] && ! $_GET ['v']) {
				if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
					include (__DIR__ . '/templates' . DS . 'ajax.php');
				} else {
					include ('templates' . DS . 'ajax.php');
				}
				exit ();
			}
			$variationContent = get_post ( $variationPostId );
			$post->post_content = $variationContent->post_content;
			$post->post_variation = get_post_meta ( $variationPostId, 'mo_variation_id', true );
			if (isset ( $variationMetaDataArr [$variationPostId] ['_post_template'] [0] ) && $variationMetaDataArr [$variationPostId] ['_post_template'] [0] != 'default' && file_exists ( get_template_directory () . '/' . $variationMetaDataArr [$variationPostId] ['_post_template'] [0] )) {
				include (get_template_directory () . '/' . $variationMetaDataArr [$variationPostId] ['_post_template'] [0]);
				exit ();
			}
		} else {
		}
	}
}
function mo_is_experiment($post_id) {
	global $wpdb;
	if ($post_id) {
		$variations = $wpdb->get_results ( "SELECT post_id from wp_postmeta WHERE meta_key = 'mo_variation_parent' AND meta_value = " . ( int ) $post_id );
		
		if (count ( $variations ) > 0) {
			return true;
		} else {
			return false;
		}
	}
}
add_action ( 'template_redirect', 'get_variation_template_for_template_loader' );
function mo_get_variation_metadata($metadata, $object_id, $meta_key, $single) {
	global $post, $variation_post_id;
	if ($object_id == $post->ID && $variation_post_id != $object_id && ! is_admin () && ! is_home ()) {
		mo_writelog ( 'returning variation meta ' . $meta_key );
		return get_post_meta ( $variation_post_id, $meta_key, true );
	}
	mo_writelog ( 'returning parent meta ' . $meta_key );
	return get_post_meta ( $object_id, $meta_key, $single );
}
add_filter ( 'get_page_metadata', 'mo_get_variation_metadata', 1, 4 );
// add_filter ( 'get_post_metadata', 'mo_get_variation_metadata', 1, 4 );

// function mo_get_metaboxes(){
// global $wp_meta_boxes;
// if(is_array($wp_meta_boxes['page']) && is_array($wp_meta_boxes['page']['advanced']) && is_array($wp_meta_boxes['page']['advanced']['default'])){

// }
// }
// add_action('admin_head','mo_get_metaboxes');
function mo_get_variation_meta_data($post_id = false, $show_paused = false) {
	global $wpdb;
	if ($post_id) {
		$variations = $wpdb->get_results ( "SELECT post_id from wp_postmeta WHERE meta_key = 'mo_variation_parent' AND meta_value = " . ( int ) $post_id );
		
		if (count ( $variations ) > 0) {
			$variationMetaDataArr = array ();
			foreach ( $variations as $variation ) {
				if (! $show_paused) {
					if (get_post_meta ( $variation->post_id, 'mo_variation_active', true ) == 'true') {
						$variationMetaDataArr [$variation->post_id] = get_post_meta ( $variation->post_id );
					}
				} else {
					$variationMetaDataArr [$variation->post_id] = get_post_meta ( $variation->post_id );
				}
			}
			return $variationMetaDataArr;
		} else {
			return false;
		}
	}
}
function mo_get_variation_to_show($post_id = 0, $variationMetaDataArr = array()) {
	if ($post_id > 0 && ! empty ( $variationMetaDataArr )) {
		foreach ( $variationMetaDataArr as $postid => $metaDataArr ) {
			if (array_key_exists ( 'mo_variation_id_' . $postid, $_COOKIE )) {
				return $postid;
			}
			$conversionRateVariationArr [$postid] = mo_get_conversion_rate ( $metaDataArr ['mo_unique_page_views_count'] [0], $metaDataArr ['mo_conversion_count'] [0] );
		}
		arsort ( $conversionRateVariationArr );
		reset ( $conversionRateVariationArr );
		$randNum = rand ( 1, 10 );
		$showPercentage = (( int ) get_option ( 'mo_variation_percentage' ) >= 10) ? ( int ) get_option ( 'mo_variation_percentage' ) / 10 : 0;
		if ($randNum > ( int ) $showPercentage) {
			$variationArr = array ();
			foreach ( $variationMetaDataArr as $postid => $metaDataArr ) {
				if ($postid != key ( $conversionRateVariationArr )) {
					$variationArr [$postid] = $metaDataArr;
				}
			}
			if (count ( $variationArr ) > 1) {
				return array_rand ( $variationArr, 1 );
			} else {
				return key ( $variationArr );
			}
		} else {
			return key ( $conversionRateVariationArr );
		}
	}
}
function get_remaining_show_percentage($post_id) {
	global $wpdb;
	$variations = $wpdb->get_results ( "SELECT post_id from wp_postmeta WHERE meta_key = 'mo_variation_parent' AND meta_value = $post_id", ARRAY_A );
	$totalShowPercentage = 0;
	$showPercentageArr = array ();
	foreach ( $variations as $variation ) {
		$showPercentageArr [$variation ['post_id']] = get_post_meta ( $variation ['post_id'], 'mo_show_percentage', true );
	}
	return 100 - array_sum ( $showPercentageArr );
}
function mo_track_conversion() {
	global $post;
	if (isset ( $_POST ['cookie'] ) && $_POST ['cookie']) {
		$cookieArr = json_decode ( stripslashes ( $_POST ['cookie'] ) );
		$needle = 'mo_experiment_';
		if (! empty ( $cookieArr )) {
			foreach ( $cookieArr as $v ) {
				$cookie = explode ( '=', $v );
				if (strpos ( $cookie [0], $needle ) !== false) {
					$page_id = substr ( $cookie [0], strlen ( $needle ) );
					$variation_id = $cookie [1];
				}
				if (! isset ( $page_id ) || ! $page_id) {
				} elseif (! isset ( $variation_id ) || ! $variation_id) {
				} else {
					$conversion_count = get_post_meta ( $variation_id, 'mo_conversion_count', true );
					if ($conversion_count) {
						update_post_meta ( $variation_id, 'mo_conversion_count', ( int ) $conversion_count + 1 );
					} else {
						update_post_meta ( $variation_id, 'mo_conversion_count', 1 );
					}
				}
			}
		}
	} else {
	}
	return;
}
add_action ( 'wp_ajax_mo_track_conversion', 'mo_track_conversion' );
add_action ( 'wp_ajax_nopriv_mo_track_conversion', 'mo_track_conversion' );
function mo_get_variation_for_display() {
	if (isset ( $_POST ['mo_variation_post_id'] ) && $_POST ['mo_variation_post_id']) {
		$post_id = $_POST ['mo_variation_post_id'];
		$postObj = get_post ( $post_id );
		$post_url = $postObj->guid;
		
		$htmlArr = wp_remote_get ( $post_url );
		echo $htmlArr ['body'];
		die ();
	}
}
add_action ( 'wp_ajax_mo_get_variation_for_display', 'mo_get_variation_for_display' );
add_action ( 'wp_ajax_nopriv_mo_get_variation_for_display', 'mo_get_variation_for_display' );
function mo_get_variation_page_stats_table($post_id = false) {
	if ($post_id) {
		$variationStatsTable = '<table><tr style="background-color:#ECECEC;"><th style="width:200px;">Title</th><th style="width:300px;">Variation Name</th><th style="width:50px;">Visitors</th><th style="width:50px;">Conversions</th><th style="width:110px;">Conversion Rate</th><th style="width:110px;">Conv Rate Diff</th><th style="width:110px;">Confidence</th><th>Variation ID</th></tr>';
		$controlRow = '';
		$variationRows = '';
		$variationMetaDataArr = mo_get_variation_meta_data ( $post_id, true );
		// $variationMetaDataArr = array (
		// $post_id => get_post_meta ( $post_id )
		// ) + $variationMetaDataArr;
		$total_visitors = 0;
		$total_conversions = 0;
		foreach ( $variationMetaDataArr as $k => $v ) {
			$visitors = $v ['mo_unique_page_views_count'] [0] ? $v ['mo_unique_page_views_count'] [0] : 0;
			$conversions = $v ['mo_conversion_count'] [0] ? $v ['mo_conversion_count'] [0] : 0;
			$conversionRatesArr [$k] = mo_get_conversion_rate ( $visitors, $conversions );
		}
		arsort ( $conversionRatesArr );
		reset ( $conversionRatesArr );
		$bestConversionRate = current ( $conversionRatesArr );
		$bestConversionRatePostId = key ( $conversionRatesArr );
		$controlZscoreArr = array (
				'conversion_rate' => $bestConversionRate,
				'visitors' => $variationMetaDataArr [$bestConversionRatePostId] ['mo_unique_page_views_count'] [0] 
		);
		foreach ( $variationMetaDataArr as $k => $v ) {
			// set title
			$title = substr ( get_the_title ( $k ), 0, 30 );
			
			// set variation name
			$variation_name_full = $v ['mo_variation_name'] [0] ? $v ['mo_variation_name'] [0] : '';
			$variation_name = strlen ( $variation_name_full ) > 30 ? substr ( $variation_name_full, 0, 27 ) . '...' : $variation_name_full;
			$variation_name = '<a   title=\'' . $variation_name_full . '\' href=\'/wp-admin/post.php?post=' . $k . "&action=edit'>" . $variation_name . '</a>';
			
			// set variation id
			$variation_id = $v ['mo_variation_id'] [0] ? $v ['mo_variation_id'] [0] : '';
			if ($v ['mo_variation_id'] [0]) {
				$variation_id = '<a  class=\'mo-variation-id\' title=\'click to edit this variation\' href=\'/wp-admin/post.php?post=' . $k . "&action=edit'>" . $v ['mo_variation_id'] [0] . '</a>';
			} else {
				$variation_id = '<a  class=\'mo-variation-id\' title=\'Click to go to the Marketing Optimizer website.\' href=\'http://www.marketingoptimizer.com\'><img src="' . plugins_url ( '/images/moicon.png', __FILE__ ) . '" />' . '</a>';
			}
			// set stats
			$visitors = $v ['mo_unique_page_views_count'] [0] ? $v ['mo_unique_page_views_count'] [0] : 0;
			$conversions = $v ['mo_conversion_count'] [0] ? $v ['mo_conversion_count'] [0] : 0;
			$conversion_rate = mo_get_conversion_rate ( ( float ) $visitors, ( float ) $conversions );
			$total_visitors = $total_visitors + $visitors;
			$total_conversions = $total_conversions + $conversions;
			$is_paused = ((get_post_meta ( $k, 'mo_variation_active', true ) == 'true') ? true : false);
			
			$edit_link = '<a title=\'click to edit this variation\' href=\'/wp-admin/post.php?post=' . $k . '&action=edit\'>Edit</a>';
			$preview_link = '<a href="' . esc_url ( apply_filters ( 'preview_post_link', set_url_scheme ( add_query_arg ( 'preview', 'true', post_permalink ( $k ) ) ) ) ) . '" title="' . esc_attr ( sprintf ( __ ( 'Preview &#8220;%s&#8221;' ), $title ) ) . '" rel="permalink">' . __ ( 'Preview' ) . '</a>';
			$pause_link = '<a href="admin.php?action=mo_pause_variation&post=' . $k . '" ' . (! $is_paused ? 'style="color:red;font-style:italic;font-weight:bold;"' : '') . '>' . ($is_paused ? 'Pause' : 'Unpause') . '</a>';
			$duplicate_link = '<a href="admin.php?action=mo_duplicate_variation&post_id=' . $k . '">Duplicate</a>';
			$trash_link = '<a href="' . get_delete_post_link ( $k ) . '">Trash</a>';
			$promote_link = '<a href="admin.php?action=mo_promote_variation&post=' . $k . '">Promote</a>';
			// if ($k == $post_id) {
			// $control_conversion_rate = $conversion_rate;
			// $controlZscoreArr = array (
			// 'conversion_rate' => $control_conversion_rate,
			// 'visitors' => $visitors
			// );
			// $controlRow .= '<tr><td>' . $title . '</td><td>' . $variation_name . '<br />[' . $edit_link . ' | ' . $preview_link . ' | ' . $duplicate_link . ']</td><td>' . $visitors . '</td><td>' . $conversions . '</td><td>' . number_format ( $conversion_rate * 100, 2 ) . '%</td><td>NA</td><td>NA</td><td>' . $variation_id . '</td></tr>';
			// } else {
			$variationZscoreArr = array (
					'conversion_rate' => $conversion_rate,
					'visitors' => $visitors 
			);
			$zScore = mo_get_zscore ( $controlZscoreArr, $variationZscoreArr );
			$confidence = number_format ( mo_get_cumnormdist ( $zScore ) * 100, 2 ) . '%';
			if (($bestConversionRate - $conversion_rate) > 0) {
				$conversion_rate_diff = '<span style="color:red;">-' . number_format ( ($bestConversionRate - $conversion_rate) * 100, 2 ) . '%</span>';
			} elseif (($bestConversionRate - $conversion_rate) == 0) {
				$conversion_rate_diff = number_format ( ($bestConversionRate - $conversion_rate) * 100, 2 ) . '%';
			} else {
				$conversion_rate_diff = '<span style="color:green;">+' . number_format ( ($bestConversionRate - $conversion_rate) * 100, 2 ) * (- 1) . '%</span>';
			}
			$variationRows .= '<tr><td>' . $title . '</td><td>' . $variation_name . '<br />[' . $edit_link . ' | ' . $preview_link . ' |  ' . $pause_link . ' | ' . $duplicate_link . ' | ' . $trash_link . ' | ' . $promote_link . ']</td><td>' . $visitors . '</td><td>' . $conversions . '</td><td>' . number_format ( $conversion_rate * 100, 2 ) . '%</td><td>' . $conversion_rate_diff . '</td><td>' . $confidence . '</td><td>' . $variation_id . '</td></tr>';
			// }
		}
		$totalRows .= '<tr ><td></td><td style="font-size:14px;"><b>Totals</b></td><td style="font-size:14px;"><b>' . $total_visitors . '</b></td><td style="font-size:14px;"><b>' . $total_conversions . '</b></td><td style="font-size:14px;"><b>' . number_format ( mo_get_conversion_rate ( $total_visitors, $total_conversions ) * 100, 2 ) . '%</b></td><td></td><td></td><td></td></tr>';
		$variationStatsTable .= $variationRows . $totalRows . '</table>';
		return $variationStatsTable;
	}
}
function mo_track_unique_pageview() {
	$response = false;
	if ($_POST ['mo_variation_post_id']) {
		$post_id = $_POST ['mo_variation_post_id'];
		$uniquePageViews = get_post_meta ( $post_id, 'mo_unique_page_views_count', true );
		$incrementedUniquePageViews = ( int ) $uniquePageViews ? ( int ) $uniquePageViews + 1 : 1;
		$response = update_post_meta ( $post_id, 'mo_unique_page_views_count', $incrementedUniquePageViews );
	}
	echo json_encode ( array (
			'success' => $response ? 'true' : 'false',
			'post_id' => $post_id,
			'uniquePageViews' => $uniquePageViews,
			'incrementedUniquePageViews' => $incrementedUniquePageViews 
	) );
}
add_action ( 'wp_ajax_mo_track_unique_pageview', 'mo_track_unique_pageview' );
add_action ( 'wp_ajax_nopriv_mo_track_unique_pageview', 'mo_track_unique_pageview' );
function mo_conversion() {
	if ($_GET ['preview'] != true) {
		echo '<script type="text/javascript" >
						
						
jQuery(document).ready(function($) {
function moGetVariationCookie(){
							var cookies = document.cookie.split(/;\s*/);
							var cookiesArr = [];
							for(var i=0;i < cookies.length;i++){
								var cookie = cookies[i];
								if(cookie.indexOf("mo_experiment_") != -1){
									cookiesArr.push(cookie);
								}
							}
							console.log(cookiesArr);
							return JSON.stringify(cookiesArr);
						}
	var data = {
		action: \'mo_track_conversion\',
		cookie: moGetVariationCookie()
	};

	// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
	$.post(\'' . admin_url ( 'admin-ajax.php' ) . '\', data, function(response) {
	});
});
</script>';
	}
}
add_shortcode ( 'mo_conversion', 'mo_conversion' );
function mo_reset_ab_stats() {
	if (isset ( $_GET ['post'] ) && $_GET ['post']) {
		$post_id = $_GET ['post'];
		$variationMetaDataArr = mo_get_variation_meta_data ( $post_id, true );
		$variationMetaDataArr [$post_id] = get_post_meta ( $post_id );
		foreach ( $variationMetaDataArr as $k => $v ) {
			update_post_meta ( $k, 'mo_page_views_count', 0 );
			update_post_meta ( $k, 'mo_unique_page_views_count', 0 );
			update_post_meta ( $k, 'mo_conversion_count', 0 );
		}
		update_post_meta ( $post_id, 'mo_last_stat_reset', time () );
	}
	wp_redirect ( admin_url ( 'admin.php?page=edit.php?post_type=variation-page' ) );
	exit ();
}
add_action ( 'admin_action_mo_reset_ab_stats', 'mo_reset_ab_stats' );
function mo_promote_variation($post_id = false) {
	global $wpdb;
	if ($post_id || isset ( $_GET ['post'] ) && $_GET ['post']) {
		$post_id = isset ( $_GET ['post'] ) ? $_GET ['post'] : $post_id;
		
		// get the control post id
		$control_post_id = get_post_meta ( $post_id, 'mo_variation_parent', true );
		
		// get the current control post array
		$oldControlArr = get_post ( $control_post_id, ARRAY_A );
		
		// get the variation being promoted post array
		$newControlArr = get_post ( $post_id, ARRAY_A );
		
		// get the current control post meta array
		$oldControlVariationMetaDataArr = get_post_meta ( $control_post_id );
		
		// get the variation being promoted post meta array
		$newControlVariationMetaDataArr = get_post_meta ( $post_id );
		
		foreach ( $oldControlArr as $k => $v ) {
			if ($k == 'post_content' || $k == 'post_title') {
				$oldControlPostArr [$k] = $v;
			}
		}
		$oldControlUpdate = $wpdb->update ( 'wp_posts', $oldControlPostArr, array (
				'ID' => $post_id 
		) );
		foreach ( $newControlArr as $k => $v ) {
			if ($k == 'post_content') {
				$newControlPostArr [$k] = $v;
			}
		}
		$newControlUpdate = $wpdb->update ( 'wp_posts', $newControlPostArr, array (
				'ID' => $control_post_id 
		) );
		foreach ( $newControlVariationMetaDataArr as $k => $v ) {
			if ($k == 'mo_variation_parent') {
				update_post_meta ( $control_post_id, 'mo_variation_parent', '' );
			} else {
				update_post_meta ( $control_post_id, $k, $v [0] );
			}
		}
		foreach ( $oldControlVariationMetaDataArr as $k => $v ) {
			
			if ($k == 'mo_variation_parent') {
				update_post_meta ( $post_id, 'mo_variation_parent', $control_post_id );
			} else {
				update_post_meta ( $post_id, $k, $v [0] );
			}
		}
	}
	if (isset ( $_GET ['post'] ) && $_GET ['post']) {
		wp_redirect ( admin_url ( 'admin.php?page=edit.php?post_type=variation-page' ) );
		exit ();
	}
}
add_action ( 'admin_action_mo_promote_variation', 'mo_promote_variation' );
function mo_pause_variation() {
	if (isset ( $_GET ['post'] ) && $_GET ['post']) {
		$post_id = $_GET ['post'];
		$variation_active = get_post_meta ( $post_id, 'mo_variation_active', true );
		update_post_meta ( $post_id, 'mo_variation_active', $variation_active == 'true' ? 'false' : 'true' );
	}
	wp_redirect ( admin_url ( 'admin.php?page=edit.php?post_type=variation-page' ) );
	exit ();
}
add_action ( 'admin_action_mo_pause_variation', 'mo_pause_variation' );
function mo_set_cookie($args) {
	global $post, $variation_post_id;
	$variationMetaDataArr = mo_get_variation_meta_data ( $post->ID );
	$variation_id = get_post_meta ( $variation_post_id, 'mo_variation_id', true ) ? get_post_meta ( $variation_post_id, 'mo_variation_id', true ) : 0;
	if (is_array ( $variationMetaDataArr ) && $_GET ['preview'] != true) {
		echo '<script>
				jQuery(document).ready(function($) {
					function moGetExperimentCookie(){
					var cookies = document.cookie.split(/;\s*/);
					for(var i=0;i < cookies.length;i++){
						var cookie = cookies[i];
						if(cookie.indexOf("mo_experiment_' . $post->ID . '") != -1){
							cookie = cookie.split(\'=\',2);
							console.log(cookie[1]);
							return cookie[1];
						}
					}
					return null;
				}
	
					function setCookie(name, value, days) {
					    if (days) {
					        var date = new Date();
					        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
					        var expires = "; expires=" + date.toGMTString();
					    } else var expires = "";
					    document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
					}
					
				if(moGetExperimentCookie() == null){
					setCookie("mo_experiment_' . $post->ID . '",' . $variation_post_id . ',365);
					var data = {
					action: \'mo_track_unique_pageview\',
					mo_variation_post_id: ' . $variation_post_id . '
					};
	
					// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
					$.post(\'' . admin_url ( 'admin-ajax.php' ) . '\', data, function(response) {
					});
				}
});
				
						</script>';
	}
}
add_action ( 'wp_footer', 'mo_set_cookie' );
function mo_get_experiments_control_post_ids() {
	global $wpdb;
	$experimentControls = $wpdb->get_results ( "SELECT meta_value from wp_postmeta WHERE meta_key = 'mo_variation_parent' AND meta_value <> '' GROUP BY meta_value", ARRAY_A );
	$controlPostIds = array ();
	foreach ( $experimentControls as $v ) {
		$controlPostIds [] = $v ['meta_value'];
	}
	if (! empty ( $controlPostIds )) {
		return $controlPostIds;
	} else {
		return false;
	}
}
add_action ( 'wp', 'mo_get_experiments_control_post_ids' );
function mo_promote_variation_by_perfomance() {
	$controlPostIds = mo_get_experiments_control_post_ids ();
	if (is_array ( $controlPostIds )) {
		foreach ( $controlPostIds as $v ) {
			$variationMetaDataArr [$v] = get_post_meta ( $v );
			$variationMetaDataArr = mo_get_variation_meta_data ( $v );
			$conversionRatesArr = array ();
			foreach ( $variationMetaDataArr as $k => $v ) {
				$visitors = $v ['mo_unique_page_views_count'] [0] ? $v ['mo_unique_page_views_count'] [0] : 0;
				$conversions = $v ['mo_conversion_count'] [0] ? $v ['mo_conversion_count'] [0] : 0;
				$conversion_rate = ( float ) $conversions && ( float ) $visitors ? (( float ) $conversions / ( float ) $visitors) : 0;
				$conversionRatesArr [$k] = number_format ( $conversion_rate, 2 );
			}
			arsort ( $conversionRatesArr );
			reset ( $conversionRatesArr );
			if (! in_array ( key ( $conversionRatesArr ), $controlPostIds )) {
				mo_promote_variation ( key ( $conversionRatesArr ) );
			} else {
				continue;
			}
		}
	}
}
add_action ( 'mo_promote_variation_by_perfomance', 'mo_promote_variation_by_perfomance' );
function mo_create_experiment() {
	if (isset ( $_POST ['mo_variation_parent'] ) && $_POST ['mo_variation_parent']) {
		$prefix = 'Copy ';
		$parentPostId = $_POST ['mo_variation_parent'];
		$parentPostObj = get_post ( $parentPostId );
		$parentPostMetaDataArr = get_post_meta ( $parentPostId );
		$newVariationPage = array (
				'menu_order' => $parentPostObj->menu_order,
				'comment_status' => $parentPostObj->comment_status,
				'ping_status' => $parentPostObj->ping_status,
				'post_author' => $parentPostObj->post_author,
				'post_content' => $parentPostObj->post_content,
				'post_excerpt' => $parentPostObj->post_excerpt ? $parentPostObj->post_excerpt : "",
				'post_mime_type' => $parentPostObj->post_mime_type,
				'post_parent' => $parentPostObj->post_parent ? $parentPostObj->post_parent : 0,
				'post_password' => $parentPostObj->post_parent->post_password,
				'post_status' => 'variation',
				'post_title' => $parentPostObj->post_title,
				'post_type' => 'variation-page' 
		);
		if (mo_get_variation_count ( $parentPostId ) == 0) {
			$controlVariationPostId = wp_insert_post ( $newVariationPage );
		}
		$newVariationPostId = wp_insert_post ( $newVariationPage );
		
		foreach ( $parentPostMetaDataArr as $k => $v ) {
			if ($k != 'mo_variation_id' && $k != 'mo_unique_page_views_count' && $k != 'mo_conversion_count' && $k != 'mo_page_views_count' && $k != 'mo_variation_name') {
				update_post_meta ( $newVariationPostId, $k, $v [0] );
				if (mo_get_variation_count ( $parentPostId ) == 0) {
					update_post_meta ( $controlVariationPostId, $k, $v [0] );
				}
			}
			if ($k == 'mo_variation_name') {
				if (mo_get_variation_count ( $parentPostId ) == 0) {
					update_post_meta ( $controlVariationPostId, 'mo_variation_name', 'Original Control' );
				}
				update_post_meta ( $newVariationPostId, 'mo_variation_name', $prefix . ' Original Control' );
			}
		}
		if (mo_get_variation_count ( $parentPostId ) == 0) {
			update_post_meta ( $controlVariationPostId, 'mo_variation_parent', $parentPostId );
		}
		update_post_meta ( $newVariationPostId, 'mo_variation_parent', $parentPostId );
	}
	wp_redirect ( admin_url ( 'admin.php?page=edit.php?post_type=variation-page' ) );
	exit ();
}
add_action ( 'admin_action_mo_create_experiment', 'mo_create_experiment' );
function mo_get_variation_count($post_id) {
	global $wpdb;
	if ($post_id) {
		return ( int ) $wpdb->get_var ( "SELECT COUNT(*) from wp_postmeta WHERE meta_key = 'mo_variation_parent' AND meta_value = $post_id" );
	}
}
function mo_duplicate_variation() {
	if (isset ( $_GET ['post_id'] ) && $_GET ['post_id']) {
		$prefix = 'Copy ';
		$originalPostId = $_GET ['post_id'];
		$originalPostObj = get_post ( $originalPostId );
		$originalPostMetaDataArr = get_post_meta ( $originalPostId );
		$newVariationPage = array (
				'menu_order' => $originalPostObj->menu_order,
				'comment_status' => $originalPostObj->comment_status,
				'ping_status' => $originalPostObj->ping_status,
				'post_author' => $originalPostObj->post_author,
				'post_content' => $originalPostObj->post_content,
				'post_excerpt' => $originalPostObj->post_excerpt ? $originalPostObj->post_excerpt : "",
				'post_mime_type' => $originalPostObj->post_mime_type,
				'post_parent' => $originalPostObj->post_parent ? $originalPostObj->post_parent : 0,
				'post_password' => $originalPostObj->post_parent->post_password,
				'post_status' => 'variation',
				'post_title' => $originalPostObj->post_title,
				'post_type' => 'variation-page' 
		);
		$newVariationPostId = wp_insert_post ( $newVariationPage );
		foreach ( $originalPostMetaDataArr as $k => $v ) {
			if ($k != 'mo_variation_id' && $k != 'mo_unique_page_views_count' && $k != 'mo_conversion_count' && $k != 'mo_page_views_count') {
				update_post_meta ( $newVariationPostId, $k, $v [0] );
			}
		}
		if (! get_post_meta ( $originalPostId, 'mo_variation_parent', true )) {
			update_post_meta ( $newVariationPostId, 'mo_variation_parent', $originalPostId );
		}
	}
	wp_redirect ( admin_url ( 'admin.php?page=edit.php?post_type=variation-page' ) );
	exit ();
}
add_action ( 'admin_action_mo_duplicate_variation', 'mo_duplicate_variation' );
function mo_get_conversion_rate($visitors, $conversions) {
	$c = ( float ) $conversions > 0 ? 'true' : 'false';
	$v = ( float ) $visitors > 0 ? 'true' : 'false';
	if ((( float ) $visitors > 0) && (( float ) $conversions > 0)) {
		return (( float ) $conversions / ( float ) $visitors);
	} else {
		return 0;
	}
}
function mo_get_zscore($c, $t) {
	if ($t ['visitors'] && $c ['visitors'] && $t ['conversion_rate'] && $c ['conversion_rate']) {
		$z = $t ['conversion_rate'] - $c ['conversion_rate'];
		$s = ($t ['conversion_rate'] * (1 - $t ['conversion_rate'])) / $t ['visitors'] + ($c ['conversion_rate'] * (1 - $c ['conversion_rate'])) / $c ['visitors'];
		return $z / sqrt ( $s );
	} else {
		return 0;
	}
}
function mo_get_cumnormdist($x) {
	$b1 = 0.319381530;
	$b2 = - 0.356563782;
	$b3 = 1.781477937;
	$b4 = - 1.821255978;
	$b5 = 1.330274429;
	$p = 0.2316419;
	$c = 0.39894228;
	
	if ($x >= 0.0) {
		$t = 1.0 / (1.0 + $p * $x);
		return (1.0 - $c * exp ( - $x * $x / 2.0 ) * $t * ($t * ($t * ($t * ($t * $b5 + $b4) + $b3) + $b2) + $b1));
	} else {
		$t = 1.0 / (1.0 - $p * $x);
		return ($c * exp ( - $x * $x / 2.0 ) * $t * ($t * ($t * ($t * ($t * $b5 + $b4) + $b3) + $b2) + $b1));
	}
}
function mo_admin_bar_render() {
	global $wp_admin_bar;
	$wp_admin_bar->add_menu ( array (
			'parent' => false, // use 'false' for a root menu, or pass the ID of the parent menu
			'id' => 'experiments', // link ID, defaults to a sanitized title value
			'title' => __ ( 'Experiments' ), // link title
			'href' => admin_url ( 'admin.php?page=edit.php?post_type=variation-page' ), // name of file
			'meta' => false  // array of any of the following options: array( 'html' => '', 'class' => '', 'onclick' => '', target => '', title => '' );
		) );
}
add_action ( 'wp_before_admin_bar_render', 'mo_admin_bar_render' );