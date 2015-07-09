<?php

class mo_page_metaboxes extends mo_metaboxes {

    CONST APIURI = 'http://development.marketingoptimizer.com/api/v1/';

    public function __construct() {
        $short_type = 'mo_page';
        $post_type = 'page';
        $api_post_type = 'website_page';
        parent::__construct ( $short_type,$post_type,$api_post_type );
        
        add_action('edit_form_after_title', array(
            $this,
            'mo_page_ab_testing_add_tabs'
        ), 5);
        add_action('edit_form_after_title', array(
            $this,
            'mo_page_add_description_input_box'
        ));
        add_action('add_meta_boxes_page', array(
            $this,
            'mo_page_display_meta_boxes'
        ), 10, 2);
        add_action('save_post', array(
            $this,
            'mo_page_ab_testing_save_post'
        ));
        
        add_filter('content_save_pre', array(
            $this,
            'mo_page_content_save_pre'
        ));
        add_filter('title_save_pre', array(
            $this,
            'mo_page_title_save_pre'
        ));
        add_action ( 'redirect_post_location', array (
            $this,
            'variation_redirect_after_save' 
	) );
    }

    function mo_page_ab_testing_add_tabs(){
        $this->mo_ab_testing_add_tabs();
    }

    function mo_page_add_description_input_box($post){
        $this->mo_add_description_input_box($post);
    }

    public function mo_page_display_meta_boxes($post){
       $reset_states = '<a style="float:right; padding-right:5px; padding-top:2px; " href="admin.php?action='.$this->get_mo_short_type().'_clear_stats&post='.$post->ID.'">Reset All</a> ';
         
       add_meta_box('mo_page_variation_stats', __('Variation Testing Stats'.$reset_states), array(
            $this,
            'mo_page_display_meta_box_variation_stats'
        ), 'page', 'advanced', 'high');
        add_meta_box('mo_page_variation_id', __('Marketing Optimizer Variation Id'), array(
            $this,
            'mo_page_variation_id_metabox'
        ), 'page', 'side', 'high');
    }

    public function mo_page_variation_id_metabox($post){
        $this->mo_variation_id_metabox($post);
    }

    function mo_page_display_meta_box_variation_stats($post){
        $this->mo_display_meta_box_variation_stats($post);
    }

    function mo_page_save_meta($post_id){
        $this->mo_save_meta($post_id);
    }
    
    function variation_redirect_after_save(){
        $this->mo_redirect_post_variation();
    }
     
    function mo_page_ab_testing_save_post($postID){
        global $post;
        
        $var_final = (isset($_POST['mo_page_open_variation'])) ? $_POST['mo_page_open_variation'] : '0';
        if (isset($_POST['post_type']) && $_POST['post_type'] == 'page') {
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || $_POST['post_type'] == 'revision') {
                return;
            }
            
            if ($parent_id = wp_is_post_revision($postID)) {
                $postID = $parent_id;
            }
            
            $this_variation = $var_final;
            
            if (! get_post_meta($postID, 'mo_page_status_' . $this_variation, true) == 1 || ! get_post_meta($postID, 'mo_page_status_' . $this_variation, true) == 0) {
                update_post_meta($postID, 'mo_page_status_' . $this_variation, 1);
            }
            
            // next alter all custom fields to store correct varation and create custom fields for special inputs
            $ignore_list = array(
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
            
            $mo_page_obj = mo_pages::instance($postID);
            $v_id = $mo_page_obj->get_current_variation();
            $letter = mo_lp_ab_key_to_letter($v_id);
            $variation_ids_arr = $mo_page_obj->get_variation_ids_arr();
            if (! in_array($v_id, $variation_ids_arr)) {
                $variation_ids_arr[$v_id] = $v_id;
                $mo_page_obj->set_variation_ids_arr($variation_ids_arr);
                $mo_page_obj->save();
                $mo_page_obj->set_variations_arr_custom ( $mo_page_obj->get_variation_ids_arr () );
            }
            foreach ($_POST as $k => $v) {
                if ((int) $v_id !== 0) {
                    if ($k == 'post_title') {
                        $k = 'title';
                    }
                }
                if ($k == 'page_template') {
                    $k = 'template';
                }
                if (property_exists('mo_variation', $k)) {
                    $mo_page_obj->set_variation_property($v_id, $k, $v);
                }
                //----------------------------start create variation in api --------------------
                if ($k == 'variation_id') {
                    if ($v == "") {
                        $new_variation_name = $_POST['post_title'] . " - " . $letter;
                        $postvar = array(
                            'variation_name' => $new_variation_name,
                            'description' => $_POST['description']
                        );
                        if(count($variation_ids_arr)>1) {
                            $v = $this->mo_get_tests_from_api_new_variation($postvar);
                            $mo_page_obj->set_variation_property($v_id, $k, $v);
                        }
                    } else {
                        
                        $new_variation_name = $_POST['post_title'] . " - " . $letter;
                        $postvar = array(
                            'variation_name' => $new_variation_name,
                            'description' => $_POST['description'],
                            'id' => $v
                        );
                        if(count($variation_ids_arr)>1) {
                            $v = $this->mo_get_tests_from_api_update($postvar);
                            $mo_page_obj->set_variation_property($v_id, $k, $v);
                        }
                    }
                }
                //----------------------------end create variation in api --------------------
            }
            $mo_page_obj->save();
            // save taxonomies
            if ((int) $v_id == 0) {
                $post = get_post($postID);
            }
        }
    }

    public function mo_page_content_save_pre($content)
    {
        global $post;
        if ($post && $post->post_type == 'page') {
            $mo_page_obj = new mo_pages($post->ID);
            $v_id = $mo_page_obj->get_current_variation();
            if ((int) $v_id !== 0) {
                $content = $post->post_content;
            }
        }
        return $content;
    }

    public function mo_page_title_save_pre($title)
    {
        global $post;
        if ($post && $post->post_type == 'page') {
            $mo_page_obj = new mo_pages($post->ID);
            $v_id = $mo_page_obj->get_current_variation();
            if ((int) $v_id !== 0) {
                $title = $post->post_title;
            }
        }
        return $title;
    }
}
$mo_page_metaboxes_obj = new mo_page_metaboxes();