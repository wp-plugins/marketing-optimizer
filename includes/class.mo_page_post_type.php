<?php
class mo_page_post_type {
	public function __construct() {
		add_action ( 'init', array (
				$this,
				'mo_page_add_shortcodes' 
		) );
		add_action ( 'wp_footer', array (
				$this,
				'mo_page_add_variation_cookie_js' 
		) );
		add_action ( 'wp_ajax_mo_page_get_variation_id_to_display', array (
				$this,
				'mo_page_get_variation_id_to_display' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_page_get_variation_id_to_display', array (
				$this,
				'mo_page_get_variation_id_to_display' 
		) );
		add_action ( 'wp_ajax_mo_page_track_visit', array (
				$this,
				'mo_page_track_visit' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_page_track_visit', array (
				$this,
				'mo_page_track_visit' 
		) );
		add_action ( 'wp_ajax_mo_page_track_conversion', array (
				$this,
				'mo_page_track_conversion' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_page_track_conversion', array (
				$this,
				'mo_page_track_conversion' 
		) );
		add_action ( 'wp_ajax_mo_page_track_impression', array (
				$this,
				'mo_page_track_impression' 
		) );
		add_action ( 'wp_ajax_nopriv_mo_page_track_impression', array (
				$this,
				'mo_page_track_impression' 
		) );
		
		add_filter ( 'manage_pages_columns', array (
				$this,
				'mo_page_columns' 
		) );
		add_filter ( 'title_edit_pre', array (
				$this,
				'mo_page_get_variation_title_for_editor' 
		), 10, 2 );
		add_action ( 'wp', array (
				$this,
				'mo_page_set_variation_id' 
		) );
		add_filter ( 'content_edit_pre', array (
				$this,
				'mo_page_get_variation_content_for_editor' 
		), 10, 2 );
		add_filter ( 'the_content', array (
				$this,
				'mo_page_get_variation_content' 
		), 10 );
		add_action ( "manage_pages_custom_column", array (
				$this,
				"mo_page_column" 
		) );
		add_filter ( 'wp_title', array (
				$this,
				'mo_page_get_variation_meta_title' 
		), 10, 3 );
		add_filter ( 'the_title', array (
				$this,
				'mo_page_get_variation_title' 
		), 10, 2 );
		add_action ( 'wp_footer', array (
				$this,
				'mo_page_get_mo_website_tracking_js' 
		) );
		add_action ( 'admin_action_mo_page_pause_variation', array (
				$this,
				'mo_page_pause_variation' 
		) );
		add_action ( 'admin_action_mo_page_delete_variation', array (
				$this,
				'mo_page_delete_variation' 
		) );
		add_filter ( 'page_row_actions', array (
				$this,
				'mo_page_add_clear_tracking' 
		), 10, 2 );
		add_action ( 'admin_action_mo_page_clear_stats', array (
				$this,
				'mo_page_clear_stats' 
		) );
		if (get_option ( 'mo_lp_cache_compatible' ) == 'true' && ! isset ( $_GET ['mo_page_variation_id'] ) && ! isset ( $_GET ['t'] )) {
			add_action ( 'wp_head', array (
					$this,
					'mo_page_get_cache_compatible_js' 
			) );
		}
		add_filter ( 'get_edit_post_link', array (
				$this,
				'mo_page_get_variation_edit_link' 
		), 10, 3 );
	}
	function mo_page_columns($columns) {
		$columns = $this->insert_before_key ( $columns, 'author', 'stats', __ ( "Variation Testing Stats", mo_landing_pages_plugin::MO_LP_TEXT_DOMAIN ) );
		return $columns;
	}
	public function mo_page_column($column) {
		global $post;
		$mo_page_obj = mo_pages::instance ( $post->ID );
		$v_id = $mo_page_obj->get_current_variation ();
		switch ($column) {
			case 'ID' :
				echo $post->ID;
				break;
			case 'title' :
			case 'author' :
			case 'date' :
				break;
			case 'stats' :
				
				$this->mo_page_show_stats_list ();
				break;
		}
	}
	function insert_before_key($original_array, $original_key, $insert_key, $insert_value) {
		$new_array = array ();
		$inserted = false;
		
		foreach ( $original_array as $key => $value ) {
			
			if (! $inserted && $key === $original_key) {
				$new_array [$insert_key] = $insert_value;
				$inserted = true;
			}
			$new_array [$key] = $value;
		}
		
		return $new_array;
	}
	public function mo_page_add_variation_cookie_js() {
		global $post, $variation_id;
		$mo_page_obj = mo_pages::instance ( $post->ID );
		$mo_settings_obj = new mo_settings ();
		if ( $mo_settings_obj->get_mo_lp_cache_compatible () != 'true' || isset ( $_GET ['mo_page_variation_id'] ) || isset ( $_GET ['t'] ) || isset($_COOKIE['mo_page_variation_'.$post->ID])) {
			if (($post->post_type == 'page' || is_home () || is_front_page ()) && $this->mo_page_track_admin_user () && ! $mo_page_obj->mo_bot_detected ()) {
				$variation_id = $variation_id ? $variation_id : 0;
				echo '<script>
					window.onload = function(){
					
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
									xmlhttp.open("POST","' . admin_url ( 'admin-ajax.php' ) . '" ,true);
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
									xmlhttp.open("POST","' . admin_url ( 'admin-ajax.php' ) . '" ,true);
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
									xmlhttp.open("POST","' . admin_url ( 'admin-ajax.php' ) . '" ,true);
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
				if ($mo_page_obj->mo_is_testing ()) {
					echo 'if(mo_page_get_variation_cookie() == null){
							mo_page_get_variation_id_to_display();
	
				    }else{
							mo_page_track_impression();
					}
				}
						
									</script>';
				} else {
					echo 'if(mo_page_get_variation_cookie() == null){
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
	public function mo_page_get_variation_id_to_display() {
		if (isset ( $_POST ['action'] ) && isset ( $_POST ['post_id'] )) {
			if ($_POST ['action'] == 'mo_page_get_variation_id_to_display' && $_POST ['post_id'] > 0) {
				$post_id = $_POST ['post_id'];
				$response_arr = array ();
				$mo_page_obj = mo_pages::instance ( $post_id );
				$v_id = $mo_page_obj->get_current_variation ();
				if ($v_id !== false) {
					$response_arr ['v_id'] = $v_id;
					wp_send_json ( $response_arr );
				} else {
					wp_send_json ( false );
				}
			}
		}
	}
	public static function mo_page_track_impression() {
		if (mo_lp_track_admin_user ()) {
			if (isset ( $_POST ['action'] ) && $_POST ['action'] == 'mo_page_track_impression') {
				if (isset ( $_POST ['post_id'] ) && $_POST ['post_id']) {
					$post_id = $_POST ['post_id'];
					if (isset ( $_POST ['v_id'] ) && $_POST ['v_id'] >= 0) {
						$v_id = $_POST ['v_id'];
						$mo_page_obj = mo_pages::instance ( $post_id );
						$impressions = $mo_page_obj->get_variation_property ( $v_id, 'impressions' );
						if ($impressions) {
							$impressions = $impressions + 1;
							$mo_page_obj->set_variation_property ( $v_id, 'impressions', $impressions );
							$mo_page_obj->save ();
							wp_send_json ( array (
									'impressions' => $impressions 
							) );
						} else {
							$impressions = 1;
							$mo_page_obj->set_variation_property ( $v_id, 'impressions', $impressions );
							$mo_page_obj->save ();
							wp_send_json ( array (
									'impressions' => $impressions 
							) );
						}
					}
				}
			}
		}
	}
	public function mo_page_track_conversion() {
		if (isset ( $_POST ['cookie'] ) && $_POST ['cookie']) {
			$cookieArr = json_decode ( stripslashes ( $_POST ['cookie'] ) );
			$needle = 'mo_page_variation_';
			if (! empty ( $cookieArr )) {
				foreach ( $cookieArr as $v ) {
					$cookie = explode ( '=', $v );
					if (strpos ( $cookie [0], $needle ) !== false) {
						$page_id = substr ( $cookie [0], strlen ( $needle ) );
						$v_id = $cookie [1];
					}
					if (isset ( $page_id ) && $v_id >= 0) {
						$mo_page_obj = mo_pages::instance ( $page_id );
						$conversions = $mo_page_obj->get_variation_property ( $v_id, 'conversions' );
						if ($conversions) {
							$conversions = $conversions + 1;
							$mo_page_obj->set_variation_property ( $v_id, 'conversions', $conversions );
							$mo_page_obj->save ();
						} else {
							$conversions = 1;
							$mo_page_obj->set_variation_property ( $v_id, 'conversions', $conversions );
							$mo_page_obj->save ();
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
	public function mo_page_track_visit() {
		$response = false;
		if ($_POST ['action'] == 'mo_page_track_visit') {
			$post_id = $_POST ['post_id'];
			$v_id = $_POST ['v_id'];
			$mo_page_obj = mo_pages::instance ( $post_id );
			$current_visits = $mo_page_obj->get_variation_property ( $v_id, 'visitors' );
			
			if ($current_visits) {
				$visits = $current_visits + 1;
				$mo_page_obj->set_variation_property ( $v_id, 'visitors', $visits );
				$mo_page_obj->save ();
			} else {
				$visits = 1;
				$mo_page_obj->set_variation_property ( $v_id, 'visitors', $visits );
				$mo_page_obj->save ();
			}
		}
		wp_send_json ( array (
				'post_id' => $post_id,
				'current_visits' => $current_visits,
				'incremented_visits' => $visits 
		) );
	}
	public function mo_page_get_variation_title_for_editor($title, $id) {
		global $pagenow;
		if (get_post_type ( $id ) == 'page') {
			$mo_page_obj = mo_pages::instance ( $id );
			$v_id = $mo_page_obj->get_current_variation ();
			if ($pagenow != 'edit.php' && ( int ) $v_id !== 0) {
				
				$title = $mo_page_obj->get_variation_property ( $v_id, 'title' ) ? $mo_page_obj->get_variation_property ( $v_id, 'title' ) : '';
			}
		}
		return $title;
	}
	public function mo_page_set_variation_id() {
		global $post, $variation_id;
		if ($post) {
			$mo_page_obj = mo_pages::instance ( $post->ID );
			$variation_id = $mo_page_obj->get_current_variation ();
		}
	}
	public function mo_page_get_variation_content_for_editor($content, $post_id) {
		if (get_post_type ( $post_id ) == 'page') {
			$mo_page_obj = mo_pages::instance ( $post_id );
			$v_id = $mo_page_obj->get_current_variation ();
			if (( int ) $v_id != 0) {
				
				$content = $mo_page_obj->get_variation_property ( $v_id, 'content' ) ? $mo_page_obj->get_variation_property ( $v_id, 'content' ) : '';
			}
		}
		return $content;
	}
	public function mo_page_get_variation_content($content) {
		global $post, $variation_id;
		$post_id = $post->ID;
		if (get_post_type ( $post_id ) == 'page') {
			
			$mo_page_obj = mo_pages::instance ( $post_id );
			if (is_null ( $variation_id )) {
				$v_id = $mo_page_obj->get_current_variation ();
			} else {
				$v_id = $variation_id;
			}
			
			if (( int ) $v_id !== 0) {
				$content = $mo_page_obj->get_variation_property ( $v_id, 'content' ) ? $mo_page_obj->get_variation_property ( $v_id, 'content' ) : '';
			}
		}
		return $content;
	}
	public function mo_page_show_stats_list() {
		global $post;
		$mo_page_obj = mo_pages::instance ( $post->ID );
		$variations = $mo_page_obj->get_variation_ids_arr ();
		if (count ( $variations )) {
			$variations_arr = $mo_page_obj->get_variations_arr ();
			echo '<table class="mo_stats_table">
					  <tr class="mo_lp_stats_header_row">
					    <th class="mo_stats_header_cell">ID</th>
					    <th class="mo_stats_header_cell">Imp</th>
					    <th class="mo_stats_header_cell">Visits</th>
					    <th class="mo_stats_header_cell">Conv</th>
					    <th class="mo_stats_header_cell">CR</th>
					    <th class="mo_stats_header_cell">Confidence</th>
						<th class="mo_stats_header_cell">Actions</th>
					  </tr>';
			
			// echo "<ul class='mo_lp_stats_list'>";
			
			$first_status = get_post_meta ( $post->ID, 'mo_lp_variation_status', true ); // Current status
			$i = 0;
			$total_visits = 0;
			$total_impressions = 0;
			$total_conversions = 0;
			foreach ( $variations_arr as $var_obj ) {
				
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
				$confidence = $mo_page_obj->get_confidence ( $var_obj->get_id () );
				$description = $var_obj->get_description () ? $var_obj->get_description () : 'Default';
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
				echo '<td class="mo_stats_cell"><a title="' . $description . '" href="/wp-admin/post.php?post=' . $post->ID . '&mo_page_variation_id=' . $var_obj->get_id () . '&action=edit">' . $letter . '</a> </td>';
				echo '<td class="mo_stats_cell">' . $impressions . '</td>';
				echo '<td class="mo_stats_cell">' . $visits . '</td>';
				echo '<td class="mo_stats_cell">' . $conversions . '</td>';
				echo '<td class="mo_stats_cell">' . $conversion_rate . '%</td>';
				echo '<td class="mo_stats_cell">' . $confidence . '</td>';
				echo '<td class="mo_stats_cell"><a target="_blank" href="' . get_permalink ( $post->ID ) . '?mo_page_variation_id=' . $var_obj->get_id () . '" <i class="fa fa-search"></i></a> | ' . sprintf ( '<a href="admin.php?action=%s&post=%s&v_id=%s">' . $status_text . ' </a>', 'mo_page_pause_variation', $post->ID, $var_obj->get_id () ) . '| ' . sprintf ( '<a href="admin.php?action=%s&post=%s&v_id=%s"><i title="Delete Variation" style="color:red;" class="fa fa-trash-o"></i></a>', 'mo_page_delete_variation', $post->ID, $var_obj->get_id () ) . '</td>';
				echo '</tr>';
			}
			echo "</table>";
		}
	}
	public function mo_page_get_variation_meta_title($title, $sep, $seplocation) {
		global $post, $variation_id;
		if (get_post_type ( $post->ID ) == 'page') {
			
			$mo_page_obj = mo_pages::instance ( $post->ID );
			// $v_id = $mo_lp_obj->get_current_variation();
			$v_id = $variation_id;
			$title = $mo_page_obj->get_variation_property ( $v_id, 'title' ) ? $mo_page_obj->get_variation_property ( $v_id, 'title' ) . ' | ' : '';
			return $title;
		}
	}
	public function mo_page_get_variation_title($title, $id) {
		global $variation_id, $pagenow;
		if (get_post_type ( $id ) == 'page') {
			if ($pagenow != 'edit.php') {
				$mo_page_obj = mo_pages::instance ( $id );
				$v_id = $mo_page_obj->get_current_variation ();
			} else {
				$v_id = 0;
			}
			if (( int ) $v_id !== 0) {
				$title = $mo_page_obj->get_variation_property ( $v_id, 'title' ) ? $mo_page_obj->get_variation_property ( $v_id, 'title' ) : '';
			}
		}
		return $title;
	}
	public function mo_page_get_mo_website_tracking_js() {
		global $post, $variation_id;
		
		$mo_settings_obj = new mo_settings ();
		if ($mo_settings_obj->get_mo_account_id ()) {
			$mo_page_obj = mo_pages::instance ( $post->ID );
			if ($mo_settings_obj->get_mo_lp_cache_compatible () == 'false' || isset ( $_GET ['mo_page_variation_id'] ) || isset ( $_GET ['t'] ) || count ( $mo_page_obj->get_variation_ids_arr () ) == 1) {
				if (is_null ( $variation_id )) {
					$v_id = $mo_page_obj->get_current_variation ();
				} else {
					$v_id = $variation_id;
				}
				$website_tracking_js = '';
				$website_tracking_js .= "\n<!-- Start of Asynchronous Tracking Code --> \n";
				$website_tracking_js .= "<script type='text/javascript'> \n";
				$website_tracking_js .= "var _apVars = _apVars || []; \n";
				$website_tracking_js .= "_apVars.push(['_trackPageview']); \n";
				$website_tracking_js .= "_apVars.push(['_setAccount','" . $mo_settings_obj->get_mo_account_id () . "']); \n";
				
				if (( int ) $mo_page_obj->get_variation_property ( $v_id, 'variation_id' ) > 0) {
					$website_tracking_js .= "_apVars.push([ '_trackVariation','" . ( int ) $mo_page_obj->get_variation_property ( $v_id, 'variation_id' ) . "']); \n";
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
				}
				$website_tracking_js .= "(function(d){ \n";
				$website_tracking_js .= "var t = d.createElement(\"script\"), s = d.getElementsByTagName(\"script\")[0]; \n";
				$website_tracking_js .= "t.src =  \"//app.marketingoptimizer.com/remote/ap.js\"; \n";
				$website_tracking_js .= "s.parentNode.insertBefore(t, s); \n";
				$website_tracking_js .= "})(document); \n";
				$website_tracking_js .= "</script> \n";
				$website_tracking_js .= "<!-- End of Asynchronous Tracking Code --> \n";
				if (! $mo_page_obj->mo_bot_detected () || $this->mo_page_track_admin_user ()) {
					echo $website_tracking_js;
				}
			}
		}
	}
	public function mo_page_pause_variation() {
		if (isset ( $_GET ['post'] ) && $_GET ['post']) {
			$post_id = $_GET ['post'];
			$mo_page_obj = mo_pages::instance ( $post_id );
			$v_id = $_GET ['v_id'];
			$mo_page_obj->pause_variation ( $post_id, $v_id );
		}
		wp_redirect ( wp_get_referer () );
		// exit ();
	}
	public function mo_page_delete_variation() {
		if (isset ( $_GET ['post'] ) && $_GET ['post']) {
			$post_id = $_GET ['post'];
			$mo_page_obj = mo_pages::instance ( $post_id );
			$v_id = $_GET ['v_id'];
			$mo_page_obj->delete_variation ( $post_id, $v_id );
		}
		wp_redirect ( wp_get_referer () );
	}
	public function mo_page_is_ab_testing() {
		global $post;
		$mo_page_obj = mo_pages::instance ( $post->ID );
		$mo_variation_ids_arr = $mo_page_obj->get_variation_ids_arr ();
		if ($post->post_type == 'page') {
			if (count ( $mo_variation_ids_arr ) > 1) {
				return true;
			} else {
				return false;
			}
		}
	}
	public function mo_page_get_cache_compatible_js() {
		global $post;
		$mo_page_obj = mo_pages::instance ( $post->ID );
		if ($post->post_type == 'page' && $mo_page_obj->mo_is_testing () && ! $mo_page_obj->mo_bot_detected () && (! isset ( $_GET ['mo_page_variation_id'] ) || ! isset ( $_GET ['t'] ) || $mo_page_obj->get_current_variation () == 0)) {
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
	function mo_page_track_admin_user() {
		if (current_user_can ( 'manage_options' )) {
			if (get_option ( 'mo_lp_track_admin' ) == 'true') {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	public function mo_page_add_clear_tracking($actions, $post) {
		if ($post->post_type == 'page') {
			$last_reset = get_post_meta ( $post->ID, 'mo_lp_stat_reset_date', true ) ? get_post_meta ( $post->ID, 'mo_lp_stat_reset_date', true ) : 'Never';
			if ($last_reset !== 'Never') {
				$last_reset = Date ( 'm/d/Y', $last_reset );
			}
			$actions ['mo_page_clear_stats'] = sprintf ( '<a href="admin.php?action=%s&post=%s">Reset All Stats</a> <br><i>(Last Stat Reset: ' . $last_reset, 'mo_page_clear_stats', $post->ID ) . ')</i>';
		}
		return $actions;
	}
	public function mo_page_clear_stats() {
		if (isset ( $_GET ['post'] ) && $_GET ['post']) {
			$post_id = $_GET ['post'];
			$mo_page_obj = mo_pages::instance ( $post_id );
			$mo_page_obj->clear_stats ();
		}
		wp_redirect ( wp_get_referer () );
		exit ();
	}
	public function mo_page_add_shortcodes() {
		add_shortcode ( 'mo_page_conversion', array (
				$this,
				'mo_page_conversion' 
		) );
		add_shortcode ( 'mo_conversion', array (
				$this,
				'mo_page_conversion' 
		) );
		add_shortcode ( 'mo_phone', array (
				$this,
				'mo_phone_shortcode' 
		) );
		add_shortcode ( 'aim_phone', array (
				$this,
				'mo_phone_shortcode' 
		) );
	}
	public function mo_page_conversion() {
		global $post;
		if (! isset ( $_GET ['preview'] ) && $this->mo_page_track_admin_user ()) {
			echo '<script type="text/javascript" >
				
					function mo_page_get_conv_variation_cookie(){
							var cookies = document.cookie.split(/;\s*/);
							var cookiesArr = [];
							for(var i=0;i < cookies.length;i++){
								var cookie = cookies[i];
								if(cookie.indexOf("mo_page_variation_") != -1){
									cookiesArr.push(cookie);
								}
							}
							return JSON.stringify(cookiesArr);
						}
					if(mo_page_get_conv_variation_cookie() != null){
									xmlhttp = new XMLHttpRequest();
									xmlhttp.open("POST","' . admin_url ( 'admin-ajax.php' ) . '" ,true);
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
	public function mo_page_get_variation_edit_link($link, $id, $context) {
		if (get_post_type ( $id ) == 'page') {
			// $mo_lp_obj = mo_landing_pages::instance ( $id );
			// $v_id = $mo_lp_obj->get_current_variation ();
			
			return $link . '&mo_page_variation_id=0';
		} else {
			return $link;
		}
	}
	function mo_phone_shortcode($attributes, $content = null) {
		$mo_settings_obj = new mo_settings ();
		if ($mo_settings_obj->get_mo_phone_tracking () == 'true') {
			$defaultPhone = $mo_settings_obj->get_mo_phone_tracking_default_number () ? $mo_settings_obj->get_mo_phone_tracking_default_number () : '';
			if ($mo_settings_obj->get_mo_phone_publish_cls ()) {
				$class = get_option ( 'mo_phone_publish_cls' );
				return "<span class=\"$class\">$defaultPhone</span>";
			} else {
				return '<span class="phonePublishCls">' . $defaultPhone . '</span>';
			}
		} else {
			return '<span style="color:red;">(Phone tracking is currently disabled, enable phone tracking <a href="/wp-admin/admin.php?page=marketing-optimizer-settings">here</a> to use phone tracking short codes.)';
		}
	}
}
$mo_page_post_type_obj = new mo_page_post_type ();