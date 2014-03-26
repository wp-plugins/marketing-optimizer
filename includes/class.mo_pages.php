<?php
class mo_pages extends mo_ab_testing {
	public $post_type = 'page';
	public $meta_value_prefix = 'mo_page_';
	public static $instance;
	public static function instance($post_id) {
		if ($post_id) {
			if (!isset(self::$instance [$post_id]) || self::$instance [$post_id] === null) {
				self::$instance [$post_id] = new mo_pages ( $post_id );
			}
			return self::$instance [$post_id];
		}
	}
	public function __construct($post_id) {
		parent::__construct ( $post_id );
		$this->set_current_variation ();
	}
	public function set_current_variation() {
		global $post, $pagenow;
		if (isset ( $_GET ['mo_page_variation_id'] )) {
			$this->current_variation = ( int ) $_GET ['mo_page_variation_id'];
		} elseif (isset ( $_POST ['mo_page_open_variation'] )) {
			$this->current_variation = ( int ) $_POST ['mo_page_open_variation'];
		} elseif (isset($post) && isset ( $_COOKIE ['mo_page_variation_' . $post->ID] ) && ! is_admin ()) {
			$this->current_variation = ( int ) $_COOKIE ['mo_page_variation_' . $post->ID];
		} elseif (isset ( $this->current_variation )) {
			$this->current_variation = ( int ) $this->current_variation;
		}
		if (! isset ( $this->current_variation )) {
			$variation_ids_arr = $this->get_variation_ids_arr ();
			if (count ( $variation_ids_arr ) > 1) {
				foreach ( $this->get_variations_arr () as $v_id => $var_obj ) {
					if ($pagenow == 'edit.php') {
						$conversion_rate_variation_arr [$v_id] = $var_obj->get_conversion_rate ();
					} elseif ($var_obj->get_status ()) {
						$conversion_rate_variation_arr [$v_id] = $var_obj->get_conversion_rate ();
					}
				}
				if (! empty ( $conversion_rate_variation_arr )) {
					arsort ( $conversion_rate_variation_arr );
					reset ( $conversion_rate_variation_arr );
					$randNum = rand ( 1, 10 );
					$showPercentage = (( int ) get_option ( 'mo_lp_variation_percentage' ) >= 10) ? ( int ) get_option ( 'mo_lp_variation_percentage' ) / 10 : 0;
					if ($randNum > ( int ) $showPercentage) {
						if (count ( $conversion_rate_variation_arr ) > 1) {
							$this->current_variation = ( int ) array_rand ( $conversion_rate_variation_arr, 1 );
						} else {
							$this->current_variation = ( int ) key ( $conversion_rate_variation_arr );
						}
					} else {
						$this->current_variation = ( int ) key ( $conversion_rate_variation_arr );
					}
				}
			} else {
				$this->current_variation = 0;
			}
		}
	}
}