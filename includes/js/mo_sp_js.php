<?php
require('../../../../../wp-blog-header.php');
global $post,$wpdb;
$post_type = $post->post_type;
switch ($post_type){
	case 'mo_landing_page':
		$post_type = 'lp';
		break;
	case 'page':
		$post_type = 'pages';
		break;
	case 'post':
		$post_type = 'posts';
		break;
}
$post_id_arr = $wpdb->get_results('SELECT post_id FROM '.$wpdb->prefix.'postmeta WHERE meta_key = \'mo_sp_post_types\' ');

foreach($post_id_arr as $v){
	$post_types_arr = json_decode(get_post_meta($v->post_id,'mo_sp_post_types',true));
	if($post_types_arr->$post_type){
		$post_id = $v->post_id;
	}
}
$mo_settings_obj = new mo_settings();
$mo_sp_obj = mo_squeeze_pages::instance($post_id);
$v_id = $mo_sp_obj->get_current_variation();
$mo_sp_url =get_permalink($post_id).'?mo_sp_variation_id='.$v_id;
$modal_width = get_post_meta($v_id,'mo_sp_modal_width_'.$v_id,true);
$modal_height = get_post_meta($v_id,'mo_sp_modal_height_'.$v_id,true);
echo'

jQuery(document).ready(function($){
	jQuery(\'body\').append(\'<a href="'. $mo_sp_url.'" target="_blank" class="nyroModal">MO SP</a><div id="mo_sp_container" style="width:500px;height:500px;display:none;">mo pop up test</div>\');
        mo_sp = jQuery(".nyroModal").nyroModal([sizes:{initW: '.$modal_width.',initH:'. $modal_height.'}).nmCall();
	
});';

