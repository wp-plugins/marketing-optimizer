<?php
class mo_sp_post_type {
	public function __construct() {
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
	
	// ***********ADDS 'CLEAR STATS' BUTTON TO POSTS EDITING AREA******************/
	public function mo_sp_add_clear_tracking($actions, $post) {
		if ($post->post_type == 'mo_sp') {
			$last_reset = get_post_meta ( $post->ID, 'mo_sp_stat_reset_date', true ) ? get_post_meta ( $post->ID, 'mo_sp_stat_reset_date', true ) : 'Never';
			if ($last_reset !== 'Never') {
				$last_reset = Date ( 'm/d/Y', $last_reset );
			}
			$actions ['mo_sp_clear_stats'] = sprintf ( '<a href="admin.php?action=%s&post=%s">Reset All Stats</a> <br><i>(Last Stat Reset: ' . $last_reset, 'mo_sp_clear_stats', $post->ID ) . ')</i>';
		}
		return $actions;
	}
	public function mo_sp_add_variation_cookie_js() {
		global $post, $variation_id;
		$mo_sp_obj = mo_squeeze_pages::instance ( $post->ID );
		if ($post->post_type == 'mo_sp' && $this->mo_sp_track_admin_user () && ! $mo_sp_obj->mo_bot_detected ()) {
			echo '<script>
				window.onload = function() {
					function mo_sp_get_variation_cookie() {
					
						var cookies = document.cookie.split(/;\s*/);
						for ( var i = 0; i < cookies.length; i++) {
							var cookie = cookies[i];
							var control = ' . $post->ID . ';
							if (control > 0
									&& cookie.indexOf("mo_sp_variation_" + control) != -1) {
								cookie = cookie.split("=", 2);
								return cookie[1];
							}
						}
						return null;
					}
					function mo_sp_set_variation_cookie(name, value, days) {
									    if (days) {
									        var date = new Date();
									        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
									        var expires = "; expires=" + date.toGMTString();
									    } else var expires = "";
									    document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
					}
					function mo_sp_track_impression(){
									xmlhttp = new XMLHttpRequest();
									xmlhttp.open("POST","' . admin_url ( 'admin-ajax.php' ) . '" ,true);
									xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
									xmlhttp.send("action=mo_sp_track_impression&post_id=' . $post->ID . '&v_id="+mo_sp_get_variation_cookie());
														xmlhttp.onreadystatechange = function () {
							        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
							           var response  = xmlhttp.responseText;
							        }
		
						}
					}
					function mo_sp_track_visit(v_id){
									xmlhttp = new XMLHttpRequest();
									xmlhttp.open("POST","' . admin_url ( 'admin-ajax.php' ) . '" ,true);
									xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
									xmlhttp.send("action=mo_sp_track_visit&post_id=' . $post->ID . '&v_id=' . $variation_id . '");
														xmlhttp.onreadystatechange = function () {
							        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
							           var response  = xmlhttp.responseText;
							        }
		
						}
					}
					function mo_sp_get_variation_id_to_display(){
						xmlhttp = new XMLHttpRequest();
									xmlhttp.open("POST","' . admin_url ( 'admin-ajax.php' ) . '" ,true);
									xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
									xmlhttp.send("action=mo_sp_get_variation_id_to_display&post_id=' . $post->ID . '");
									xmlhttp.onreadystatechange =  function () {
								        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
								           var response  = xmlhttp.responseText;
															var json_response = JSON.parse(response);
															variation_id = json_response.v_id;
															mo_sp_set_variation_cookie("mo_sp_variation_' . $post->ID . '",' . $variation_id . ',365);
															mo_sp_track_impression();
															mo_sp_track_visit(' . $variation_id . ');
								        }
						}
	
					}';
			if ($mo_sp_obj->mo_is_testing ()) {
				echo 'if(mo_sp_get_variation_cookie() == null){
							mo_sp_get_variation_id_to_display();
		
				    }else{
							mo_sp_track_impression();
					}
				}
						</script>';
			} else {
				echo 'if(mo_sp_get_variation_cookie() == null){
							mo_sp_set_variation_cookie("mo_sp_variation_' . $post->ID . '",' . $variation_id . ',365);
															mo_sp_track_impression();
															mo_sp_track_visit(' . $variation_id . ');
	
				    }else{
							mo_sp_track_impression();
					}
				}
									</script>';
			}
		}
	}
	public function mo_sp_category_register_taxonomy() {
		$args = array (
				'hierarchical' => true,
				'label' => __ ( "Categories", mo_plugin::MO_LP_TEXT_DOMAIN ),
				'singular_label' => __ ( "MO Squeeze Page Category", mo_plugin::MO_LP_TEXT_DOMAIN ),
				'show_ui' => true,
				'query_var' => true,
				"rewrite" => true 
		);
		
		register_taxonomy ( 'mo_sp_category', array (
				'mo_sp' 
		), $args );
	}
	public function mo_sp_clear_stats() {
		if (isset ( $_GET ['post'] ) && $_GET ['post']) {
			$post_id = $_GET ['post'];
			$mo_sp_obj = mo_squeeze_pages::instance ( $post_id );
			$mo_sp_obj->clear_stats ();
		}
		wp_redirect ( wp_get_referer () );
		exit ();
	}
	
	// populate collumsn for landing pages
	public function mo_sp_column($column) {
		global $post;
		$mo_sp_obj = mo_squeeze_pages::instance ( $post->ID );
		$v_id = $mo_sp_obj->get_current_variation ();
		switch ($column) {
			case 'ID' :
				echo $post->ID;
				break;
			case 'title' :
			case 'author' :
			case 'date' :
				break;
			case 'stats' :
				$this->mo_sp_show_stats_list ();
				break;
			case 'impressions' :
				echo $this->mo_sp_show_aggregated_stats ( "impressions" );
				break;
			case 'visits' :
				echo $this->mo_sp_show_aggregated_stats ( "visits" );
				break;
			case 'conversions' :
				echo $this->mo_sp_show_aggregated_stats ( "conversions" );
				break;
			case 'cr' :
				echo $this->mo_sp_show_aggregated_stats ( "cr" ) . "%";
				break;
		}
	}
	
	/**
	 * *******PREPARE COLUMNS FOR IMPRESSIONS AND CONVERSIONS**************
	 */
	
	// define columns for landing pages
	public function mo_sp_columns($columns) {
		$columns = array (
				"cb" => "<input type=\"checkbox\" />",
				// "ID" => "ID",
				// "thumbnail-lander" => __( "Preview" , mo_plugin::MO_LP_TEXT_DOMAIN),
				"title" => __ ( "Squeeze Page Title", mo_plugin::MO_LP_TEXT_DOMAIN ),
				"stats" => __ ( "Variation Testing Stats", mo_plugin::MO_LP_TEXT_DOMAIN ),
				"impressions" => __ ( "Impressions", mo_plugin::MO_LP_TEXT_DOMAIN ),
				"visits" => __ ( "Visits", mo_plugin::MO_LP_TEXT_DOMAIN ),
				"conversions" => __ ( "Conversions", mo_plugin::MO_LP_TEXT_DOMAIN ),
				"cr" => __ ( "Conversion Rate", mo_plugin::MO_LP_TEXT_DOMAIN ) 
		);
		return $columns;
	}
	public function mo_sp_conversion() {
		global $post;
		if (! isset ( $_GET ['preview'] ) && $this->mo_sp_track_admin_user ()) {
			echo '<script type="text/javascript" >
				window.onload = public function() {
					function mo_sp_get_variation_cookie(){
							var cookies = document.cookie.split(/;\s*/);
							var cookiesArr = [];
							for(var i=0;i < cookies.length;i++){
								var cookie = cookies[i];
								if(cookie.indexOf("mo_sp_variation_") != -1){
									cookiesArr.push(cookie);
								}
							}
							return JSON.stringify(cookiesArr);
						}
					if(mo_sp_get_variation_cookie() != null){
									xmlhttp = new XMLHttpRequest();
									xmlhttp.open("POST","' . admin_url ( 'admin-ajax.php' ) . '" ,true);
									xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	
									xmlhttp.send("action=mo_sp_track_conversion&cookie=+mo_sp_get_variation_cookie()");
											xmlhttp.onreadystatechange = public function () {
				        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
				           var response  = xmlhttp.responseText;
											var json_response = JSON.parse(response);
				        }
					}
				}
											}
</script>';
		}
	}
	public function mo_sp_flush_rewrite_rules() {
		$activation_check = get_option ( 'mo_sp_plugin_activated', 0 );
		
		if ($activation_check) {
			global $wp_rewrite;
			$wp_rewrite->flush_rules ();
			update_option ( 'mo_sp_plugin_activated', '0' );
		}
	}
	public function mo_sp_get_post_template_for_template_loader($template) {
		global $post, $variation_id;
		if ($post && $post->post_type == 'mo_sp') {
			$post_id = $post->ID;
			$mo_sp_obj = mo_squeeze_pages::instance ( $post_id );
			// $v_id = $mo_sp_obj->get_current_variation();
			$v_id = $variation_id;
			
			$post_template = $mo_sp_obj->get_variation_property ( $v_id, 'template' );
			if (! empty ( $post_template ) && $post_template != 'default' && file_exists ( get_template_directory () . "/{$post_template}" ))
				$template = get_template_directory () . "/{$post_template}";
			return ($template);
		}
		return $template;
	}
	public function mo_sp_get_variation_content($content) {
		global $post, $variation_id;
		$post_id = $post->ID;
		if (get_post_type ( $post_id ) == 'mo_sp') {
			
			$mo_sp_obj = mo_squeeze_pages::instance ( $post_id );
			$v_id = $mo_sp_obj->get_current_variation ();
			
			$content = $mo_sp_obj->get_variation_property ( $v_id, 'content' ) ? $mo_sp_obj->get_variation_property ( $v_id, 'content' ) : '';
		}
		return $content;
	}
	public function mo_sp_get_variation_content_for_editor($content, $post_id) {
		if (get_post_type ( $post_id ) == 'mo_sp') {
			
			$mo_sp_obj = mo_squeeze_pages::instance ( $post_id );
			$v_id = $mo_sp_obj->get_current_variation ();
			
			try {
				$content = $mo_sp_obj->get_variation_property ( $v_id, 'content' );
			} catch ( Exception $e ) {
				$content = '';
			}
			return $content;
		}
		return $content;
	}
	public function mo_sp_get_variation_edit_link($link, $id, $context) {
		if (get_post_type ( $id ) == 'mo_sp') {
			return $link . '&mo_sp_variation_id=0';
		} else {
			return $link;
		}
	}
	public function mo_sp_get_variation_id_to_display() {
		if (isset ( $_POST ['action'] ) && isset ( $_POST ['post_id'] )) {
			if ($_POST ['action'] == 'mo_sp_get_variation_id_to_display' && $_POST ['post_id'] > 0) {
				$post_id = $_POST ['post_id'];
				$response_arr = array ();
				$mo_sp_obj = mo_squeeze_pages::instance ( $post_id );
				$v_id = $mo_sp_obj->get_current_variation ();
				if ($v_id !== false) {
					$response_arr ['v_id'] = $v_id;
					wp_send_json ( $response_arr );
				} else {
					wp_send_json ( false );
				}
			}
		}
	}
	public function mo_sp_get_variation_meta_title($title, $sep, $seplocation) {
		global $post, $variation_id;
		if (get_post_type ( $post->ID ) == 'mo_sp') {
			
			$mo_sp_obj = mo_squeeze_pages::instance ( $post->ID );
			$v_id = $variation_id;
			try {
				$title = $mo_sp_obj->get_variation_property ( $v_id, 'title' ) . ' | ';
			} catch ( Exception $e ) {
				$title = '';
			}
		}
		return $title;
	}
	public function mo_sp_get_variation_permalink($permalink, $post) {
		global $variation_id;
		if ($post->post_type == 'mo_sp') {
			$mo_sp_obj = mo_squeeze_pages::instance ( $post->ID );
			$v_id = $variation_id;
			
			$permalink = $permalink;
		}
		return $permalink;
	}
	public function mo_sp_get_variation_title($title, $id) {
		global $variation_id, $pagenow;
		if (get_post_type ( $id ) == 'mo_sp') {
			$mo_sp_obj = mo_squeeze_pages::instance ( $id );
			if ($pagenow != 'edit.php') {
				$v_id = $mo_sp_obj->get_current_variation ();
			} else {
				$v_id = 0;
			}
			
			$title = $mo_sp_obj->get_variation_property ( $v_id, 'title' ) ? $mo_sp_obj->get_variation_property ( $v_id, 'title' ) : '';
		}
		return $title;
	}
	public function mo_sp_get_variation_title_for_editor($title, $id) {
		if (get_post_type ( $id ) == 'mo_sp') {
			$mo_sp_obj = mo_squeeze_pages::instance ( $id );
			$v_id = $mo_sp_obj->get_current_variation ();
			
			$title = $mo_sp_obj->get_variation_property ( $v_id, 'title' ) ? $mo_sp_obj->get_variation_property ( $v_id, 'title' ) : '';
		}
		return $title;
	}
	public function mo_sp_post_type_register() {
		$slug = get_option ( 'mo_sp_permalink_prefix', 'mosp' );
		
		$labels = array (
				'name' => _x ( 'Marketing Optimizer Squeeze Pages', 'post type general name', mo_plugin::MO_LP_TEXT_DOMAIN ),
				'menu_name' => _x ( 'Squeeze Pages', 'post type general name', mo_plugin::MO_LP_TEXT_DOMAIN ),
				'singular_name' => _x ( 'Marketing Optimizer Squeeze Page', 'post type singular name', mo_plugin::MO_LP_TEXT_DOMAIN ),
				'add_new' => _x ( 'Add New', 'Squeeze Page', mo_plugin::MO_LP_TEXT_DOMAIN ),
				'add_new_item' => __ ( 'Add New Squeeze Page', mo_plugin::MO_LP_TEXT_DOMAIN ),
				'edit_item' => __ ( 'Edit Squeeze Page', mo_plugin::MO_LP_TEXT_DOMAIN ),
				'new_item' => __ ( 'New Squeeze Page', mo_plugin::MO_LP_TEXT_DOMAIN ),
				'view_item' => __ ( 'View Squeeze Page', mo_plugin::MO_LP_TEXT_DOMAIN ),
				'search_items' => __ ( 'Search Squeeze Page', mo_plugin::MO_LP_TEXT_DOMAIN ),
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
		
		register_post_type ( 'mo_sp', $args );
		register_taxonomy ( 'mo_sp_page_cat', 'mo_squeeze_page', array (
				'hierarchical' => true,
				'label' => "Categories",
				'singular_label' => "Squeeze Page Category",
				'show_ui' => true,
				'query_var' => true,
				"rewrite" => true 
		) );
	}
	public function mo_sp_show_aggregated_stats($type_of_stat) {
		global $post;
		$landing_page_obj = mo_squeeze_pages::instance ( $post->ID );
		$variations = $landing_page_obj->get_variations_arr ();
		
		$visits = 0;
		$impressions = 0;
		$conversions = 0;
		
		foreach ( $variations as $vid ) {
			$each_visit = $vid->get_visitors ();
			$each_impression = $vid->get_impressions ();
			$each_conversion = $vid->get_conversions ();
			(($each_conversion === "")) ? $final_conversion = 0 : $final_conversion = $each_conversion;
			$visits += $vid->get_visitors ();
			$impressions += $vid->get_impressions ();
			$conversions += $vid->get_conversions ();
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
			$conversion_rate = round ( $conversion_rate, 2 ) * 100;
			return $conversion_rate;
		}
	}
	public function mo_sp_show_stats_list() {
		global $post;
		$mo_sp_obj = mo_squeeze_pages::instance ( $post->ID );
		$variations = $mo_sp_obj->get_variation_ids_arr ();
		if (count ( $variations )) {
			$variations_arr = $mo_sp_obj->get_variations_arr ();
			echo '<table class="mo_stats_table">
					  <tr class="mo_stats_header_row">
					    <th class="mo_stats_header_cell">ID</th>
					    <th class="mo_stats_header_cell">Imp</th>
					    <th class="mo_stats_header_cell">Visits</th>
					    <th class="mo_stats_header_cell">Conv</th>
					    <th class="mo_stats_header_cell">CR</th>
					    <th class="mo_stats_header_cell">Confidence</th>
						<th class="mo_stats_header_cell">Actions</th>
					  </tr>';
			
			$first_status = get_post_meta ( $post->ID, 'mo_sp_variation_status', true ); // Current status
			$i = 0;
			$total_visits = 0;
			$total_impressions = 0;
			$total_conversions = 0;
			foreach ( $variations_arr as $var_obj ) {
				if ($var_obj->id !== '') {
					// assign variation id a letter
					$letter = mo_lp_ab_key_to_letter ( $var_obj->get_id () );
					// get variation visits
					$visits = $var_obj->get_visitors () ? $var_obj->get_visitors () : 0;
					// get variation impressions
					$impressions = $var_obj->get_impressions () ? $var_obj->get_impressions () : 0;
					// current variation status
					$status = $var_obj->get_status ();
					$status_text = $status ? '<i title="Pause Variation" class="fa fa-pause"></i>' : '<i title="UnPause Variation" class="fa fa-play"></i>';
					$status_class_text = $status ? 'mo_status_unpaused' : 'mo_status_paused';
					$confidence = $mo_sp_obj->get_confidence ( $var_obj->get_id () );
					
					// get variation conversions
					$conversions = $var_obj->get_conversions () ? $var_obj->get_conversions () : 0;
					(($conversions === "")) ? $total_conversions = 0 : $total_conversions = $conversions;
					
					// add variaton visits to total
					$total_visits += $var_obj->get_visitors ();
					// add variaton impressions to total
					$total_impressions += $var_obj->get_impressions ();
					// add variaton conversions to total
					$total_conversions += $var_obj->get_conversions ();
					// get conversion rate
					if ($visits != 0) {
						$conversion_rate = $conversions / $visits;
					} else {
						$conversion_rate = 0;
					}
					
					$conversion_rate = round ( $conversion_rate, 2 ) * 100;
					$cr_array [] = $conversion_rate;
					
					echo '<tr class="' . $status_class_text . '">';
					echo '<td class="mo_stats_cell"><a title="' . $var_obj->get_description () . '" href="/wp-admin/post.php?post=' . $post->ID . '&mo_sp_variation_id=' . $var_obj->get_id () . '&action=edit">' . $letter . '</a> </td>';
					echo '<td class="mo_stats_cell">' . $impressions . '</td>';
					echo '<td class="mo_stats_cell">' . $visits . '</td>';
					echo '<td class="mo_stats_cell">' . $conversions . '</td>';
					echo '<td class="mo_stats_cell">' . $conversion_rate . '%</td>';
					echo '<td class="mo_stats_cell">' . $confidence . '</td>';
					echo '<td class="mo_stats_cell"><a target="_blank" href="' . get_permalink ( $post->ID ) . '?mo_sp_variation_id=' . $var_obj->get_id () . '" <i class="fa fa-search"></i></a> | ' . sprintf ( '<a href="admin.php?action=%s&post=%s&v_id=%s">' . $status_text . ' </a>', 'mo_sp_pause_variation', $post->ID, $var_obj->get_id () ) . ' | ' . sprintf ( '<a href="admin.php?action=%s&post=%s&v_id=%s"><i title="Delete Variation" style="color:red;" class="fa fa-trash-o"></i></a>', 'mo_sp_delete_variation', $post->ID, $var_obj->get_id () ) . '</td>';
					echo '</tr>';
				}
			}
			echo "</table>";
		}
	}
	
	// Make these columns sortable
	public function mo_sp_sortable_columns() {
		return array (
				'title' => 'title',
				'impressions' => 'impressions',
				'conversions' => 'conversions',
				'cr' => 'cr' 
		);
	}
	
	// Add category sort to landing page list
	public function mo_sp_taxonomy_filter_restrict_manage_posts() {
		global $typenow;
		
		if ($typenow === "mo_sp") {
			$post_types = get_post_types ( array (
					'_builtin' => false 
			) );
			if (in_array ( $typenow, $post_types )) {
				$filters = get_object_taxonomies ( $typenow );
				
				foreach ( $filters as $tax_slug ) {
					$tax_obj = get_taxonomy ( $tax_slug );
					(isset ( $_GET [$tax_slug] )) ? $current = $_GET [$tax_slug] : $current = 0;
					wp_dropdown_categories ( array (
							'show_option_all' => __ ( 'Show All ' . $tax_obj->label ),
							'taxonomy' => $tax_slug,
							'name' => $tax_obj->name,
							'orderby' => 'name',
							'selected' => $current,
							'hierarchical' => $tax_obj->hierarchical,
							'show_count' => false,
							'hide_empty' => true 
					) );
				}
			}
		}
	}
	public function mo_sp_track_visit() {
		$response = false;
		if ($_POST ['action'] == 'mo_sp_track_visit') {
			$post_id = $_POST ['post_id'];
			$v_id = $_POST ['v_id'];
			$mo_sp_obj = mo_squeeze_pages::instance ( $post_id );
			$current_visits = $mo_sp_obj->get_variation_property ( $v_id, 'visitors' );
			if ($current_visits) {
				$visits = $current_visits + 1;
				$mo_sp_obj->set_variation_property ( $v_id, 'visitors', $visits );
				$mo_sp_obj->save ();
			} else {
				$visits = 1;
				$mo_sp_obj->set_variation_property ( $v_id, 'visitors', $visits );
				$mo_sp_obj->save ();
			}
		}
		wp_send_json ( array (
				'post_id' => $post_id,
				'current_visits' => $current_visits,
				'incremented_visits' => $visits 
		) );
	}
	
	/* perform trash actions for landing pages */
	public function mo_sp_trash_lander($post_id) {
		global $post;
		
		if (! isset ( $post ) || isset ( $_POST ['split_test'] ))
			return;
		
		if ($post->post_type == 'revision') {
			return;
		}
		if (defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || (isset ( $_POST ['post_type'] ) && $_POST ['post_type'] == 'revision')) {
			return;
		}
		
		if ($post->post_type == 'mo_sp') {
			
			$lp_id = $post->ID;
			
			$args = array (
					'post_type' => 'sp-group',
					'post_satus' => 'publish' 
			);
			
			$my_query = null;
			$my_query = new WP_Query ( $args );
			
			if ($my_query->have_posts ()) {
				$i = 1;
				while ( $my_query->have_posts () ) :
					$my_query->the_post ();
					$group_id = get_the_ID ();
					$group_data = get_the_content ();
					$group_data = json_decode ( $group_data, true );
					
					$lp_ids = array ();
					foreach ( $group_data as $key => $value ) {
						$lp_ids [] = $key;
					}
					
					if (in_array ( $lp_id, $lp_ids )) {
						unset ( $group_data [$lp_id] );
						
						$this_data = json_encode ( $group_data );
						$new_post = array (
								'ID' => $group_id,
								'post_title' => get_the_title (),
								'post_content' => $this_data,
								'post_status' => 'publish',
								'post_date' => date ( 'Y-m-d H:i:s' ),
								'post_author' => 1,
								'post_type' => 'sp-group' 
						);
						$post_id = wp_update_post ( $new_post );
					}
				endwhile
				;
			}
		}
	}
	public function mo_sp_track_conversion() {
		if (isset ( $_POST ['cookie'] ) && $_POST ['cookie']) {
			$cookieArr = json_decode ( stripslashes ( $_POST ['cookie'] ) );
			$needle = 'mo_sp_variation_';
			if (! empty ( $cookieArr )) {
				foreach ( $cookieArr as $v ) {
					$cookie = explode ( '=', $v );
					if (strpos ( $cookie [0], $needle ) !== false) {
						$page_id = substr ( $cookie [0], strlen ( $needle ) );
						$v_id = $cookie [1];
					}
					if (isset ( $page_id ) && $v_id >= 0) {
						$mo_sp_obj = mo_squeeze_pages::instance ( $page_id );
						$conversions = $mo_sp_obj->get_variation_property ( $v_id, 'conversions' );
						if ($conversions) {
							$conversions = $conversions + 1;
							$mo_sp_obj->set_variation_property ( $v_id, 'conversions', $conversions );
							$mo_sp_obj->save ();
						} else {
							$conversions = 1;
							$mo_sp_obj->set_variation_property ( $v_id, 'conversions', $conversions );
							$mo_sp_obj->save ();
						}
						return wp_send_json ( array (
								'v_id' => $v_id,
								'post_id' => $page_id,
								'conversions' => $conversions 
						) );
					}
				}
			}
		} else {
		}
		return;
	}
	public function mo_sp_track_impression() {
		if (mo_lp_track_admin_user ()) {
			if (isset ( $_POST ['action'] ) && $_POST ['action'] == 'mo_sp_track_impression') {
				if (isset ( $_POST ['post_id'] ) && $_POST ['post_id']) {
					$post_id = $_POST ['post_id'];
					if (isset ( $_POST ['v_id'] ) && $_POST ['v_id'] >= 0) {
						$v_id = $_POST ['v_id'];
						$mo_sp_obj = mo_squeeze_pages::instance ( $post_id );
						$impressions = $mo_sp_obj->get_variation_property ( $v_id, 'impressions' );
						if ($impressions) {
							$impressions = $impressions + 1;
							$mo_sp_obj->set_variation_property ( $v_id, 'impressions', $impressions );
							$mo_sp_obj->save ();
							wp_send_json ( array (
									'impressions' => $impressions 
							) );
						} else {
							$impressions = 1;
							$mo_sp_obj->set_variation_property ( $v_id, 'impressions', $impressions );
							$mo_sp_obj->save ();
							wp_send_json ( array (
									'impressions' => $impressions 
							) );
						}
					}
				}
			}
		}
	}
	public function mo_sp_set_variation_id() {
		global $post, $variation_id;
		if ($post && $post->post_type == 'mo_sp') {
			show_admin_bar ( false );
			$mo_sp_obj = mo_squeeze_pages::instance ( $post->ID );
			$variation_id = $mo_sp_obj->get_current_variation ();
		}
	}
	public function mo_sp_pause_variation() {
		if (isset ( $_GET ['post'] ) && $_GET ['post']) {
			$post_id = $_GET ['post'];
			$mo_sp_obj = mo_squeeze_pages::instance ( $post_id );
			$v_id = $_GET ['v_id'];
			$mo_sp_obj->pause_variation ( $post_id, $v_id );
		}
		wp_redirect ( wp_get_referer () );
	}
	public function mo_sp_delete_variation() {
		if (isset ( $_GET ['post'] ) && $_GET ['post']) {
			$post_id = $_GET ['post'];
			$mo_sp_obj = mo_squeeze_pages::instance ( $post_id );
			$v_id = $_GET ['v_id'];
			$mo_sp_obj->delete_variation ( $post_id, $v_id );
		}
		wp_redirect ( wp_get_referer () );
	}
	public function mo_sp_add_shortcodes() {
		add_shortcode ( 'mo_sp_conversion', array (
				$this,
				'mo_sp_conversion' 
		) );
	}
	public function mo_sp_is_ab_testing() {
		global $post;
		$mo_sp_obj = mo_squeeze_pages::instance ( $post->ID );
		$mo_variation_ids_arr = $mo_sp_obj->get_variation_ids_arr ();
		if ($post->post_type == 'page') {
			if (count ( $mo_variation_ids_arr ) > 1) {
				return true;
			} else {
				return false;
			}
		}
	}
	function mo_sp_track_admin_user() {
		if (current_user_can ( 'manage_options' )) {
			if (get_option ( 'mo_sp_track_admin' ) == 'true') {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	public function mo_sp_get_template_via_curl($url) {
		$ch = curl_init ();
		curl_setopt ( $ch, CURLOPT_URL, $url );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		$template = curl_exec ( $ch );
		curl_close ( $ch );
		return $template;
	}
	public function mo_sp_get_template($template) {
		global $post;
		if ($post->post_type == 'mo_sp') {
			$mo_sp_obj = mo_squeeze_pages::instance ( $post->ID );
			$v_id = $mo_sp_obj->get_current_variation ();
			$mo_sp_template = $mo_sp_obj->get_variation_property ( $v_id, 'template' );
			$template_dir = PLUGINDIR . '/' . mo_plugin::MO_DIRECTORY . '/templates/' . $mo_sp_template;
			if ($mo_sp_template != 'theme') {
				$template = $template_dir . '/template.php';
			} else {
				if ($mo_sp_obj->get_variation_property ( $v_id, 'theme_template' ) != 'default') {
					$template = get_template_directory () . '/' . $mo_sp_obj->get_variation_property ( $v_id, 'theme_template' );
				} else {
					$template = get_template_directory () . '/index.php';
				}
			}
		}
		return $template;
	}
	public function mo_sp_get_js() {
		global $post, $wpdb;
		if (isset ( $post ) && $post->post_type == 'mo_sp' && is_admin ()) {
			echo '<script>
					jQuery(document)
		.ready(
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
				$mo_sp_url = get_permalink ( $post_id ) . '?mo_sp_variation_id=' . $v_id;
				$modal_width = get_post_meta ( $post_id, 'mo_sp_modal_width_' . $v_id, true ) ? get_post_meta ( $post_id, 'mo_sp_modal_width_' . $v_id, true ) : 250;
				$modal_length = get_post_meta ( $post_id, 'mo_sp_modal_length_' . $v_id, true ) ? get_post_meta ( $post_id, 'mo_sp_modal_length_' . $v_id, true ) : 250;
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
							 		if (control > 0 
							 				&& cookie.indexOf("mo_sp_variation_" + control) != -1) { 
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
							 		if (control > 0 
							 				&& cookie.indexOf("mo_sp_conversion_" + control) != -1) { 
							 			cookie = cookie.split("=", 2); 
							 			return cookie[1]; 
							 		} 
							 	} 
							 	return null; 
							 } 
							
							 function mo_sp_add_event() { 
								 window.addEventListener("mouseout", 
								     function(e){ 
								         mouseX = e.pageX; 
								         mouseY = e.pageY; 
								     	if ((mouseY >= 0 && mouseY <= window.innerHeight) 
								     	&& (mouseX >= 0 && mouseX <= window.innerWidth)) 
								     		return; 
								     	if(!spShown()){ 
							 				jQuery("#mo_sp_iframe").prop("src","' . $mo_sp_url . '");
								     		mo_sp.dialog("open")
								     	}
								     	counter++; 
								     	mouseIsIn = false; 
								     	//document.getElementById("in_out").innerHTML="out" + counter; 
								     }, 
								     false); 
							 } 
							 if(!spShown()){
									jQuery(\'body\').append(\'<div id="mo_sp_container" style="display:none;"><button type="button" class="ui-button ui-widget ui-state-default ui-corner-all ui-button-icon-only ui-dialog-titlebar-close" role="button" aria-disabled="false" title="close"><span class="ui-button-icon-primary ui-icon ui-icon-closethick"></span><span class="ui-button-text">close</span></button><iframe id="mo_sp_iframe" src="" style="border:none;height:' . $modal_length . 'px;width:' . $modal_width . 'px;"></iframe></div>\');
									var width = ' . $modal_width . ';
									var height = ' . $modal_length . ';
									mo_sp = jQuery("#mo_sp_container");
											mo_sp.dialog({
											modal: true,
													autoOpen: false,
											//minHeight:height,
											//minWidth:width,
											height:"auto",
											width: width,
											maxHeight: height,
											maxWidth : width,
													dialogClass: "mo_sp_modal",
													 open: function(){
            jQuery(\'.ui-widget-overlay\').bind(\'click\',function(){
                mo_sp.dialog(\'close\');
            })
            jQuery(\'#mo_sp_container .ui-dialog-titlebar-close\').bind(\'click\',function(){
                mo_sp.dialog(\'close\');
            })
        }
											});	
													jQuery(".ui-dialog-titlebar").removeClass(\'ui-widget-header\')		;
									 		mo_sp_add_event();
											setTimeout(function(){mo_sp_show_sp();},' . $mo_sp_timeout . ');
									}
							
									function mo_sp_show_sp(){
										if(!spShown()){
													jQuery("#mo_sp_iframe").prop("src","'. $mo_sp_url . '");
											mo_sp.dialog("open");
											}
										}
							});
			</script>';
				}
			}
		}
	}
	public function mo_sp_change_post_type() {
		global $wpdb;
		$post_id = $_POST ['post_id'];
		preg_match ( "/\[(.*?)\]/", $_POST ['post_type'], $matches );
		$post_type = $matches [1];
		$post_meta_arr = $wpdb->get_results ( 'SELECT post_id FROM wp_postmeta WHERE meta_key = \'mo_sp_post_types\' AND post_id != ' . $post_id );
		foreach ( $post_meta_arr as $v ) {
			$post_types_arr = json_decode ( get_post_meta ( $v->post_id, 'mo_sp_post_types', true ) );
			if (isset ( $post_types_arr->$post_type ) && $post_types_arr->$post_type) {
				$post_types_arr->$post_type = 0;
			}
		}
		update_post_meta ( $v->post_id, 'mo_sp_post_types', json_encode ( $post_types_arr ) );
		wp_send_json ( 'true' );
	}
}
$mo_sp_post_type_obj = new mo_sp_post_type ();