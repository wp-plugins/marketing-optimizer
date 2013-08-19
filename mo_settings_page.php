<?php
//
if( isset( $_GET[ 'tab' ] ) ) {
	$active_tab = $_GET[ 'tab' ];
}else{
	$active_tab =  'mo_general_settings';
}

if (count ( $_POST ) > 0) {
	$moObj = new marketingoptimizer ( $_POST ['account_id'] );
}
foreach ( $_POST as $k => $v ) {
	if (property_exists ( 'marketingoptimizer', $k )) {
		$name = 'mo_' . $k;
		update_option ( $name, $v );
		$moObj->$k = $v;
	}
}
if (isset ( $_POST['action'] ) && ! isset ( $_POST ['marketing_optimizer'] )  && $active_tab == 'mo_integration_settings' ) {
	update_option ( 'mo_marketing_optimizer', 'false' );
}
if (isset ( $_POST['action'] ) && ! isset ( $_POST ['phone_tracking'] ) ) {
	update_option ( 'mo_phone_tracking', 'false' );
}
if (isset ( $_POST['action'] ) && ! isset ( $_POST ['variation_pages'] ) && $active_tab == 'mo_general_settings' ) {
	update_option ( 'mo_variation_pages', 'false' );
}
if ($_POST) {
	echo '<div class="updated" style="float:left;" >The Marketing Optimizer plugin settings updated</div>';
}
?>

<div class="wrap">
<div style="display:block;width:80%;float:left;">
	<h2 ><a href="http://www.marketingoptimizer.com" title="marketing optimizer logo" >
	<img src="<?php echo plugins_url().DS.MO_PLUGIN_DIRECTORY?>/images/mologo.png" /></a>
	 <?php echo "<span style=\"float:right;font-size:14px;padding-top:40px;font-style:italic;\">Version ". MO_CURRENT_VERSION ."</span>";?></h2>
	</div>
	<div style="width:80%">
	<h2 class="nav-tab-wrapper">  
    <a href="?page=mo/mo_settings_page.php&tab=mo_general_settings" class="nav-tab <?php echo $active_tab == 'mo_general_settings' ? 'nav-tab-active' : ''; ?>">General Settings</a>  
    <a href="?page=mo/mo_settings_page.php&tab=mo_integration_settings" class="nav-tab  <?php echo $active_tab == 'mo_integration_settings' ? 'nav-tab-active' : ''; ?>">Marketing Optimizer Integration</a>  
    <a href="?page=mo/mo_settings_page.php&tab=mo_shortcodes" class="nav-tab <?php echo $active_tab == 'mo_shortcodes' ? 'nav-tab-active' : ''; ?>">Shortcodes</a>  
</h2>  
	<div style="padding:20px;background-color:#ECECEC;">
		<form method="post" action="">
		<input type='hidden' name="action" value="mo_plugin_settings" />
<?php switch ($active_tab){ 
case 'mo_general_settings':?>		      <div id="tabs-1">
    			
    			<fieldset style="border-top:1px solid black;margin-bottom:20px;">
    			<legend><b>A/B Testing:</b></legend>
    			<table class="form-table">
					<tr valign="top">
						<td style="width:20%">Enable A/B Testing:</td>
						<td style="width:30%"><div class="toggle-abtesting toggle-modern"></div><input type="hidden" name="variation_pages" value="<?php echo get_option ( 'mo_variation_pages' ) == 'true'?'true':''; ?>"  /></td>
						<td style="width:50%"><p style="font-style: italic;">Check this box to enable A/B testing.</p></td>
					</tr>
					<tr>
					<td style="width:20%" colspan="2" ><label for="amount">Set Exploitation/Exploration Percentage for Variations</label></td>
					<td style="width:30%"><input type="text" id="amount" style="border: 0;background-color:#ECECEC; color: #2990BF;font-size:2em; font-weight: bold;width:17em;padding:5px;" />
					<input type="hidden" name="variation_percentage" id="variation_percentage" value="<?php echo get_option('mo_variation_percentage')?>" /></td>
					</tr>
					<tr><td colspan="2"><div id="slider-range-max"></div></td></tr>
				</table>
				</fieldset>
				
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</div>
			</form>
			<?php break;
case 'mo_integration_settings' :?>
<form method="post" action="">
		<input type='hidden' name="action" value="mo_plugin_settings" />
			<div id="tabs-3">
			<fieldset style="border-top:1px solid black;margin-bottom:20px;">
    			<legend><b>Account Settings:</b></legend>
				<table class="form-table">
				<tr valign="top">
						<td style="width:20%">Enable Marketing Optimizer Integration:</td>
						<td style="width:30%"><div class="toggle-mointegration toggle-modern"></div><input type="hidden" name="marketing_optimizer" value="<?php echo get_option ( 'mo_marketing_optimizer' ) == 'true'?'true':''; ?>"  /></td>
						<td style="width:50%"><p style="font-style: italic;"><a href="http://www.marketingoptimizer.com" >Learn more about Marketing Optimizer</a></p></td>
					</tr>
					<tr valign="top">
						<td style="width:20%">Account Id:</td>
						<td style="width:30%"><input type="text" name="account_id" value="<?php echo get_option ( 'mo_account_id' ); ?>" /></td>
						<td style="width:50%"><p>
								<span style="color: red;">*REQUIRED</span> this field is required for the all Marketing Optimizer plugin functionality to work.
							</p></td>
					</tr>
				</table>
				</fieldset>
				<fieldset style="border-top:1px solid black;margin-bottom:20px;">
    			<legend><b>Phone Tracking Settings:</b></legend>
    			<table class="form-table">
					<tr valign="top">
						<td style="width:20%">Enable Phone Tracking:</td>
						<td style="width:30%"><div class="toggle-phonetracking toggle-modern"></div><input type="hidden" name="phone_tracking" value="<?php echo get_option ( 'mo_phone_tracking' ) == 'true'?'true':''; ?>"  /></td>
						<td style="width:50%"><p style="font-style: italic;">Check this box to enable phone number tracking.</p></td>
					</tr>
					<tr valign="top">
						<td style="width:20%">Phone Publish Class:</td>
						<td style="width:30%"><input type="text" name="phone_publish_cls" value="<?php echo  get_option ( 'mo_phone_publish_cls' ); ?>" /></td>
						<td style="width:50%"><p style="font-style: italic;">Input the class name to be used for the phone tracking span.</p></td>
					</tr>
					<tr valign="top">
						<td style="width:20%">Default Phone Number:</td>
						<td style="width:30%"><input type="text" name="phone_tracking_default_number" value="<?php echo get_option ( 'mo_phone_tracking_default_number' ); ?>" /></td>
						<td style="width:50%"><p style="font-style: italic;">Input the default phone number to be used in the phone tracking span for users that don't have javascript enabled.</p></td>
					</tr>
					<tr valign="top">
						<td style="width:20%">Phone Tracking Thank You Url:</td>
						<td style="width:30%"><input type="text" name="phone_tracking_thank_you_url" value="<?php echo get_option('mo_phone_tracking_thank_you_url'); ?>" /></td>
						<td style="width:50%"><p style="font-style: italic;">Input the url of the thank you page to redirect users when they call a phone tracking number.</p></td>
					</tr>
				</table>
    			</fieldset>
    			<fieldset style="border-top:1px solid black;margin-bottom:20px;">
    			<legend><b>Form Settings:</b></legend>
    			<table class="form-table">
					<tr valign="top">
						<td style="width:20%">Default Form Id:</td>
						<td style="width:30%"><input type="text" name="form_default_id" value="<?php echo get_option ( 'mo_form_default_id' ); ?>" /></td>
						<td style="width:50%"><p style="font-style: italic;">Input default form id to be used for the form widget and form shortcodes.</p></td>
					</tr>
				</table>
    			</fieldset>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
				</p>
			</div>
			<?php break;
case 'mo_shortcodes':?>
	<fieldset style="border-top:1px solid black;margin-bottom:20px;">
	<legend><b>Shortcodes:</b></legend>
	<table class="form-table">
	<tr valign="top">
						<td>Form Shortcodes usage and examples <span style="font-style: italic;">(can be used in pages, posts, and text widgets)</span>:
						</td>
						<td><p>[mo_form] - shortcode to display form using the default form id.</p>
							<p>[mo_form id="xxxx"] - shortcode to display form by form id replace "xxxx" with the form id you wish to use.</p></td>
					</tr>
					<tr valign="top">
						<td>Phone Shortcodes usage and examples <span style="font-style: italic;">(can be used in pages, posts, and text widgets)</span>:
						</td>
						<td><p>[mo_phone] - shortcode to display visitor phone tracking numbers.</p></td>
					</tr>
	<tr valign="top">
	<td>A/B testing conversion page shortcode usage and examples <span style="font-style: italic;">(can be used in pages, posts, and text widgets)</span>:
	</td>
	<td><p>[mo_conversion] - shortcode to add A/B testing conversion tracking to a page.</p>
	</tr>
	</table>
	</fieldset>
<?php break;
default: echo "There is no content for this tab.";}?>
		</form>
	</div>
	</div>
	
	<div style="width:20%;"></div>
</div>