<?php
/*
 * Plugin Name: Marketing Optimizer Phone Widget 
 * Plugin URI: http://www.marketingoptimizer.com/?apcid=8381
 * Description: Will output Marketing Optimizer Phone Tracking code 
 * Author: Stephen R. Croskey Version: 1 
 * Author URI: * http://www.marketingoptimizer.com/?apcid=8381
 */
class MoPhoneWidget extends WP_Widget {
	function MoPhoneWidget() {
		$widget_ops = array ('classname' => 'MoPhoneWidget','description' => 'Outputs Maketing Optimizer Phone <span> tags' 
		);
		$this->WP_Widget ( 'MoPhoneWidget', 'MO Phone Widget', $widget_ops );
	}
	function form($instance) {
		$instance = wp_parse_args ( ( array ) $instance, array ('before_phone' => '','default_number' => '','after_phone' => '' 
		) );
		$before_phone = $instance ['before_phone'];
		$default_number = $instance ['default_number'];
		$after_phone = $instance ['after_phone'];
		?>
<p>
	<label for="<?php echo $this->get_field_id('before_phone'); ?>">Before Phone: <input class="widefat" id="<?php echo $this->get_field_id('before_phone'); ?>" name="<?php echo $this->get_field_name('before_phone'); ?>" type="text" value="<?php echo attribute_escape($before_phone); ?>" /></label>
</p>
<p>
	<label for="<?php echo $this->get_field_id('default_number'); ?>">Default Phone Number: <input class="widefat" id="<?php echo $this->get_field_id('default_number'); ?>" name="<?php echo $this->get_field_name('default_number'); ?>" type="text" value="<?php echo attribute_escape($default_number); ?>" /></label>
</p>
<p>
	<label for="<?php echo $this->get_field_id('after_phone'); ?>">After Phone: <input class="widefat" id="<?php echo $this->get_field_id('after_phone'); ?>" name="<?php echo $this->get_field_name('after_phone'); ?>" type="text" value="<?php echo attribute_escape($after_phone); ?>" /></label>
</p>

<?php
	}
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance ['before_phone'] = strip_tags ( $new_instance ['before_phone'] );
		$instance ['default_number'] = strip_tags ( $new_instance ['default_number'] );
		$instance ['after_phone'] = strip_tags ( $new_instance ['after_phone'] );
		return $instance;
	}
	function widget($args, $instance) {
		extract ( $args, EXTR_SKIP );
		$before_phone = empty ( $instance ['before_phone'] ) ? '' : $instance ['before_phone'];
		$default_number = empty ( $instance ['default_number'] ) ? '' : $instance ['default_number'];
		$after_phone = empty ( $instance ['after_phone'] ) ? '' : $instance ['after_phone'];
		echo $before_widget;
		// WIDGET CODE GOES HERE
		$phone_class = get_option ( 'mo_phone_publish_cls' ) ? get_option ( 'mo_phone_publish_cls' ) : 'phonePublishCls';
		$phone_number = $default_number ? $default_number : get_option ( 'mo_phone_tracking_default_number' );
		$phone_span = ' <span class="' . $phone_class . '">' . $phone_number . '</span> ';
		echo $before_phone . $phone_span . $after_phone;
		
		echo $after_widget;
	}
}
add_action ( 'widgets_init', create_function ( '', 'return register_widget("MoPhoneWidget");' ) );
?>