<?php
class mo_sp_post_type extends mo_post_type { 
	
        public function __construct() {
		$short_type = 'mo_sp';
                $post_type = 'mo_sp';
                $api_post_type = 'squeeze_page';
                parent::__construct ( $short_type,$post_type,$api_post_type );
                add_action ( 'admin_init', array (
				$this,
				'mo_sp_flush_rewrite_rules' 
		) );
		add_action ( 'wp', array (
				$this,
				'mo_sp_set_variation_id' 
		) );
		add_action ( 'init', array (
				$this,
				'mo_sp_add_shortcodes' 
		) );
		// add admin actions
		add_action ( 'init', array (
				$this,
				'mo_sp_post_type_register' 
		) );
		add_action ( 'wp_footer', array (
				$this,
				'mo_sp_get_mo_website_tracking_js' 
		) );
		if (is_admin ()) {
			
			add_action ( 'init', array (
					$this,
					'mo_sp_category_register_taxonomy' 
			) );
			add_action ( 'wp_trash_post', array (
					$this,
					'mo_sp_trash_lander' 
			) );
			add_filter ( "manage_edit-mo_sp_columns", array (
					$this,
					'mo_sp_columns' 
			) );
			add_action ( "manage_mo_sp_posts_custom_column", array (
					$this,
					"mo_sp_column" 
			) );
			add_action ( 'admin_action_mo_sp_clear_stats', array (
					$this,
					'mo_sp_clear_stats' 
			) );
			add_action ( 'admin_action_mo_sp_pause_variation', array (
					$this,
					'mo_sp_pause_variation' 
			) );
			add_action ( 'admin_action_mo_sp_delete_variation', array (
					$this,
					'mo_sp_delete_variation' 
			) );
			
			// add admin filters
			add_filter ( 'post_row_actions', array (
					$this,
					'mo_sp_add_clear_tracking' 
			), 10, 2 );
			add_filter ( 'content_edit_pre', array (
					$this,
					'mo_sp_get_variation_content_for_editor' 
			), 10, 2 );
			add_filter ( 'manage_edit-mo_sp_sortable_columns', array (
					$this,
					'mo_sp_sortable_columns' 
			) );
			add_filter ( 'title_edit_pre', array (
					$this,
					'mo_sp_get_variation_title_for_editor' 
			), 10, 2 );
			add_filter ( 'get_edit_post_link', array (
					$this,
					'mo_sp_get_variation_edit_link' 
			), 10, 3 );
		}
		
		add_action ( 'wp_ajax_mo_sp_get_variation_id_to_display', array (
				$this,
				'mo_sp_get_variation_id_to_display' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_sp_get_variation_id_to_display', array (
				$this,
				'mo_sp_get_variation_id_to_display' 
		) );
		add_action ( 'wp_footer', array (
				$this,
				'mo_sp_add_variation_cookie_js' 
		) );
		add_action ( 'wp_ajax_mo_sp_track_impression', array (
				$this,
				'mo_sp_track_impression' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_sp_track_impression', array (
				$this,
				'mo_sp_track_impression' 
		) );
		add_action ( 'wp_ajax_mo_sp_track_visit', array (
				$this,
				'mo_sp_track_visit' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_sp_track_visit', array (
				$this,
				'mo_sp_track_visit' 
		) );
		add_action ( 'wp_ajax_mo_sp_track_conversion', array (
				$this,
				'mo_sp_track_conversion' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_sp_track_conversion', array (
				$this,
				'mo_sp_track_conversion' 
		) );
		
		add_action ( 'wp_ajax_mo_sp_change_post_type', array (
				$this,
				'mo_sp_change_post_type' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_sp_change_post_type', array (
				$this,
				'mo_sp_change_post_type' 
		) );
		
		add_filter ( 'the_content', array (
				$this,
				'mo_sp_get_variation_content' 
		), 10 );
		add_filter ( 'wp_title', array (
				$this,
				'mo_sp_get_variation_meta_title' 
		), 10, 3 );
		add_filter ( 'template_include', array (
				$this,
				'mo_sp_get_post_template_for_template_loader' 
		) );
		add_filter ( 'post_type_link', array (
				$this,
				"mo_sp_get_variation_permalink" 
		), 10, 2 );
		add_filter ( 'the_title', array (
				$this,
				'mo_sp_get_variation_title' 
		), 10, 2 );
		
		add_filter ( 'template_include', array (
				$this,
				'mo_sp_get_template' 
		) );
		add_action ( 'admin_head', array (
				$this,
				'mo_sp_get_js' 
		) );
		add_action ( 'wp_head', array (
				$this,
				'mo_sp_get_js' 
		) );
	}
	
	public function mo_sp_add_clear_tracking($actions, $post) {
            return  $this->mo_add_clear_tracking($actions, $post);
	}

	public function mo_sp_add_variation_cookie_js() {
            $this->mo_add_variation_cookie_js();
	}

	public function mo_sp_category_register_taxonomy() {
            $this->mo_category_register_taxonomy("MO Pop-Ups Category");
	}

	public function mo_sp_clear_stats() {
		$this->mo_clear_stats();
	}
	
	public function mo_sp_column($column) {
            $this->mo_column($column);
	}

	public function mo_sp_columns($columns) {
            return $this->mo_columns($columns,"Pop-Ups Title");
	}

	public function mo_sp_conversion() {
            $this->mo_conversion();
	}

	public function mo_sp_flush_rewrite_rules() {
            $this->mo_flush_rewrite_rules();
	}

	public function mo_sp_get_post_template_for_template_loader($template) {
            return $this->mo_get_post_template_for_template_loader($template);
        }

	public function mo_sp_get_variation_content($content) {
            return $this->mo_get_variation_content($content);
	}

	public function mo_sp_get_variation_content_for_editor($content, $post_id) {
		return $this->mo_get_variation_content_for_editor($content, $post_id);
	}

	public function mo_sp_get_variation_edit_link($link, $id, $context) {
		return $this->mo_get_variation_edit_link($link, $id, $context);
	}

	public function mo_sp_get_variation_id_to_display() {
		$this->mo_get_variation_id_to_display();
	}

	public function mo_sp_get_variation_meta_title($title, $sep, $seplocation) {
		return $this->mo_get_variation_meta_title($title, $sep, $seplocation);
	}

	public function mo_sp_get_variation_permalink($permalink, $post) {
		return $this->mo_get_variation_permalink($permalink, $post);
	}

	public function mo_sp_get_variation_title($title, $id) {
		return $this->mo_get_variation_title($title, $id);
	}

	public function mo_sp_get_variation_title_for_editor($title, $id) {
		return $this->mo_get_variation_title_for_editor($title, $id);
	}

	public function mo_sp_get_mo_website_tracking_js() {
		$this->mo_get_mo_website_tracking_js();
	}

	public function mo_sp_post_type_register() {
                $slug_short = "mosp";
                $post_title = "Pop-Ups";
                $post_title_single = "Pop-Ups";
                $taxonomy_link = "mo_sp";
                $taxonomy = "mo_squeeze_page";
                $this->mo_post_type_register($slug_short,$post_title,$post_title_single,$taxonomy_link,$taxonomy);
        }

	public function mo_sp_sortable_columns() {
                return $this->mo_sortable_columns();
	}
	
	public function mo_sp_taxonomy_filter_restrict_manage_posts() {
                $this->mo_taxonomy_filter_restrict_manage_posts();
	}

	public function mo_sp_track_visit() {
                $this->mo_track_visit();
	}
	
	public function mo_sp_trash_lander($post_id) {
                $this->mo_trash_lander($post_id,'sp-group');
	}

	public function mo_sp_track_conversion() {
                return $this->mo_track_conversion();
	}

	public function mo_sp_track_impression() {
               $this->mo_track_impression();
	}
        
       
	public function mo_sp_set_variation_id() {
                $this->mo_set_variation_id();
	}

	public function mo_sp_pause_variation() {
		$this->mo_pause_variation();
	}

	public function mo_sp_delete_variation() {
		$this->mo_delete_variation();
	}
	
	public function mo_sp_add_shortcodes() {
                $this->mo_add_shortcodes();
	}

	public function mo_sp_is_ab_testing() {
                return $this->mo_is_ab_testing();
	}

	

	public function mo_sp_get_template($template) {
		return $this->mo_get_template($template);
	}

	public function mo_sp_get_js() {
		global $post, $wpdb;
		if (isset ( $post ) && $post->post_type == 'mo_sp' && is_admin ()) {
                    echo '<script>
                            jQuery(document).ready(
				function($) {
					jQuery("input[name^=\'mo_sp_post_types\']").click(function(eventData,handler){
                                            if(this.checked){
                                                var data = {action:\'mo_sp_change_post_type\',post_type:this.name,post_id:' . $post->ID . '};
                                                jQuery.post(ajaxurl,data,function(response){
                                                });
                                            }   
                                        });
                                });
					
                            </script>';
		} elseif (isset ( $post ) && $post->post_type != 'mo_sp' && ! is_admin ()) {
			$post_type = $post->post_type;
			switch ($post_type) {
				case 'mo_landing_page' :
					$post_type = 'lp';
					break;
				case 'page' :
					$post_type = 'pages';
					break;
				case 'post' :
					$post_type = 'posts';
					break;
			}
			$post_id_arr = $wpdb->get_results ( 'SELECT post_id FROM wp_postmeta WHERE meta_key = \'mo_sp_post_types\' ' );
			
			foreach ( $post_id_arr as $v ) {
				
				$post_types_arr = json_decode ( get_post_meta ( $v->post_id, 'mo_sp_post_types', true ) );
				if (isset ( $post_types_arr->$post_type ) && $post_types_arr->$post_type) {
					$post_id = $v->post_id;
				}
			}
			if ($post_id) {
				$mo_page_obj = mo_pages::instance ( $post_id );
				$mo_settings_obj = new mo_settings ();
				$mo_sp_obj = mo_squeeze_pages::instance ( $post_id );
				$v_id = $mo_sp_obj->get_current_variation ();
				$mo_sp_timeout = $mo_settings_obj->get_mo_sp_show_time () ? $mo_settings_obj->get_mo_sp_show_time () : 15;
				$mo_sp_timeout = $mo_sp_timeout * 1000;
                                $link_ch =  (get_option( 'permalink_structure' )=="")?'&':'?';
				$mo_sp_url = get_permalink ( $post_id ) .$link_ch.'mo_sp_variation_id=' . $v_id;
				$modal_width = get_post_meta ( $post_id, 'mo_sp_modal_width_' . $v_id, true ) ? get_post_meta ( $post_id, 'mo_sp_modal_width_' . $v_id, true ) : 250;
				$modal_height = get_post_meta ( $post_id, 'mo_sp_modal_height_' . $v_id, true ) ? get_post_meta ( $post_id, 'mo_sp_modal_height_' . $v_id, true ) : 250;
				if(get_post_status( $post_id )=="publish"){
                                if ($mo_settings_obj->get_mo_lp_cache_compatible () == 'false' || isset ( $_GET ['mo_page_variation_id'] ) || isset ( $_GET ['t'] ) || ! $mo_page_obj->mo_is_testing ()) {
					echo '<script>
							jQuery(document).ready(function($){
							var mouseX = 0; 
							 var mouseY = 0; 
							 var counter = 0; 
							 var mouseIsIn = true; 
							 var spShown = function(){
                                                                if(mo_sp_get_variation_cookie() != null){ 
							 		return true; 
							 	}else if(mo_sp_get_conversion_cookie() != null){
									return true;
								}else{
									return false;
								}
							 }
									
							 function mo_sp_get_variation_cookie() { 
							 	var cookies = document.cookie.split(/;\s*/); 
							 	for ( var i = 0; i < cookies.length; i++) { 
							 		var cookie = cookies[i]; 
							 		var control = ' . $post_id . '; 
							 		if (control > 0 && cookie.indexOf("mo_sp_variation_" + control) != -1) { 
							 			cookie = cookie.split("=", 2); 
							 			return cookie[1]; 
							 		} 
							 	} 
							 	return null; 
							 } 
							 function mo_sp_get_conversion_cookie() { 
							 	var cookies = document.cookie.split(/;\s*/); 
							 	for ( var i = 0; i < cookies.length; i++) { 
							 		var cookie = cookies[i]; 
							 		var control = ' . $post_id . '; 
							 		if (control > 0 && cookie.indexOf("mo_sp_conversion_" + control) != -1) { 
							 			cookie = cookie.split("=", 2); 
							 			return cookie[1]; 
							 		} 
							 	} 
							 	return null; 
							 } 
							
                                                         function addEvent(obj, evt, fn) {
                                                               if (obj.addEventListener) {
                                                                    obj.addEventListener(evt, fn, false);
                                                                }
                                                                else if (obj.attachEvent) {
                                                                    obj.attachEvent("on" + evt, fn);
                                                                }
                                                         }
							 function mo_sp_add_event() { 
								 addEvent(window,"load",function(e) {
                                                                        addEvent(document, "mouseout", function(e) {
                                                                            e = e ? e : window.event;
                                                                            var from = e.relatedTarget || e.toElement;
                                                                            if (!from || from.nodeName == "HTML") {
                                                                                if(!spShown()){ 
                                                                                     jQuery("#mo_sp_container").dialog("close");
                                                                                     jQuery("#mo_sp_iframe").prop("src","' . $mo_sp_url . '");
                                                                                     mo_sp.dialog("open");
                                                                                }
                                                                            }
                                                                        });
                                                                    });
							 } 
							 if(!spShown()){
									jQuery(\'body\').append(\'<div id="mo_sp_container" style="display:none;"><button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close" role="button" aria-disabled="false" title="close"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text">close</span></button><iframe id="mo_sp_iframe" src="" style="border:none;height:' . $modal_height . 'px;width:' . $modal_width . 'px;"></iframe></div>\');
									var width = ' . $modal_width . ';
									var height = ' . $modal_height . ';
									mo_sp = jQuery("#mo_sp_container");
											mo_sp.dialog({
											modal: true,
											autoOpen: false,
											minHeight:height,
											minWidth:width,
											height:"auto",
											width: width,
											maxHeight: height,
											maxWidth : width,
											dialogClass: "mo_sp_modal",
											open: function(event, ui){
                                                                                                $(this).parent().css(\'position\', \'fixed\');
                                                                                                jQuery(this).parent().removeClass("ui-corner-all");
                                                                                                jQuery(this).parent().css(\'border\',\'0px\');
                                                                                                jQuery(\'.ui-widget-overlay\').bind(\'click\',function(){
                                                                                                    mo_sp.dialog(\'close\');
                                                                                                })
                                                                                                jQuery(\'#mo_sp_container .ui-dialog-titlebar-close\').bind(\'click\',function(){
                                                                                                    mo_sp.dialog(\'close\');
                                                                                                })
                                                                                        }
										});	
										jQuery(".ui-dialog-titlebar").removeClass(\'ui-widget-header\');
									 	mo_sp_add_event();
										setTimeout(function(){mo_sp_show_sp();},' . $mo_sp_timeout . ');
									}
							
									function mo_sp_show_sp(){
                                                                            //jQuery("#mo_sp_container").dialog("close");
                                                                            if(!spShown()){
										jQuery("#mo_sp_iframe").prop("src","' . $mo_sp_url . '");
										mo_sp.dialog("open");
                                                                            }
									}
							});
                                    </script>';
				}
                                }
			}
		}
	}

	public function mo_sp_change_post_type() {
		$this->mo_change_post_type();
	}
}
$mo_sp_post_type_obj = new mo_sp_post_type ();