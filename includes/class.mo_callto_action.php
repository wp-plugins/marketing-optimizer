<?php

class mo_callto_action extends mo_ab_testing {

    public $post_type = 'mo_ct';
    public $meta_value_prefix = 'mo_ct_';
    public static $instance = null;

    public static function instance($post_id) {
        if ($post_id) {
            if (!isset(self::$instance [$post_id]) || self::$instance [$post_id] === null) {
                self::$instance [$post_id] = new mo_callto_action($post_id);
            }
            return self::$instance [$post_id];
        }
    }

    public function __construct($post_id) {
        parent::__construct($post_id);
        $this->set_current_variation($this->_set_current_variation());
        add_action ( 'wp_enqueue_scripts', array ($this,'remove_default_stylesheet') , 20);
    }
    
    public function remove_default_stylesheet() {
        wp_dequeue_style( 'twentyfifteen-style' );
        wp_deregister_style( 'twentyfifteen-style' );
        wp_dequeue_style('screen');
        wp_deregister_style('screen');
        wp_dequeue_script( 'site' );
        wp_deregister_script( 'site' );
    }

}
