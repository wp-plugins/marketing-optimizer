<?php
class mo_sp_variation extends mo_variation {
	public $modal_height = 250;
	public $modal_width = 250;
	public $post_types;

	public function __construct($post_id, $id, $prefix = '') {
		parent::__construct ( $post_id, $id, $prefix );
		$this->set_modal_height ( get_post_meta ( $this->get_post_id (), $prefix . 'modal_height_' . $this->get_id (), true ) );
		$this->set_modal_width ( get_post_meta ( $this->get_post_id (), $prefix . 'modal_width_' . $this->get_id (), true ) );
		$this->set_post_types ( get_post_meta ( $this->get_post_id (), $prefix . 'post_types', true ) );
		$this->set_prefix ( $prefix );
		return $this;
	}

	public function get_modal_height() {
		return $this->modal_height;
	}

	public function get_modal_width() {
		return $this->modal_width;
	}

	public function get_post_types() {
		return $this->post_types;
	}

	public function set_modal_height($modal_height) {
		$this->modal_height = $modal_height;
	}

	public function set_modal_width($modal_width) {
		$this->modal_width = $modal_width;
	}

	public function set_post_types($post_types) {
		$this->post_types = $post_types;
	}

	public function save() {
		update_post_meta ( $this->get_post_id (), $this->prefix . 'modal_height_' . $this->get_id (), $this->get_modal_height () );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'modal_width_' . $this->get_id (), $this->get_modal_width () );
		update_post_meta ( $this->get_post_id (), $this->prefix . 'post_types', $this->get_post_types () );
		parent::save ();
	}
}