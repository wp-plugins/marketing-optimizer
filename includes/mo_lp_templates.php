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
			),
			'mo_lp_form1' => array (
					'title' => 'Custom Template1',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_lp_form1/screenshot.png' 
			), 
			'mo_lp_form2' => array (
					'title' => 'Custom Template2',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_lp_form2/screenshot.png' 
			),
			'mo_lp_form3' => array (
					'title' => 'Custom Template3',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_lp_form3/screenshot.png' 
			),
			'mo_lp_form4' => array (
					'title' => 'Custom Template4',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_lp_form4/screenshot.png' 
			),
			'mo_lp_form5' => array (
					'title' => 'Custom Template5',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_lp_form5/screenshot.png' 
			),
			'mo_lp_form6' => array (
					'title' => 'Custom Template6',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_lp_form6/screenshot.png' 
			),
			'mo_lp_form7' => array (
					'title' => 'Custom Template7',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_lp_form7/screenshot.png' 
			),
			'mo_lp_form8' => array (
					'title' => 'Custom Template8',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_lp_form8/screenshot.png' 
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
			),
			'mo_sp_custom1' => array (
					'title' => 'Newsletter 2',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_sp_custom1/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_sp_custom2' => array (
					'title' => 'Free Product',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_sp_custom2/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_sp_custom3' => array (
					'title' => 'Email Updates',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_sp_custom3/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_sp_custom4' => array (
					'title' => 'Email Updates 2',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_sp_custom4/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_sp_custom5' => array (
					'title' => 'Video',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_sp_custom5/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_sp_custom6' => array (
					'title' => 'Report',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_sp_custom6/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_sp_custom7' => array (
					'title' => 'Sign Up',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_sp_custom7/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_sp_custom8' => array (
					'title' => 'Report 2',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_sp_custom8/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			)
	);
	return $templates_array;
}
function mo_ct_get_templates() {
	$templates_array = array (
			'mo_ct_blank' => array (
					'title' => 'Blank',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_ct_blank/screenshot.png' 
			),
			'mo_ct_newsletter' => array (
					'title' => 'Newsletter',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_ct_newsletter/screenshot.png',
					'height' => 179, 
					'width' => 700, 
			),
			
			'mo_ct_advertisement1' => array (
					'title' => 'Sale',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_ct_advertisement1/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_ct_advertisement2' => array (
					'title' => 'Signup',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_ct_advertisement2/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_ct_advertisement3' => array (
					'title' => 'Tour',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_ct_advertisement3/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_ct_advertisement4' => array (
					'title' => 'Digital Download',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_ct_advertisement4/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_ct_advertisement5' => array (
					'title' => 'Application',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_ct_advertisement5/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_ct_advertisement6' => array (
					'title' => 'Course',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_ct_advertisement6/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_ct_advertisement7' => array (
					'title' => 'Product',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_ct_advertisement7/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			),
			'mo_ct_advertisement8' => array (
					'title' => 'Instant Access',
					'thumbnail' => plugins_url() . '/'.mo_plugin::MO_DIRECTORY.'/templates/mo_ct_advertisement8/screenshot.png',
					'height' => 364, 
					'width' => 860, 
			)

	);
	return $templates_array;
}