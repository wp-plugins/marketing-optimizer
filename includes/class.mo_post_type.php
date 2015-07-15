<?php

/*
 * Base Class for all the Post types 
 */

class mo_post_type {

    public $mo_pt_post_type;
    public $mo_pt_short_type;
    public $mo_pt_api_post_type;

    /*
     * Main Class construct to set default values 
     */

    public function __construct($pt_short_type,$pt_post_type,$pt_api_post_type) {
        $this->set_mo_pt_short_type($pt_short_type);
        $this->set_mo_pt_post_type($pt_post_type);
        $this->set_mo_pt_api_post_type($pt_api_post_type);
    }

    /*
     * Function to set and get parameters  
     */
    
    public function get_mo_pt_short_type() {
        return $this->mo_pt_short_type;
    }

    public function set_mo_pt_short_type($mo_pt_short_type) {
        $this->mo_pt_short_type = $mo_pt_short_type;
    }

    public function get_mo_pt_post_type() {
        return $this->mo_pt_post_type;
    }

    public function set_mo_pt_post_type($mo_pt_post_type) {
        $this->mo_pt_post_type = $mo_pt_post_type;
    }
    
    public function get_mo_pt_api_post_type() {
        return $this->mo_pt_api_post_type;
    }

    public function set_mo_pt_api_post_type($mo_pt_api_post_type) {
        $this->mo_pt_api_post_type = $mo_pt_api_post_type;
    }
    
    /*
     * Add Clear Tracking 
     */

    public function mo_add_clear_tracking($actions, $post) {
        
        if ($post->post_type === $this->get_mo_pt_post_type()) {
            $last_reset = get_post_meta($post->ID, $this->get_mo_pt_short_type().'_stat_reset_date', true) ? get_post_meta($post->ID, $this->get_mo_pt_short_type().'_stat_reset_date', true) : 'Never';
            if ($last_reset !== 'Never') {
                $last_reset = Date('m/d/Y', $last_reset);
            }
            $actions [$this->get_mo_pt_short_type().'_clear_stats'] = sprintf('<a href="admin.php?action=%s&post=%s">Reset All Stats</a> <br><i>(Last Stat Reset: ' . $last_reset, $this->get_mo_pt_short_type().'_clear_stats', $post->ID) . ')</i>';
        }
        return $actions;
    }
    
    public function mo_add_variation_cookie_js() {
        global $post, $variation_id;
        $variation_id = ($variation_id=="")?0:$variation_id;
        $mo_obj = $this->get_obj($post); 
        if ($post->post_type == $this->get_mo_pt_post_type() && $this->mo_track_admin_user() && !$mo_obj->mo_bot_detected()) {
            define( 'DONOTCACHEPAGE', true );
            echo '<script>
                    jQuery(function($){
                        function '.$this->get_mo_pt_short_type().'_get_variation_cookie() {
                                var cookies = document.cookie.split(/;\s*/);
                                    for ( var i = 0; i < cookies.length; i++) {
                                            var cookie = cookies[i];
                                            var control = ' . $post->ID . ';
                                            if (control > 0 && cookie.indexOf("'.$this->get_mo_pt_short_type().'_variation_" + control) != -1) {
                                                    cookie = cookie.split("=", 2);
                                                    return cookie[1];
                                            }
                                    }
                                return null;
                        }
                    
                        function '.$this->get_mo_pt_short_type().'_set_variation_cookie(name, value, days) {
                                if (days) {
                                        var date = new Date();
                                        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                                        var expires = "; expires=" + date.toGMTString();
                                } else var expires = "";
                                document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
                        }
                    
                        function '.$this->get_mo_pt_short_type().'_track_impression(){
                                xmlhttp = new XMLHttpRequest();
                                xmlhttp.open("POST","' . admin_url('admin-ajax.php') . '" ,true);
                                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                                xmlhttp.send("action='.$this->get_mo_pt_short_type().'_track_impression&post_id=' . $post->ID . '&v_id="+'.$this->get_mo_pt_short_type().'_get_variation_cookie());
                                xmlhttp.onreadystatechange = function () {
                                        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                           var response  = xmlhttp.responseText;
                                        }
                                }
                        }
                    
                        function '.$this->get_mo_pt_short_type().'_track_visit(v_id){
                                xmlhttp = new XMLHttpRequest();
                                xmlhttp.open("POST","' . admin_url('admin-ajax.php') . '" ,true);
                                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                                xmlhttp.send("action='.$this->get_mo_pt_short_type().'_track_visit&post_id=' . $post->ID . '&v_id=' . $variation_id . '");
                                xmlhttp.onreadystatechange = function () {
                                        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                           var response  = xmlhttp.responseText;
                                        }

                                }
                        }
                    
                        function '.$this->get_mo_pt_short_type().'_get_variation_id_to_display(){
                                xmlhttp = new XMLHttpRequest();
                                xmlhttp.open("POST","' . admin_url('admin-ajax.php') . '" ,true);
                                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                                xmlhttp.send("action='.$this->get_mo_pt_short_type().'_get_variation_id_to_display&post_id=' . $post->ID . '");
                                xmlhttp.onreadystatechange =  function () {
                                        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                           var response  = xmlhttp.responseText;
                                                var json_response = JSON.parse(response);
                                                variation_id = json_response.v_id;
                                                '.$this->get_mo_pt_short_type().'_set_variation_cookie("'.$this->get_mo_pt_short_type().'_variation_' . $post->ID . '",' . $variation_id . ',365);
                                                '.$this->get_mo_pt_short_type().'_track_impression();
                                                '.$this->get_mo_pt_short_type().'_track_visit(' . $variation_id . ');
                                        }
                                }

                        }';
            if ($mo_obj->mo_is_testing()) {
                    echo    'if('.$this->get_mo_pt_short_type().'_get_variation_cookie() == null){
                                    '.$this->get_mo_pt_short_type().'_get_variation_id_to_display();
                            }else{
                                    '.$this->get_mo_pt_short_type().'_track_impression();
                            }
                    });
		</script>';
            } else {
                    echo    'if('.$this->get_mo_pt_short_type().'_get_variation_cookie() == null){
                                    '.$this->get_mo_pt_short_type().'_set_variation_cookie("'.$this->get_mo_pt_short_type().'_variation_' . $post->ID . '",' . $variation_id . ',365);
                                    '.$this->get_mo_pt_short_type().'_track_impression();
                                    '.$this->get_mo_pt_short_type().'_track_visit(' . $variation_id . ');
                                }else{
                                    '.$this->get_mo_pt_short_type().'_track_impression();
                                }
                        });
                </script>';
            }
        }
    }
    
    
    /*
     * Register category taxonomy  
     */
    public function mo_category_register_taxonomy($title) {
        $args = array(
            'hierarchical' => true,
            'label' => __("Categories", mo_plugin::MO_LP_TEXT_DOMAIN),
            'singular_label' => __($title, mo_plugin::MO_LP_TEXT_DOMAIN),
            'show_ui' => true,
            'query_var' => true,
            "rewrite" => true
        );

        register_taxonomy($this->get_mo_pt_post_type().'_category', array(
            $this->get_mo_pt_post_type()
                ), $args);
    }
    /*
     * Clear status data  
     */
    public function mo_clear_stats() {
        
        if (isset($_GET ['post']) && $_GET ['post']) {
            $post_id = $_GET ['post'];
            $mo_obj = $this->get_obj_by_type($post_id);
            $mo_obj->clear_stats();
        }
        wp_redirect(wp_get_referer());
        exit();
    }
    
    /*
     * Clear status data  
     */
    
    public function mo_column($column) {
        global $post;
        $mo_obj = $this->get_obj($post);
        $v_id = $mo_obj->get_current_variation();
        switch ($column) {
            case 'ID' :
                echo $post->ID;
                break;
            case 'title' :
            case 'author' :
            case 'date' :
                break;
            case 'stats' :
                $this->mo_show_stats_list();
                break;
            case 'impressions' :
                echo $this->mo_show_aggregated_stats ( "impressions" );
                break;
            case 'visits' :
                echo $this->mo_show_aggregated_stats ( "visits" );;
                break;
            case 'conversions' :
                echo $this->mo_show_aggregated_stats ( "conversions" );
                break;
            case 'cr' :
                echo $this->mo_show_aggregated_stats ( "cr" ) . "%";
                break;
        }
    }
    
    /*
     * show stats list  
     */
    
    public function mo_show_stats_list() {
        global $post;
        $mo_obj = $this->get_obj($post);
        $variations = $mo_obj->get_variation_ids_arr();
        if (count($variations)) {
            $variations_arr = $mo_obj->get_variations_arr();
            echo '<div class="mo_stats_table" >
                        <div class="mo_stats_header_row" style="float:left; width:400px;">
                          <div class="mo_stats_header_cell mo_stats_d" >ID</div>
                          <div class="mo_stats_header_cell mo_stats_d" style="padding:8px 16.5px;" >Imp</div>
                          <div class="mo_stats_header_cell mo_stats_d" style="width:31px;">Visits</div>
                          <div class="mo_stats_header_cell mo_stats_d" style="width:31px;">Conv</div>
                          <div class="mo_stats_header_cell mo_stats_d" style="width:31px;">CR</div>
                          <div class="mo_stats_header_cell mo_stats_d" >Confidence</div>
                          <div class="mo_stats_header_cell mo_stats_d" style="padding:8px 15.5px !important; border-right:0px;" >Actions</div>
                        </div>';
            foreach ($variations_arr as $var_obj) {
                if ($var_obj->id !== '') {
                    // assign variation id a letter
                    $letter = mo_lp_ab_key_to_letter($var_obj->get_id());
                    // get variation visits
                    $visits = $var_obj->get_visitors() ? $var_obj->get_visitors() : 0;
                    // get variation impressions
                    $impressions = $var_obj->get_impressions() ? $var_obj->get_impressions() : 0;
                    // current variation status
                    $status = $var_obj->get_status();
                    $status_text = $status ? '<i title="Pause Variation" class="fa fa-pause"></i>' : '<i title="Resume Variation" class="fa fa-play"></i>';
                    $status_class_text = $status ? 'mo_status_unpaused' : 'mo_status_paused';
                    $confidence = $mo_obj->get_confidence($var_obj->get_id());

                    // get variation conversions
                    $conversions = $var_obj->get_conversions() ? $var_obj->get_conversions() : 0;
                    (($conversions === "")) ? $total_conversions = 0 : $total_conversions = $conversions;

                    // add variaton visits to total
                    $total_visits += $var_obj->get_visitors();
                    // add variaton impressions to total
                    $total_impressions += $var_obj->get_impressions();
                    // add variaton conversions to total
                    $total_conversions += $var_obj->get_conversions();
                    // get conversion rate
                    if ($visits != 0) {
                        $conversion_rate = $conversions / $visits;
                    } else {
                        $conversion_rate = 0;
                    }

                    $conversion_rate = round($conversion_rate, 2) * 100;
                    $cr_array [] = $conversion_rate;
                    $url = $status ? '<a title="' . $var_obj->get_description() . '" href="/wp-admin/post.php?post=' . $post->ID . '&'.$this->get_mo_pt_short_type().'_variation_id=' . $var_obj->get_id() . '&action=edit">' . $letter . '</a>' : $letter;    
                    
                    echo '<div class="' . $status_class_text . '" style="float:left; width:400px;">';
                    echo '<div class="mo_stats_header_cell mo_stats_r" style="padding:8px 13.5px;">'.$url.'</div>';
                    echo '<div class="mo_stats_header_cell mo_stats_r" style="width:18px;" >'.$impressions.'</div>';
                    echo '<div class="mo_stats_header_cell mo_stats_r" style="width:27px;">'.$visits.'</div>';
                    echo '<div class="mo_stats_header_cell mo_stats_r" style="width:27px;">'.$conversions .'</div>';
                    echo '<div class="mo_stats_header_cell mo_stats_r" style="width:27px;">'.$conversion_rate.'</div>';
                    echo '<div class="mo_stats_header_cell mo_stats_r" style="padding:8px 34px;">'.$confidence.'</div>';
                    $link_ch = (get_option('permalink_structure') == "") ? '&' : '?';
                    echo '<div class="mo_stats_cell mo_stats_r" style="border-right:0px;" ><a target="_blank" href="' . get_permalink($post->ID) . $link_ch . ''.$this->get_mo_pt_short_type().'_variation_id=' . $var_obj->get_id() . '" <i class="fa fa-search"></i></a> | ' . sprintf('<a href="admin.php?action=%s&post=%s&v_id=%s">' . $status_text . ' </a>', ''.$this->get_mo_pt_short_type().'_pause_variation', $post->ID, $var_obj->get_id()) . ' | ' . sprintf('<a href="admin.php?action=%s&post=%s&v_id=%s"><i title="Delete Variation" style="color:red;" class="fa fa-trash-o"></i></a>', $this->get_mo_pt_short_type().'_delete_variation', $post->ID, $var_obj->get_id()) . '</div>';
                    echo '</div>';
                    
                }
            }
            
            echo "</div>";
        }
    }
    
    /*
     * show aggregated stats list  
     */
    public function mo_show_aggregated_stats($type_of_stat) {
        global $post;
        $mo_obj = $this->get_obj($post);
        $variations = $mo_obj->get_variations_arr();

        $visits = 0;
        $impressions = 0;
        $conversions = 0;

        foreach ($variations as $vid) {
            $each_visit = $vid->get_visitors();
            $each_impression = $vid->get_impressions();
            $each_conversion = $vid->get_conversions();
            (($each_conversion === "")) ? $final_conversion = 0 : $final_conversion = $each_conversion;
            $visits += $vid->get_visitors();
            $impressions += $vid->get_impressions();
            $conversions += $vid->get_conversions();
        }
        if ($type_of_stat === "conversions") {
            return $conversions;
        }
        if ($type_of_stat === "visits") {
            return $visits;
        }
        if ($type_of_stat === "impressions") {
            return $impressions;
        }
        if ($type_of_stat === "cr") {
            if ($visits != 0) {
                $conversion_rate = $conversions / $visits;
            } else {
                $conversion_rate = 0;
            }
            $conversion_rate = round($conversion_rate, 2) * 100;
            return $conversion_rate;
        }
    }

    /*
     * Get post type object  
     */
    public function mo_columns($columns, $title) {
        $columns = array(
            "cb" => "<input type=\"checkbox\" />",
            "title" => __($title, mo_plugin::MO_LP_TEXT_DOMAIN),
            "ID" => "ID",
            "impressions" => __("Impressions", mo_plugin::MO_LP_TEXT_DOMAIN),
            "visits" => __("Visits", mo_plugin::MO_LP_TEXT_DOMAIN),
            "conversions" => __("Conversions", mo_plugin::MO_LP_TEXT_DOMAIN),
            "cr" => __("Conversion Rate", mo_plugin::MO_LP_TEXT_DOMAIN),
            "author" => __("Author", mo_plugin::MO_LP_TEXT_DOMAIN),
            "comments" => __("Comments", mo_plugin::MO_LP_TEXT_DOMAIN),
            "date" => __("Date", mo_plugin::MO_LP_TEXT_DOMAIN),
            "stats" => __("Variation Testing Stats", mo_plugin::MO_LP_TEXT_DOMAIN),
        );
        
        return $columns;
    }
    /*
     * Get page conversion  
     */
    public function mo_conversion_page(){
        
        global $post;
        if (! isset($_GET['preview']) && $this->mo_track_admin_user()) {
            echo '<script type="text/javascript" >
                    function mo_page_get_conv_variation_cookie(){
                             var cookies = document.cookie.split(/;\s*/);
                             var cookiesArr = [];
                             for(var i=0;i < cookies.length;i++){
                                var cookie = cookies[i];
                                if(cookie.indexOf("mo_page_variation_") != -1 || cookie.indexOf("mo_lp_variation_") != -1 || cookie.indexOf("mo_sp_variation_") != -1 || cookie.indexOf("mo_ct_variation_") != -1 ){
                                        cookiesArr.push(cookie);
                                }
                             }
                             return JSON.stringify(cookiesArr);
                    }
                    if(mo_page_get_conv_variation_cookie() != null){
                            xmlhttp = new XMLHttpRequest();
                            xmlhttp.open("POST","' . admin_url('admin-ajax.php') . '" ,true);
                            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                            xmlhttp.send("action=mo_page_track_conversion&cookie="+mo_page_get_conv_variation_cookie());
                            xmlhttp.onreadystatechange = function () {
                                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                   var response  = xmlhttp.responseText;
                                   var json_response = JSON.parse(response);
                                }
                            }
                }
											
                </script>';
        }
        
    }
    /*
     * Get conversion  
     */
    public function mo_conversion() {
        global $post;
        if (!isset($_GET ['preview']) && $this->mo_track_admin_user()) {
            echo '<script type="text/javascript" >
			jQuery(function($){
                            function '.$this->get_mo_pt_short_type().'_get_variation_cookie(){
                                    var cookies = document.cookie.split(/;\s*/);
                                    var cookiesArr = [];
                                    for(var i=0;i < cookies.length;i++){
                                            var cookie = cookies[i];
                                            if(cookie.indexOf("'.$this->get_mo_pt_short_type().'_variation_") != -1){
                                                    cookiesArr.push(cookie);
                                            }
                                    }
                                    return JSON.stringify(cookiesArr);
                            }
                            '.$this->get_mo_pt_short_type().'_get_variation_cookie();
                            if('.$this->get_mo_pt_short_type().'_get_variation_cookie() != null){
                                    xmlhttp = new XMLHttpRequest();
                                    xmlhttp.open("POST","' . admin_url('admin-ajax.php') . '" ,true);
                                    xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

                                    xmlhttp.send("action='.$this->get_mo_pt_short_type().'_track_conversion&cookie=+'.$this->get_mo_pt_short_type().'_get_variation_cookie()");
                                    xmlhttp.onreadystatechange = function () {
                                            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                               var response  = xmlhttp.responseText;
                                               var json_response = JSON.parse(response);
                                            }
                                    }
                            }
                        });
                    </script>';
        }
    }
    
    /*
     * Track conversion  
     */
    
    public function mo_track_conversion() {
        if (isset($_POST ['cookie']) && $_POST ['cookie']) {
            $cookieArr = json_decode(stripslashes($_POST ['cookie']));
            $needle = $this->get_mo_pt_short_type().'_variation_';
            if (!empty($cookieArr)) {
                foreach ($cookieArr as $v) {
                    $cookie = explode('=', $v);
                    if (strpos($cookie [0], $needle) !== false) {
                        $page_id = substr($cookie [0], strlen($needle));
                        $v_id = $cookie [1];
                    }
                    if (isset($page_id) && $v_id >= 0) {
                        $mo_obj = $this->get_obj_by_type($page_id); //mo_landing_pages::instance($page_id);
                        $conversions = $mo_obj->get_variation_property($v_id, 'conversions');
                        if ($conversions) {
                            $conversions = $conversions + 1;
                            $mo_obj->set_variation_property($v_id, 'conversions', $conversions);
                            $mo_obj->save();
                        } else {
                            $conversions = 1;
                            $mo_obj->set_variation_property($v_id, 'conversions', $conversions);
                            $mo_obj->save();
                        }
                        return wp_send_json(array(
                            'v_id' => $v_id,
                            'post_id' => $page_id,
                            'conversions' => $conversions
                                ));
                    }
                }
            }
        } else {
            
        }
        return;
    }
    /*
     * Flush Rewrite Rules  
     */
    public function mo_flush_rewrite_rules() {
        $activation_check = get_option('mo_lp_plugin_activated', 0);
        if ($activation_check) {
            global $wp_rewrite;
            $wp_rewrite->flush_rules();
            update_option('mo_lp_plugin_activated', 0);
        }
    }
    /*
     * Get post template for template loader  
     */
    public function mo_get_post_template_for_template_loader($template){
        
        global $post, $variation_id;
        if ($post && $post->post_type === $this->get_mo_pt_post_type()) {
                $post_id = $post->ID;
                $mo_obj = $this->get_obj_by_type($post_id);
                $v_id = $variation_id;

                $post_template = $mo_obj->get_variation_property ( $v_id, 'template' );
                if (! empty ( $post_template ) && $post_template != 'default' && file_exists ( get_template_directory () . "/{$post_template}" )) {
                        $template = get_template_directory () . "/{$post_template}";
                } else {
                        $template = get_template_directory () . '/index.php';
                }
        }
        return $template;
    }
    
    /*
     * Get variation content 
     */
    public function mo_get_variation_content($content) {
        global $post, $variation_id;
        $post_id = $post->ID;
        if (get_post_type($post_id) === $this->get_mo_pt_post_type()) {
            $mo_obj = $this->get_obj_by_type($post_id);
            $v_id = $mo_obj->get_current_variation();
            $content = $mo_obj->get_variation_property($v_id, 'content') ? $mo_obj->get_variation_property($v_id, 'content') : '';
        }
        return $content;
    }

    /*
     * Get variation content for editor 
     */
    public function mo_get_variation_content_for_editor($content, $post_id) {
        if (get_post_type($post_id) === $this->get_mo_pt_post_type()) {
            $mo_obj = $this->get_obj_by_type($post_id);
            $v_id = $mo_obj->get_current_variation();

            try {
                $content = $mo_obj->get_variation_property($v_id, 'content');
            } catch (Exception $e) {
                $content = '';
            }
            return $content;
        }
        return $content;
    }

    /*
     * Get Variation edit link  
     */
    public function mo_get_variation_edit_link($link, $id, $context) {
        if (get_post_type($id) === $this->get_mo_pt_post_type()) {
            return $link . '&' . $this->get_mo_pt_short_type() . '_variation_id=0';
        } else {
            return $link;
        }
    }

    /*
     * Get Variation id to display  
     */    
    public function mo_get_variation_id_to_display() {
        if (isset($_POST ['action']) && isset($_POST ['post_id'])) {
            if ($_POST ['action'] == $this->get_mo_pt_short_type() . '_get_variation_id_to_display' && $_POST ['post_id'] > 0) {
                $post_id = $_POST ['post_id'];
                $response_arr = array();
                $mo_obj = $this->get_obj_by_type($post_id);
                $v_id = $mo_obj->get_current_variation();
                if ($v_id !== false) {
                    $response_arr ['v_id'] = $v_id;
                    wp_send_json($response_arr);
                } else {
                    wp_send_json(false);
                }
            }
        }
    }
     /*
     * Get Variation meta title  
     */ 
     public function mo_get_variation_meta_title($title, $sep, $seplocation) {
        global $post, $variation_id;
        if (get_post_type($post->ID) === $this->get_mo_pt_post_type()) {
            $mo_obj = $this->get_obj_by_type($post->ID);
            $v_id = $variation_id;
            try {
                $title = $mo_obj->get_variation_property($v_id, 'title') . ' | ';
            } catch (Exception $e) {
                $title = '';
            }
        }
        return $title;
    }
    /*
     * Get Variation permalink  
     */ 
    public function mo_get_variation_permalink($permalink, $post) {
        global $variation_id;
        if ($post->post_type === $this->get_mo_pt_post_type()) {
            $mo_obj = $this->get_obj_by_type($post->ID);
            $v_id = $variation_id;
            $permalink = $permalink;
        }
        return $permalink;
    }
    /*
     * Get Variation title  
     */ 
    public function mo_get_variation_title($title, $id) {
        global $variation_id, $pagenow;
        if (get_post_type($id) === $this->get_mo_pt_post_type()) {
            $mo_obj = $this->get_obj_by_type($id);
            if ($pagenow != 'edit.php') {
                $v_id = $mo_obj->get_current_variation();
            } else {
                $v_id = 0;
            }

            $title = $mo_obj->get_variation_property($v_id, 'title') ? $mo_obj->get_variation_property($v_id, 'title') : '';
        }
        return $title;
    }
    /*
     * Get Variation title for editor  
     */
    
    public function mo_get_variation_title_for_editor($title, $id) {
        if (get_post_type($id) === $this->get_mo_pt_post_type()) {
            $mo_obj = $this->get_obj_by_type($id);
            $v_id = $mo_obj->get_current_variation();
            $title = $mo_obj->get_variation_property($v_id, 'title') ? $mo_obj->get_variation_property($v_id, 'title') : '';
        }
        return $title;
    }

    /*
     * Sortable Columns 
     */
    public function mo_post_type_register($slug_short,$post_title,$post_title_single,$taxonomy_link,$taxonomy) {
		$slug = get_option ( $this->get_mo_pt_short_type().'_permalink_prefix', $slug_short );
		
		$labels = array (
				'name' => _x ( 'Marketing Optimizer '.$post_title, 'post type general name', mo_plugin::MO_LP_TEXT_DOMAIN ),
				'menu_name' => _x ( $post_title, 'post type general name', mo_plugin::MO_LP_TEXT_DOMAIN ),
				'singular_name' => _x ( 'Marketing Optimizer '.$post_title_single, 'post type singular name', mo_plugin::MO_LP_TEXT_DOMAIN ),
				'add_new' => _x ( 'Add New', $post_title_single, mo_plugin::MO_LP_TEXT_DOMAIN ),
				'add_new_item' => __ ( 'Add New '.$post_title_single, mo_plugin::MO_LP_TEXT_DOMAIN ),
				'edit_item' => __ ( 'Edit '.$post_title_single, mo_plugin::MO_LP_TEXT_DOMAIN ),
				'new_item' => __ ( 'New '.$post_title_single, mo_plugin::MO_LP_TEXT_DOMAIN ),
				'view_item' => __ ( 'View '.$post_title_single, mo_plugin::MO_LP_TEXT_DOMAIN ),
				'search_items' => __ ( 'Search '.$post_title_single, mo_plugin::MO_LP_TEXT_DOMAIN ),
				'not_found' => __ ( 'Nothing found', mo_plugin::MO_LP_TEXT_DOMAIN ),
				'not_found_in_trash' => __ ( 'Nothing found in Trash', mo_plugin::MO_LP_TEXT_DOMAIN ),
				'parent_item_colon' => '' 
		);
		
		$args = array (
				'labels' => $labels,
				'public' => true,
				'publicly_queryable' => true,
				'show_ui' => true,
				'query_var' => true,
				'menu_icon' => plugins_url () . '/' . mo_plugin::MO_DIRECTORY . '/images/moicon.png',
				'rewrite' => array (
						"slug" => "$slug",
						'with_front' => false 
				),
				'capability_type' => 'post',
				'hierarchical' => false,
				'menu_position' => null,
				'supports' => array (
						'title',
						'editor',
						'custom-fields',
						'thumbnail',
						'excerpt',
						'page-attributes' 
				) 
		);
		
		register_post_type ( $this->get_mo_pt_post_type(), $args );
		register_taxonomy ( $taxonomy_link.'_page_cat', $taxonomy, array (
				'hierarchical' => true,
				'label' => "Categories",
				'singular_label' => $post_title_single." Category",
				'show_ui' => true,
				'query_var' => true,
				"rewrite" => true 
		) );
	}
    
    /*
     * Sortable Columns 
     */
    public function mo_sortable_columns() {
        return array(
            'title' => 'title',
            'impressions' => 'impressions',
            'conversions' => 'conversions',
            'cr' => 'cr'
        );
    }
    
    /*
     * Taxonomy filter restrict manage posts
     */
    public function mo_taxonomy_filter_restrict_manage_posts() {
        global $typenow;

        if ($typenow === $this->get_mo_pt_post_type()) {
            $post_types = get_post_types(array(
                '_builtin' => false
                    ));
            if (in_array($typenow, $post_types)) {
                $filters = get_object_taxonomies($typenow);

                foreach ($filters as $tax_slug) {
                    $tax_obj = get_taxonomy($tax_slug);
                    (isset($_GET [$tax_slug])) ? $current = $_GET [$tax_slug] : $current = 0;
                    wp_dropdown_categories(array(
                        'show_option_all' => __('Show All ' . $tax_obj->label),
                        'taxonomy' => $tax_slug,
                        'name' => $tax_obj->name,
                        'orderby' => 'name',
                        'selected' => $current,
                        'hierarchical' => $tax_obj->hierarchical,
                        'show_count' => false,
                        'hide_empty' => true
                    ));
                }
            }
        }
    }
     /*
     * Track visit  
     */

    public function mo_track_visit() {
        $response = false;
        if ($_POST ['action'] === $this->get_mo_pt_short_type().'_track_visit') {
            $post_id = $_POST ['post_id'];
            $v_id = $_POST ['v_id'];
            $mo_obj = $this->get_obj_by_type($post_id);
            $current_visits = $mo_obj->get_variation_property($v_id, 'visitors');
            if ($current_visits) {
                $visits = $current_visits + 1;
                $mo_obj->set_variation_property($v_id, 'visitors', $visits);
                $mo_obj->save();
            } else {
                $visits = 1;
                $mo_obj->set_variation_property($v_id, 'visitors', $visits);
                $mo_obj->save();
            }
        }
        wp_send_json(array(
            'post_id' => $post_id,
            'current_visits' => $current_visits,
            'incremented_visits' => $visits
        ));
    }
    
    /*
     * Track visit  
     */
    
    public function mo_trash_lander($post_id,$group_name) {
        global $post;

        if (!isset($post) || isset($_POST ['split_test']))
            return;

        if ($post->post_type == 'revision') {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE || (isset($_POST ['post_type']) && $_POST ['post_type'] == 'revision')) {
            return;
        }

        if ($post->post_type === $this->get_mo_pt_post_type()) {

            $lp_id = $post->ID;

            $args = array(
                'post_type' => $group_name,
                'post_satus' => 'publish'
            );

            $my_query = null;
            $my_query = new WP_Query($args);

            if ($my_query->have_posts()) {
                $i = 1;
                while ($my_query->have_posts()) :
                    $my_query->the_post();
                    $group_id = get_the_ID();
                    $group_data = get_the_content();
                    $group_data = json_decode($group_data, true);

                    $lp_ids = array();
                    foreach ($group_data as $key => $value) {
                        $lp_ids [] = $key;
                    }

                    if (in_array($lp_id, $lp_ids)) {
                        unset($group_data [$lp_id]);

                        $this_data = json_encode($group_data);
                        $new_post = array(
                            'ID' => $group_id,
                            'post_title' => get_the_title(),
                            'post_content' => $this_data,
                            'post_status' => 'publish',
                            'post_date' => date('Y-m-d H:i:s'),
                            'post_author' => 1,
                            'post_type' => 'landing-page-group'
                        );
                        $post_id = wp_update_post($new_post);
                    }
                endwhile
                ;
            }
        }
    }
    /*
     * Track Impression  
     */
    public function mo_track_impression() {
        if ($this->mo_track_admin_user()) {
            if (isset($_POST ['action']) && $_POST ['action'] === $this->get_mo_pt_short_type() . '_track_impression') {
                
                if (isset($_POST ['post_id']) && $_POST ['post_id']) {
                    $post_id = $_POST ['post_id'];
                    if (isset($_POST ['v_id']) && $_POST ['v_id'] >= 0) {
                        $v_id = $_POST ['v_id'];
                        $mo_obj = $this->get_obj_by_type($post_id);
                        $impressions = $mo_obj->get_variation_property($v_id, 'impressions');
                        
                        if ($impressions) {
                            $impressions = $impressions + 1;
                            $mo_obj->set_variation_property($v_id, 'impressions', $impressions);
                            $mo_obj->save();
                            wp_send_json(array(
                                'impressions' => $impressions
                            ));
                        } else {
                            $impressions = 1;
                            $mo_obj->set_variation_property($v_id, 'impressions', $impressions);
                            $mo_obj->save();
                            wp_send_json(array(
                                'impressions' => $impressions
                            ));
                        }
                    }
                }
            }
        }
    }
    /*
     * Set Variation id  
     */
    public function mo_set_variation_id() {
        global $post, $variation_id;
        if ($post && $post->post_type === $this->get_mo_pt_post_type()) {
            $mo_obj = $this->get_obj_by_type($post->ID);
            $variation_id = $mo_obj->get_current_variation();
        }
    }
    /*
     * Pause variation
     */
    public function mo_pause_variation() {
        if (isset($_GET ['post']) && $_GET ['post']) {
            $post_id = $_GET ['post'];
            $mo_obj = $this->get_obj_by_type($post_id);
            $v_id = $_GET ['v_id'];
            $mo_obj->pause_variation($post_id, $v_id);
        }
        wp_redirect(wp_get_referer());
    }
    
    /*
     * Get value from settings  
     */
    
    public function mo_delete_variation() {
        if (isset($_GET ['post']) && $_GET ['post']) {
            $post_id = $_GET ['post'];
            $mo_obj = $this->get_obj_by_type($post_id);
            $v_id = $_GET ['v_id'];
            $mo_obj->delete_variation($post_id, $v_id);

            $mo_variation_id = $mo_obj->get_variation_property($v_id, 'variation_id');
            if ($mo_variation_id) {
                $this->mo_get_tests_from_api_delete($mo_variation_id);
            }
        }
        wp_redirect(wp_get_referer());
    }
    
    /*
     * Delete using API 
     */
    
    public function mo_get_tests_from_api_delete($postvars){
        if (isset($postvars)) {
            $postDataObj              = new stdClass();
            $postDataObj->id          = $postvars;
            $postData                 = json_encode($postDataObj);
            $mo_api_test = new mo_api_tests();
            $response = $mo_api_test->set_request_type('DELETE')->set_request($postData)->execute();
            return $response;
        }
       
    }
    /*
     * Add shortcodes
     */
    public function mo_add_shortcodes() {
        add_shortcode($this->get_mo_pt_short_type().'_conversion', array(
            $this,
            $this->get_mo_pt_short_type().'_conversion'
        ));
    }
    
    /*
     * Is A/B testing
     */
    public function mo_is_ab_testing() {
        global $post;
        $mo_obj = $this->get_obj_by_type($post->ID);
        $mo_variation_ids_arr = $mo_obj->get_variation_ids_arr();
        if ($post->post_type == 'page') {
            if (count($mo_variation_ids_arr) > 1) {
                return true;
            } else {
                return false;
            }
        }
    }

    /*
     * Get value from settings  
     */
    function mo_track_admin_user() {
        if (current_user_can('manage_options')) {
            if (get_option('mo_lp_track_admin') == 'true') {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }
    /*
     * Website Tracking javascript  
     */
    public function mo_get_mo_website_tracking_js() {
		global $post, $variation_id;
		$mo_settings_obj = new mo_settings ();
		if($mo_settings_obj->get_mo_marketing_optimizer() == true){
			if ($mo_settings_obj->get_mo_account_id ()) {
				if (is_object ( $post ) && $post->post_type === $this->get_mo_pt_post_type()) {
					$mo_obj = $this->get_obj_by_type($post->ID);
                                        if ($mo_settings_obj->get_mo_lp_cache_compatible () == 'false' || isset ( $_GET ['mo_page_variation_id'] ) || isset ( $_GET ['t'] ) || count ( $mo_obj->get_variation_ids_arr () ) > 0) {
						if (is_null ( $variation_id )) {
							$v_id = $mo_obj->get_current_variation ();
						} else {
							$v_id = $variation_id;
						}
						$website_tracking_js = '';
						$website_tracking_js .= "\n<!-- Start of Asynchronous Tracking Code --> \n";
						$website_tracking_js .= "<script type='text/javascript'> \n";
						$website_tracking_js .= "var _apVars = _apVars || []; \n";
						$website_tracking_js .= "_apVars.push(['_trackPageview']); \n";
						$website_tracking_js .= "_apVars.push(['_setAccount','" . $mo_settings_obj->get_mo_account_id () . "']); \n";
							
						if (( int ) $mo_obj->get_variation_property ( $v_id, 'variation_id' ) > 0) {
							$website_tracking_js .= "_apVars.push([ '_trackVariation','" . ( int ) $mo_obj->get_variation_property ( $v_id, 'variation_id' ) . "']); \n";
						}
						if ($mo_settings_obj->get_mo_phone_tracking () == 'true') {
							$website_tracking_js .= "_apVars.push([ '_publishPhoneNumber' ]); \n";
							if ($mo_settings_obj->get_mo_phone_publish_cls ()) {
								$website_tracking_js .= "_apVars.push([ '_setPhonePublishCls', '" . $mo_settings_obj->get_mo_phone_publish_cls () . "' ]); \n";
							} else {
								$website_tracking_js .= "_apVars.push([ '_setPhonePublishCls', 'phonePublishCls' ]); \n";
							}
							if ($mo_settings_obj->get_mo_phone_tracking_default_number ()) {
								$website_tracking_js .= "_apVars.push([ '_setDefaultPhoneNumber', '" . $mo_settings_obj->get_mo_phone_tracking_default_number () . "' ]);\n";
							}
							if ($mo_settings_obj->get_mo_phone_tracking_thank_you_url ()) {
								$website_tracking_js .= "_apVars.push([ '_redirectConversionUrl','" . $mo_settings_obj->get_mo_phone_tracking_thank_you_url () . "']); \n";
							}
                                                        if($mo_settings_obj->get_mo_phone_ctc()){
                                                                $website_tracking_js .= "_apVars.push([ '_phoneMobileCtc', true ]); \n";
                                                        }
						}
						
						$website_tracking_js .= "(function(d){ \n";
						$website_tracking_js .= "var t = d.createElement(\"script\"), s = d.getElementsByTagName(\"script\")[0]; \n";
						$website_tracking_js .= "t.src =  \"//app.marketingoptimizer.com/remote/ap.js\"; \n";
						$website_tracking_js .= "s.parentNode.insertBefore(t, s); \n";
						$website_tracking_js .= "})(document); \n";
						$website_tracking_js .= 'function setVariation(e,t){ if(typeof window._apPostCount!=="undefined"){if(window._apPostCount>0){var n=[];n.push(["_setAccount",e]);n.push(["_trackVariation",t]);_apRegisterVars(n);_apPost(n);return}}setTimeout(setVariation,50)}  setVariation('.$mo_settings_obj->get_mo_account_id ().','.( int ) $mo_obj->get_variation_property ( $v_id, 'variation_id' ).');'."\n";
                                                $website_tracking_js .= "</script> \n";
						$website_tracking_js .= "<!-- End of Asynchronous Tracking Code --> \n";
                                                
                                                
						if (! $mo_obj->mo_bot_detected () || $this->mo_page_track_admin_user ()) {
							echo $website_tracking_js;
						}
					}
				}
			}
		}
	}
        
    
    /*
     * Get Template  
     */    
    public function mo_get_template($template) {
        global $post;
        if ($post->post_type === $this->get_mo_pt_post_type()) {
            $mo_obj = $this->get_obj_by_type($post->ID);
            $v_id = $mo_obj->get_current_variation();
            $mo_lp_template = $mo_obj->get_variation_property($v_id, 'template');
            $template_dir = PLUGINDIR . '/' . mo_plugin::MO_DIRECTORY . '/templates/' . $mo_lp_template;
            if ($mo_lp_template != 'theme') {
                $template = $template_dir . '/template.php';
            } else {
                if ($mo_obj->get_variation_property($v_id, 'theme_template') != 'default') {
                    $template = get_template_directory() . '/' . $mo_obj->get_variation_property($v_id, 'theme_template');
                } else {
                    $template = get_template_directory() . '/index.php';
                }
            }
        }
        return $template;
    }
    /*
     * Change post type
     */
    public function mo_change_post_type() {
		global $wpdb;
		$post_id = $_POST ['post_id'];
		preg_match ( "/\[(.*?)\]/", $_POST ['post_type'], $matches );
		$post_type = $matches [1];
		$post_meta_arr = $wpdb->get_results ( 'SELECT post_id FROM wp_postmeta WHERE meta_key = \''.$this->get_mo_pt_short_type().'_post_types\' AND post_id != ' . $post_id );
		foreach ( $post_meta_arr as $v ) {
                    $post_types_arr = json_decode ( get_post_meta ( $v->post_id, 'mo_sp_post_types', true ) );
                    if (isset ( $post_types_arr->$post_type ) && $post_types_arr->$post_type) {
                            $post_types_arr->$post_type = 0;
                    }
		}
		update_post_meta ( $v->post_id, $this->get_mo_pt_short_type().'_post_types', json_encode ( $post_types_arr ) );
		wp_send_json ( 'true' );
	}

    /*
     * Get post type object  
     */
    public function get_obj($post){
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
    /*
     * Get post type object by id  
     */
    public function get_obj_by_type($post_id){
        if ($this->get_mo_pt_short_type() === "mo_lp") {
            $mo_obj = mo_landing_pages::instance($post_id);
        }else if ($this->get_mo_pt_short_type() === "mo_ct") { 
            $mo_obj = mo_callto_action::instance($post_id);
        }else if ($this->get_mo_pt_short_type() === "mo_sp") { 
            $mo_obj = mo_squeeze_pages::instance ( $post_id );
        }else if ($this->get_mo_pt_short_type() === "page" || $this->get_mo_pt_short_type() === "mo_page") { 
            $mo_obj = mo_pages::instance($post_id);
        }
        return $mo_obj;
    }

}
