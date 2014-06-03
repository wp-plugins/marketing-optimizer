<?php
function mo_lp_get_templates() {
	$templates_array = array (
			'theme' => array (
					'title' => 'Theme',
					'thumbnail' => get_bloginfo ( 'template_directory' ) . "/screenshot.png" 
			),
			'mo_lp_form_min' => array (
					'title' => 'Form Min',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_lp_form_min/screenshot.png' 
			), 
			'mo_lp_is' => array (
					'title' => 'Product',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_lp_is/screenshot.png' 
			) 
	);
	return $templates_array;
}
function mo_sp_get_templates() {
	$templates_array = array (
			'mo_sp_blank' => array (
					'title' => 'Blank',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_sp_blank/screenshot.png' 
			),
			'mo_sp_newsletter' => array (
					'title' => 'Newsletter',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_sp_newsletter/screenshot.png',
					'height' => 179, 
					'width' => 700, 
			),
			'mo_sp_blog' => array (
					'title' => 'Blog',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_sp_blog/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			)
// 			'mo_sp_email' => array (
// 					'title' => 'Email',
// 					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_sp_email/screenshot.png',
// 					'height' => 364, 
// 					'width' => 860, 
// 			)
	);
	return $templates_array;
}