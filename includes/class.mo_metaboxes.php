<?php

/*
 * Base Class for all the Metaboxes 
 */

class mo_metaboxes {

    public $mo_post_type;
    public $mo_short_type;
    public $mo_api_post_type;

    /*
     * Main Class construct to set default values 
     */

    public function __construct($short_type,$post_type,$api_post_type) {
        $this->set_mo_short_type($short_type);
        $this->set_mo_post_type($post_type);
        $this->set_mo_api_post_type($api_post_type);
    }

    /*
     * Function to set and get parameters  
     */
    
    public function get_mo_short_type() {
        return $this->mo_short_type;
    }

    public function set_mo_short_type($mo_short_type) {
        $this->mo_short_type = $mo_short_type;
    }

    public function get_mo_post_type() {
        return $this->mo_post_type;
    }

    public function set_mo_post_type($mo_post_type) {
        $this->mo_post_type = $mo_post_type;
    }
    
    public function get_mo_api_post_type() {
        return $this->mo_api_post_type;
    }

    public function set_mo_api_post_type($mo_api_post_type) {
        $this->mo_api_post_type = $mo_api_post_type;
    }
    
    /*
     * A/B Testing Add Tabs 
     */

    function mo_ab_testing_add_tabs() {
        global $post;
        $post_type_is = get_post_type($post->ID);
        $permalink = get_permalink($post->ID);
        if ($post_type_is === "mo_landing_page") {
            $current_variation_id = mo_landing_pages::instance($post->ID)->get_current_variation();
        }else if($post_type_is === "mo_ct"){
            $current_variation_id = mo_callto_action::instance($post->ID)->get_current_variation();
        }else if($post_type_is === "mo_sp"){
            $current_variation_id = mo_squeeze_pages::instance ( $post->ID )->get_current_variation ();
        }else if($post_type_is === "page"){
            $mo_obj = new mo_pages($post->ID);
            $current_variation_id = $mo_obj->get_current_variation();
        }
        if ($post_type_is === $this->get_mo_post_type()) {
            if (isset($_GET ['new_meta_key']))
                $current_variation_id = $_GET ['new_meta_key'];
            echo "<input type='hidden' name='" . $this->get_mo_short_type() . "_open_variation' id='" . $this->get_mo_short_type() . "_open_variation' value='{$current_variation_id}'>";

            $variations = get_post_meta($post->ID, '' . $this->get_mo_short_type() . '_variations', true);
            $array_variations = explode(',', $variations);
            $variations = array_filter($array_variations, 'is_numeric');
            sort($array_variations, SORT_NUMERIC);

            $lid = end($array_variations);
            $new_variation_id = $lid + 1;

            if ($current_variation_id > 0 || isset($_GET ['new-variation'])) {
                $first_class = 'inactive';
            } else {
                $first_class = 'active';
            }

            echo '<h2 class="nav-tab-wrapper a_b_tabs">';
            echo '<a href="?post=' . $post->ID . '&' . $this->get_mo_short_type() . '_variation_id=0&action=edit" class="lp-ab-tab nav-tab nav-tab-' . $first_class . '" id="tabs-0">A</a>';

            $var_id_marker = 1;

            foreach ($array_variations as $i => $vid) {

                if ($vid != 0) {
                    $letter = mo_lp_ab_key_to_letter($vid);

                    if ($current_variation_id == $vid && !isset($_GET ['new-variation'])) {
                        $cur_class = 'active';
                    } else {
                        $cur_class = 'inactive';
                    }

                    echo '<div class="nav-tab-div"><a href="?post=' . $post->ID . '&' . $this->get_mo_short_type() . '_variation_id=' . $vid . '&action=edit" class="lp-nav-tab nav-tab nav-tab-' . $cur_class . '" id="tabs-add-variation">' . $letter . '</a><a Onclick="return confirm(\'Are you sure?\');" href="admin.php?action=' . $this->get_mo_short_type() . '_delete_variation&post=' . $post->ID . '&v_id=' . $vid . '" class="nav-tab-delete">X</a></div>';
                }
            }

            if (!isset($_GET ['new-variation'])) {
                echo '<a href="?post=' . $post->ID . '&' . $this->get_mo_short_type() . '_variation_id=' . $new_variation_id . '&action=edit&new-variation=1" class="lp-nav-tab nav-tab nav-tab-inactive nav-tab-add-new-variation" id="tabs-add-variation">Add New Variation</a>';
            } else {
                $variation_count = count($array_variations);
                $letter = mo_lp_ab_key_to_letter($variation_count);
                echo '<a href="?post=' . $post->ID . '&' . $this->get_mo_short_type() . '_variation_id=' . $new_variation_id . '&action=edit" class="lp-nav-tab nav-tab nav-tab-active" id="tabs-add-variation">' . $letter . '</a>';
            }
            $edit_link = (isset($_GET [$this->get_mo_short_type() . '_variation_id'])) ? '?' . $this->get_mo_short_type() . '_variation_id=' . $_GET [$this->get_mo_short_type() . '_variation_id'] . '' : '?' . $this->get_mo_short_type() . '_variation_id=0';
            $post_link = get_permalink($post->ID);
            $post_link = preg_replace('/\?.*/', '', $post_link);
            echo '</h2>';
        }
    }

    /*
     * Description input box  
     */

    function mo_add_description_input_box($post) {
        $mo_obj = $this->get_obj($post);
        if ($post->post_type === $this->get_mo_post_type()) {
            $v_id = $mo_obj->get_current_variation();
            $mo_description = $mo_obj->get_variation_property($v_id, 'description');
            $mo_description = ($mo_description=="" && $v_id==0)?$post->post_title:$mo_description;
            echo "<div id='" . $this->get_mo_short_type() . "_description_div'><div id='description_wrap'><input placeholder='" . __('Add Description for this variation.', mo_plugin::MO_LP_TEXT_DOMAIN) . "' type='text' class='description' name='description' id='description' value='{$mo_description}' style='width:100%;line-height:1.7em'></div></div>";
        }
    }

    /*
     * Display meta boxes  
     */

    function mo_display_meta_boxes() {
        global $post;
        add_meta_box($this->get_mo_short_type() . '_templates', 'Current Selected Template ', array(
            $this,
            $this->get_mo_short_type() . '_get_template_selected_metabox'
         ), $this->get_mo_post_type(), 'side', 'high');
        $reset_states = '<a style="float:right; padding-right:5px; padding-top:2px; " href="admin.php?action='.$this->get_mo_short_type().'_clear_stats&post='.$post->ID.'">Reset All</a> ';
        
        add_meta_box($this->get_mo_short_type() . '_variation_stats', __('Variation Testing Stats '.$reset_states), array(
            $this,
            $this->get_mo_short_type() . '_display_meta_box_variation_stats'
        ),  $this->get_mo_post_type(), 'side', 'high');

        add_meta_box($this->get_mo_short_type() . '_variation_id', __('Marketing Optimizer Variation Id'), array(
            $this,
            $this->get_mo_short_type() . '_variation_id_metabox'
         ), $this->get_mo_post_type(), 'side', 'high');
    }
    
    /*
     * Bulk reset option in action dropdown  
     */
    
    public function mo_add_bulk_option(){
        global $post;
        if(!isset($post)) return false;
        if($post->post_type==='page' || $post->post_type==='mo_landing_page' || $post->post_type==='mo_ct'|| $post->post_type==='mo_sp') {
            echo "<script type='text/javascript'>
                    jQuery(document).ready(function() {
                      jQuery('<option>').val('bulk_reset_status').text('Reset All Stats').appendTo(\"select[name='action']\");
                      jQuery('<option>').val('bulk_reset_status').text('Reset All Stats').appendTo(\"select[name='action2']\");
                    });
                  </script>";
        }
    } 
    /*
     * Bulk reset status  
     */
    public function mo_bulk_reset_status(){
        if(isset($_REQUEST['action']) && ($_REQUEST['action']=='bulk_reset_status' || $_REQUEST['action2']=='bulk_reset_status' )) {
            $post_array = $_REQUEST['post'];
            foreach ($post_array as $key=>$post_id){
                $mo_obj = $this->get_obj_by_type($_REQUEST['post_type'],$post_id);
                $mo_obj->clear_stats();
            }
        }
    }
    
    /*
     * Display meta boxes  
     */

    function mo_display_meta_box_variation_stats($post) {
        $mo_obj = $this->get_obj($post);
        if ($post->post_type === $this->get_mo_post_type()) {
        $mo_variation_ids_arr = $mo_obj->get_variation_ids_arr();
        echo '<table class="mo_meta_box_stats_table">
                    <tr class="mo_stats_header_row">
                        <th class="mo_stats_header_cell">ID</th>
                        <th class="mo_stats_header_cell">Imp</th>
                        <th class="mo_stats_header_cell">Visits</th>
                        <th class="mo_stats_header_cell">Conv</th>
                        <th class="mo_stats_header_cell">CR%</th>
                        <th class="mo_stats_header_cell">Cd</th>   
                        <th class="mo_stats_header_cell">Act</th>
                    </tr>';

        foreach ($mo_variation_ids_arr as $v) {
            $letter = mo_lp_ab_key_to_letter($v);
            $impressions = $mo_obj->get_variation_property($v, 'impressions') ? $mo_obj->get_variation_property($v, 'impressions') : 0;
            $visits = $mo_obj->get_variation_property($v, 'visitors') ? $mo_obj->get_variation_property($v, 'visitors') : 0;
            $conversions = $mo_obj->get_variation_property($v, 'conversions') ? $mo_obj->get_variation_property($v, 'conversions') : 0;
            $conversion_rate = $mo_obj->get_variation_property($v, 'conversion_rate') ? number_format($mo_obj->get_variation_property($v, 'conversion_rate'), 1) * 100 : 0;
            $status = $mo_obj->get_variation_property($v, 'status');

            $confidence = $mo_obj->get_confidence($v);
            $status_text = $status ? '<i title="Pause Variation" class="fa fa-pause"></i>' : '<i title="Resume Variation" class="fa fa-play"></i>';
            $link_ch = (get_option('permalink_structure') == "") ? '&' : '?';

            echo '<tr>';
            echo '<td class="mo_stats_cell"><a title="click to edit this variation" href="/wp-admin/post.php?post=' . $post->ID . '&' . $this->get_mo_short_type() . '_variation_id=' . $v . '&action=edit">' . $letter . '</a> </td>';
            echo '<td class="mo_stats_cell">' . $impressions . '</td>';
            echo '<td class="mo_stats_cell">' . $visits . '</td>';
            echo '<td class="mo_stats_cell">' . $conversions . '</td>';
            echo '<td class="mo_stats_cell">' . $conversion_rate . '%</td>';
            echo '<td class="mo_stats_cell">' . $confidence . '</td>';
            echo '<td class="mo_stats_cell"><a target="_blank" href="' . get_permalink($post->ID) . $link_ch . '' . $this->get_mo_short_type() . '_variation_id=' . $v . '" <i class="fa fa-search"></i></a> | ' . sprintf('<a href="admin.php?action=%s&post=%s&v_id=%s">' . $status_text . ' </a>', '' . $this->get_mo_short_type() . '_pause_variation', $post->ID, $v) . ' | ' . sprintf('<a href="admin.php?action=%s&post=%s&v_id=%s"><i title="Delete Variation" style="color:red;" class="fa fa-trash-o"></i></a>', '' . $this->get_mo_short_type() . '_delete_variation', $post->ID, $v) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        }
    }
    
    /*
     * Get template selected metabox  
     */
    function mo_get_template_selected_metabox($post) {
        
        if ($post->post_type == 'mo_landing_page') {
            $mo_obj = mo_landing_pages::instance($post->ID);
            $template_temp = 'theme';
            $templates_arr = mo_lp_get_templates();
        }else if ($post->post_type == 'mo_ct') {
            $mo_obj = mo_callto_action::instance($post->ID);
            $template_temp = 'mo_ct_blank';
            $templates_arr = mo_ct_get_templates();
        }else if ($post->post_type == 'mo_sp') {
            $mo_obj = mo_squeeze_pages::instance ( $post->ID );
            $template_temp = 'mo_sp_blank';
            $templates_arr = mo_sp_get_templates();
        }
        
        $v_id = $mo_obj->get_current_variation();
        $template = $mo_obj->get_variation_property($v_id, 'template') ? $mo_obj->get_variation_property($v_id, 'template') : $template_temp;
        // on base of first variation set default template for next variations 
        $next_temp = '';
        if ($v_id > 0 && $template == $template_temp) {
            $template = $mo_obj->get_variation_property(0, 'template') ? $mo_obj->get_variation_property(0, 'template') : $template_temp;
            $current_id = '';
            foreach ($templates_arr as $k => $v) {
                if ($v['title'] == $templates_arr[$template]['title']) {
                    $current_id = $k;
                }
            }
            $next_temp = '<a style="display:none;" href="#" label="' . $templates_arr[$template]['title'] . '" id="' . $current_id . '" class="mo_template_select custom_trigger"></a>';
        }
        
        $template_name = $templates_arr [$template] ['title'];
        
        if ($template == 'theme') {
            $theme_template = $mo_obj->get_variation_property($v_id, 'theme_template');
            $template_dir = get_template_directory_uri();
        } else {
            $template_dir = plugins_url() . '/' . mo_plugin::MO_DIRECTORY . '/templates/' . $template;
        }
        // Add an nonce field so we can check for it later.
        wp_nonce_field('mo_get_template_selected_metabox', 'mo_get_template_selected_metabox_nonce');
        echo '<div id="mo_templates" class="postbox">
		<h3 class="hndle">Template: 
                    <span id="mo_template_name">' . $template_name . '</span>
		</h3>
		<div id="mo_template_image_container">
                    <span id="mo_template_image">
			<img height="200" width="200" src="' . $template_dir . '/screenshot.png" id="c_temp">
                    </span>
                </div>
                <div id="mo_current_template">
                    <input type="hidden" name="mo_template" value="' . $template . '">
		</div>' . $next_temp;

        echo '<div id="mo_theme_template" style="margin-top:10px;">
              <label  for="theme_template" style="font-weight:bold;margin-bottom:10px;">Theme Template</label>
              <select name="theme_template" id="theme_template">
                    <option value="default">';
                    _e('Default Template');
              echo '</option>';
                    page_template_dropdown($theme_template);
        echo '</select></div>';

        echo '<div id="mo_template_change">
		<h2><a class="button" id="mo-change-template-button">Choose Another Template</a></h2>
              </div>
            </div>';
    }
    
    /*
     * Save Meta  
     */
    
    function mo_save_meta($post_id) {
        global $post;

        if (!isset($post))
            return;

        if ($post->post_type == 'revision') {
            return;
        }

        if ((defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) || (isset($_POST ['post_type']) && $_POST ['post_type'] == 'revision')) {
            return;
        }

        if ($post->post_type == 'mo_landing_page') {
            $mo_obj = mo_landing_pages::instance($post_id);
        }else  if ($post->post_type == 'page') {
            $mo_obj = new mo_pages($post_id);
        } 
        
        if ($post->post_type === $this->get_mo_post_type()) {
            $v_id = $mo_obj->get_current_variation();
            $variation_ids_arr = $mo_obj->get_variation_ids_arr();
            

            if (!in_array($v_id, $variation_ids_arr) && !is_null($v_id)) {
                $variation_ids_arr [$v_id] = $v_id;
                $letter = mo_lp_ab_key_to_letter($v_id);
                $mo_obj->set_variation_ids_arr($variation_ids_arr);
                $mo_obj->save();
                $mo_obj->set_variations_arr_custom($mo_obj->get_variation_ids_arr());
            }
            
            foreach ($_POST as $k => $v) {
                if ($k == 'post_title') {
                    $k = 'title';
                }
                if ($k == 'mo_template') {
                    $k = 'template';
                }
                if (property_exists('mo_variation', $k)) {
                    $mo_obj->set_variation_property($v_id, $k, $v);
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
                            $mo_obj->set_variation_property($v_id, $k, $v);
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
                            $mo_obj->set_variation_property($v_id, $k, $v);
                        }
                    }
                }
                //----------------------------end create variation in api --------------------
            }
            $mo_obj->save();
            // save taxonomies
            $post = get_post($post_id);
          }  
    }
    
    public function mo_redirect_post_variation(){
        $link_r =  get_edit_post_link( $post_id, 'url' );
        $post_type = $_POST['post_type'];
        if($post_type=='mo_landing_page'){
            $post_type = 'mo_lp';
        }else if($post_type=='page'){
            $post_type = 'mo_page';
        }
        $vid = $_POST[$post_type.'_open_variation'];
        $find = '_variation_id=0';
        $rep = '_variation_id='.$vid;
        $link_r = str_replace($find,$rep,$link_r);
        wp_redirect($link_r);
    }
    
    /*
     * Get tests from API new variation  
     */
    
    public function mo_get_tests_from_api_new_variation($postvars){
        if (isset($postvars)) {
            $postDataObj = new stdClass();
            $postDataObj->name = $postvars['variation_name'];
            $postDataObj->experiment = $postvars['description'];
            $postDataObj->type = $this->get_mo_api_post_type();
            $postData = json_encode($postDataObj);
            
            $mo_api_test = new mo_api_tests();
            $response = $mo_api_test->set_request_type('POST')
                ->set_request($postData)
                ->execute();
            $response = $mo_api_test->get_response();
            $decoded_response = json_decode($response, true);
            return $decoded_response['data']['id'];
        }
    } 
    
    /*
     * Get tests from API update  
     */
    
     public function mo_get_tests_from_api_update($postvars){
        if (isset($postvars)) {
            $postDataObj = new stdClass();
            $postDataObj->id = $postvars['id'];
            $postDataObj->name = $postvars['variation_name'];
            $postDataObj->experiment = $postvars['description'];
            $postDataObj->type = $this->get_mo_api_post_type();
            $postData = json_encode($postDataObj);
            
            $mo_api_test = new mo_api_tests();
            $response = $mo_api_test->set_request_type('POST')
                ->set_request($postData)
                ->execute();
            $response = $mo_api_test->get_response();
            $decoded_response = json_decode($response, true);
            return $decoded_response['data']['id'];
            //return $response;
        }
    }
    
    /*
     * Variation id metabox  
     */
    public function mo_variation_id_metabox($post) {
        $mo_obj = $this->get_obj($post);
        if ($post->post_type === $this->get_mo_post_type()) {
            $v_id = $mo_obj->get_current_variation();
            $mo_variation_id = $mo_obj->get_variation_property($v_id, 'variation_id');
            $variant_html = "<div id='" . $this->get_mo_short_type() . "_variation_id_div'><div id='variation_id_wrap'>";
            $variant_html .= "<input readonly='true'  name='variation_id' id='variation_id' class='variation_id' style='width:100%'  value='$mo_variation_id' />";
            $variant_html .= "<input type='hidden' name='permalink_structure' id='permalink_structure' value='" . get_option('permalink_structure') . "' /></div></div>";
            echo $variant_html;
        }
    }
    
    /*
     * Meta box elect template container
     */
    function mo_display_meta_box_select_template_container() {
        global $post;
        if(!isset($post)) return false;
        if($post->post_type=='mo_landing_page'){
            $title_page = "Select Your Landing Page Template ";
            $dir_path = $this->get_mo_short_type() . '_landing_pages';
            $templates_array = mo_lp_get_templates();
        }
        else if($post->post_type=='mo_ct'){
            $title_page = "Select Your Calls-To-Action Template ";
            $dir_path = $this->get_mo_short_type() . '_landing_pages';
            $templates_array = mo_ct_get_templates();
        }
        else if($post->post_type=='mo_sp'){
            $title_page = "Select Your Pop-Ups Template ";
            $dir_path = $this->get_mo_short_type() . '_landing_pages';
            $templates_array = mo_sp_get_templates();
        }
        $current_url = "http://" . $_SERVER ["HTTP_HOST"] . $_SERVER ["REQUEST_URI"] . "";

        if (isset($post) && $post->post_type != $this->get_mo_post_type() || !isset($post)) {
            return false;
        }

        (!strstr($current_url, 'post-new.php')) ? $toggle = "display:none" : $toggle = "";

        $uploads = wp_upload_dir();
        $uploads_path = $uploads ['basedir'];
        $extended_path = $uploads_path . '/' . $dir_path . '/templates/';

        $template = get_post_meta($post->ID, 'lp-selected-template', true);
        $template = apply_filters('lp_selected_template', $template);
        echo '<div id="mo_template_select_container" style="' . $toggle . '">
                        <div class="mo_template_select_heading"><h1>'.$title_page.'</h1></div>
                      ';
        echo '<ul id="Grid" style=" ">';
        foreach ($templates_array as $k => $v) {
            $preview = '';
                if($v ['title']!="Blank"){
                //$preview =  ' | <a href="javascript:void(0)"   label="' . $v ['title'] . '" id="' . $k . '" class="mo_template_preview">Preview</a>';    
                    $img_path = $v ['thumbnail'];
                    if($k!="theme"){
                    $img_path = str_replace('screenshot.png','preview.png',$img_path);
                    }
                    $preview =  ' | <a href="' . $img_path . '"   class="template_preview">Preview</a>';    
                }
            
            echo '<li class="mix category_1 mix_all" data-cat="1" style=" display: inline-block; opacity: 1;"><div style="color:#444; padding:5px 0px;">' . $v ['title'] . '</div><a href="#" label="' . $v ['title'] . '" id="' . $k . '" class="mo_template_select"><img class="mo_template_thumbnail" width="200" height="200" src="' . $v ['thumbnail'] . '" /></a><span style=""><a href="#" label="' . $v ['title'] . '" id="' . $k . '" class="mo_template_select">Select</a> '.$preview.' </span> </li>';
        }

        echo '<li class="gap"></li> <!-- "gap" elements fill in the gaps in justified grid -->
                </ul></div>';
    }
    
    
    
    /*
     * Template dialog box 
     */
   function mo_add_template_dialog_box() {
        global $post;
        if (isset($post) && $post->post_type == $this->get_mo_post_type()) {
            echo '<div id="dialog-confirm" title="Change Template" style="display:none;">
                    <p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Changing the template will replace the current content with the new template. Are you sure you want to do this?</p>
                  </div>';
        }
    }
    /*
     * Get post type object  
     */
    public function get_obj($post){
        $mo_obj = false;
        if ($post->post_type == "mo_landing_page") {
            $mo_obj = mo_landing_pages::instance($post->ID);
        }else if ($post->post_type == "mo_ct") { 
            $mo_obj = mo_callto_action::instance($post->ID);
        }else if ($post->post_type == "mo_sp") { 
            $mo_obj = mo_squeeze_pages::instance ( $post->ID );
        }else if ($post->post_type == "page") { 
            $mo_obj = mo_pages::instance($post->ID);
        }
        return $mo_obj;
    }
    
    public function get_obj_by_type($post_type,$post_id) {
        $mo_obj = false;
        if ($post_type == "mo_landing_page") {
            $mo_obj = mo_landing_pages::instance($post_id);
        }else if ($post_type == "mo_ct") { 
            $mo_obj = mo_callto_action::instance($post_id);
        }else if ($post_type == "mo_sp") { 
            $mo_obj = mo_squeeze_pages::instance ( $post_id );
        }else if ($post_type == "page") { 
            $mo_obj = mo_pages::instance($post_id);
        }
        return $mo_obj;
    }
}
