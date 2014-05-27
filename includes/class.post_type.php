<?php
class post_type {
	public $post_type;
	public $prefix;

	public function __construct() {
	}

	public function mo_bot_detected() {
		if (isset ( $_SERVER ['HTTP_USER_AGENT'] ) && preg_match ( '/bot|crawl|slurp|spider/i', $_SERVER ['HTTP_USER_AGENT'] )) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}