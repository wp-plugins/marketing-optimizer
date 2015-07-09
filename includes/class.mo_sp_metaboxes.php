<?php
class mo_sp_metaboxes extends mo_metaboxes {
    
	public function __construct() {
                $short_type = 'mo_sp';
                $post_type = 'mo_sp';
                $api_post_type = 'squeeze_page';
                parent::__construct ( $short_type,$post_type,$api_post_type );
                
		add_action ( 'edit_form_after_title', array (
				$this,
				'mo_sp_ab_testing_add_tabs' 
		), 5 );
		add_action ( 'edit_form_after_title', array (
				$this,
				'mo_sp_add_description_input_box' 
		) );
		add_action ( 'in_admin_footer', array (
				$this,
				'mo_sp_add_template_dialog_box' 
		) );
		add_action ( 'save_post', array (
				$this,
				'mo_sp_save_meta' 
		) );
                add_action ( 'redirect_post_location', array (
				$this,
				'variation_redirect_after_save' 
		) );
		add_action ( 'add_meta_boxes_mo_sp', array (
				$this,
				'mo_sp_display_meta_boxes' 
		), 10, 2 );
		add_filter ( 'content_save_pre', array (
				$this,
				'mo_sp_content_save_pre' 
		) );
		add_filter ( 'title_save_pre', array (
				$this,
				'mo_sp_title_save_pre' 
		) );
		add_action ( 'admin_notices', array (
				$this,
				'mo_sp_display_meta_box_select_template_container' 
		) );
	}

	function mo_sp_ab_testing_add_tabs() {
            $this->mo_ab_testing_add_tabs();
        }

	function mo_sp_add_description_input_box($post) {
            $this->mo_add_description_input_box($post);
         }

	function mo_sp_display_meta_boxes($post) {
            $this->mo_display_meta_boxes();
            add_meta_box ( 'mo_sp_get_sp_settings', __ ( 'Marketing Optimizer Pop-Ups Settings' ), array (
                            $this,
                            'mo_sp_get_sp_settings_metabox' 
            ), 'mo_sp', 'normal', 'high' );
	}

	function mo_sp_display_meta_box_variation_stats($post) {
            $this->mo_display_meta_box_variation_stats($post);
	}

	function mo_sp_get_template_selected_metabox($post) {
            $this->mo_get_template_selected_metabox($post);
        }

	function mo_sp_save_meta($post_id) {
		global $post;
		
		if (! isset ( $post ))
			return;
		
		if ($post->post_type == 'revision') {
			return;
		}
		
		if ((defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE) || (isset ( $_POST ['post_type'] ) && $_POST ['post_type'] == 'revision')) {
			return;
		}
		
		if ($post->post_type == 'mo_sp') {
			$mo_sp_obj = mo_squeeze_pages::instance ( $post_id );
			$v_id = $mo_sp_obj->get_current_variation ();
			$variation_ids_arr = $mo_sp_obj->get_variation_ids_arr ();
			if (! in_array ( $v_id, $variation_ids_arr ) && ! is_null ( $v_id )) {
                            $variation_ids_arr [$v_id] = $v_id;
                            $letter = mo_lp_ab_key_to_letter ( $v_id );
                            $mo_sp_obj->set_variation_ids_arr ( $variation_ids_arr );
                            $mo_sp_obj->save ();
                            $mo_sp_obj->set_variations_arr_custom ( $mo_sp_obj->get_variation_ids_arr () );
			}
			
			if (! isset ( $_POST ['mo_sp_post_types'] )) {
                            $_POST ['mo_sp_post_types'] ['lp'] = 0;
                            $_POST ['mo_sp_post_types'] ['posts'] = 0;
                            $_POST ['mo_sp_post_types'] ['pages'] = 0;
                            $_POST ['mo_sp_post_types'] ['cats'] = 0;
                            $_POST ['mo_sp_post_types'] ['tags'] = 0;
			}
			foreach ( $_POST as $k => $v ) {
                            if ($k == 'post_title') {
                                $k = 'title';
                            }
                            if ($k == 'mo_template') {
                                $k = 'template';
                            }
                            if ($k == 'mo_sp_post_types') {
                                $k = 'post_types';
                                $v = json_encode ( $v );
                            }
                            if (property_exists ( 'mo_sp_variation', $k )) {
                                $mo_sp_obj->set_variation_property ( $v_id, $k, $v );
                            }
				
                            //----------------------------start create variation in api --------------------
				if($k == 'variation_id'){
                                    if($v == ""){
                                            $new_variation_name = $_POST['post_title'] ." - ".$letter; 
                                            $postvar = array(
                                                    'variation_name' => $new_variation_name,
                                                    'description' => $_POST['description']
                                            );
                                        if(count($variation_ids_arr)>1) {        
                                            $v = $this->mo_get_tests_from_api_new_variation($postvar);
                                            $mo_sp_obj->set_variation_property ( $v_id, $k, $v );
                                        }
                                    }
                                    else{

                                            $new_variation_name = $_POST['post_title'] ." - ".$letter; 
                                            $postvar = array(
                                                    'variation_name' => $new_variation_name,
                                                    'description'	 => $_POST['description'],
                                                    'id'			 => $v
                                            );
                                        if(count($variation_ids_arr)>1) {    
                                            $v = $this->mo_get_tests_from_api_update($postvar);
                                            $mo_sp_obj->set_variation_property ( $v_id, $k, $v );
                                        }    
                                    }
				}
				//----------------------------end create variation in api --------------------
			}
                        
                        $mo_sp_obj->save ();
			// save taxonomies
                        $post = get_post ( $post_id );
		}
	}
        
        public function variation_redirect_after_save(){
            $this->mo_redirect_post_variation();
        }
	
        public function mo_sp_variation_id_metabox($post) {
            $this->mo_variation_id_metabox($post);
	}

	public function mo_sp_content_save_pre($content) {
		global $post;
		if ($post && $post->post_type == 'mo_sp') {
                    $mo_sp_obj = mo_squeeze_pages::instance ( $post->ID );
                    $v_id = $mo_sp_obj->get_current_variation ();
                    if (( int ) $v_id !== 0) {
                            $content = $post->post_content;
                    }
		}
		return $content;
	}

	public function mo_sp_title_save_pre($title) {
		global $post;
		if ($post && $post->post_type == 'mo_sp') {
                    $mo_sp_obj = mo_squeeze_pages::instance ( $post->ID );
                    $v_id = $mo_sp_obj->get_current_variation ();
                    if (( int ) $v_id !== 0) {
                            $title = $post->post_title;
                    }
		}
		return $title;
	}
	
	function mo_sp_display_meta_box_select_template_container() {
            $this->mo_display_meta_box_select_template_container();
	}

	function mo_sp_add_template_dialog_box() {
            $this->mo_add_template_dialog_box();
	}

	function mo_sp_get_sp_settings_metabox() {
		global $post;
		if ($post->post_type == 'mo_sp') {
			$mo_sp_obj = mo_squeeze_pages::instance ( $post->ID );
                        $v_id = $mo_sp_obj->get_current_variation ();
			$post_types =  json_decode ( $mo_sp_obj->get_variation_property ( $v_id, 'post_types' ), true ) ? json_decode ( $mo_sp_obj->get_variation_property ( $v_id, 'post_types' ), true ) : json_decode ( $mo_sp_obj->get_variation_property ( 0, 'post_types' ), true );
			$post_type_lp = (isset ( $post_types ['lp'] ) && $post_types ['lp']) ? 'checked' : '';
			$post_type_posts = (isset ( $post_types ['posts'] ) && $post_types ['posts']) ? 'checked' : '';
			$post_type_pages = (isset ( $post_types ['pages'] ) && $post_types ['pages']) ? 'checked' : '';
			$post_type_cats = (isset ( $post_types ['cats'] ) && $post_types ['cats']) ? 'checked' : '';
			$post_type_tags = (isset ( $post_types ['tags'] ) && $post_types ['tags']) ? 'checked' : '';
			echo '<div id="mo_sp_settings_container" style="overflow:hidden;">
				<ul>
                                    <li><div class="mo_sp_settings_label" style="width:30%;float:left;"><label for="mo_sp_post_type">Display for post types:</label></div><div class="mo_sp_settings_field" style="width:70%;float:left;"><input type="checkbox" name="mo_sp_post_types[lp]" value="1" ' . $post_type_lp . ' /><label>Landing Pages</label><input type="checkbox" name="mo_sp_post_types[posts]"    value="1" ' . $post_type_posts . ' /><label>Posts</label><input type="checkbox" name="mo_sp_post_types[pages]" value="1" ' . $post_type_pages . ' /><label>Pages</label><input type="checkbox" name="mo_sp_post_types[cats]" value="1" ' . $post_type_cats . ' /><label>Categories</label><input type="checkbox" name="mo_sp_post_types[tags]" value="1" ' . $post_type_tags . '  /><label>Tags</label></div></li>
                                    <li><div class="mo_sp_settings_label" style="width:30%;float:left;"><label for="mo_sp_modal_size">Modal Size (px):</label></div><div class="mo_sp_settings_field" style="width:70%;float:left;"><input type="text" name="modal_height" value="' . $mo_sp_obj->get_variation_property ( $v_id, "modal_height" ) . '"/><label> Height</label><input type="text" name="modal_width" value="'.$mo_sp_obj->get_variation_property ( $v_id, "modal_width" ).'" /><label> Width</label></div></li>
				</ul>
                              </div>';
		}
	}
}
$mo_sp_metaboxes_obj = new mo_sp_metaboxes ();