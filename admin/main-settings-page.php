<?php
//
if (isset ( $_GET ['tab'] )) {
	$active_tab = $_GET ['tab'];
} else {
	$active_tab = 'mo_lp_general_settings';
}
$mo_settings_obj = new mo_settings ();
if ($_POST) {
	if(isset($_POST['action']) && $_POST['action'] == 'mo_gf_field_mapping'){
		
		mo_gravity_forms::mo_save_form_field_mapping($_POST['mo_gf_form'], $_POST);
	}
	echo '<div class="updated" style="float:left;" >The Marketing Optimizer plugin settings have been updated</div>';
	
	switch ($active_tab) {
		case 'mo_lp_general_settings' :
			$mo_settings_obj->set_mo_lp_permalink_prefix ( $_POST ['mo_lp_permalink_prefix'] );
			if (! isset ( $_POST ['mo_lp_cache_compatible'] )) {
				$mo_settings_obj->set_mo_lp_cache_compatible ( 'false' );
			} else {
				$mo_settings_obj->set_mo_lp_cache_compatible ( $_POST ['mo_lp_cache_compatible'] );
			}
			if (! isset ( $_POST ['mo_lp_track_admin'] )) {
				$mo_settings_obj->set_mo_lp_track_admin ( 'false' );
			} else {
				$mo_settings_obj->set_mo_lp_track_admin ( $_POST ['mo_lp_track_admin'] );
			}
			if (isset ( $_POST ['mo_lp_variation_percentage'] )) {
				$mo_settings_obj->set_mo_lp_variation_percentage ( $_POST ['mo_lp_variation_percentage'] );
			}
			break;
		case 'mo_integration_settings' :
			if (! isset ( $_POST ['mo_marketing_optimizer'] )) {
				$mo_settings_obj->set_mo_marketing_optimizer ( 'false' );
			} else {
				$mo_settings_obj->set_mo_marketing_optimizer ( $_POST ['mo_marketing_optimizer'] );
			}
			if (isset ( $_POST ['mo_account_id'] )) {
				$mo_settings_obj->set_mo_account_id ( $_POST ['mo_account_id'] );
			}
			if (! isset ( $_POST ['mo_phone_tracking'] )) {
				$mo_settings_obj->set_mo_phone_tracking ( 'false' );
			} else {
				$mo_settings_obj->set_mo_phone_tracking ( $_POST ['mo_phone_tracking'] );
			}
			if (isset ( $_POST ['mo_phone_publish_cls'] )) {
				$mo_settings_obj->set_mo_phone_publish_cls ( $_POST ['mo_phone_publish_cls'] );
			}
			if (isset ( $_POST ['mo_phone_tracking_default_number'] )) {
				$mo_settings_obj->set_mo_phone_tracking_default_number ( $_POST ['mo_phone_tracking_default_number'] );
			}
			if (isset ( $_POST ['mo_phone_tracking_thank_you_url'] )) {
				$mo_settings_obj->set_mo_phone_tracking_thank_you_url ( $_POST ['mo_phone_tracking_thank_you_url'] );
			}
			if (isset ( $_POST ['mo_form_default_id'] )) {
				$mo_settings_obj->set_mo_form_default_id ( $_POST ['mo_form_default_id'] );
			}
			break;
		case 'mo_sp_general_settings' :
			$mo_settings_obj->set_mo_sp_permalink_prefix ( $_POST ['mo_sp_permalink_prefix'] );
			if (! isset ( $_POST ['mo_sp_track_admin'] )) {
				$mo_settings_obj->set_mo_sp_track_admin ( 'false' );
			} else {
				$mo_settings_obj->set_mo_sp_track_admin ( $_POST ['mo_sp_track_admin'] );
			}
			if (isset ( $_POST ['mo_sp_variation_percentage'] )) {
				$mo_settings_obj->set_mo_sp_variation_percentage ( $_POST ['mo_sp_variation_percentage'] );
			}
			if (isset ( $_POST ['mo_sp_show_time'] )) {
				$mo_settings_obj->set_mo_sp_show_time ( $_POST ['mo_sp_show_time'] );
			}
			break;
	}
	
	$mo_settings_obj->save ();
}
$cache_compatible = $mo_settings_obj->get_mo_lp_cache_compatible () ? $mo_settings_obj->get_mo_lp_cache_compatible () : 'false';
$track_admin = $mo_settings_obj->get_mo_lp_track_admin () ? $mo_settings_obj->get_mo_lp_track_admin () : 'false';
$mo_sp_track_admin = $mo_settings_obj->get_mo_sp_track_admin () ? $mo_settings_obj->get_mo_sp_track_admin () : 'false';
$mo_integration = $mo_settings_obj->get_mo_marketing_optimizer () ? $mo_settings_obj->get_mo_marketing_optimizer () : 'false';
$mo_phone_tracking = $mo_settings_obj->get_mo_phone_tracking () ? $mo_settings_obj->get_mo_phone_tracking () : 'false';
echo '<script>
	jQuery(document).ready(function(){
				jQuery(\'.toggle-cachecompatible\').toggles({on:' . $cache_compatible . '});
			   jQuery(\'.toggle-cachecompatible\').on(\'toggle\',function(e,active){
				        		if(active){
				        			jQuery(\'[name="mo_lp_cache_compatible"]\').val("true");
				        		}else{
				        			jQuery(\'[name="mo_lp_cache_compatible"]\').val("");
				        		}
			        		});
				jQuery(\'.toggle-trackadmin\').toggles({on:' . $track_admin . '});
				jQuery(\'.toggle-trackadmin\').on(\'toggle\',function(e,active){
				if(active){
					jQuery(\'[name="mo_lp_track_admin"]\').val("true");
				}else{
					jQuery(\'[name="mo_lp_track_admin"]\').val("");
				}
				});		
				
				jQuery(\'.toggle-mosptrackadmin\').toggles({on:' . $mo_sp_track_admin . '});
				jQuery(\'.toggle-mosptrackadmin\').on(\'toggle\',function(e,active){
				if(active){
					jQuery(\'[name="mo_sp_track_admin"]\').val("true");
				}else{
					jQuery(\'[name="mo_sp_track_admin"]\').val("");
				}
});
						jQuery(\'.toggle-mointegration\').toggles({on:' . $mo_integration . '});
				jQuery(\'.toggle-mointegration\').on(\'toggle\',function(e,active){
				if(active){
					jQuery(\'[name="mo_marketing_optimizer"]\').val("true");
				}else{
					jQuery(\'[name="mo_marketing_optimizer"]\').val("");
				}
});
								jQuery(\'.toggle-phonetracking\').toggles({on:' . $mo_phone_tracking . '});
				jQuery(\'.toggle-phonetracking\').on(\'toggle\',function(e,active){
				if(active){
					jQuery(\'[name="mo_phone_tracking"]\').val("true");
				}else{
					jQuery(\'[name="mo_phone_tracking"]\').val("");
				}
});
						});
									
</script>';
?>

<div class="wrap">
	<div style="display: block; width: 80%; float: left;">
		<h2>
			<a href="http://www.marketingoptimizer.com/?apcid=8381"
				title="marketing optimizer logo"> <img
				src="<?php echo plugins_url()?>/marketing-optimizer/images/mologo.png" /></a>
	 <?php echo "<span style=\"float:right;font-size:14px;padding-top:40px;font-style:italic;\">Version ".mo_plugin::get_version() ."</span>";?></h2>
	</div>
	<div style="width: 80%">
		<h2 class="nav-tab-wrapper">
			<a
				href="?page=marketing-optimizer-settings&tab=mo_lp_general_settings"
				class="nav-tab <?php echo $active_tab == 'mo_lp_general_settings' ? 'nav-tab-active' : ''; ?>">Landing Page Settings</a> <a
				href="?page=marketing-optimizer-settings&tab=mo_sp_general_settings"
				class="nav-tab <?php echo $active_tab == 'mo_sp_general_settings' ? 'nav-tab-active' : ''; ?>">Squeeze
				Page Settings </a> <a
				href="?page=marketing-optimizer-settings&tab=mo_integration_settings"
				class="nav-tab  <?php echo $active_tab == 'mo_integration_settings' ? 'nav-tab-active' : ''; ?>">Marketing
				Optimizer Integration</a> <a
				href="?page=marketing-optimizer-settings&tab=mo_gf_integration"
				class="nav-tab  <?php echo $active_tab == 'mo_gf_integration' ? 'nav-tab-active' : ''; ?>">Gravity
				Forms Integration</a>

		</h2>
		<div
			style="padding: 20px; background-color: #ECECEC; border-left: 1px solid #ccc; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc;">
			<form method="post" action="">
		
<?php

switch ($active_tab) {
	case 'mo_lp_general_settings' :
		$mo_lp_permalink = $mo_settings_obj->get_mo_lp_permalink_prefix()?$mo_settings_obj->get_mo_lp_permalink_prefix():'mo_lp';
		?>		  
    <div id="tabs-1">
					<input type='hidden' name="action" value="mo_lp_plugin_settings" />
					<table class="form-table">
						<tr valign="top">
							<td style="width: 20%">Landing Page Permalink Prefix:</td>
							<td style="width: 30%"><div
									class="toggle-abtesting toggle-modern"></div> <input
								type="text" name="mo_lp_permalink_prefix"
								value="<?php echo $mo_lp_permalink ?>" /></td>
							<td style="width: 50%"><p style="font-style: italic;">This will
									prefix your landing page permalinks, ex.
									http://www.yoursite.com/{ prefix }/landing-page-slug</p></td>
						</tr>
						<tr valign="top">
							<td style="width: 20%">Cache Compatability:</td>
							<td style="width: 30%"><div
									class="toggle-cachecompatible toggle-modern"></div> <input
								type="hidden" name="mo_lp_cache_compatible"
								value="<?php echo $mo_settings_obj->get_mo_lp_cache_compatible() == 'true'?'true':''; ?>" /></td>
							<td style="width: 50%"><p style="font-style: italic;">Turn on/off
									Cache compatability.</p></td>
						</tr>
						<tr valign="top">
							<td style="width: 20%">Track Admin Users:</td>
							<td style="width: 30%"><div
									class="toggle-trackadmin toggle-modern"></div> <input
								type="hidden" name="mo_lp_track_admin"
								value="<?php echo $mo_settings_obj->get_mo_lp_track_admin() == 'true'?'true':''; ?>" /></td>
							<td style="width: 50%"><p style="font-style: italic;">Turn on/off
									Admin User Tracking.</p></td>
						</tr>
						<tr>
							<td style="width: 20%" colspan="2"><label for="amount">Set
									Exploitation/Exploration Percentage for Variations</label></td>
							<td style="width: 30%"><input type="text" id="mo_lp_amount"
								style="border: 0; background-color: #ECECEC; color: #2990BF; font-size: 2em; font-weight: bold; width: 18em; padding: 5px;" />
								<input type="hidden" name="mo_lp_variation_percentage"
								id="mo_lp_variation_percentage"
								value="<?php echo $mo_settings_obj->get_mo_lp_variation_percentage()?>" /></td>
						</tr>
						<tr>
							<td colspan="2"><div id="mo_lp_slider-range-max"></div></td>
						</tr>
					</table>
					<p class="submit">
						<input type="submit" class="button-primary"
							value="<?php _e('Save Changes') ?>" />
					</p>
				</div>
		
		</div>
		</form>
			<?php
		
		break;
	case 'mo_sp_general_settings' :
		$mo_sp_permalink = $mo_settings_obj->get_mo_sp_permalink_prefix()?$mo_settings_obj->get_mo_sp_permalink_prefix():'mo_sp';
		$mo_sp_showtime = $mo_settings_obj->get_mo_sp_show_time()?$mo_settings_obj->get_mo_sp_show_time():15;
		?>
		    <div id="tabs-2">
			<input type='hidden' name="action" value="mo_sp_plugin_settings" />
			<table class="form-table">
				<tr valign="top">
					<td style="width: 20%">Squeeze Page Permalink Prefix:</td>
					<td>
						<input type="text" name="mo_sp_permalink_prefix" value="<?php echo $mo_sp_permalink ?>" /></td>
					<td style="width: 50%"><p style="font-style: italic;">This will
							prefix your squeeze page permalinks, ex.
							http://www.yoursite.com/{ prefix }/squeeze-page-slug</p></td>
				</tr>
				<tr valign="top">
					<td style="width: 20%">Show Squeeze Page After:</td>
					<td style="width: 30%">
						<input type="text" name="mo_sp_show_time" value="<?php echo $mo_sp_showtime ?>" /> Seconds</td>
					<td style="width: 50%"><p style="font-style: italic;">How many seconds to wait till automatically showing the squuze page</p></td>
				</tr>
				<tr valign="top">
					<td style="width: 20%">Track Admin Users:</td>
					<td style="width: 30%"><div class="toggle-mosptrackadmin toggle-modern"></div>
						<input type="hidden" name="mo_sp_track_admin"
						value="<?php echo $mo_settings_obj->get_mo_sp_track_admin() == 'true'?'true':''; ?>" /></td>
					<td style="width: 50%"><p style="font-style: italic;">Turn on/off
							Admin User Tracking.</p></td>
				</tr>
				<tr>
					<td style="width: 20%" colspan="2"><label for="amount">Set
							Exploitation/Exploration Percentage for Variations</label></td>
					<td style="width: 30%"><input type="text" id="amount"
						style="border: 0; background-color: #ECECEC; color: #2990BF; font-size: 2em; font-weight: bold; width: 18em; padding: 5px;" />
						<input type="hidden" name="mo_sp_variation_percentage"
						id="mo_sp_variation_percentage"
						value="<?php echo $mo_settings_obj->get_mo_sp_variation_percentage()?>" /></td>
				</tr>
				<tr>
					<td colspan="2"><div id="mo_sp_slider-range-max"></div></td>
				</tr>
			</table>
			<p class="submit">
				<input type="submit" class="button-primary"
					value="<?php _e('Save Changes') ?>" />
			</p>
		</div>

	</div>
	</form>
					<?php
		
		break;
	case 'mo_integration_settings' :
		?>
		<input type='hidden' name="action" value="mo_plugin_settings" />
	<div id="tabs-3">
		<fieldset style="border-top: 1px solid black; margin-bottom: 20px;">
			<legend>
				<b>Account Settings:</b>
			</legend>
			<table class="form-table">
				<tr valign="top">
					<td style="width: 20%">Marketing Optimizer Integration:</td>
					<td style="width: 30%"><div
							class="toggle-mointegration toggle-modern"></div> <input
						type="hidden" name="mo_marketing_optimizer"
						value="<?php echo $mo_settings_obj->get_mo_marketing_optimizer() == 'true'?'true':''; ?>" /></td>
					<td style="width: 50%"><p style="font-style: italic;">
							<a href="http://www.marketingoptimizer.com/?apcid=8381">Learn
								more about Marketing Optimizer</a>
						</p></td>
				</tr>
				<tr valign="top">
					<td style="width: 20%">Account Id:</td>
					<td style="width: 30%"><input type="text" name="mo_account_id"
						value="<?php echo $mo_settings_obj->get_mo_account_id(); ?>" /></td>
					<td style="width: 50%"><p>
							<span style="color: red;">*REQUIRED</span> this field is required
							for the all Marketing Optimizer plugin functionality to work.
						</p></td>
				</tr>
			</table>
		</fieldset>
		<fieldset style="border-top: 1px solid black; margin-bottom: 20px;">
			<legend>
				<b>Phone Tracking Settings:</b>
			</legend>
			<table class="form-table">
				<tr valign="top">
					<td style="width: 20%">Phone Tracking:</td>
					<td style="width: 30%"><div
							class="toggle-phonetracking toggle-modern"></div> <input
						type="hidden" name="mo_phone_tracking"
						value="<?php echo $mo_settings_obj->get_mo_phone_tracking() == 'true'?'true':''; ?>" /></td>
					<td style="width: 50%"><p style="font-style: italic;">Turn on/off
							phone number tracking.</p></td>
				</tr>
				<tr valign="top">
					<td style="width: 20%">Phone Publish Class:</td>
					<td style="width: 30%"><input type="text"
						name="mo_phone_publish_cls"
						value="<?php echo  $mo_settings_obj->get_mo_phone_publish_cls(); ?>" /></td>
					<td style="width: 50%"><p style="font-style: italic;">Input the
							class name to be used for the phone tracking span.</p></td>
				</tr>
				<tr valign="top">
					<td style="width: 20%">Default Phone Number:</td>
					<td style="width: 30%"><input type="text"
						name="mo_phone_tracking_default_number"
						value="<?php echo $mo_settings_obj->get_mo_phone_tracking_default_number(); ?>" /></td>
					<td style="width: 50%"><p style="font-style: italic;">Input the
							default phone number to be used in the phone tracking span for
							users that don't have javascript enabled.</p></td>
				</tr>
				<tr valign="top">
					<td style="width: 20%">Phone Tracking Thank You Url:</td>
					<td style="width: 30%"><input type="text"
						name="mo_phone_tracking_thank_you_url"
						value="<?php echo $mo_settings_obj->get_mo_phone_tracking_thank_you_url(); ?>" /></td>
					<td style="width: 50%"><p style="font-style: italic;">Input the url
							of the thank you page to redirect users when they call a phone
							tracking number.</p></td>
				</tr>
			</table>
		</fieldset>
		<fieldset style="border-top: 1px solid black; margin-bottom: 20px;">
			<legend>
				<b>Form Settings:</b>
			</legend>
			<table class="form-table">
				<tr valign="top">
					<td style="width: 20%">Default Form Id:</td>
					<td style="width: 30%"><input type="text" name="mo_form_default_id"
						value="<?php echo get_option ( 'mo_form_default_id' ); ?>" /></td>
					<td style="width: 50%"><p style="font-style: italic;">Input default
							form id to be used for the form widget and form shortcodes.</p></td>
				</tr>
			</table>
		</fieldset>
		<p class="submit">
			<input type="submit" class="button-primary"
				value="<?php _e('Save Changes') ?>" />
		</p>
	</div>
			<?php
		
		break;
	case 'mo_gf_integration' :
		?>
			<input type='hidden' name="action" value="mo_gf_field_mapping" />
	<div id="tabs-4">

		<fieldset style="border-top: 1px solid black; margin-bottom: 20px;">
			<legend>
				<b>Gravity Forms Integration:</b>
			</legend>
			<table class="form-table">
				<tr valign="top">
					<td style="width: 20%">Select Form to Map Fields:</td>
					<td style="width: 30%"><?php echo mo_gravity_forms::mo_get_gf_dropdown(); ?></td>
				</tr>
			</table>
		</fieldset>
		<div id="form_field_mapping_table"></div>
		<p class="submit">
			<input type="submit" class="button-primary mo_gf_submit"
				value="<?php _e('Save Changes') ?>" />
		</p>
	</div>
	</form>
		<?php
		
break;
}
?>
		
		</div>
</div>

<div style="width: 20%;"></div>
</div>