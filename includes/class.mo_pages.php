<?php

class mo_pages extends mo_ab_testing{

    public $post_type = 'page';
    public $meta_value_prefix = 'mo_page_';
    public static $instance;

    public static function instance($post_id){
        if ($post_id) {
            if (! isset(self::$instance[$post_id]) || self::$instance[$post_id] === null) {
                self::$instance[$post_id] = new mo_pages($post_id);
            }
            return self::$instance[$post_id];
        }
    }

    public function __construct($post_id){
        parent::__construct($post_id);
        $this->set_current_variation($this->_set_current_variation());
    }
}