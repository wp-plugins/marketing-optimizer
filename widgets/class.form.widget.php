<?php
/*
Plugin Name: Marketing Optimizer Form Widget
Plugin URI: http://www.marketingoptimizer.com/?apcid=8381
Description: Will output Marketing Optimizer form publishing javascript code
Author: Stephen R. Croskey
Version: 1
Author URI: http://www.marketingoptimizer.com/?apcid=8381
*/
 
 
class MoFormWidget extends WP_Widget
{
  function MoFormWidget()
  {
    $widget_ops = array('classname' => 'MoFormWidget', 'description' => 'Outputs Marketing Optimizer javascript form code' );
    $this->WP_Widget('MoFormWidget', 'MO Form Widget', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'id' => '','title'=>'' ) );
    $id = $instance['id'];
    $title = $instance['title'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
  <p><label for="<?php echo $this->get_field_id('id'); ?>">Form Id: <input class="widefat" id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>" type="text" value="<?php echo attribute_escape($id); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['id'] = strip_tags($new_instance['id']);
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;
 
    // WIDGET CODE GOES HERE
  if (!empty($instance['id'])) {
		echo '<script type="text/javascript" src="http://app.marketingoptimizer.com/remote/ap_js.php?f=' . $instance['id'] . '&o=' . get_option ( 'mo_account_id' ) . '"></script>';
	} elseif (get_option ( 'mo_form_default_id' )) {
		echo '<script type="text/javascript" src="http://app.marketingoptimizer.com/remote/ap_js.php?f=' . get_option ( 'mo_form_default_id' ) . '&o=' . get_option ( 'mo_account_id' ) . '"></script>';
	}
 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("MoFormWidget");') );?>