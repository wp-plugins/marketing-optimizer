<?php
class mo_lp_metaboxes extends mo_metaboxes {
	public function __construct() {
                
                $short_type = 'mo_lp';
                $post_type = 'mo_landing_page';
                $api_post_type = 'landing_page';
                parent::__construct ( $short_type,$post_type,$api_post_type );
            
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
                add_action ( 'in_admin_footer', array (
				$this,
				'mo_lp_add_bulk_option' 
		) );
                
                /*add_action ( 'admin_action_bulk_reset_status', array (
				$this,
				'mo_lp_bulk_reset_status' 
		) );*/
                
                add_action ( 'load-edit.php', array (
				$this,
				'mo_lp_bulk_reset_status' 
		) );
                
                add_action ( 'save_post', array (
				$this,
				'mo_lp_save_meta' 
		) );
                
                add_action ( 'redirect_post_location', array (
				$this,
				'variation_redirect_after_save' 
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
            $this->mo_ab_testing_add_tabs();
	}

	function mo_lp_add_description_input_box($post) {
                $this->mo_add_description_input_box($post);
	}
        
        function mo_lp_add_bulk_option(){
            $this->mo_add_bulk_option();
        }
        
        function mo_lp_bulk_reset_status(){
            $this->mo_bulk_reset_status();
        }

	function mo_lp_display_meta_boxes($post) {
                $this->mo_display_meta_boxes();
	}

	function mo_lp_display_meta_box_variation_stats($post) {
		$this->mo_display_meta_box_variation_stats($post);
	}

	function mo_lp_get_template_selected_metabox($post) {
		$this->mo_get_template_selected_metabox($post);
	}

	function mo_lp_save_meta($post_id) {
           $this->mo_save_meta($post_id);
	}
        
        function variation_redirect_after_save(){
            $this->mo_redirect_post_variation();
        }
	
	public function mo_lp_variation_id_metabox($post) {
		$this->mo_variation_id_metabox($post);
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
	
	function mo_lp_display_meta_box_select_template_container() {
		$this->mo_display_meta_box_select_template_container();
	}

	function mo_lp_add_template_dialog_box() {
		$this->mo_add_template_dialog_box();
	}
}

$mo_lp_metaboxes_obj = new mo_lp_metaboxes ();
