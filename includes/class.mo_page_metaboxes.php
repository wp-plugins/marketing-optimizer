<?php
class mo_page_metaboxes {
	public function __construct() {
		add_action ( 'edit_form_after_title', array (
				$this,
				'mo_page_ab_testing_add_tabs' 
		), 5 );
		add_action ( 'edit_form_after_title', array (
				$this,
				'mo_page_add_description_input_box' 
		) );
		add_action ( 'add_meta_boxes_page', array (
				$this,
				'mo_page_display_meta_boxes' 
		), 10, 2 );
		add_action ( 'save_post', array (
				$this,
				'mo_page_ab_testing_save_post' 
		) );
		add_filter ( 'content_save_pre', array (
				$this,
				'mo_page_content_save_pre' 
		) );
		add_filter ( 'title_save_pre', array (
				$this,
				'mo_page_title_save_pre' 
		) );
	}
	function mo_page_ab_testing_add_tabs() {
		global $post;
		$post_type_is = get_post_type ( $post->ID );
		$permalink = get_permalink ( $post->ID );
		// Only show lp tabs on landing pages post types (for now)
		if ($post_type_is === "page") {
			$current_variation_id = mo_pages::instance ( $post->ID )->get_current_variation ();
			if (isset ( $_GET ['new_meta_key'] ))
				$current_variation_id = $_GET ['new_meta_key'];
			echo "<input type='hidden' name='mo_page_open_variation' id='mo_page_open_variation' value='{$current_variation_id}'>";
			
			$variations = get_post_meta ( $post->ID, 'mo_page_variations', true );
			$array_variations = explode ( ',', $variations );
			$variations = array_filter ( $array_variations, 'is_numeric' );
			sort ( $array_variations, SORT_NUMERIC );
			
			$lid = end ( $array_variations );
			$new_variation_id = $lid + 1;
			
			if ($current_variation_id > 0 || isset ( $_GET ['new-variation'] )) {
				$first_class = 'inactive';
			} else {
				$first_class = 'active';
			}
			
			echo '<h2 class="nav-tab-wrapper a_b_tabs">';
			echo '<a href="?post=' . $post->ID . '&mo_page_variation_id=0&action=edit" class="lp-ab-tab nav-tab nav-tab-' . $first_class . '" id="tabs-0">A</a>';
			
			$var_id_marker = 1;
			
			foreach ( $array_variations as $i => $vid ) {
				
				if ($vid != 0) {
					$letter = mo_lp_ab_key_to_letter ( $vid );
					
					// alert (variation.new_variation);
					if ($current_variation_id == $vid && ! isset ( $_GET ['new-variation'] )) {
						$cur_class = 'active';
					} else {
						$cur_class = 'inactive';
					}
					echo '<a href="?post=' . $post->ID . '&mo_page_variation_id=' . $vid . '&action=edit" class="lp-nav-tab nav-tab nav-tab-' . $cur_class . '" id="tabs-add-variation">' . $letter . '</a>';
				}
			}
			
			if (! isset ( $_GET ['new-variation'] )) {
				echo '<a href="?post=' . $post->ID . '&mo_page_variation_id=' . $new_variation_id . '&action=edit&new-variation=1" class="lp-nav-tab nav-tab nav-tab-inactive nav-tab-add-new-variation" id="tabs-add-variation">Add New Variation</a>';
			} else {
				$variation_count = count ( $array_variations );
				$letter = mo_lp_ab_key_to_letter ( $variation_count );
				echo '<a href="?post=' . $post->ID . '&mo_page_variation_id=' . $new_variation_id . '&action=edit" class="lp-nav-tab nav-tab nav-tab-active" id="tabs-add-variation">' . $letter . '</a>';
			}
			$edit_link = (isset ( $_GET ['mo_page_variation_id'] )) ? '?mo_page_variation_id=' . $_GET ['mo_page_variation_id'] . '' : '?mo_page_variation_id=0';
			$post_link = get_permalink ( $post->ID );
			$post_link = preg_replace ( '/\?.*/', '', $post_link );
			// echo "<a rel='".$post_link."' id='launch-visual-editer' class='button-primary new-save-lp-frontend' href='$post_link$edit_link&template-customize=on'>Launch Visual Editor</a>";
			echo '</h2>';
		}
	}
	function mo_page_add_description_input_box($post) {
		if ($post->post_type == 'page') {
			$mo_page_obj = mo_pages::instance ( $post->ID );
			$v_id = $mo_page_obj->get_current_variation ();
			if (( int ) $v_id !== 0) {
				$mo_page_description = $mo_page_obj->get_variation_property ( $v_id, 'description' );
				echo "<div id='mo_lp_description_div'><div id='description_wrap'><input placeholder='" . __ ( 'Add Description for this variation.', mo_landing_pages_plugin::MO_LP_TEXT_DOMAIN ) . "' type='text' class='description' name='description' id='description' value='{$mo_page_description}' style='width:100%;line-height:1.7em'></div></div>";
			}
		}
	}
	public function mo_page_display_meta_boxes($post) {
		add_meta_box ( 'mo_page_variation_stats', __ ( 'Variation Stats' ), array (
				$this,
				'mo_page_display_meta_box_variation_stats' 
		), 'page', 'side', 'high' );
		add_meta_box ( 'mo_page_variation_id', __ ( 'Marketing Optimizer Variation Id' ), array (
				$this,
				'mo_page_variation_id_metabox' 
		), 'page', 'side', 'high' );
	}
	public function mo_page_variation_id_metabox($post) {
		if ($post->post_type == 'page') {
			$mo_page_obj = mo_pages::instance ( $post->ID );
			$v_id = $mo_page_obj->get_current_variation ();
			$mo_page_variation_id = $mo_page_obj->get_variation_property ( $v_id, 'variation_id' );
			echo "<div id='mo_lp_variation_id_div'><div id='variation_id_wrap'><input placeholder='" . __ ( 'Add the marketing optimizer variation id.', mo_landing_pages_plugin::MO_LP_TEXT_DOMAIN ) . "' type='text' class='variation_id' name='variation_id' id='variation_id' value='{$mo_page_variation_id}' style='width:100%;line-height:1.7em'></div></div>";
		}
	}
	function mo_page_display_meta_box_variation_stats($post) {
		if ('page' == $post->post_type) {
			$mo_page_obj = mo_pages::instance ( $post->ID );
			$v_id = $mo_page_obj->get_current_variation ();
			$letter = mo_lp_ab_key_to_letter ( $v_id );
			$impressions = $mo_page_obj->get_variation_property ( $v_id, 'impressions' ) ? $mo_page_obj->get_variation_property ( $v_id, 'impressions' ) : 0;
			$visits = $mo_page_obj->get_variation_property ( $v_id, 'visitors' ) ? $mo_page_obj->get_variation_property ( $v_id, 'visitors' ) : 0;
			$conversions = $mo_page_obj->get_variation_property ( $v_id, 'conversions' ) ? $mo_page_obj->get_variation_property ( $v_id, 'conversions' ) : 0;
			$conversion_rate = $mo_page_obj->get_variation_property ( $v_id, 'conversion_rate' ) ? (number_format ( $mo_page_obj->get_variation_property ( $v_id, 'conversion_rate' ), 1 ) * 100) : 0;
			$status = $mo_page_obj->get_variation_property ( $v_id, 'status' );
			$status_text = $status ? 'pause' : 'unpause';
			echo '<table class="mo_lp_meta_box_stats_table">
							  <tr class="mo_lp_stats_header_row">
							    <th class="mo_lp_stats_header_cell">ID</th>
							    <th class="mo_lp_stats_header_cell">Imp</th>
							    <th class="mo_lp_stats_header_cell">Visits</th>
							    <th class="mo_lp_stats_header_cell">Conv</th>
							    <th class="mo_lp_stats_header_cell">CR%</th>
							    <th class="mo_lp_stats_header_cell">Status</th>
							  </tr>';
			echo '<tr>';
			echo '<td class="mo_lp_stats_cell"><a title="click to edit this variation" href="/wp-admin/post.php?post=' . $post->ID . '&mo_page_variation_id=' . $v_id . '&action=edit">' . $letter . '</a> </td>';
			echo '<td class="mo_lp_stats_cell">' . $impressions . '</td>';
			echo '<td class="mo_lp_stats_cell">' . $visits . '</td>';
			echo '<td class="mo_lp_stats_cell">' . $conversions . '</td>';
			echo '<td class="mo_lp_stats_cell">' . $conversion_rate . '%</td>';
			echo '<td class="mo_lp_stats_cell">' . sprintf ( '<a href="admin.php?action=%s&post=%s&v_id=%s">' . $status_text . '</a>', 'mo_page_pause_variation', $post->ID, $v_id ) . '</td>';
			echo '</tr>';
			echo '</table>';
		}
	}
	function mo_page_save_meta($post_id) {
		global $post;
		
		if (! isset ( $post ))
			return;
		
		if ($post->post_type == 'revision') {
			return;
		}
		
		if ((defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) || (isset ( $_POST ['post_type'] ) && $_POST ['post_type'] == 'revision')) {
			return;
		}
		
		if ($post->post_type == 'page') {
			
			$mo_page_obj = mo_pages::instance ( $post_id );
			$v_id = $mo_page_obj->get_current_variation ();
			$variation_ids_arr = $mo_page_obj->get_variation_ids_arr ();
			if (! in_array ( $v_id, $variation_ids_arr )) {
				$variation_ids_arr [$v_id] = $v_id;
				$mo_page_obj->set_variation_ids_arr ( $variation_ids_arr );
				$mo_page_obj->save ();
				$mo_page_obj->set_variations_arr ( $mo_page_obj->get_variation_ids_arr () );
			}
			foreach ( $_POST as $k => $v ) {
				if (( int ) $v_id !== 0) {
					if ($k == 'post_title') {
						$k = 'title';
					}
					
					if (property_exists ( 'mo_variation', $k )) {
						$mo_page_obj->set_variation_property ( $v_id, $k, $v );
					}
				}
			}
			$mo_page_obj->save ();
			// save taxonomies
			$post = get_post ( $post_id );
		}
	}
	function mo_page_ab_testing_save_post($postID) {
		global $post;
		
		$var_final = (isset ( $_POST ['mo_page_open_variation'] )) ? $_POST ['mo_page_open_variation'] : '0';
		if (isset ( $_POST ['post_type'] ) && $_POST ['post_type'] == 'page') {
			if (defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || $_POST ['post_type'] == 'revision') {
				return;
			}
			
			if ($parent_id = wp_is_post_revision ( $postID )) {
				$postID = $parent_id;
			}
			
			$this_variation = $var_final;
			
			if (! get_post_meta ( $postID, 'mo_page_status_' . $this_variation, true ) == 1 || ! get_post_meta ( $postID, 'mo_page_status_' . $this_variation, true ) == 0) {
				update_post_meta ( $postID, 'mo_page_status_' . $this_variation, 1 );
			}
			
			// next alter all custom fields to store correct varation and create custom fields for special inputs
			$ignore_list = array (
					'post_status',
					'post_type',
					'tax_input',
					'post_author',
					'user_ID',
					'post_ID',
					'catslist',
					'mo_lp_open_variation',
					'samplepermalinknonce',
					'autosavenonce',
					'action',
					'autosave',
					'mm',
					'jj',
					'aa',
					'hh',
					'mn',
					'ss',
					'_wp_http_referer',
					'mo_lp_variation_id',
					'_wpnonce',
					'originalaction',
					'original_post_status',
					'referredby',
					'_wp_original_http_referer',
					'meta-box-order-nonce',
					'closedpostboxesnonce',
					'hidden_post_status',
					'hidden_post_password',
					'hidden_post_visibility',
					'visibility',
					'post_password',
					'hidden_mm',
					'cur_mm',
					'hidden_jj',
					'cur_jj',
					'hidden_aa',
					'cur_aa',
					'hidden_hh',
					'cur_hh',
					'hidden_mn',
					'cur_mn',
					'original_publish',
					'save',
					'newlanding_page_category',
					'newlanding_page_category_parent',
					'_ajax_nonce-add-landing_page_category',
					'lp_lp_custom_fields_nonce',
					'lp-selected-template',
					'post_mime_type',
					'ID',
					'comment_status',
					'ping_status' 
			);
			
			$mo_page_obj = mo_pages::instance ( $postID );
			$v_id = $mo_page_obj->get_current_variation ();
			$variation_ids_arr = $mo_page_obj->get_variation_ids_arr ();
			if (! in_array ( $v_id, $variation_ids_arr )) {
				$variation_ids_arr [$v_id] = $v_id;
				$mo_page_obj->set_variation_ids_arr ( $variation_ids_arr );
				$mo_page_obj->save ();
				$mo_page_obj->set_variations_arr ( $mo_page_obj->get_variation_ids_arr () );
			}
			foreach ( $_POST as $k => $v ) {
				if (( int ) $v_id !== 0) {
					if ($k == 'post_title') {
						$k = 'title';
					}
				}
				if($k == 'page_template'){
					$k = 'template';
				}
				if (property_exists ( 'mo_variation', $k )) {
					$mo_page_obj->set_variation_property ( $v_id, $k, $v );
				}
			}
			$mo_page_obj->save ();
			// save taxonomies
			if (( int ) $v_id == 0) {
				$post = get_post ( $postID );
			}
		}
	}
	public function mo_page_content_save_pre($content) {
		global $post;
		if ($post && $post->post_type == 'page') {
			$mo_page_obj = mo_pages::instance ( $post->ID );
			$v_id = $mo_page_obj->get_current_variation ();
			if (( int ) $v_id !== 0) {
				$content = $post->post_content;
			}
		}
		return $content;
	}
	public function mo_page_title_save_pre($title) {
		global $post;
		if ($post && $post->post_type == 'page') {
			$mo_page_obj = mo_pages::instance ( $post->ID );
			$v_id = $mo_page_obj->get_current_variation ();
			if (( int ) $v_id !== 0) {
				$title = $post->post_title;
			}
		}
		return $title;
	}
}
$mo_page_metaboxes_obj = new mo_page_metaboxes ();