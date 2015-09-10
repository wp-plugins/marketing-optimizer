<?php
class mo_landing_pages extends mo_ab_testing {
	public $post_type = 'mo_landing_page';
	public $meta_value_prefix = 'mo_lp_';
	public static $instance = null;
	public static function instance($post_id) {
		if ($post_id) {
			if (! isset ( self::$instance [$post_id] ) || self::$instance [$post_id] === null) {
				self::$instance [$post_id] = new mo_landing_pages ( $post_id );
			}
			return self::$instance [$post_id];
		}
	}
	public function __construct($post_id) {
		parent::__construct ( $post_id );
		$this->set_current_variation ();
                add_action ( 'wp_enqueue_scripts', array ($this,'remove_default_stylesheet') , 20);
	}
        
        public function remove_default_stylesheet() {
            global $post;
            $contents = $post->post_content;
            $pos = strpos($contents,'id="main_landing_page"');
            if ($pos !== false) {
                wp_dequeue_style( 'twentyfifteen-style' );
                wp_deregister_style( 'twentyfifteen-style' );
                wp_dequeue_style('screen');
                wp_deregister_style('screen');
                wp_dequeue_script( 'site' );
                wp_deregister_script( 'site' );
            }
        }
        
	public function set_current_variation($current_variation = false) {
		global $post, $pagenow;
                if (isset($_GET ['mo_lp_variation_id'])) {
                    $this->current_variation = $_GET ['mo_lp_variation_id'];
                } elseif (isset($_GET['v_id'])) {
                    $this->current_variation = (int) $_GET['v_id'];
                } elseif (isset($_POST ['mo_lp_open_variation'])) {
                    $this->current_variation = $_POST ['mo_lp_open_variation'];
                } elseif (isset($_POST['v_id'])) {
                    $this->current_variation = (int) $_POST['v_id'];
                } elseif (isset($post) && isset($_COOKIE ['mo_lp_variation_' . $post->ID]) && !is_admin()) {
                    $this->current_variation = $_COOKIE ['mo_lp_variation_' . $post->ID];
                } elseif (isset($this->current_variation)) {
                    $this->current_variation = $this->current_variation;
                }
                
        if (! isset ( $this->current_variation )) {
			$variation_ids_arr = $this->get_variation_ids_arr ();
			
			if ($this->get_active_variation_count() > 1) {
				foreach ( $this->get_variations_arr () as $v_id => $var_obj ) {
					if ($pagenow == 'edit.php') {
						$conversion_rate_variation_arr [$v_id] = $var_obj->get_conversion_rate ();
					} elseif ((int)$var_obj->get_status ()) {
						$conversion_rate_variation_arr [$v_id] = $var_obj->get_conversion_rate ();
					}
				}
				arsort ( $conversion_rate_variation_arr );
				reset ( $conversion_rate_variation_arr );
				$randNum = rand ( 1, 10 );
				$showPercentage = (( int ) get_option ( 'mo_lp_variation_percentage' ) >= 10) ? ( int ) get_option ( 'mo_lp_variation_percentage' ) / 10 : 0;
				if ($randNum > ( int ) $showPercentage) {
					if (count ( $conversion_rate_variation_arr ) > 1) {
						$this->current_variation = array_rand ( $conversion_rate_variation_arr, 1 );
					} else {
						$this->current_variation = key ( $conversion_rate_variation_arr );
					}
				} else {
					$this->current_variation = key ( $conversion_rate_variation_arr );
				}
			} else {
				$this->current_variation = 0;
			}
            } 
            /*
             else {
                    if ($_GET['action'] != 'mo_lp_pause_variation' && ! (int) $this->get_variation_property($this->get_current_variation(), 'status')) {
                        $this->current_variation = 0;
                    }
            }
             */
	}
}