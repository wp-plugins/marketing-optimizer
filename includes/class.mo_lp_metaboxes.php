<?php
class mo_lp_metaboxes {

	public function __construct() {
		add_action ( 'edit_form_after_title', array (
				$this,
				'mo_lp_ab_testing_add_tabs' 
		), 5 );
		add_action ( 'edit_form_after_title', array (
				$this,
				'mo_lp_add_description_input_box' 
		) );
		add_action ( 'in_admin_footer', array (
				$this,
				'mo_lp_add_template_dialog_box' 
		) );
		add_action ( 'save_post', array (
				$this,
				'mo_lp_save_meta' 
		) );
		add_action ( 'add_meta_boxes_mo_landing_page', array (
				$this,
				'mo_lp_display_meta_boxes' 
		), 10, 2 );
		add_filter ( 'content_save_pre', array (
				$this,
				'mo_lp_content_save_pre' 
		) );
		add_filter ( 'title_save_pre', array (
				$this,
				'mo_lp_title_save_pre' 
		) );
		add_action ( 'admin_notices', array (
				$this,
				'mo_lp_display_meta_box_select_template_container' 
		) );
	}

	function mo_lp_ab_testing_add_tabs() {
		global $post;
		$post_type_is = get_post_type ( $post->ID );
		$permalink = get_permalink ( $post->ID );
		// Only show lp tabs on landing pages post types (for now)
		if ($post_type_is === "mo_landing_page") {
			$current_variation_id = mo_landing_pages::instance ( $post->ID )->get_current_variation ();
			if (isset ( $_GET ['new_meta_key'] ))
				$current_variation_id = $_GET ['new_meta_key'];
			echo "<input type='hidden' name='mo_lp_open_variation' id='mo_lp_open_variation' value='{$current_variation_id}'>";
			
			$variations = get_post_meta ( $post->ID, 'mo_lp_variations', true );
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
			echo '<a href="?post=' . $post->ID . '&mo_lp_variation_id=0&action=edit" class="lp-ab-tab nav-tab nav-tab-' . $first_class . '" id="tabs-0">A</a>';
			
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
					echo '<a href="?post=' . $post->ID . '&mo_lp_variation_id=' . $vid . '&action=edit" class="lp-nav-tab nav-tab nav-tab-' . $cur_class . '" id="tabs-add-variation">' . $letter . '</a>';
				}
			}
			
			if (! isset ( $_GET ['new-variation'] )) {
				echo '<a href="?post=' . $post->ID . '&mo_lp_variation_id=' . $new_variation_id . '&action=edit&new-variation=1" class="lp-nav-tab nav-tab nav-tab-inactive nav-tab-add-new-variation" id="tabs-add-variation">Add New Variation</a>';
			} else {
				$variation_count = count ( $array_variations );
				$letter = mo_lp_ab_key_to_letter ( $variation_count );
				echo '<a href="?post=' . $post->ID . '&mo_lp_variation_id=' . $new_variation_id . '&action=edit" class="lp-nav-tab nav-tab nav-tab-active" id="tabs-add-variation">' . $letter . '</a>';
			}
			$edit_link = (isset ( $_GET ['mo_lp_variation_id'] )) ? '?mo_lp_variation_id=' . $_GET ['mo_lp_variation_id'] . '' : '?mo_lp_variation_id=0';
			$post_link = get_permalink ( $post->ID );
			$post_link = preg_replace ( '/\?.*/', '', $post_link );
			// echo "<a rel='".$post_link."' id='launch-visual-editer' class='button-primary new-save-lp-frontend' href='$post_link$edit_link&template-customize=on'>Launch Visual Editor</a>";
			echo '</h2>';
		}
	}

	function mo_lp_add_description_input_box($post) {
		if ($post->post_type == 'mo_landing_page') {
			$mo_lp_obj = mo_landing_pages::instance ( $post->ID );
			$v_id = $mo_lp_obj->get_current_variation ();
			$mo_lp_description = $mo_lp_obj->get_variation_property ( $v_id, 'description' );
			echo "<div id='mo_lp_description_div'><div id='description_wrap'><input placeholder='" . __ ( 'Add Description for this variation.', mo_plugin::MO_LP_TEXT_DOMAIN ) . "' type='text' class='description' name='description' id='description' value='{$mo_lp_description}' style='width:100%;line-height:1.7em'></div></div>";
		}
	}

	function mo_lp_display_meta_boxes($post) {
		add_meta_box ( 'mo_lp_templates', 'Current Selected Template', array (
				$this,
				'mo_lp_get_template_selected_metabox' 
		), 'mo_landing_page', 'side', 'high' );
		
		add_meta_box ( 'mo_lp_variation_stats', __ ( 'Variation Stats' ), array (
				$this,
				'mo_lp_display_meta_box_variation_stats' 
		), 'mo_landing_page', 'side', 'high' );
		
		add_meta_box ( 'mo_lp_variation_id', __ ( 'Marketing Optimizer Variation Id' ), array (
				$this,
				'mo_lp_variation_id_metabox' 
		), 'mo_landing_page', 'side', 'high' );
	}

	function mo_lp_display_meta_box_variation_stats($post) {
		if ('mo_landing_page' == $post->post_type) {
			$mo_lp_obj = mo_landing_pages::instance ( $post->ID );
			$mo_lp_variation_ids_arr = $mo_lp_obj->get_variation_ids_arr ();
			echo '<table class="mo_meta_box_stats_table">
							  <tr class="mo_lp_stats_header_row">
							    <th class="mo_stats_header_cell">ID</th>
							    <th class="mo_stats_header_cell">Imp</th>
							    <th class="mo_stats_header_cell">Visits</th>
							    <th class="mo_stats_header_cell">Conv</th>
							    <th class="mo_stats_header_cell">CR%</th>
							    <th class="mo_stats_header_cell">Status</th>
							  </tr>';
			foreach ( $mo_lp_variation_ids_arr as $v ) {
				$letter = mo_lp_ab_key_to_letter ( $v );
				$impressions = $mo_lp_obj->get_variation_property ( $v, 'impressions' ) ? $mo_lp_obj->get_variation_property ( $v, 'impressions' ) : 0;
				$visits = $mo_lp_obj->get_variation_property ( $v, 'visitors' ) ? $mo_lp_obj->get_variation_property ( $v, 'visitors' ) : 0;
				$conversions = $mo_lp_obj->get_variation_property ( $v, 'conversions' ) ? $mo_lp_obj->get_variation_property ( $v, 'conversions' ) : 0;
				$conversion_rate = $mo_lp_obj->get_variation_property ( $v, 'conversion_rate' ) ? number_format ( $mo_lp_obj->get_variation_property ( $v, 'conversion_rate' ), 1 ) * 100 : 0;
				$status = $mo_lp_obj->get_variation_property ( $v, 'status' );
				$status_text = $status ? 'pause' : 'unpause';
				
				echo '<tr>';
				echo '<td class="mo_stats_cell"><a title="click to edit this variation" href="/wp-admin/post.php?post=' . $post->ID . '&mo_lp_variation_id=' . $v . '&action=edit">' . $letter . '</a> </td>';
				echo '<td class="mo_stats_cell">' . $impressions . '</td>';
				echo '<td class="mo_stats_cell">' . $visits . '</td>';
				echo '<td class="mo_stats_cell">' . $conversions . '</td>';
				echo '<td class="mo_stats_cell">' . $conversion_rate . '%</td>';
				echo '<td class="mo_stats_cell">' . sprintf ( '<a href="admin.php?action=%s&post=%s&v_id=%s">' . $status_text . '</a>', 'mo_pause_variation', $post->ID, $v ) . '</td>';
				echo '</tr>';
			}
			echo '</table>';
		}
	}

	function mo_lp_get_template_selected_metabox($post) {
		$mo_lp_obj = mo_landing_pages::instance ( $post->ID );
		$v_id = $mo_lp_obj->get_current_variation ();
		$template = $mo_lp_obj->get_variation_property ( $v_id, 'template' ) ? $mo_lp_obj->get_variation_property ( $v_id, 'template' ) : 'theme';
		$templates_arr = mo_lp_get_templates ();
		$template_name = $templates_arr [$template] ['title'];
		if ($template == 'theme') {
			$theme_template = $mo_lp_obj->get_variation_property ( $v_id, 'theme_template' );
			$template_dir = get_template_directory_uri ();
		} else {
			$template_dir = '/' . PLUGINDIR . '/' . mo_plugin::MO_DIRECTORY . '/templates/' . $template;
		}
		// Add an nonce field so we can check for it later.
		wp_nonce_field ( 'mo_get_template_selected_metabox', 'mo_get_template_selected_metabox_nonce' );
		echo '<div id="mo_templates" class="postbox">
				<h3 class="hndle">Template: 
					<span id="mo_template_name">' . $template_name . '</span>
				</h3>
				<div id="mo_template_image_container">
					<span id="mo_template_image">
							<img height="200" width="200" src="' . $template_dir . '/screenshot.png" id="c_temp">
									</span></div>
									<div id="mo_current_template">
										<input type="hidden" name="mo_template" value="' . $template . '">
									</div>';
		
		echo '<div id="mo_theme_template" style="margin-top:10px;">
					<label  for="theme_template" style="font-weight:bold;margin-bottom:10px;">Theme Template</label>
					<select name="theme_template" id="theme_template">
	<option value="default">';
		_e ( 'Default Template' );
		echo '</option>';
		page_template_dropdown ( $theme_template );
		echo '</select></div>';
		
		echo '<div id="mo_template_change">
												<h2><a class="button" id="mo-change-template-button">Choose Another Template</a></h2>
											</div>
				</div>';
	}

	function mo_lp_post_template_meta_box($post) {
		if ('mo_landing_page' == $post->post_type && 0 != count ( get_page_templates () )) {
			$mo_lp_obj = mo_landing_pages::instance ( $post->ID );
			$v_id = $mo_lp_obj->get_current_variation ();
			$template = $mo_lp_obj->get_variation_property ( $v_id, 'template' ) ? $mo_lp_obj->get_variation_property ( $v_id, 'template' ) : 'default';
			?>
<label class="screen-reader-text" for="post_template"><?php _e('Post Template') ?></label>
<select name="template" id="post_template">
	<option value='default'><?php _e('Default Template'); ?></option>
		<?php page_template_dropdown($template); ?>
		</select>
<?php
		}
		?>
		<?php
	}

	function mo_lp_save_meta($post_id) {
		global $post;
		
		if (! isset ( $post ))
			return;
		
		if ($post->post_type == 'revision') {
			return;
		}
		
		if ((defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) || (isset ( $_POST ['post_type'] ) && $_POST ['post_type'] == 'revision')) {
			return;
		}
		
		if ($post->post_type == 'mo_landing_page') {
			
			$mo_lp_obj = mo_landing_pages::instance ( $post_id );
			$v_id = $mo_lp_obj->get_current_variation ();
			$variation_ids_arr = $mo_lp_obj->get_variation_ids_arr ();
			if (! in_array ( $v_id, $variation_ids_arr ) && ! is_null ( $v_id )) {
				$variation_ids_arr [$v_id] = $v_id;
				$mo_lp_obj->set_variation_ids_arr ( $variation_ids_arr );
				$mo_lp_obj->save ();
				$mo_lp_obj->set_variations_arr ( $mo_lp_obj->get_variation_ids_arr () );
			}
			foreach ( $_POST as $k => $v ) {
				if ($k == 'post_title') {
					$k = 'title';
				}
				if ($k == 'mo_template') {
					$k = 'template';
				}
				if (property_exists ( 'mo_variation', $k )) {
					$mo_lp_obj->set_variation_property ( $v_id, $k, $v );
				}
			}
			$mo_lp_obj->save ();
			// save taxonomies
			$post = get_post ( $post_id );
		}
	}

	public function mo_lp_variation_id_metabox($post) {
		if ($post->post_type == 'mo_landing_page') {
			$mo_lp_obj = mo_landing_pages::instance ( $post->ID );
			$v_id = $mo_lp_obj->get_current_variation ();
			$mo_lp_variation_id = $mo_lp_obj->get_variation_property ( $v_id, 'variation_id' );
			echo "<div id='mo_lp_variation_id_div'><div id='variation_id_wrap'><input placeholder='" . __ ( 'Add the marketing optimizer variation id.', mo_plugin::MO_LP_TEXT_DOMAIN ) . "' type='text' class='variation_id' name='variation_id' id='variation_id' value='{$mo_lp_variation_id}' style='width:100%;line-height:1.7em'></div></div>";
		}
	}

	public function mo_lp_content_save_pre($content) {
		global $post;
		if ($post && $post->post_type == 'mo_landing_page') {
			$mo_lp_obj = mo_landing_pages::instance ( $post->ID );
			$v_id = $mo_lp_obj->get_current_variation ();
			if (( int ) $v_id !== 0) {
				$content = $post->post_content;
			}
		}
		return $content;
	}

	public function mo_lp_title_save_pre($title) {
		global $post;
		if ($post && $post->post_type == 'mo_landing_page') {
			$mo_lp_obj = mo_landing_pages::instance ( $post->ID );
			$v_id = $mo_lp_obj->get_current_variation ();
			if (( int ) $v_id !== 0) {
				$title = $post->post_title;
			}
		}
		return $title;
	}
	
	// Render select template box
	function mo_lp_display_meta_box_select_template_container() {
		global $post;
		
		$current_url = "http://" . $_SERVER ["HTTP_HOST"] . $_SERVER ["REQUEST_URI"] . "";
		
		if (isset ( $post ) && $post->post_type != 'mo_landing_page' || ! isset ( $post )) {
			return false;
		}
		
		(! strstr ( $current_url, 'post-new.php' )) ? $toggle = "display:none" : $toggle = "";
		
		$uploads = wp_upload_dir ();
		$uploads_path = $uploads ['basedir'];
		$extended_path = $uploads_path . '/mo_lp_landing_pages/templates/';
		
		$template = get_post_meta ( $post->ID, 'lp-selected-template', true );
		$template = apply_filters ( 'lp_selected_template', $template );
		echo '<div id="mo_template_select_container" style="' . $toggle . '">
<div class="mo_template_select_heading"><h1>Select Your Landing Page Template</h1></div>
';
		echo '<ul id="Grid" style=" ">';
		foreach ( mo_lp_get_templates () as $k => $v ) {
			// echo '<li class="mix category_1 mix_all" data-cat="1" style=" display: inline-block; opacity: 1;"><span style=""><a href="#" id="defualt" class="mo_lp_template_select">Select</a></span> | <span style=""><a href="#">Preview</a></span></li>';
			echo '<li class="mix category_1 mix_all" data-cat="1" style=" display: inline-block; opacity: 1;"><div>' . $v ['title'] . '</div><a href="#" label="' . $k . '" id="' . $k . '" class="mo_template_select"><img class="mo_template_thumbnail" width="200" height="200" src="' . $v ['thumbnail'] . '" /></a><span style=""><a href="#" label="' . $k . '" id="' . $k . '" class="mo_template_select">Select</a></span> </li>';
		}
		
		echo '<li class="gap"></li> <!-- "gap" elements fill in the gaps in justified grid -->
</ul></div>';
	}

	function mo_lp_add_template_dialog_box() {
		global $post;
		if (isset ( $post ) && $post->post_type == 'mo_landing_page') {
			echo '<div id="dialog-confirm" title="Change Template" style="display:none;">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Changing the template will replace the current content with the new template. Are you sure you want to do this?</p>
</div>';
		}
	}
}
$mo_lp_metaboxes_obj = new mo_lp_metaboxes ();