<?php
/*
 * Plugin Name: Marketing Optimizer 
 * Plugin URI: http://www.marketingoptimizer.com/wordpress/?apcid=8381 
 * Description: A plugin to integrate with Marketing Optimizer and perform A/B testing experiments on your wordpress pages. 
 * Version: 20131129 
 * Author: Marketing Optimizer, customercare@marketingoptimizer.com 
 * Author URI: http://www.marketingoptimizer.com/?apcid=8381
 */
?>
<?php
// some definition we will use
define ( 'MO_PUGIN_NAME', 'Marketing Optimizer' );
define ( 'MO_CURRENT_VERSION', '20131129' );
define ( 'MO_CURRENT_BUILD', '1' );
define ( 'MO_PLUGIN_DIRECTORY', 'marketing-optimizer' );
define ( 'MO_LOGPATH', str_replace ( '\\', '/', WP_CONTENT_DIR ) . '/mo-logs/' );
define ( 'MO_DEBUG', true ); // never use debug mode on productive systems
define ( 'EMU2_I18N_DOMAIN', 'mo' ); // i18n plugin domain for language files
define ( 'DS', '/' );
define ( 'USING_GF', class_exists ( 'GFForms' ) ? true : false );

if (USING_GF) {
	require_once ('class.mogravityforms.php');
}
include_once ('mo_config.php');

// how to handle log files, don't load them if you don't log
require_once ('mo_logfilehandling.php');

// require plugin classes
require_once ('class.marketingoptimizer.php');
if (get_option ( 'mo_account_id' ) > 0) {
	require_once ('widgets/class.form.widget.php');
	if (get_option ( 'mo_phone_tracking' ) == 'true') {
		require_once ('widgets/class.phone.widget.php');
	}
}

if (get_option ( 'mo_variation_pages' ) == 'true') {
	include_once ('mo_variation_pages.php');
}
include_once ('mo_list_table_variation_pages.php');
// load language files
function mo_set_lang_file() {
	// set the language file
	$currentLocale = get_locale ();
	if (! empty ( $currentLocale )) {
		$moFile = dirname ( __FILE__ ) . "/lang/" . $currentLocale . ".mo";
		if (@file_exists ( $moFile ) && is_readable ( $moFile )) {
			load_textdomain ( EMU2_I18N_DOMAIN, $moFile );
		}
	}
}
mo_set_lang_file ();

// create custom plugin settings menu
add_action ( 'admin_menu', 'mo_create_menu', 9 );

// call register settings function
add_action ( 'admin_init', 'mo_register_settings' );

// call register js scripts
add_action ( 'admin_init', 'mo_register_scripts' );

add_action ( 'admin_notices', 'custom_error_notice' );
function custom_error_notice() {
	if (get_option ( 'mo_marketing_optimizer' ) == 'true' && ! strlen ( get_option ( 'mo_account_id' ) )) {
		echo '<div class="error" style="font-size:16px;font-weight:bold;font-style:italic;padding:10px;">The Marketing Optimizer plugin setting "Account Id" has not been set, none of the Marketing Optimizer plugin functionality will work until this option is configured. Click <a href="/wp-admin/admin.php?page=' . MO_PLUGIN_DIRECTORY . '/mo_settings_page.php">Here</a> to configure.</div>';
	}
	if (get_option ( 'mo_google_analytics' ) == 'true' && ! strlen ( get_option ( 'mo_google_analytics_account_id' ) )) {
		echo '<div class="error" style="font-size:16px;font-weight:bold;font-style:italic;padding:10px;">The Marketing Optimizer plugin setting "Google Analytics Account Id" has not been set, Google analytics functionality will work until this option is configured. Click <a href="/wp-admin/admin.php?page=' . MO_PLUGIN_DIRECTORY . '/mo_settings_page.php">Here</a> to configure.</div>';
	}
}
// add shortcodes
add_shortcode ( 'mo_form', 'mo_form_shortcode' );
add_shortcode ( 'mo_phone', 'mo_phone_shortcode' );
add_shortcode ( 'aim_phone', 'mo_phone_shortcode' );
add_shortcode ( 'mo_pagegenerator', 'mo_pagegenerator_shortcode' );

// add short code functionality to text widgets
add_filter ( 'widget_text', 'do_shortcode' );

// register plugin hooks
register_activation_hook ( __FILE__, 'mo_activate' );
register_deactivation_hook ( __FILE__, 'mo_deactivate' );
register_uninstall_hook ( __FILE__, 'mo_uninstall' );

// call register head items
add_action ( 'wp_head', 'mo_register_head_items' );

// begin plugin functions

// activating the default values
function mo_activate() {
	if (is_multisite ()) {
		global $wpdb;
		$blogs = $wpdb->get_results ( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
		if ($blogs) {
			foreach ( $blogs as $blog ) {
				switch_to_blog ( $blog ['blog_id'] );
				! get_option ( 'mo_marketing_optimizer' ) ? add_option ( 'mo_marketing_optimizer', MARKETING_OPTIMIZER ) : '';
				! get_option ( 'mo_account_id' ) ? add_option ( 'mo_account_id', ACCOUNT_ID ) : '';
				! get_option ( 'mo_phone_tracking' ) ? add_option ( 'mo_phone_tracking', PHONE_TRACKING ) : '';
				! get_option ( 'mo_phone_publish_cls' ) ? add_option ( 'mo_phone_publish_cls', PHONE_TRACKING_PUBLISH_CLS ) : '';
				! get_option ( 'mo_phone_tracking_thank_you_url' ) ? add_option ( 'mo_phone_tracking_thank_you_url', PHONE_TRACKING_THANK_YOU_URL ) : '';
				! get_option ( 'mo_phone_tracking_default_number' ) ? add_option ( 'mo_phone_tracking_default_number', PHONE_TRACKING_DEFAULT_NUMBER ) : '';
				! get_option ( 'mo_form_default_id' ) ? add_option ( 'mo_form_default_id', FORM_ID ) : '';
				! get_option ( 'mo_variation_pages' ) ? add_option ( 'mo_variation_pages', VARIATION_PAGES ) : '';
				! get_option ( 'mo_track_admin' ) ? add_option ( 'mo_track_admin', TRACK_ADMIN ) : '';
			}
			restore_current_blog ();
		}
	} else {
		! get_option ( 'mo_marketing_optimizer' ) ? add_option ( 'mo_marketing_optimizer', MARKETING_OPTIMIZER ) : '';
		! get_option ( 'mo_account_id' ) ? add_option ( 'mo_account_id', ACCOUNT_ID ) : '';
		! get_option ( 'mo_phone_tracking' ) ? add_option ( 'mo_phone_tracking', PHONE_TRACKING ) : '';
		! get_option ( 'mo_phone_publish_cls' ) ? add_option ( 'mo_phone_publish_cls', PHONE_TRACKING_PUBLISH_CLS ) : '';
		! get_option ( 'mo_phone_tracking_thank_you_url' ) ? add_option ( 'mo_phone_tracking_thank_you_url', PHONE_TRACKING_THANK_YOU_URL ) : '';
		! get_option ( 'mo_phone_tracking_default_number' ) ? add_option ( 'mo_phone_tracking_default_number', PHONE_TRACKING_DEFAULT_NUMBER ) : '';
		! get_option ( 'mo_form_default_id' ) ? add_option ( 'mo_form_default_id', FORM_ID ) : '';
		! get_option ( 'mo_variation_pages' ) ? add_option ( 'mo_variation_pages', VARIATION_PAGES ) : '';
		! get_option ( 'mo_track_admin' ) ? add_option ( 'mo_track_admin', TRACK_ADMIN ) : '';
	}
	flush_rewrite_rules ();
}
// create admin menu
function mo_create_menu() {
	// create new top-level menu
	add_menu_page ( __ ( 'Marketing Optimizer', EMU2_I18N_DOMAIN ), __ ( 'Optimizer', EMU2_I18N_DOMAIN ), 0, DS . MO_PLUGIN_DIRECTORY . '/mo_settings_page.php', '', plugins_url ( '/images/moicon.png', __FILE__ ) );
	add_submenu_page ( DS . MO_PLUGIN_DIRECTORY . '/mo_settings_page.php', 'Settings', 'Settings', 10, DS . MO_PLUGIN_DIRECTORY . '/mo_settings_page.php' );
}
// deactivating
function mo_deactivate() {
	
	flush_rewrite_rules ();
}
// check if debug is activated
function mo_debug() {
	// only run debug on localhost
	if ($_SERVER ["HTTP_HOST"] == "localhost" && defined ( 'MO_DEBUG' ) && MO_DEBUG == true)
		return true;
}
// form shortcode function
function mo_form_shortcode($attributes, $content = null) {
	if (isset ( $attributes ['id'] )) {
		return '<script type="text/javascript" src="http://app.marketingoptimizer.com/remote/ap_js.php?f=' . $attributes ['id'] . '&o=' . get_option ( 'mo_account_id' ) . '"></script>';
	} elseif (get_option ( 'mo_form_default_id' )) {
		return '<script type="text/javascript" src="http://app.marketingoptimizer.com/remote/ap_js.php?f=' . get_option ( 'mo_form_default_id' ) . '&o=' . get_option ( 'mo_account_id' ) . '"></script>';
	}
}
function mo_pagegenerator_shortcode($attributes, $content = null) {
	global $post;
	if (isset ( $attributes ['id'] )) {
		$hostlen = strlen(get_bloginfo('wpurl'));
		return file_get_contents("http://app.marketingoptimizer.com/remote/form_post.php?id=".$attributes ['id']."&host=".urlencode(get_bloginfo('wpurl'))."&path=".urlencode(substr(get_permalink($post->ID),$hostlen))."&keyword=".urlencode((isset($_GET['keyword'])?$_GET['keyword']:''))."&action=pagegenerator_remote_form_post");
	} 
}
// phone shortcode function
function mo_phone_shortcode($attributes, $content = null) {
	if (get_option ( 'mo_phone_tracking' ) == 'true') {
		$defaultPhone = get_option ( 'mo_phone_tracking_default_number' ) ? get_option ( 'mo_phone_tracking_default_number' ) : '';
		if (get_option ( 'mo_phone_publish_cls' )) {
			$class = get_option ( 'mo_phone_publish_cls' );
			return "<span class=\"$class\">$defaultPhone</span>";
		} else {
			return '<span class="phonePublishCls">' . $defaultPhone . '</span>';
		}
	} else {
		return '<span style="color:red;">(Phone tracking is currently disabled, enable phone tracking <a href="/wp-admin/admin.php?page=' . MO_PLUGIN_DIRECTORY . '/mo_settings_page.php">here</a> to use phone tracking short codes.)';
	}
}
// register items to be output by the wp_head() function
function mo_register_head_items() {
	global $post;
	
	if (get_option ( 'mo_account_id' ) && $_GET ['preview'] != true && mo_track_admin_user()) {
		$moObj = new marketingoptimizer ( get_option ( 'mo_account_id' ) );
		if ($post->post_variation) {
			$moObj->setVariationId ( $post->post_variation );
		}
		echo $moObj->_getWebsiteTrackingCode ();
	}
}
function mo_track_admin_user(){
	if(current_user_can('manage_options')){
		if(get_option('mo_track_admin') == 'true'){
			return true;
		}else{
			return false;
		}
	}else{
		return true;
	}
}
// register plugin functions
function mo_register_settings() {
	// register general settings
	register_setting ( 'mo-settings-group', 'mo_marketing_optimizer' );
	register_setting ( 'mo-settings-group', 'mo_account_id' );
	// register phone settings
	register_setting ( 'mo-settings-group', 'mo_phone_tracking' );
	register_setting ( 'mo-settings-group', 'mo_phone_publish_cls' );
	register_setting ( 'mo-settings-group', 'mo_phone_tracking_thank_you_url' );
	register_setting ( 'mo-settings-group', 'mo_phone_tracking_default_number' );
	
	// register form settings
	register_setting ( 'mo-settings-group', 'mo_form_default_id' );
	
	// register google analytics settings
	register_setting ( 'mo-settings-group', 'mo_google_analytics' );
	register_setting ( 'mo-settings-group', 'mo_google_analytics_account_id' );
	register_setting ( 'mo-settings-group', 'mo_google_analytics_cross_domain' );
	register_setting ( 'mo-settings-group', 'mo_google_analytics_domains' );
	register_setting ( 'mo-settings-group', 'mo_variation_pages' );
	register_setting ( 'mo-settings-group', 'mo_variation_conversion_page' );
	register_setting ( 'mo-settings-group', 'mo_track_admin' );
}

// uninstalling
function mo_uninstall() {
	if (is_multisite ()) {
		global $wpdb;
		$blogs = $wpdb->get_results ( "SELECT blog_id FROM {$wpdb->blogs}", ARRAY_A );
		if ($blogs) {
			foreach ( $blogs as $blog ) {
				switch_to_blog ( $blog ['blog_id'] );
				delete_option ( 'mo_marketing_optimizer' );
				delete_option ( 'mo_account_id' );
				delete_option ( 'mo_phone_tracking' );
				delete_option ( 'mo_phone_publish_cls' );
				delete_option ( 'mo_phone_tracking_thank_you_url' );
				delete_option ( 'mo_phone_tracking_default_number' );
				delete_option ( 'mo_form_default_id' );
				delete_option ( 'mo_google_analytics' );
				delete_option ( 'mo_google_analytics_account_id' );
				delete_option ( 'mo_google_analytics_cross_domain' );
				delete_option ( 'mo_google_analytics_domains' );
				delete_option ( 'mo_variation_pages' );
			}
			restore_current_blog ();
		}
	} else {
		delete_option ( 'mo_marketing_optimizer' );
		delete_option ( 'mo_account_id' );
		delete_option ( 'mo_phone_tracking' );
		delete_option ( 'mo_phone_publish_cls' );
		delete_option ( 'mo_phone_tracking_thank_you_url' );
		delete_option ( 'mo_phone_tracking_default_number' );
		delete_option ( 'mo_form_default_id' );
		delete_option ( 'mo_google_analytics' );
		delete_option ( 'mo_google_analytics_account_id' );
		delete_option ( 'mo_google_analytics_cross_domain' );
		delete_option ( 'mo_google_analytics_domains' );
		delete_option ( 'mo_variation_pages' );
	}
	// needed for proper deletion of every option
	flush_rewrite_rules ();
	// delete log files and folder only if needed
	if (function_exists ( 'mo_deleteLogFolder' ))
		mo_deleteLogFolder ();
}
// output jquery tabs init function
function mo_jquery_tabs_init() {
	$moObj = new marketingoptimizer ( get_option ( 'mo_account_id' ) );
	$abtesting = get_option ( 'mo_variation_pages' ) == 'true' ? 'true' : 'false';
	$marketingOptimizer = get_option ( 'mo_marketing_optimizer' ) == 'true' ? 'true' : 'false';
	$phoneTracking = get_option ( 'mo_phone_tracking' ) == 'true' ? 'true' : 'false';
	$sliderStartValue = get_option ( 'mo_variation_percentage' ) ? get_option ( 'mo_variation_percentage' ) : 90;
	$cacheCompatible = get_option ( 'mo_cache_compatible' ) == 'true' ? 'true' : 'false';
	$trackAdmin = get_option ( 'mo_track_admin' ) == 'true' ? 'true' : 'false';
	echo '<script>
			  jQuery(function() {
			    jQuery( "#slider-range-max" ).slider({
			      range: "max",
			      min: 10,
			      max: 90,
			      value: ' . $sliderStartValue . ',
				  step:10,
			      slide: function( event, ui ) {
					var label = "Exploitation: "+ui.value+"%/Exploration: "+(100-ui.value)+"%"
			        jQuery( "#amount" ).val(label  );
			        jQuery( "#variation_percentage" ).val(ui.value  );
			      }
			    });
						var labelval =  "Exploitation: "+jQuery( "#slider-range-max" ).slider( "value" )+"%/Exploration: "+(100-jQuery( "#slider-range-max" ).slider( "value" ))+"%";
			    jQuery( "#amount" ).val( labelval );
				
			  });
				jQuery(\'.toggle-abtesting\').toggles({on:' . $abtesting . '});
			   jQuery(\'.toggle-abtesting\').on(\'toggle\',function(e,active){
				        		if(active){
				        			jQuery(\'[name="variation_pages"]\').val("true");
				        		}else{
				        			jQuery(\'[name="variation_pages"]\').val("");
				        		}
			        		});
				jQuery(\'.toggle-mointegration\').toggles({on:' . $marketingOptimizer . '});
			   jQuery(\'.toggle-mointegration\').on(\'toggle\',function(e,active){
				        		if(active){
				        			jQuery(\'[name="marketing_optimizer"]\').val("true");
				        		}else{
				        			jQuery(\'[name="marketing_optimizer"]\').val("");
				        		}
			        		});
				jQuery(\'.toggle-phonetracking\').toggles({on:' . $phoneTracking . '});
			   jQuery(\'.toggle-phonetracking\').on(\'toggle\',function(e,active){
				        		if(active){
				        			jQuery(\'[name="phone_tracking"]\').val("true");
				        		}else{
				        			jQuery(\'[name="phone_tracking"]\').val("");
				        		}
			        		});
				jQuery(\'.toggle-cachecompatible\').toggles({on:' . $cacheCompatible . '});
			   jQuery(\'.toggle-cachecompatible\').on(\'toggle\',function(e,active){
				        		if(active){
				        			jQuery(\'[name="cache_compatible"]\').val("true");
				        		}else{
				        			jQuery(\'[name="cache_compatible"]\').val("");
				        		}
			        		});
				jQuery(\'.toggle-trackadmin\').toggles({on:' . $trackAdmin . '});
			   jQuery(\'.toggle-trackadmin\').on(\'toggle\',function(e,active){
				        		if(active){
				        			jQuery(\'[name="track_admin"]\').val("true");
				        		}else{
				        			jQuery(\'[name="track_admin"]\').val("");
				        		}
			        		});
			jQuery(document).ready(function($) {

$(\'#mo_gf_form\').change(function(){
var data = {
						action: \'mo_gf_form_field_mapping\',
						form_id: $(\'#mo_gf_form\').val()
};
	if(data.form_id > 0){					
	$.post(\'' . admin_url ( 'admin-ajax.php' ) . '\', data, function(response) {
		$("#form_field_mapping_table").empty();		        					
		$("#form_field_mapping_table").html(response);		        					
		//alert(\'Got this from the server: \' + response);
	});
				        					}
})
});
  </script>';
}
// register required plugin javascripts
function mo_register_scripts() {
	if (is_admin ()) {
		// wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script ( 'jquery-ui-slider' );
		wp_enqueue_script ( 'jquery-toggles', plugins_url ( '/js/toggles.min.js', __FILE__ ) );
		
		add_action ( 'admin_footer', 'mo_jquery_tabs_init' );
		add_action ( 'admin_head', 'mo_register_styles' );
	}
}
// register required plugin styles
function mo_register_styles() {
		wp_enqueue_style ( 'jquery_ui-css', plugins_url ( '/css/jquery_ui.css', __FILE__ ) );
		wp_enqueue_style ( 'toggles-modern-css', plugins_url ( '/css/toggles-modern.css', __FILE__ ) );
		wp_enqueue_style ( 'custom_css', plugins_url ( '/css/custom.css', __FILE__ ) );
}
