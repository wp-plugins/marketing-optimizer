<?php
//add_action ( 'save_post', 'mo_lp_ab_testing_save_post' );
// function mo_lp_ab_testing_save_post($postID) {
// 	global $post;
	
// 	$var_final = (isset ( $_POST ['mo_lp_open_variation'] )) ? $_POST ['mo_lp_open_variation'] : '0';
// 	if (isset ( $_POST ['post_type'] ) && $_POST ['post_type'] == 'mo_landing_page') {
// 		if (defined ( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || $_POST ['post_type'] == 'revision') {
// 			return;
// 		}
		
// 		if ($parent_id = wp_is_post_revision ( $postID )) {
// 			$postID = $parent_id;
// 		}
		
// 		$this_variation = $var_final;
// 		// echo $this_variation;
// 		// print_r($_POST);exit;
		
// 		// first add to varation list if not present.
// 		$variations = get_post_meta ( $postID, 'mo_lp_variations', true );
// 		if ($variations) {
// 			$array_variations = explode ( ',', $variations );
// 			if (! in_array ( $this_variation, $array_variations )) {
// 				$array_variations [$this_variation] = $this_variation;
// 			}
// 		} else {
// 			if ($this_variation > 0) {
// 				$array_variations [] = 0;
// 				$array_variations [$this_variation] = $this_variation;
// 			} else {
// 				$array_variations [$this_variation] = $this_variation;
// 			}
// 		}
		
// 		update_post_meta ( $postID, 'mo_lp_variations', implode ( ',', $array_variations ) );
// 		if (! get_post_meta ( $postID, 'mo_lp_status_' . $this_variation, true ) == 1 || ! get_post_meta ( $postID, 'mo_lp_status_' . $this_variation, true ) == 0) {
// 			update_post_meta ( $postID, 'mo_lp_status_' . $this_variation, 1 );
// 		}
// 		// echo $this_variation;exit;
// 		if ($this_variation == 0) {
// 			return;
// 		}
// 		// echo $this_variation;exit;
// 		// print_r($_POST);
		
// 		// next alter all custom fields to store correct varation and create custom fields for special inputs
// 		$ignore_list = array (
// 				'post_status',
// 				'post_type',
// 				'tax_input',
// 				'post_author',
// 				'user_ID',
// 				'post_ID',
// 				'catslist',
// 				 'mo_lp_open_variation',
// 				'samplepermalinknonce',
// 				'autosavenonce',
// 				'action',
// 				'autosave',
// 				'mm',
// 				'jj',
// 				'aa',
// 				'hh',
// 				'mn',
// 				'ss',
// 				'_wp_http_referer',
// 				'mo_lp_variation_id',
// 				'_wpnonce',
// 				'originalaction',
// 				'original_post_status',
// 				'referredby',
// 				'_wp_original_http_referer',
// 				'meta-box-order-nonce',
// 				'closedpostboxesnonce',
// 				'hidden_post_status',
// 				'hidden_post_password',
// 				'hidden_post_visibility',
// 				'visibility',
// 				'post_password',
// 				'hidden_mm',
// 				'cur_mm',
// 				'hidden_jj',
// 				'cur_jj',
// 				'hidden_aa',
// 				'cur_aa',
// 				'hidden_hh',
// 				'cur_hh',
// 				'hidden_mn',
// 				'cur_mn',
// 				'original_publish',
// 				'save',
// 				'newlanding_page_category',
// 				'newlanding_page_category_parent',
// 				'_ajax_nonce-add-landing_page_category',
// 				'lp_lp_custom_fields_nonce',
// 				'lp-selected-template',
// 				'post_mime_type',
// 				'ID',
// 				'comment_status',
// 				'ping_status' 
// 		);
		
// 		// $special_list = array('content','post-content');
// 		// print_r($_POST);exit;
// 		// echo $this_variation;exit;
// 		foreach ( $_POST as $key => $value ) {
// 			// echo $key." : -{$this_variation} : $value<br>";
// 			if (! in_array ( $key, $ignore_list ) && ! strstr ( $key, 'nonce' )) {
// 				if ($key == 'post_content')
// 					$key = 'content';
// 				if ($key == 'post_title') {
// 					$key = 'title';
// 				}
// 				if (! strstr ( $key, "_{$this_variation}" )) {
// 					$new_array [$key . '_' . $this_variation] = $value;
// 				} else {
// 					// echo $key." : -{$this_variation}<br>";
// 					$new_array [$key] = $value;
// 				}
// 			}
// 			// echo $key." : -{$this_variation} : $value<br>";
// 		}
		
// 		// print_r($new_array);exit;
		
// 		foreach ( $new_array as $key => $val ) {
// 			$old = get_post_meta ( $postID, $key, true );
// 			$new = $val;
// 			// echo "$key : $old v. $new <br>";
// 			// if (isset($new) && $new != $old ) {
// 			update_post_meta ( $postID, $key, $new );
// 			// } elseif ('' == $new && $old) {
// 			// delete_post_meta($postID, $key, $old);
// 			// }
// 		}
// 	}
// }
function mo_lp_ab_key_to_letter($key) {
	$alphabet = array (
			'A',
			'B',
			'C',
			'D',
			'E',
			'F',
			'G',
			'H',
			'I',
			'J',
			'K',
			'L',
			'M',
			'N',
			'O',
			'P',
			'Q',
			'R',
			'S',
			'T',
			'U',
			'V',
			'W',
			'X',
			'Y',
			'Z' 
	);
	
	if (isset ( $alphabet [$key] ))
		return $alphabet [$key];
}
function mo_lp_track_admin_user() {
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