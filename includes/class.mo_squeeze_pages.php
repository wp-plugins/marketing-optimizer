<?php
class mo_squeeze_pages extends mo_ab_testing {
	public $post_type = 'mo_sp';
	public $meta_value_prefix = 'mo_sp_';
	public static $instance = null;
	public static function instance($post_id) {
		if ($post_id) {
			if (! isset ( self::$instance [$post_id] ) || self::$instance [$post_id] === null) {
				self::$instance [$post_id] = new mo_squeeze_pages ( $post_id );
			}
			return self::$instance [$post_id];
		}
	}
	public function __construct($post_id) {
		parent::__construct ( $post_id );
		//$this->set_variations_arr ( $this->get_variation_ids_arr () );
// 		foreach ( $this->get_variations_arr () as $k => $var_obj ) {
// 			if (( int ) $var_obj->get_status ()) {
// 				$this->set_active_variation_count ( ($this->get_active_variation_count () + 1) );
// 			}
// 		}
		$this->set_current_variation ();
		
	}
	public function set_variations_arr($variation_ids_arr) {
		if (is_array ( $variation_ids_arr ) && ! empty ( $variation_ids_arr )) {
			$this->variations_arr = array ();
			foreach ( $variation_ids_arr as $id ) {
				$this->variations_arr [$id] = new mo_sp_variation ( $this->get_post_id (), $id, $this->get_meta_value_prefix () );
			}
		} else {
			throw new InvalidArgumentException ( 'Not a valid array or array is empty' );
		}
	}
	public function set_current_variation() {
		global $post, $pagenow;
		if (isset ( $_GET ['mo_sp_variation_id'] )) {
			$this->current_variation = $_GET ['mo_sp_variation_id'];
		} elseif (isset ( $_GET ['v_id'] )) {
			$this->current_variation = $_GET ['v_id'];
		} elseif (isset ( $_POST ['mo_sp_open_variation'] )) {
			$this->current_variation = $_POST ['mo_sp_open_variation'];
		} elseif (isset ( $post ) && isset ( $_COOKIE ['mo_sp_variation_' . $post->ID] ) && ! is_admin ()) {
			$this->current_variation = $_COOKIE ['mo_sp_variation_' . $post->ID];
		} elseif (isset ( $this->current_variation )) {
			$this->current_variation = $this->current_variation;
		}
		if (! isset ( $this->current_variation )) {
			$variation_ids_arr = $this->get_variation_ids_arr ();
			
			if ($this->get_active_variation_count () > 1) {
				foreach ( $this->get_variations_arr () as $v_id => $var_obj ) {
					if ($pagenow == 'edit.php') {
						$conversion_rate_variation_arr [$v_id] = $var_obj->get_conversion_rate ();
					} elseif (( int ) $var_obj->get_status ()) {
						$conversion_rate_variation_arr [$v_id] = $var_obj->get_conversion_rate ();
					}
				}
				arsort ( $conversion_rate_variation_arr );
				reset ( $conversion_rate_variation_arr );
				$randNum = rand ( 1, 10 );
				$showPercentage = (( int ) get_option ( 'mo_sp_variation_percentage' ) >= 10) ? ( int ) get_option ( 'mo_sp_variation_percentage' ) / 10 : 0;
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
				$mo_sp_var_arr = $this->get_variations_arr ();
				foreach ( $mo_sp_var_arr as $v ) {
					if (( int ) $v->get_status ()) {
						$this->current_variation = $v->get_id ();
					}
				}
				if (! $this->get_current_variation ()) {
					$this->current_variation = 0;
				}
			}
		}
	}
}