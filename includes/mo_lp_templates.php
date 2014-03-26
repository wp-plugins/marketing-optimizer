<?php
function mo_lp_get_templates() {
	$templates_array = array (
			'theme' => array (
					'title' => 'Theme',
					'thumbnail' => get_bloginfo ( 'template_directory' ) . "/screenshot.png" 
			),
			'mo_lp_form_min' => array (
					'title' => 'Form Min',
					'thumbnail' => '/'.PLUGINDIR . '/'.mo_landing_pages_plugin::MO_LP_DIRECTORY.'/templates/mo_lp_form_min/screenshot.png' 
			), 
			'mo_lp_is' => array (
					'title' => 'Product',
					'thumbnail' => '/'.PLUGINDIR . '/'.mo_landing_pages_plugin::MO_LP_DIRECTORY.'/templates/mo_lp_is/screenshot.png' 
			) 
	);
	return $templates_array;
}