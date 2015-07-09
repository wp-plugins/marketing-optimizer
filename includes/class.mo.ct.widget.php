<?php
/**
 * Adds Call_To_Action widget.
 */
 
add_action ( 'widgets_init', 'register_ct_widget' );

function register_ct_widget() {
    register_widget( 'Call_To_Action' );
}
		
class Call_To_Action extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
            parent::__construct(
            // Base ID
            'mo_cta_widget', 
            // Name
            __('Marketing Optimizer CTA', 'text_domain'), 
            // Widget description
            array( 'description' => __( 'Marketing Optimizer CTA Widget', 'text_domain' ), ) 
            );
        }
        
        /**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
                $obj_ct  =  new mo_ct_post_type();
		extract( $args );
                echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
                    echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}
		$atts = array($instance['post_name']);
		$argument = array(
		'post_type' => 'mo_ct',
		'post__in' => $atts );
		
		$spotlight = new WP_Query($argument);
		while ($spotlight->have_posts()) : $spotlight->the_post();
                    $vid = (isset($_COOKIE['mo_ct_variation_'.get_the_ID()]))?$_COOKIE['mo_ct_variation_'.get_the_ID()]:0;
                    $obj_cta = mo_callto_action::instance(get_the_ID());
                    $content = $obj_cta->get_variation_property($vid, 'content');
                    echo '<div class="short-code-content">'.$content.$obj_ct->mo_ct_add_variation_cookie_js().'</div>';
		endwhile;
		wp_reset_postdata($spotlight->the_post());
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'post_name' => 'post_name', 'title' => '') );
		$title = $instance[ 'title' ];
		$post_name = $instance['post_name'] ;
		$mo_ct_post = new WP_Query( array( 'post_type' => 'mo_ct') );
?>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('post_name'); ?>"><?php _e( 'Call to Action' ); ?></label>
			<select name="<?php echo $this->get_field_name('post_name'); ?>" id="<?php echo $this->get_field_id('post_name'); ?>" class="widefat">
			<?php
                        if(isset($mo_ct_post->posts)) {
                            foreach($mo_ct_post->posts as $key=>$val){ 
                        ?>
			<?php $post_id = $val->ID;?> 
                        <option value="<?php echo $post_id; ?>"<?php selected( $instance['post_name'], $post_id ); ?>><?php echo $val->post_title; ?></option>
                        <?php 
                            }
                        }
			wp_reset_postdata();?>
                        </select>
		</p>
<?php 
}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['post_name'] = $new_instance['post_name'] ;
		return $instance;
	}

} // class Call_To_Action
?>