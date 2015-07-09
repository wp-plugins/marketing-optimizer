<?php
if (isset ( $_GET ['tab'] )) {
	$active_tab = $_GET ['tab'];
} else {
	$active_tab = 'mo_general_settings';
}

if ($_POST) {
	echo '<div class="updated" style="float:left;" >The Marketing Optimizer plugin settings updated</div>';
}

?>

<div class="wrap">
	<div style="display: block; width: 80%; float: left;">
		<h2> Marketing Optimizer Landing Pages for Wordpress
	 <?php echo "<span style=\"float:right;font-size:14px;padding-top:40px;font-style:italic;\">Version ".mo_plugin::get_version() ."</span>";?></h2>
	</div>
	<div style="width: 80%">
		<h2 class="nav-tab-wrapper">
                    <a  href="?page=<?php echo MO_PLUGIN_DIRECTORY ?>/mo_settings_page.php&tab=mo_general_settings"
                        class="nav-tab <?php echo $active_tab == 'mo_general_settings' ? 'nav-tab-active' : ''; ?>">General
                        Settings</a> 
		</h2>
		<div style="padding: 20px; background-color: #ECECEC;">
			<form method="post" action="">
		
<?php
switch ($active_tab) {
	case 'mo_general_settings' :
?>		  
    <div id="tabs-1">
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