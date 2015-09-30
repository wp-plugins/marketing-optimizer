<?php

class mo_page_post_type extends mo_post_type
{

    public function __construct()
    {
        $short_type = 'mo_page';
        $post_type = 'page';
        $api_post_type = 'website_page';
        parent::__construct($short_type, $post_type, $api_post_type);
        
        add_action('init', array(
            $this,
            'mo_page_add_shortcodes'
        ));
        add_action('wp_footer', array(
            $this,
            'mo_page_add_variation_cookie_js'
        ));
        add_action('wp_ajax_mo_page_get_variation_id_to_display', array(
            $this,
            'mo_page_get_variation_id_to_display'
        ));
        add_action('wp_ajax_nopriv_mo_page_get_variation_id_to_display', array(
            $this,
            'mo_page_get_variation_id_to_display'
        ));
        add_action('wp_ajax_mo_page_track_visit', array(
            $this,
            'mo_page_track_visit'
        ));
        add_action('wp_ajax_nopriv_mo_page_track_visit', array(
            $this,
            'mo_page_track_visit'
        ));
        add_action('wp_ajax_mo_page_track_conversion', array(
            $this,
            'mo_page_track_conversion'
        ));
        add_action('wp_ajax_nopriv_mo_page_track_conversion', array(
            $this,
            'mo_page_track_conversion'
        ));
        add_action('wp_ajax_mo_page_track_impression', array(
            $this,
            'mo_page_track_impression'
        ));
        add_action('wp_ajax_nopriv_mo_page_track_impression', array(
            $this,
            'mo_page_track_impression'
        ));
        
        add_filter('manage_pages_columns', array(
            $this,
            'mo_page_columns'
        ));
        add_filter('manage_pages_columns', array(
            $this,
            'mo_page_sortable_columns'
        ));
        add_filter('title_edit_pre', array(
            $this,
            'mo_page_get_variation_title_for_editor'
        ), 10, 2);
        add_action('wp', array(
            $this,
            'mo_page_set_variation_id'
        ));
        add_filter('content_edit_pre', array(
            $this,
            'mo_page_get_variation_content_for_editor'
        ), 10, 2);
        add_filter('the_content', array(
            $this,
            'mo_page_get_variation_content'
        ), 10);
        add_action("manage_pages_custom_column", array(
            $this,
            "mo_page_column"
        ));
        add_filter('wp_title', array(
            $this,
            'mo_page_get_variation_meta_title'
        ), 10, 3);
        add_filter('the_title', array(
            $this,
            'mo_page_get_variation_title'
        ), 10, 2);
        add_action('wp_footer', array(
            $this,
            'mo_page_get_mo_website_tracking_js'
        ));
        add_action('admin_action_mo_page_pause_variation', array(
            $this,
            'mo_page_pause_variation'
        ));
        add_action('admin_action_mo_page_delete_variation', array(
            $this,
            'mo_page_delete_variation'
        ));
        add_filter('page_row_actions', array(
            $this,
            'mo_page_add_clear_tracking'
        ), 10, 2);
        add_action('admin_action_mo_page_clear_stats', array(
            $this,
            'mo_page_clear_stats'
        ));
        if (get_option('mo_lp_cache_compatible') == 'true' && ! isset($_GET['mo_page_variation_id']) && ! isset($_GET['t'])) {
            add_action('wp_head', array(
                $this,
                'mo_page_get_cache_compatible_js'
            ));
        }
        add_filter('get_edit_post_link', array(
            $this,
            'mo_page_get_variation_edit_link'
        ), 10, 3);
    }

    public function mo_page_column($column)
    {
        $this->mo_column($column);
    }

    public function mo_page_sortable_columns($columns)
    {
        return $this->mo_columns($columns, "Page Title");
    }

    function mo_page_columns($columns)
    {
        $columns = $this->insert_before_key($columns, 'author', 'stats', __("Variation Testing Stats", mo_plugin::MO_LP_TEXT_DOMAIN));
        return $columns;
    }

    function insert_before_key($original_array, $original_key, $insert_key, $insert_value)
    {
        $new_array = array();
        $inserted = false;
        
        foreach ($original_array as $key => $value) {
            
            if (! $inserted && $key === $original_key) {
                $new_array[$insert_key] = $insert_value;
                $inserted = true;
            }
            $new_array[$key] = $value;
        }
        
        return $new_array;
    }

    public function mo_page_add_variation_cookie_js()
    {
        global $post;
        if (is_object($post) && $post->post_type == 'page') {
            $mo_page_obj = mo_pages::instance($post->ID);
            $variation_id = $mo_page_obj->get_current_variation();
            $mo_settings_obj = new mo_settings();
            if ($mo_settings_obj->get_mo_lp_cache_compatible() == 'false' || isset($_GET['mo_page_variation_id']) || isset($_GET['t']) || count($mo_page_obj->get_variation_ids_arr()) >= 1) {
                if (($post->post_type == 'page' || is_home() || is_front_page()) && $this->mo_track_admin_user() && ! $mo_page_obj->mo_bot_detected()) {
                    $variation_id = $variation_id ? $variation_id : 0;
                    echo '<script>
					window.onload = function(){
                                                function mo_page_get_variation_cookie() {
                                                        var cookies = document.cookie.split(/;\s*/);
                                                        for ( var i = 0; i < cookies.length; i++) {
                                                                var cookie = cookies[i];
                                                                cookie = cookie.split("=", 2);
                                                                var control = ' . $post->ID . ';
                                                                if (control > 0	&& cookie[0] == "mo_page_variation_" + control) {
                                                                        return cookie[1];
                                                                }
                                                        }
                                                        return null;
                                                }
                                                function mo_page_set_variation_cookie(name, value, days) {
                                                            if (days) {
                                                                var date = new Date();
                                                                date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
                                                                var expires = "; expires=" + date.toGMTString();
                                                            } else var expires = "";
                                                            document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
                                                }
                                                function mo_page_track_impression(){
                                                            xmlhttp = new XMLHttpRequest();
                                                            xmlhttp.open("POST","' . admin_url('admin-ajax.php') . '" ,true);
                                                            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                                                            xmlhttp.send("action=mo_page_track_impression&post_id=' . $post->ID . '&v_id="+mo_page_get_variation_cookie());
                                                            xmlhttp.onreadystatechange = function () {
                                                            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                                               var response  = xmlhttp.responseText;
                                                            }

						}
					}
                                            function mo_page_track_visit(v_id){
                                                                            xmlhttp = new XMLHttpRequest();
                                                                            xmlhttp.open("POST","' . admin_url('admin-ajax.php') . '" ,true);
                                                                            xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                                                                            xmlhttp.send("action=mo_page_track_visit&post_id=' . $post->ID . '&v_id=' . $variation_id . '");
                                                                                                                    xmlhttp.onreadystatechange = function () {
                                                                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                                                       var response  = xmlhttp.responseText;
                                                                    }

                                                    }
                                            }
                                            function mo_page_get_variation_id_to_display(){
						xmlhttp = new XMLHttpRequest();
                                                xmlhttp.open("POST","' . admin_url('admin-ajax.php') . '" ,true);
                                                xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
                                                xmlhttp.send("action=mo_page_get_variation_id_to_display&post_id=' . $post->ID . '");
                                                xmlhttp.onreadystatechange = function () {
                                                    if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                                        var response  = xmlhttp.responseText;
                                                         var json_response = JSON.parse(response);
                                                         variation_id = json_response.v_id;
                                                         mo_page_set_variation_cookie("mo_page_variation_' . $post->ID . '",' . $variation_id . ',365);
                                                         mo_page_track_impression();
                                                         mo_page_track_visit(' . $variation_id . ');
                                                     }
                                            }
					}											
	
					';
                    if ($mo_page_obj->mo_is_testing()) {
                        echo ' if(mo_page_get_variation_cookie() == null){
					 mo_page_get_variation_id_to_display();
				}else{
				         mo_page_track_impression();
				}
			      }
			</script>';
                    } else {
                        echo '  if(mo_page_get_variation_cookie() == null){
                                    mo_page_set_variation_cookie("mo_page_variation_' . $post->ID . '",' . $variation_id . ',365);
                                    mo_page_track_impression();
                                    mo_page_track_visit(' . $variation_id . ');
                                }else{
					mo_page_track_impression();
				}
                            }
			</script>';
                    }
                }
            }
        }
    }

    public function mo_page_get_variation_id_to_display()
    {
        if (isset($_POST['action']) && isset($_POST['post_id'])) {
            if ($_POST['action'] == 'mo_page_get_variation_id_to_display' && $_POST['post_id'] > 0) {
                $post_id = $_POST['post_id'];
                $response_arr = array();
                $mo_page_obj = mo_pages::instance($post_id);
                $v_id = $mo_page_obj->get_current_variation();
                
                $variationObj = new mo_variation($post_id, $v_id);
                if (! $variationObj->get_status()) {
                    $v_id = 0;
                }
                if ($variationObj->get_status()) {}
                if ($v_id !== false) {
                    $response_arr['v_id'] = $v_id;
                    wp_send_json($response_arr);
                } else {
                    wp_send_json(false);
                }
            }
        }
    }

    public function mo_page_track_impression()
    {
        $this->mo_track_impression();
    }

    public function mo_page_track_conversion()
    {
        if (isset($_POST['cookie']) && $_POST['cookie']) {
            $cookieArr = json_decode(stripslashes($_POST['cookie']));
            
            if (! empty($cookieArr)) {
                $needlesArr = array(
                    'mo_page_variation_',
                    'mo_lp_variation_',
                    'mo_sp_variation_',
                    'mo_ct_variation_'
                );
                foreach ($needlesArr as $needle) {
                    switch ($needle) {
                        case 'mo_page_variation_':
                            foreach ($cookieArr as $v) {
                                $cookie = explode('=', $v);
                                if (strpos($cookie[0], $needle) !== false) {
                                    $post_id = substr($cookie[0], strlen($needle));
                                    $v_id = $cookie[1];
                                    
                                    if (isset($post_id) && $v_id >= 0) {
                                        $mo_page_obj = mo_pages::instance($post_id);
                                        $conversions = $mo_page_obj->get_variation_property($v_id, 'conversions');
                                        if ($conversions) {
                                            $conversions = $conversions + 1;
                                            $mo_page_obj->set_variation_property($v_id, 'conversions', $conversions);
                                            $mo_page_obj->save();
                                        } else {
                                            $conversions = 1;
                                            $mo_page_obj->set_variation_property($v_id, 'conversions', $conversions);
                                            $mo_page_obj->save();
                                        }
                                    }
                                }
                            }
                            break;
                        case 'mo_lp_variation_':
                            foreach ($cookieArr as $v) {
                                $cookie = explode('=', $v);
                                if (strpos($cookie[0], $needle) !== false) {
                                    $post_id = substr($cookie[0], strlen($needle));
                                    $v_id = (int) $cookie[1];
                                    
                                    if (isset($post_id) && $v_id >= 0) {
                                        $mo_landing_page_obj = mo_landing_pages::instance($post_id);
                                        $conversions = $mo_landing_page_obj->get_variation_property($v_id, 'conversions');
                                        if ($conversions) {
                                            $conversions = $conversions + 1;
                                            $mo_landing_page_obj->set_variation_property($v_id, 'conversions', $conversions);
                                            $mo_landing_page_obj->save();
                                        } else {
                                            $conversions = 1;
                                            $mo_landing_page_obj->set_variation_property($v_id, 'conversions', $conversions);
                                            $mo_landing_page_obj->save();
                                        }
                                    }
                                }
                            }
                            break;
                        case 'mo_sp_variation_':
                            foreach ($cookieArr as $v) {
                                $cookie = explode('=', $v);
                                if (strpos($cookie[0], $needle) !== false) {
                                    $post_id = substr($cookie[0], strlen($needle));
                                    $v_id = $cookie[1];
                                    
                                    if (isset($post_id) && $v_id >= 0) {
                                        $mo_squeeze_page_obj = mo_squeeze_pages::instance($post_id);
                                        $conversions = $mo_squeeze_page_obj->get_variation_property($v_id, 'conversions');
                                        if ($conversions) {
                                            $conversions = $conversions + 1;
                                            $mo_squeeze_page_obj->set_variation_property($v_id, 'conversions', $conversions);
                                            $mo_squeeze_page_obj->save();
                                        } else {
                                            $conversions = 1;
                                            $mo_squeeze_page_obj->set_variation_property($v_id, 'conversions', $conversions);
                                            $mo_squeeze_page_obj->save();
                                        }
                                    }
                                }
                            }
                            break;
                        case 'mo_ct_variation_':
                            foreach ($cookieArr as $v) {
                                $cookie = explode('=', $v);
                                if (strpos($cookie[0], $needle) !== false) {
                                    $post_id = substr($cookie[0], strlen($needle));
                                    $v_id = $cookie[1];
                                    
                                    if (isset($post_id) && $v_id >= 0) {
                                        $mo_ct_obj = mo_callto_action::instance($post_id);
                                        $conversions = $mo_ct_obj->get_variation_property($v_id, 'conversions');
                                        if ($conversions) {
                                            $conversions = $conversions + 1;
                                            $mo_ct_obj->set_variation_property($v_id, 'conversions', $conversions);
                                            $mo_ct_obj->save();
                                        } else {
                                            $conversions = 1;
                                            $mo_ct_obj->set_variation_property($v_id, 'conversions', $conversions);
                                            $mo_ct_obj->save();
                                        }
                                    }
                                }
                            }
                            break;
                    }
                }
            }
        } else {}
        return;
    }

    public function mo_page_track_visit()
    {
        $this->mo_track_visit();
    }

    public function mo_page_get_variation_title_for_editor($title, $id)
    {
        global $pagenow;
        if (get_post_type($id) == 'page') {
            $mo_page_obj = mo_pages::instance($id);
            $v_id = $mo_page_obj->get_current_variation();
            if ($pagenow != 'edit.php' && (int) $v_id !== 0) {
                $title = $mo_page_obj->get_variation_property($v_id, 'title') ? $mo_page_obj->get_variation_property($v_id, 'title') : '';
            }
        }
        return $title;
    }

    public function mo_page_set_variation_id()
    {
        $this->mo_set_variation_id();
    }

    public function mo_page_get_variation_content_for_editor($content, $post_id)
    {
        return $this->mo_get_variation_content_for_editor($content, $post_id);
    }

    public function mo_page_get_variation_content($content)
    {
        global $post, $variation_id;
        $post_id = $post->ID;
        if (get_post_type($post_id) == 'page') {
            
            $mo_page_obj = mo_pages::instance($post_id);
            
            if (is_object($mo_page_obj)) {
                
                if (is_object($mo_page_obj)) {
                    if (is_null($variation_id)) {
                        $v_id = $mo_page_obj->get_current_variation();
                    } else {
                        $v_id = $variation_id;
                    }
                    
                    if ((int) $v_id !== 0) {
                        $content = $mo_page_obj->get_variation_property($v_id, 'content') ? $mo_page_obj->get_variation_property($v_id, 'content') : '';
                    }
                }
            }
        }
        return $content;
    }

    public function mo_page_get_variation_meta_title($title, $sep, $seplocation)
    {
        global $post, $variation_id;
        if (isset($post) && (get_post_type($post->ID) == 'page')) {
            $mo_page_obj = mo_pages::instance($post->ID);
            $v_id = $variation_id;
            
            if ($v_id != 0) {
                $title = $mo_page_obj->get_variation_property($v_id, 'title') ? $mo_page_obj->get_variation_property($v_id, 'title') : '';
            }
        }
        return $title;
    }

    public function mo_page_get_variation_title($title, $id)
    {
        global $variation_id, $pagenow;
        if (get_post_type($id) == 'page') {
            if ($pagenow != 'edit.php') {
                $mo_page_obj = mo_pages::instance($id);
                $v_id = $mo_page_obj->get_current_variation();
            } else {
                $v_id = 0;
            }
            if ((int) $v_id !== 0) {
                $title = $mo_page_obj->get_variation_property($v_id, 'title') ? $mo_page_obj->get_variation_property($v_id, 'title') : '';
            }
        }
        return $title;
    }

    public function mo_page_get_mo_website_tracking_js()
    {
        $this->mo_get_mo_website_tracking_js();
    }

    public function mo_page_pause_variation()
    {
        $this->mo_pause_variation();
    }

    public function mo_page_delete_variation()
    {
        $this->mo_delete_variation();
    }

    public function mo_get_tests_from_api_delete($id)
    {
        if (isset($id)) {
            $mo_api_tests = new mo_api_tests($id);
            $mo_api_tests->set_request_type('DELETE')->execute();
            $response = $mo_api_tests->get_response();
            return $response;
        }
    }

    public function mo_page_is_ab_testing()
    {
        return $this->mo_is_ab_testing();
    }

    public function mo_page_get_cache_compatible_js()
    {
        global $post;
        $mo_page_obj = mo_pages::instance($post->ID);
        if ($post->post_type === 'page') {
            define('DONOTCACHEPAGE', true);
        }
        if ($post->post_type == 'page' && $mo_page_obj->mo_is_testing() && ! $mo_page_obj->mo_bot_detected() && (! isset($_GET['mo_page_variation_id']) || ! isset($_GET['t']) || $mo_page_obj->get_current_variation() == 0)) {
            echo '<script type="text/javascript">
                        function mo_page_get_variation_cookie() {
                                var cookies = document.cookie.split(/;\s*/);
                                for ( var i = 0; i < cookies.length; i++) {
                                        var cookie = cookies[i];
                                        var control = ' . $post->ID . ';
                                        if (control > 0
                                                        && cookie.indexOf("mo_page_variation_" + control) != -1) {
                                                cookie = cookie.split("=", 2);
                                                return cookie[1];
                                        }
                                }
                                return null;
			}
                        function isIE() { 
                                return ((navigator.appName == \'Microsoft Internet Explorer\') || ((navigator.appName == \'Netscape\') && (new RegExp("Trident/.*rv:([0-9]{1,}[\.0-9]{0,})").exec(navigator.userAgent) != null)));
                        }
                        var url = window.location.href;
                        var params = "";
                        url = url.split("?");
                        if(!url[1]){
                                params = "";
                        }else{
                                params = "&"+url[1];
                        }
                        variation_id = mo_page_get_variation_cookie();
                        if(variation_id != 0){
                        if (isIE()) {
                                if (variation_id != null) {
                                    window.location =  url[0] + "?mo_page_variation_id=" + mo_page_get_variation_cookie()+params;
                                } else {
                                 window.location = url[0] + "?t=" + new Date().getTime()+params;
                                }
                        } else {
                            xmlhttp = new XMLHttpRequest();
                            xmlhttp.onreadystatechange = function () {
                                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                    var newDoc = document.open("text/html", "replace");
                                    newDoc.write(xmlhttp.responseText);
                                    newDoc.close();
                                }
                            }
                            if (variation_id != null) {
                                xmlhttp.open("GET", url[0] + "?mo_page_variation_id=" +  mo_page_get_variation_cookie()+params, true);
                            } else {
                                xmlhttp.open("GET", url[0] + "?t=" + new Date().getTime()+params, true);
                            }
                            xmlhttp.send();
                            }
                        }		
                    </script>';
        }
    }

    public function mo_page_add_clear_tracking($actions, $post)
    {
        return $this->mo_add_clear_tracking($actions, $post);
    }

    public function mo_page_clear_stats()
    {
        $this->mo_clear_stats();
    }

    public function mo_page_add_shortcodes()
    {
        $this->mo_add_shortcodes();
        add_shortcode('mo_conversion', array(
            $this,
            'mo_page_conversion'
        ));
        add_shortcode('mo_phone', array(
            $this,
            'mo_phone_shortcode'
        ));
        add_shortcode('aim_phone', array(
            $this,
            'mo_phone_shortcode'
        ));
        add_shortcode('mo_form', array(
            $this,
            'mo_form_shortcode'
        ));
    }

    public function mo_page_conversion()
    {
        $this->mo_conversion_page();
    }

    public function mo_page_get_variation_edit_link($link, $id, $context)
    {
        return $this->mo_get_variation_edit_link($link, $id, $context);
    }

    function mo_phone_shortcode($attributes, $content = null)
    {
        $mo_settings_obj = new mo_settings();
        if ($mo_settings_obj->get_mo_phone_tracking() == 'true') {
            $defaultPhone = $mo_settings_obj->get_mo_phone_tracking_default_number() ? $mo_settings_obj->get_mo_phone_tracking_default_number() : '';
            if ($mo_settings_obj->get_mo_phone_publish_cls()) {
                $class = get_option('mo_phone_publish_cls');
                return "<span class=\"$class\">$defaultPhone</span>";
            } else {
                return '<span class="phonePublishCls">' . $defaultPhone . '</span>';
            }
        } else {
            return '<span style="color:red;">(Phone tracking is currently disabled, enable phone tracking <a href="/wp-admin/admin.php?page=marketing-optimizer-settings">here</a> to use phone tracking short codes.)';
        }
    }

    function mo_form_shortcode($attributes, $content = null)
    {
        if (isset($attributes['id'])) {
            return '<script type="text/javascript" src="//app.marketingoptimizer.com/remote/ap_js.php?f=' . $attributes['id'] . '&o=' . get_option('mo_account_id') . '"></script>';
        } elseif (get_option('mo_form_default_id')) {
            return '<script type="text/javascript" src="//app.marketingoptimizer.com/remote/ap_js.php?f=' . get_option('mo_form_default_id') . '&o=' . get_option('mo_account_id') . '"></script>';
        }
    }
}
$mo_page_post_type_obj = new mo_page_post_type();