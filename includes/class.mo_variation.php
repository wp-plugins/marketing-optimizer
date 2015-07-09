<?php
class mo_variation {
	public $post_id;
	public $id;
	public $title;
	public $description;
	public $content;
	public $impressions;
	public $visitors;
	public $conversions;
	public $variation_id;
	public $template;
	public $status ;
	public $prefix;
	public $theme_template;
	public function __construct($post_id, $id, $prefix = '') {
                if ($post_id) {
			$this->set_post_id ( $post_id );
			if ($prefix) {
				$this->set_prefix ( $prefix );
			}
			if (( int ) $id >= 0) {
				$this->set_id ( $id );
				$status = get_post_meta ( $this->get_post_id (), $prefix . 'status_' . $this->get_id (), true )!==''?get_post_meta ( $this->get_post_id (), $prefix . 'status_' . $this->get_id (), true ):1;
				$this->set_title ( get_post_meta ( $this->get_post_id (), $prefix . 'title_' . $this->get_id (), true ) );
				$this->set_description ( get_post_meta ( $this->get_post_id (), $prefix . 'description_' . $this->get_id (), true ) );
				$this->set_content ( get_post_meta ( $this->get_post_id (), $prefix . 'content_' . $this->get_id (), true ) );
				$this->set_impressions ( get_post_meta ( $this->get_post_id (), $prefix . 'impressions_' . $this->get_id (), true ) );
				$this->set_visitors ( get_post_meta ( $this->get_post_id (), $prefix . 'visitors_' . $this->get_id (), true ) );
				$this->set_conversions ( get_post_meta ( $this->get_post_id (), $prefix . 'conversions_' . $this->get_id (), true ) );
				$this->set_variation_id ( get_post_meta ( $this->get_post_id (), $prefix . 'variation_id_' . $this->get_id (), true ) );
				$this->set_status ( $status );
				$this->set_template ( get_post_meta ( $this->get_post_id (), $prefix . 'template_' . $this->get_id (), true ) );
				$this->set_theme_template ( get_post_meta ( $this->get_post_id (), $prefix . 'theme_template_' . $this->get_id (), true ) );
			}
		} else {
			throw new ErrorException ( 'Invalid post id' );
		}
		return $this;
	}
	public function get_content() {
		return $this->content;
	}
	public function set_content($content) {
		$this->content = $content;
	}
	public function get_conversions() {
		return $this->conversions;
	}
	public function set_conversions($conversions) {
		$this->conversions = $conversions;
	}
	public function get_conversion_rate() {
		if (( int ) $this->get_conversions() === 0 || (int)$this->get_visitors() === 0) {
			return 0;
		} else {
			return ( int ) $this->get_conversions () / ( int ) $this->get_visitors () ;
		}
	}
	public function get_description() {
		return $this->description;
	}
	public function set_description($description) {
		$this->description = $description;
	}
	public function get_id() {
		return $this->id;
	}
	public function set_id($id) {
		$this->id = $id;
	}
	public function get_impressions() {
		return $this->impressions;
	}
	public function set_impressions($impressions) {
		$this->impressions = $impressions;
	}
	public function get_prefix() {
		return $this->prefix;
	}
	public function set_prefix($prefix) {
		$this->prefix = $prefix;
	}
	public function get_post_id() {
		return $this->post_id;
	}
	public function set_post_id($post_id) {
		$this->post_id = $post_id;
	}
	public function get_status() {
		return $this->status;
	}
	public function set_status($status) {
		$this->status = $status;
	}
	public function get_template() {
		return $this->template;
	}
	public function set_template($template) {
		$this->template = $template;
	}
	public function get_theme_template(){
		return $this->theme_template;
	}
	public function set_theme_template($theme_template){
		$this->theme_template = $theme_template;
	}
	public function get_title() {
		return $this->title;
	}
	public function set_title($title) {	   
		$this->title = $title;
	}
	public function get_variation_id() {
		return $this->variation_id;
	}
	public function set_variation_id($variation_id) {
		$this->variation_id = $variation_id;
	}
	public function get_visitors() {
		return $this->visitors;
	}
	public function set_visitors($visitors) {
		$this->visitors = $visitors;
	}
	public function save() {
		update_post_meta ( $this->get_post_id (), $this->prefix . 'title_' . $this->get_id (), $this->get_title () );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'description_' . $this->get_id (), $this->get_description () );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'content_' . $this->get_id (), $this->get_content () );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'impressions_' . $this->get_id (), $this->get_impressions () );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'visitors_' . $this->get_id (), $this->get_visitors () );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'conversions_' . $this->get_id (), $this->get_conversions () );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'variation_id_' . $this->get_id (), $this->get_variation_id () );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'status_' . $this->get_id (), $this->get_status () );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'template_' . $this->get_id (), $this->get_template () );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'theme_template_' . $this->get_id (), $this->get_theme_template () );
	}
	public function reset_stats(){
		update_post_meta ( $this->get_post_id (), $this->prefix . 'impressions_' . $this->get_id (), 0 );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'visitors_' . $this->get_id (), 0 );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'conversions_' . $this->get_id (), 0 );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'stat_reset_date', time() );
	}
	public function delete(){	   
		delete_post_meta ( $this->get_post_id (), $this->get_prefix() . 'title_' . $this->get_id () );
		delete_post_meta ( $this->get_post_id (),$this->get_prefix()  . 'description_' . $this->get_id () );
		delete_post_meta ( $this->get_post_id (), $this->get_prefix()  . 'content_' . $this->get_id () );
		delete_post_meta ( $this->get_post_id (), $this->get_prefix()  . 'impressions_' . $this->get_id () );
		delete_post_meta ( $this->get_post_id (),$this->get_prefix()  . 'visitors_' . $this->get_id () );
		delete_post_meta ( $this->get_post_id (), $this->get_prefix()  . 'conversions_' . $this->get_id () );
		delete_post_meta ( $this->get_post_id (), $this->get_prefix()  . 'variation_id_' . $this->get_id () );
		delete_post_meta ( $this->get_post_id (), $this->get_prefix()  . 'status_' . $this->get_id () );
		delete_post_meta ( $this->get_post_id (), $this->get_prefix() . 'template_' . $this->get_id () );
		delete_post_meta ( $this->get_post_id (), $this->get_prefix()  . 'theme_template_' . $this->get_id () );
	}
}