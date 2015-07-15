<?php
$mo_settings_obj = new mo_settings ();
if ($_POST) {
	if(isset($_POST['action']) && $_POST['action'] == 'mo_gf_field_mapping'){
		mo_gravity_forms::mo_save_form_field_mapping($_POST['mo_gf_form'], $_POST);
	}
	echo '<div class="updated">The Marketing Optimizer plugin settings have been updated</div>';
            $mo_settings_obj->set_mo_lp_permalink_prefix ( $_POST ['mo_lp_permalink_prefix'] );

            if (! isset ( $_POST ['mo_lp_cache_compatible'] )) {
                    $mo_settings_obj->set_mo_lp_cache_compatible ( 'false' );
            } else {
                    $mo_lp_cache_compatible = ($_POST ['mo_lp_cache_compatible']=="")?'false':$_POST ['mo_lp_cache_compatible'];
                    $mo_settings_obj->set_mo_lp_cache_compatible ( $mo_lp_cache_compatible) ;
            }
            if (! isset ( $_POST ['mo_lp_track_admin'] )) {
                    $mo_settings_obj->set_mo_lp_track_admin ( 'false' );
            } else {
                    $mo_settings_obj->set_mo_lp_track_admin ( $_POST ['mo_lp_track_admin'] );
            }
            if (isset ( $_POST ['mo_lp_variation_percentage'] )) {
                    $mo_settings_obj->set_mo_lp_variation_percentage ( $_POST ['mo_lp_variation_percentage'] );
            }
            if (! isset ( $_POST ['mo_marketing_optimizer'] )) {
                    $mo_settings_obj->set_mo_marketing_optimizer ( 'false' );
            } else {
                    $mo_settings_obj->set_mo_marketing_optimizer ( $_POST ['mo_marketing_optimizer'] );
            }
            if (isset ( $_POST ['mo_username'] )) {
                    $mo_settings_obj->set_mo_username ( $_POST ['mo_username'] );
            }
            if (isset ( $_POST ['mo_password'] )) {
                    $mo_settings_obj->set_mo_password ( $_POST ['mo_password'] );
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
            if (!isset($_POST ['mo_phone_ctc'])) {
                    $mo_settings_obj->set_mo_phone_ctc('false');
            } else {
                    $mo_settings_obj->set_mo_phone_ctc($_POST ['mo_phone_ctc']);
            }
            if (isset ( $_POST ['mo_phone_tracking_thank_you_url'] )) {
                    $thanks_page = strtolower($_POST ['mo_phone_tracking_thank_you_url']);
                    if($thanks_page!="") {
                        $find = array('http://www.',"http://","www.");
                        $rep = array('','');
                        $thanks_page = str_replace($find,$rep,$thanks_page);
                        $thanks_page = 'http://www.'.$thanks_page; 
                    }
                    $mo_settings_obj->set_mo_phone_tracking_thank_you_url ( $thanks_page );
            }
            if (isset ( $_POST ['mo_ct_permalink_prefix'] )) {
                    $mo_settings_obj->set_mo_ct_permalink_prefix ( $_POST ['mo_ct_permalink_prefix'] );
            }
            if (isset ( $_POST ['mo_form_default_id'] )) {
                    $mo_settings_obj->set_mo_form_default_id( $_POST ['mo_form_default_id'] );
            }
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

	}
	
	$mo_settings_obj->save ();

$cache_compatible = $mo_settings_obj->get_mo_lp_cache_compatible () ? $mo_settings_obj->get_mo_lp_cache_compatible () : 'false';
$track_admin = $mo_settings_obj->get_mo_lp_track_admin () ? $mo_settings_obj->get_mo_lp_track_admin () : 'false';
$mo_sp_track_admin = $mo_settings_obj->get_mo_sp_track_admin () ? $mo_settings_obj->get_mo_sp_track_admin () : 'false';
$mo_integration = $mo_settings_obj->get_mo_marketing_optimizer () ? $mo_settings_obj->get_mo_marketing_optimizer () : 'false';
$mo_phone_tracking = $mo_settings_obj->get_mo_phone_tracking () ? $mo_settings_obj->get_mo_phone_tracking () : 'false';
$mo_is_gravityforms_active = ( class_exists( 'GFForms' ) ) ? true : false;
$mo_phone_ctc = $mo_settings_obj->get_mo_phone_ctc()?$mo_settings_obj->get_mo_phone_ctc():'false';
echo '<script>
        var mo_account_id = "";
        mo_account_id = "'.$mo_settings_obj->get_mo_account_id().'";
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
		if('. $mo_integration .' == false || mo_account_id == ""){
			jQuery(\'#toggle-marketing-setting\').hide();
		}
		jQuery(\'.toggle-mointegration\').on(\'toggle\',function(e,active){
			if(active){
				jQuery(\'[name="mo_marketing_optimizer"]\').val("true");
				jQuery(\'#toggle-marketing-setting\').slideDown();
				
			}else{
				jQuery(\'[name="mo_marketing_optimizer"]\').val("");
				jQuery(\'#toggle-marketing-setting\').slideUp();
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
                
                jQuery(\'.toggle-phone-ctc\').toggles({on:' . $mo_phone_ctc . '});
                        jQuery(\'.toggle-phone-ctc\').on(\'toggle\',function(e,active){
                        if(active){
                                jQuery(\'[name="mo_phone_ctc"]\').val("true");
                        }else{
                                jQuery(\'[name="mo_phone_ctc"]\').val("");
                        }
                });
	});
									
</script>';
?>
<div class="wrap">
	<div style="display: block;">
		<h2>
                    <a href="http://www.marketingoptimizer.com/?apcid=8381"	title="marketing optimizer logo"> <img	src="<?php echo plugins_url()?>/marketing-optimizer/images/mologo.png" /></a>
                    <?php echo "<span style=\"float:right;font-size:14px;padding-top:40px;font-style:italic;\">Version ".mo_plugin::get_version() ."</span>";?>
		</h2>
	</div>
	
	<form method="post" action="">
		<?php $mo_account_id = $mo_settings_obj->get_mo_account_id() ? $mo_settings_obj->get_mo_account_id():"";?>
		<input type="hidden" name="mo_account_id" value="<?php echo $mo_account_id ;?>">
		<div class="marketing-setting-block">
			<div class="block-title">General</div>
			<div class="marketing-block-content">
				<table class="form-table">
					<tr valign="top">
						<td style="width: 20%">Cache Compatability:</td>
						<td style="width: 30%"><div class="toggle-cachecompatible toggle-modern"></div> 
						<input type="hidden" name="mo_lp_cache_compatible" value="<?php echo $mo_settings_obj->get_mo_lp_cache_compatible() == 'true'?'true':''; ?>" /></td>
						<td style="width: 50%"><p style="font-style: italic;">Turn on/off Cache compatability.</p></td>
					</tr>
					<tr valign="top">
						<td style="width: 20%">Track Admin Users:</td>
						<td style="width: 30%">
							<div class="toggle-trackadmin toggle-modern"></div>
							<input type="hidden" name="mo_lp_track_admin" value="<?php echo $mo_settings_obj->get_mo_lp_track_admin() == 'true'?'true':''; ?>" />
						</td>
						<td style="width: 50%"><p style="font-style: italic;">Turn on/off Admin User Tracking.</p></td>
					</tr>
					<tr>
						<td style="width: 20%" colspan="2">
						<label for="amount">Set Exploitation/Exploration Percentage for Variations</label></td>
						<td style="width: 30%"><input type="text" id="mo_lp_amount" style="border: 0; background-color: #ECECEC; color: #2990BF; font-size: 2em; font-weight: bold; width: 18em; padding: 5px;" />
						<input type="hidden" name="mo_lp_variation_percentage" id="mo_lp_variation_percentage" value="<?php echo $mo_settings_obj->get_mo_lp_variation_percentage()?>" /></td>
					</tr>
					<tr>
						<td colspan="2"><div id="mo_lp_slider-range-max"></div></td>
					</tr>
				</table>
				
				<div class="sumit-block">
					<p class="submit"><input type="submit" class="button-primary mo_gf_submit hello" value="<?php _e('Save Changes') ?>" /></p>
				</div>
			</div>
		</div>
		<!------------------------------------------------------------>
		
		<div class="marketing-setting-block">
			<div class="block-title">Marketing Optimizer Integration</div>
			<div class="marketing-block-content">
				<input type='hidden' name="action" value="mo_plugin_settings" />
				<fieldset style="border-top: 1px solid black; margin-bottom: 20px;">
					<legend><b>Account Settings:</b></legend>
					<table class="form-table">
						<tr valign="top">
							<td style="width: 20%">Marketing Optimizer Integration:</td>
							<td style="width: 30%"><div class="toggle-mointegration toggle-modern"></div> 
							<input type="hidden" name="mo_marketing_optimizer" value="<?php echo $mo_settings_obj->get_mo_marketing_optimizer() == 'true'?'true':''; ?>" /></td>
							<td style="width: 50%">
								<p style="font-style: italic;">	<a href="http://www.marketingoptimizer.com/?apcid=8381">Learn more about Marketing Optimizer</a></p>
							</td>
						</tr>
					</table>	
					<div id="toggle-marketing-setting">
						<table class="form-table">
							<tr valign="top">
								<td style="width: 20%">Marketing Optimizer Username:</td>
								<td style="width: 30%"><input type="text" name="mo_username" value="<?php echo $mo_settings_obj->get_mo_username(); ?>" /></td>
								<td style="width: 50%"><p><span style="color: red;">*REQUIRED</span> this field is required for the all Marketing Optimizer plugin functionality to work.</p></td>
							</tr>
							
							<tr valign="top">
								<td style="width: 20%">Account Password:</td>
								<td style="width: 30%"><input type="password" name="mo_password" value="<?php echo $mo_settings_obj->get_mo_password(); ?>" /></td>
								<td style="width: 50%"><p><span style="color: red;">*REQUIRED</span> this field is required for the all Marketing Optimizer plugin functionality to work.</p></td>
							</tr>
							
							<tr>
								<td colspan=3><legend><b>Form Settings:</b></legend></td>
							</tr>
							
							<tr valign="top">
								<td style="width: 20%">Default Form:</td>
								<td style="width: 30%">
									<?php
										if($mo_is_gravityforms_active){
											echo mo_gravity_forms::mo_get_mf_default_forms( get_option ( 'mo_form_default_id' ));
										} else {
									?>
										<input type="text" name="mo_form_default_id" value="<?php echo get_option ( 'mo_form_default_id' ); ?>" />
									<?php
										}
									?>
								</td>
								<td style="width: 50%"><p style="font-style: italic;">Input default form id to be used for the form widget and form shortcodes.</p></td>
							</tr>
							
						</table>
					</div>
				</fieldset>

				<div class="sumit-block">
					<p class="submit"><input type="submit" class="button-primary mo_gf_submit hello" value="<?php _e('Save Changes') ?>" /></p>
				</div>
			</div>
		</div>
		<!--------------------------------------------------------------------------->
		<div class="marketing-setting-block">
			<?php if($mo_settings_obj->get_mo_marketing_optimizer() != true || $mo_settings_obj->get_mo_account_id() == "" ) {?>
			<div class="disable-phone-tracking"><div>Marketing Optimizer Integration must be turned on for it to function </div></div>
			<?php } ?>
			
			<div class="block-title">Phone Tracking Settings</div>
			<div class="marketing-block-content">
				<?php $mo_phone_publish_permalink = $mo_settings_obj->get_mo_phone_publish_cls()?$mo_settings_obj->get_mo_phone_publish_cls():'phonePublishCls';	?>		  
				<table class="form-table">
					<tr valign="top">
						<td style="width: 20%">Phone Tracking:</td>
						<td style="width: 30%"><div class="toggle-phonetracking toggle-modern"></div> 
						<input type="hidden" name="mo_phone_tracking" value="<?php echo $mo_settings_obj->get_mo_phone_tracking() == 'true'?'true':''; ?>" /></td>
						<td style="width: 50%"><p style="font-style: italic;">Turn on/off phone number tracking.</p></td>
					</tr>
                                        
                                        <tr valign="top">
                                            <td style="width: 20%">Mobile Click to Call:</td>
                                            <td style="width: 30%"><div class="toggle-phone-ctc toggle-modern"></div> 
                                            <input type="hidden" name="mo_phone_ctc"  value="<?php echo $mo_settings_obj->get_mo_phone_ctc() == 'true' ? 'true' : ''; ?>" /></td>
                                            <td style="width: 50%"><p style="font-style: italic;">Turn on/off mobile phone click to call.</p></td>
                                        </tr>
					
					<tr valign="top">
						<td style="width: 20%">Phone Publish Class:</td>
						<td style="width: 30%"><input type="text" name="mo_phone_publish_cls" value="<?php echo  $mo_phone_publish_permalink; ?>" /></td>
						<td style="width: 50%"><p style="font-style: italic;">Input the class name to be used for the phone tracking span.</p></td>
					</tr>
					
					<tr valign="top">
						<td style="width: 20%">Default Phone Number:</td>
						<td style="width: 30%"><input type="text" name="mo_phone_tracking_default_number" value="<?php echo $mo_settings_obj->get_mo_phone_tracking_default_number(); ?>" /></td>
						<td style="width: 50%"><p style="font-style: italic;">Input the default phone number to be used in the phone tracking span for users that don't have javascript enabled.</p></td>
					</tr>
					
					<tr valign="top">
						<td style="width: 20%">Phone Tracking Thank You Url:</td>
                                                <td style="width: 30%"><input type="text" name="mo_phone_tracking_thank_you_url" value="<?php echo $mo_settings_obj->get_mo_phone_tracking_thank_you_url(); ?>" /></td>
						<td style="width: 50%"><p style="font-style: italic;">Input the url of the thank you page to redirect users when they call a phone tracking number. ex.<?php echo get_bloginfo('url');?>/thank-you</p></td>
					</tr>
				</table>
					
				<div class="sumit-block">
					<p class="submit"><input type="submit" class="button-primary mo_gf_submit hello" value="<?php _e('Save Changes') ?>" /></p>
				</div>
			</div>
		</div>
	<!--------------------------------------------------------------------------->
		<div class="marketing-setting-block">
			<div class="block-title">Landing Page Settings</div>
			<div class="marketing-block-content">
				<?php $mo_lp_permalink = $mo_settings_obj->get_mo_lp_permalink_prefix()?$mo_settings_obj->get_mo_lp_permalink_prefix():'mo_lp';	?>		  
				<input type='hidden' name="action" value="mo_lp_plugin_settings" />
				<table class="form-table">
					<tr valign="top">
                                            <td style="width: 20%">Landing Page Permalink Prefix:</td>
                                            <td style="width: 30%"><div class="toggle-abtesting toggle-modern"></div> 
                                            <input type="text" name="mo_lp_permalink_prefix" value="<?php echo $mo_lp_permalink ?>" /></td>
                                            <td style="width: 50%"><p style="font-style: italic;">This will prefix your landing page permalinks, ex.http://www.yoursite.com/{ prefix }/landing-page-slug</p></td>
                                        </tr>
				</table>
					
				<div class="sumit-block">
					<p class="submit"><input type="submit" class="button-primary mo_gf_submit hello" value="<?php _e('Save Changes') ?>" /></p>
				</div>
			</div>
		</div>
		<!--------------------------------------------------------------------------->
		<div class="marketing-setting-block">
			<div class="block-title">Pop Up Settings</div>
			<div class="marketing-block-content">
				<?php
				$mo_sp_permalink = $mo_settings_obj->get_mo_sp_permalink_prefix()?$mo_settings_obj->get_mo_sp_permalink_prefix():'mo_sp';
				$mo_sp_showtime = $mo_settings_obj->get_mo_sp_show_time()?$mo_settings_obj->get_mo_sp_show_time():15;
				?>
				<input type='hidden' name="action" value="mo_sp_plugin_settings" />
				<table class="form-table">
					<tr valign="top">
						<td style="width: 20%">Pop Up Permalink Prefix:</td>
						<td><input type="text" name="mo_sp_permalink_prefix" value="<?php echo $mo_sp_permalink ?>" /></td>
						<td style="width: 50%"><p style="font-style: italic;">This will prefix your pop-ups permalinks, ex. http://www.yoursite.com/{ prefix }/pop-ups-slug</p></td>
					</tr>
					<tr valign="top">
						<td style="width: 20%">Show Pop up After:</td>
						<td style="width: 30%">
						<input type="text" name="mo_sp_show_time" value="<?php echo $mo_sp_showtime ?>" /> Seconds</td>
						<td style="width: 50%"><p style="font-style: italic;">How many seconds to wait till automatically showing the pop up page</p></td>
					</tr>
				</table>

				<div class="sumit-block">
					<p class="submit"><input type="submit" class="button-primary mo_gf_submit hello" value="<?php _e('Save Changes') ?>" /></p>
				</div>
			</div>
		</div>	
		<!--------------------------------------------------------->
		
		<div class="marketing-setting-block">
			<div class="block-title">Call-To-Action Settings</div>
			<div class="marketing-block-content">
				<?php $mo_ct_permalink = $mo_settings_obj->get_mo_ct_permalink_prefix()? $mo_settings_obj->get_mo_ct_permalink_prefix():'mo_ct';	?>		  
				<input type='hidden' name="action" value="mo_ct_plugin_settings" />
				<table class="form-table">
					<tr valign="top">
                                            <td style="width: 20%">Call-To-Action Permalink Prefix:</td>
                                            <td style="width: 30%"><div class="toggle-abtesting toggle-modern"></div> 
                                            <input type="text" name="mo_ct_permalink_prefix" value="<?php echo $mo_ct_permalink ?>" /></td>
                                            <td style="width: 50%"><p style="font-style: italic;">This will prefix your call-to-action permalinks</p></td>
					</tr>
					
				</table>

				<div class="sumit-block">
					<p class="submit"><input type="submit" class="button-primary mo_gf_submit hello" value="<?php _e('Save Changes') ?>" /></p>
				</div>
			</div>
		</div>	
		<!---------------------------------------------------------------------------------------->
		
		<div class="marketing-setting-block">
			<?php if(!$mo_is_gravityforms_active){ ?>
			<div class="gravity-disabled-mask"></div>
			<?php } ?>
			<div class="block-title">Gravity Forms Integration</div>
			<div class="marketing-block-content">
				<?php $mo_gf_action_value = $mo_is_gravityforms_active ? "mo_gf_field_mapping" : "";?>
				<input type='hidden' name="action" value="<?php echo $mo_gf_action_value ;?>" />
				<fieldset style="border-top: 1px solid black; margin-bottom: 20px;">
					<legend><b>Gravity Forms Integration:</b></legend>
					<table class="form-table">
						<tr valign="top">
                                                    <td style="width: 20%">Gravity Forms Form :</td>
                                                    <td style="width: 30%; padding: 15px 0; "><?php if($mo_is_gravityforms_active){ echo mo_gravity_forms::mo_get_gf_dropdown(); }?></td>
						</tr>
					</table>
				</fieldset>
				<div id="form_field_mapping_table"></div>

				<div class="sumit-block">
					<p class=""><input type="submit" class="button-primary mo_gf_submit hello" value="<?php _e('Save Changes') ?>" /></p>
				</div>
			</div>
		</div>
                
                <div class="marketing-setting-block">
			
			<div class="block-title">Shortcodes</div>
			<div class="marketing-block-content">
                           
                            <table class="form-table">
                                <tbody><tr>
                                        <td colspan="2" style="width: 20%">[mo_form id="XXX"]
                                        </td>
                                        <td style="width: 100%"><p style="font-style: italic;">Use form shortcode in content and widgets to display a marketing optimizer form. If no id is passed it will display the default form you have set in the marketing optimizer integration settings.</p></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="width: 20%">[mo_phone]
                                        </td>
                                        <td style="width: 100%"><p style="font-style: italic;">Use phone shortcode in content and widgets to display marketing optimizer visitor tracking numbers.</p></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" style="width: 20%">[mo_conversion]
                                        </td>
                                        <td style="width: 100%"><p style="font-style: italic;">Use conversion shortcode to track page, landing page, calls-to-action and pop-up conversions.</p></td>
                                    </tr>
                                    
                                </tbody></table>
			</div>
		</div>
	</form>		
</div>

<div style="width: 20%;"></div>
