<?php
class mo_lp_post_type extends mo_post_type {
	public function __construct() {
                $short_type = 'mo_lp';
                $post_type = 'mo_landing_page';
                $api_post_type = 'landing_page';
                parent::__construct ( $short_type,$post_type,$api_post_type );
                
		add_action ( 'admin_init', array (
				$this,
				'mo_lp_flush_rewrite_rules' 
		) );
		add_action ( 'wp', array (
				$this,
				'mo_lp_set_variation_id' 
		) );
		add_action ( 'init', array (
				$this,
				'mo_lp_add_shortcodes' 
		) );
		// add admin actions
		add_action ( 'init', array (
				$this,
				'mo_lp_post_type_register' 
		) );
		if (is_admin ()) {
			
			add_action ( 'init', array (
					$this,
					'mo_lp_category_register_taxonomy' 
			) );
			add_action ( 'wp_trash_post', array (
					$this,
					'mo_lp_trash_lander' 
			) );
			add_filter ( "manage_edit-mo_landing_page_columns", array (
					$this,
					'mo_lp_columns' 
			) );
			add_action ( "manage_mo_landing_page_posts_custom_column", array (
					$this,
					"mo_lp_column" 
			) );
			add_action ( 'admin_action_mo_lp_clear_stats', array (
					$this,
					'mo_lp_clear_stats' 
			) );
			add_action ( 'admin_action_mo_lp_pause_variation', array (
					$this,
					'mo_lp_pause_variation' 
			) );
			add_action ( 'admin_action_mo_lp_delete_variation', array (
					$this,
					'mo_lp_delete_variation' 
			) );
			
			// add admin filters
			add_filter ( 'post_row_actions', array (
					$this,
					'mo_lp_add_clear_tracking' 
			), 10, 2 );
			add_filter ( 'content_edit_pre', array (
					$this,
					'mo_lp_get_variation_content_for_editor' 
			), 10, 2 );
			add_filter ( 'manage_edit-mo_landing_page_sortable_columns', array (
					$this,
					'mo_lp_sortable_columns' 
			) );
			add_filter ( 'title_edit_pre', array (
					$this,
					'mo_lp_get_variation_title_for_editor' 
			), 10, 2 );
			add_filter ( 'get_edit_post_link', array (
					$this,
					'mo_lp_get_variation_edit_link' 
			), 10, 3 );
		}
		
		add_action ( 'wp_ajax_mo_lp_get_variation_id_to_display', array (
				$this,
				'mo_lp_get_variation_id_to_display' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_lp_get_variation_id_to_display', array (
				$this,
				'mo_lp_get_variation_id_to_display' 
		) );
		add_action ( 'wp_footer', array (
				$this,
				'mo_lp_add_variation_cookie_js' 
		) );
		add_action ( 'wp_footer', array (
                                $this,
                                'mo_lp_get_mo_website_tracking_js'
				) );
		add_action ( 'wp_ajax_mo_lp_track_impression', array (
				$this,
				'mo_lp_track_impression' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_lp_track_impression', array (
				$this,
				'mo_lp_track_impression' 
		) );
		add_action ( 'wp_ajax_mo_lp_track_visit', array (
				$this,
				'mo_lp_track_visit' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_lp_track_visit', array (
				$this,
				'mo_lp_track_visit' 
		) );
		add_action ( 'wp_ajax_mo_lp_track_conversion', array (
				$this,
				'mo_lp_track_conversion' 
		) );
		add_action ( 'wp_ajax_mo_lp_get_template_content', array (
				$this,
				'mo_lp_get_template_content' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_lp_track_conversion', array (
				$this,
				'mo_lp_track_conversion' 
		) );
                
                add_filter ( 'the_content', array (
				$this,
				'mo_lp_get_variation_content' 
		), 10 );
                
		add_filter ( 'wp_title', array (
				$this,
				'mo_lp_get_variation_meta_title' 
		), 10, 3 );
		add_filter ( 'template_include', array (
				$this,
				'mo_lp_get_post_template_for_template_loader' 
		) );
		add_filter ( 'post_type_link', array (
				$this,
				"mo_lp_get_variation_permalink" 
		), 10, 2 );
		add_filter ( 'the_title', array (
				$this,
				'mo_lp_get_variation_title' 
		), 10, 2 );
		if (get_option ( 'mo_lp_cache_compatible' ) == 'true' && ! isset ( $_GET ['mo_page_variation_id'] ) && ! isset ( $_GET ['t'] )) {
			add_action ( 'wp_head', array (
                                      $this,
                                       'mo_lp_get_cache_compatible_js' 
			) );
		}
		add_filter ( 'template_include', array (
				$this,
				'mo_lp_get_template' 
		) );
	}
	
	public function mo_lp_add_clear_tracking($actions, $post) {
               return  $this->mo_add_clear_tracking($actions, $post);
        }

	public function mo_lp_add_variation_cookie_js() {
		$this->mo_add_variation_cookie_js();
	}

	public function mo_lp_category_register_taxonomy() {
            $this->mo_category_register_taxonomy("MO Landing Page Category");
	}

	public function mo_lp_clear_stats() {
            $this->mo_clear_stats();
        }
	
	public function mo_lp_column($column) {
            $this->mo_column($column);
	}

	public function mo_lp_columns($columns) {
            return $this->mo_columns($columns,"Landing Page Title");
	}

	public function mo_lp_conversion() {
            $this->mo_conversion();
	}

	public function mo_lp_flush_rewrite_rules() {
                $this->mo_flush_rewrite_rules();
	}

	public function mo_lp_get_post_template_for_template_loader($template) {
            return $this->mo_get_post_template_for_template_loader($template);
	}

	public function mo_lp_get_variation_content($content) {
            return $this->mo_get_variation_content($content);
        }

	public function mo_lp_get_variation_content_for_editor($content, $post_id) {
            return $this->mo_get_variation_content_for_editor($content, $post_id);
        }

	public function mo_lp_get_variation_edit_link($link, $id, $context) {
            return $this->mo_get_variation_edit_link($link, $id, $context);
	}

	public function mo_lp_get_variation_id_to_display() {
            $this->mo_get_variation_id_to_display();
	}

	public function mo_lp_get_variation_meta_title($title, $sep, $seplocation) {
            return $this->mo_get_variation_meta_title($title, $sep, $seplocation);
	}

	public function mo_lp_get_variation_permalink($permalink, $post) {
            return $this->mo_get_variation_permalink($permalink, $post);
	}

	public function mo_lp_get_variation_title($title, $id) {
            return $this->mo_get_variation_title($title, $id);
	}

	public function mo_lp_get_variation_title_for_editor($title, $id) {
            return $this->mo_get_variation_title_for_editor($title, $id);
         }

	public function mo_lp_post_type_register() {
            $slug_short = "molp";
            $post_title = "Landing Pages";
            $post_title_single = "Landing Page";
            $taxonomy_link = "mo_landing_page";
            $taxonomy = "mo_landing_page-page";
            $this->mo_post_type_register($slug_short,$post_title,$post_title_single,$taxonomy_link,$taxonomy);
        }

	
	public function mo_lp_sortable_columns() {
            return $this->mo_sortable_columns();
	}
	
	public function mo_lp_taxonomy_filter_restrict_manage_posts() {
            $this->mo_taxonomy_filter_restrict_manage_posts();
	}

	public function mo_lp_track_visit() {
            $this->mo_track_visit();
	}
	
	
	public function mo_lp_trash_lander($post_id) {
            $this->mo_trash_lander($post_id,'landing-page-group');
        }

	public function mo_lp_track_conversion() {
            return $this->mo_track_conversion();
	}

	public function mo_lp_track_impression() {
            $this->mo_track_impression();
	}

	public function mo_lp_set_variation_id() {
            $this->mo_set_variation_id();
        }

	public function mo_lp_pause_variation() {
            $this->mo_pause_variation();
	}

	public function mo_lp_delete_variation() {
            $this->mo_delete_variation();
	}
	
	
	public function mo_lp_add_shortcodes() {
            $this->mo_add_shortcodes();
	}

	public function mo_lp_is_ab_testing() {
            return $this->mo_is_ab_testing();
		
	}

	public function mo_lp_get_cache_compatible_js() {
           
        global $post;
        $mo_lp_obj = mo_landing_pages::instance($post->ID);
        
        if ($post->post_type === 'mo_landing_page'){
            define( 'DONOTCACHEPAGE', true );
        }
        
        if ($post->post_type == 'mo_landing_page' && $mo_lp_obj->mo_is_testing() && !$mo_lp_obj->mo_bot_detected() && defined('DOING_AJAX') && DOING_AJAX && (!isset($_GET ['mo_lp_variation_id']) || !isset($_GET ['t']))) {
            define( 'DONOTCACHEPAGE', true );
            echo '<script type="text/javascript">
                        function mo_lp_get_variation_cookie() {
                                var cookies = document.cookie.split(/;\s*/);
                                for ( var i = 0; i < cookies.length; i++) {
                                        var cookie = cookies[i];
                                        var control = ' . $post->ID . ';
                                        if (control > 0 && cookie.indexOf("mo_lp_variation_" + control) != -1) {
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
                        variation_id = mo_lp_get_variation_cookie();

                        if (isIE()) {
                                if (variation_id != null) {
                                        window.location =  url[0] + "?mo_lp_variation_id=" + mo_lp_get_variation_cookie()+params;
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
                                        xmlhttp.open("GET", url[0] + "?mo_lp_variation_id=" +  mo_lp_get_variation_cookie()+params, true);
                                } else {
                                        xmlhttp.open("GET", url[0] + "?t=" + new Date().getTime()+params, true);
                                }
                                xmlhttp.send();
                        }
                  </script>';
        }
    }

        public function mo_lp_get_template_content() {
		$response_arr = array ();
		if (isset ( $_POST ['template'] ) && $_POST ['template']) {
			$template_name = $_POST ['template'];
		}
		if ($template_name != 'theme') {
			$template_dir = site_url () . '/' . PLUGINDIR . '/' . mo_plugin::MO_DIRECTORY . '/templates/' . $template_name;
			$template = @file_get_contents ( $template_dir . '/' . $template_name . '.php' );
			
			if (! $template) {
				$template = $this->mo_get_template_via_curl ( $template_dir );
				
				if (! $template) {
					$template = @file_get_contents ( ABSPATH . PLUGINDIR . '/' . mo_plugin::MO_DIRECTORY . '/templates/' . $template_name . '/' . $template_name . '.php' );
					if (! $template) {
						$template = 'Failed to load selected template';
					}
				}
			}
			$templates_arr = mo_sp_get_templates ();
                        $response_arr ['content'] =  $template;
			if (isset($_POST['type']) && $_POST['type'] == 'mo_sp') {
				$response_arr ['modal_height'] = $templates_arr [$template_name] ['height'];
				$response_arr ['modal_width'] = $templates_arr [$template_name] ['width'];
			}
			wp_send_json ( $response_arr );
		} else {
			die ();
		}
	}

	public function mo_get_template_via_curl($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $template = curl_exec($ch);
            curl_close($ch);
            return $template;
        }

        public function mo_lp_get_mo_website_tracking_js() {
            $this->mo_get_mo_website_tracking_js();
	}

	public function mo_lp_get_template($template) {
            return $this->mo_get_template($template);
	}
}
$mo_lp_post_type_obj = new mo_lp_post_type ();