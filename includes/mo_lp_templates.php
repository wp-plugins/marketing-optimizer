<?php
function mo_lp_get_templates() {
	$templates_array = array (
			'theme' => array (
					'title' => 'Theme',
					'thumbnail' => get_bloginfo ( 'template_directory' ) . "/screenshot.png" 
			),
			'mo_lp_form_min' => array (
					'title' => 'Form Min',
					'thumbnail' => plugins_url() . '/'.mo_landing_pages_plugin::MO_DIRECTORY.'/templates/mo_lp_form_min/screenshot.png' 
			), 
			'mo_lp_is' => array (
					'title' => 'Product',
					'thumbnail' => plugins_url() . '/'.mo_landing_pages_plugin::MO_DIRECTORY.'/templates/mo_lp_is/screenshot.png' 
			) 
	);
	return $templates_array;
}
function mo_sp_get_templates() {
	$templates_array = array (
			'mo_sp_blank' => array (
					'title' => 'Blank',
					'thumbnail' => plugins_url() . '/'.mo_landing_pages_plugin::MO_DIRECTORY.'/templates/mo_sp_blank/screenshot.png' 
			),
			'mo_sp_newsletter' => array (
					'title' => 'Newsletter',
					'thumbnail' => plugins_url() . '/'.mo_landing_pages_plugin::MO_DIRECTORY.'/templates/mo_sp_newsletter/screenshot.png',
					'height' => 157, 
					'width' => 550, 
			)
	);
	return $templates_array;
}