<?php
class mo_ct_post_type extends mo_post_type {
	public function __construct() {
                $short_type = 'mo_ct';
                $post_type = 'mo_ct';
                $api_post_type = 'call-to-action';
                parent::__construct ( $short_type,$post_type,$api_post_type );
		add_action ( 'admin_init', array (
				$this,
				'mo_ct_flush_rewrite_rules' 
		) );
		add_action ( 'wp', array (
				$this,
				'mo_ct_set_variation_id' 
		) );
		add_action ( 'init', array (
				$this,
				'mo_ct_add_shortcodes' 
		) );
		
		add_filter('widget_text', 'do_shortcode');
                
                add_action ( 'init', array (
				$this,
				'register_shortcode' 
		) );
		
		add_action ( 'init', array (
				$this,
				'register_shortcode_plugin_path' 
		) );
		
		add_action ( 'init', array (
				$this,
				'mo_ct_post_type_register' 
		) );
		
                add_action ( 'wp_footer', array (
				$this,
				'mo_ct_get_mo_website_tracking_js' 
		) );
		
		if (is_admin ()) {
			
			add_action ( 'init', array (
					$this,
					'mo_ct_category_register_taxonomy' 
			) );
			add_action ( 'wp_trash_post', array (
					$this,
					'mo_ct_trash_lander' 
			) );
			add_filter ( "manage_edit-mo_ct_columns", array (
					$this,
					'mo_ct_columns' 
			) );
			add_action ( "manage_mo_ct_posts_custom_column", array (
					$this,
					"mo_ct_column" 
			) );
			add_action ( 'admin_action_mo_ct_clear_stats', array (
					$this,
					'mo_ct_clear_stats' 
			) );
			add_action ( 'admin_action_mo_ct_pause_variation', array (
					$this,
					'mo_ct_pause_variation' 
			) );
			add_action ( 'admin_action_mo_ct_delete_variation', array (
					$this,
					'mo_ct_delete_variation' 
			) );
			
			// add admin filters
			add_filter ( 'post_row_actions', array (
					$this,
					'mo_ct_add_clear_tracking' 
			), 10, 2 );
			add_filter ( 'content_edit_pre', array (
					$this,
					'mo_ct_get_variation_content_for_editor' 
			), 10, 2 );
			add_filter ( 'manage_edit-mo_ct_sortable_columns', array (
					$this,
					'mo_ct_sortable_columns' 
			) );
			add_filter ( 'title_edit_pre', array (
					$this,
					'mo_ct_get_variation_title_for_editor' 
			), 10, 2 );
			add_filter ( 'get_edit_post_link', array (
					$this,
					'mo_ct_get_variation_edit_link' 
			), 10, 3 );
		}
		
		add_action ( 'wp_ajax_mo_ct_get_variation_id_to_display', array (
				$this,
				'mo_ct_get_variation_id_to_display' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_ct_get_variation_id_to_display', array (
				$this,
				'mo_ct_get_variation_id_to_display' 
		) );
		add_action ( 'wp_footer', array (
				$this,
				'mo_ct_add_variation_cookie_js' 
		) );
		add_action ( 'wp_ajax_mo_ct_track_impression', array (
				$this,
				'mo_ct_track_impression' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_ct_track_impression', array (
				$this,
				'mo_ct_track_impression' 
		) );
		add_action ( 'wp_ajax_mo_ct_track_visit', array (
				$this,
				'mo_ct_track_visit' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_ct_track_visit', array (
				$this,
				'mo_ct_track_visit' 
		) );
		add_action ( 'wp_ajax_mo_ct_track_conversion', array (
				$this,
				'mo_ct_track_conversion' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_ct_track_conversion', array (
				$this,
				'mo_ct_track_conversion' 
		) );
		
		add_action ( 'wp_ajax_mo_ct_change_post_type', array (
				$this,
				'mo_ct_change_post_type' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_ct_change_post_type', array (
				$this,
				'mo_ct_change_post_type' 
		) );
		
		add_filter ( 'the_content', array (
				$this,
				'mo_ct_get_variation_content' 
		), 10 );
		add_filter ( 'wp_title', array (
				$this,
				'mo_ct_get_variation_meta_title' 
		), 10, 3 );
		add_filter ( 'template_include', array (
				$this,
				'mo_ct_get_post_template_for_template_loader' 
		) );
		add_filter ( 'post_type_link', array (
				$this,
				"mo_ct_get_variation_permalink" 
		), 10, 2 );
		add_filter ( 'the_title', array (
				$this,
				'mo_ct_get_variation_title' 
		), 10, 2 );
		
		add_filter ( 'template_include', array (
				$this,
				'mo_ct_get_template' 
		) );
		add_action ( 'admin_head', array (
				$this,
				'mo_ct_get_js' 
		) );
		add_action ( 'wp_head', array (
				$this,
				'mo_ct_get_js' 
		) );
	}
	
	public function mo_ct_add_clear_tracking($actions, $post) {
            return  $this->mo_add_clear_tracking($actions, $post);
	}
        public function mo_ct_add_variation_cookie_js() {
            $this->mo_add_variation_cookie_js();
	}
	
        public function mo_ct_category_register_taxonomy() {
            $this->mo_category_register_taxonomy("MO Call To Action Category");
	}

	public function mo_ct_clear_stats() {
            $this->mo_clear_stats();
	}
	
	public function mo_ct_column($column) {
            $this->mo_column($column);
	}

	public function mo_ct_columns($columns) {
            return $this->mo_columns($columns,"Calls-To-Action Title");
	}

	public function mo_ct_conversion() {
            $this->mo_conversion();
	}

	public function mo_ct_flush_rewrite_rules() {
            $this->mo_flush_rewrite_rules();
	}

	public function mo_ct_get_post_template_for_template_loader($template) {
            return $this->mo_get_post_template_for_template_loader($template);
	}

	public function mo_ct_get_variation_content($content) {
            return $this->mo_get_variation_content($content);
	}

	public function mo_ct_get_variation_content_for_editor($content, $post_id) {
            return $this->mo_get_variation_content_for_editor($content, $post_id);
	}

	public function mo_ct_get_variation_edit_link($link, $id, $context) {
            return $this->mo_get_variation_edit_link($link, $id, $context);
	}

	public function mo_ct_get_variation_id_to_display() {
            $this->mo_get_variation_id_to_display();
        }

	public function mo_ct_get_variation_meta_title($title, $sep, $seplocation) {
            return $this->mo_get_variation_meta_title($title, $sep, $seplocation);
	}

	public function mo_ct_get_variation_permalink($permalink, $post) {
            return $this->mo_get_variation_permalink($permalink, $post);
	}

	public function mo_ct_get_variation_title($title, $id) {
            return $this->mo_get_variation_title($title, $id);
	}

	public function mo_ct_get_variation_title_for_editor($title, $id) {
            return $this->mo_get_variation_title_for_editor($title, $id);
	}
        
        public function mo_ct_post_type_register() {
                $slug_short = "mosp";
                $post_title = "Calls-To-Action";
                $post_title_single = "Calls-To-Action";
                $taxonomy_link = "mo_ct_page";
                $taxonomy = "mo_callto_action";
                $this->mo_post_type_register($slug_short,$post_title,$post_title_single,$taxonomy_link,$taxonomy);
        }
        
        public function mo_ct_sortable_columns() {
            return $this->mo_sortable_columns();
	}
        
        public function mo_ct_taxonomy_filter_restrict_manage_posts() {
            $this->mo_taxonomy_filter_restrict_manage_posts();
	}
        
        public function mo_ct_track_visit() {
            $this->mo_track_visit();
	}
        
        public function mo_ct_trash_lander($post_id) {
            $this->mo_trash_lander($post_id,'sp-group');
	}

        public function mo_ct_track_impression() {
            $this->mo_track_impression();
	}

        public function mo_ct_set_variation_id() {
            $this->mo_set_variation_id();
	}

	public function mo_ct_pause_variation() {
            $this->mo_pause_variation();
	}

	public function mo_ct_delete_variation() {
            $this->mo_delete_variation();
	}
        
        public function mo_ct_add_shortcodes() {
            $this->mo_add_shortcodes();
        }
        
        public function mo_ct_is_ab_testing() {
            return $this->mo_is_ab_testing();
	}
        
        public function mo_ct_get_mo_website_tracking_js() {
            $this->mo_get_mo_website_tracking_js();
	}
        
	public function mo_ct_track_conversion() {
            return $this->mo_track_conversion();
	}

	public function register_shortcode(){
            add_shortcode('mo_cta',  array ($this,'ct_post_short_code'));
	}
		
	public function register_shortcode_plugin_path(){
		add_shortcode('mo_url',  array ($this,'mo_plugin_path'));
	}
	public function mo_plugin_path(){
		$path = plugins_url();
		return $path;
	}
	public function ct_post_short_code($atts){
                $vid = (isset($_COOKIE['mo_ct_variation_'.$atts['id']]))?$_COOKIE['mo_ct_variation_'.$atts['id']]:0;
                $obj_cta = mo_callto_action::instance($atts['id']);
                $content = $obj_cta->get_variation_property($vid, 'content'); 
                $return_string = '<div class="short-code-content">'.$content.$this->mo_ct_add_variation_cookie_js().'</div>';
		
                /*$args = array(
		'post_type' => 'mo_ct',
		'post__in' => $atts );
		
		$mo_ct_query = new WP_Query($args);
		while ($mo_ct_query->have_posts()) : $mo_ct_query->the_post();
		
		$return_string = '<div class="short-code-content">'.get_the_content().$this->mo_ct_add_variation_cookie_js().'</div>';
		endwhile;
		wp_reset_postdata($mo_ct_query->the_post());*/
		return $return_string;
	}
	
        public function mo_ct_get_template($template) {
            return $this->mo_get_template($template);
	}

	public function mo_ct_get_js() {
		global $post, $wpdb;
		if (isset ( $post ) && $post->post_type == 'mo_ct' && is_admin ()) {
		echo '<script>
			jQuery(document).ready(
                            function($) {
				jQuery("input[name^=\'mo_ct_post_types\']").click(function(eventData,handler){
                                        if(this.checked){
                                                var data = {action:\'mo_ct_change_post_type\',post_type:this.name,post_id:' . $post->ID . '};
                                                jQuery.post(ajaxurl,data,function(response){
                                                });
                                        }
                            });
			});
					
					</script>';
		} elseif (isset ( $post ) && $post->post_type != 'mo_ct' && ! is_admin ()) {
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
			$post_id_arr = $wpdb->get_results ( 'SELECT post_id FROM wp_postmeta WHERE meta_key = \'mo_ct_post_types\' ' );
			
			foreach ( $post_id_arr as $v ) {
				
				$post_types_arr = json_decode ( get_post_meta ( $v->post_id, 'mo_ct_post_types', true ) );
				if (isset ( $post_types_arr->$post_type ) && $post_types_arr->$post_type) {
					$post_id = $v->post_id;
				}
			}
		}
	}

	public function mo_ct_change_post_type() {
		$this->mo_change_post_type();
	}
}
$mo_ct_post_type_obj = new mo_ct_post_type ();