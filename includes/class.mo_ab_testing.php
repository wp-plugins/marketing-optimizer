<?php

class mo_ab_testing
{

    public $post_id;

    public $post_type;

    public $meta_value_prefix;

    public $variation_ids_arr;

    public $variations_arr;

    public $current_variation;

    public $active_variation_count = 0;

    public function __construct($post_id)
    {
        if ($post_id) {
            $this->set_post_id($post_id);
            $variation_ids_arr = explode(',', get_post_meta($this->get_post_id(), $this->meta_value_prefix . 'variations', true) ? get_post_meta($this->get_post_id(), $this->meta_value_prefix . 'variations', true) : 0);
            $this->set_variation_ids_arr($variation_ids_arr);
            $this->set_variations_arr($this->_set_variations_arr());
            
            foreach ($this->get_variations_arr() as $k => $var_obj) {
                if ((int) $var_obj->get_status()) {
                    $this->set_active_variation_count(($this->get_active_variation_count() + 1));
                }
            }
            $v_id = $this->get_current_variation();
            if (! in_array($v_id, $variation_ids_arr) && $v_id !== '') {
                $variation_ids_arr[] = $v_id;
            }
        } else {
            throw new InvalidArgumentException('Not a valid post id');
        }
    }

    public function get_post_id(){
        return $this->post_id;
    }

    public function set_post_id($post_id){
        $this->post_id = $post_id;
    }

    public function get_variation_ids_arr(){
        return $this->variation_ids_arr;
    }

    public function set_variation_ids_arr($variation_ids_arr){
        $this->variation_ids_arr = $variation_ids_arr;
    }

    public function get_variations_arr(){
        return $this->variations_arr;
    }

    public function set_variations_arr($variations_arr){
        $this->variations_arr = $variations_arr;
        return $this;
    }
    
    public function set_variations_arr_custom($variation_ids_arr) {
        if (is_array($variation_ids_arr) && !empty($variation_ids_arr)) {
            $this->variations_arr = array();
            foreach ($variation_ids_arr as $id) {
                if ($this->get_meta_value_prefix() == "mo_sp_") {
                    $this->variations_arr [$id] = new mo_sp_variation($this->get_post_id(), $id, $this->get_meta_value_prefix());
                } else if ($this->get_meta_value_prefix() == "mo_ct_") {
                    $this->variations_arr [$id] = new mo_ct_variation($this->get_post_id(), $id, $this->get_meta_value_prefix());
                } else {
                    $this->variations_arr [$id] = new mo_variation($this->get_post_id(), $id, $this->get_meta_value_prefix());
                }
            }
        } else {
            throw new InvalidArgumentException('Not a valid array or array is empty');
        }
    }

    public function _set_variations_arr() {
        $variation_ids_arr = $this->get_variation_ids_arr();
        if (is_array($variation_ids_arr) && !empty($variation_ids_arr)) {
            $variations_arr = array();
            foreach ($this->get_variation_ids_arr() as $id) {
                if ($this->get_meta_value_prefix() == "mo_sp_") {
                    $variations_arr[$id] = new mo_sp_variation($this->get_post_id(), $id, $this->get_meta_value_prefix());
                } else if ($this->get_meta_value_prefix() == "mo_ct_") {
                    $variations_arr[$id] = new mo_ct_variation($this->get_post_id(), $id, $this->get_meta_value_prefix());
                } else {
                    $variations_arr[$id] = new mo_variation($this->get_post_id(), $id, $this->get_meta_value_prefix());
                }
            }
            return $variations_arr;
        } else {
            throw new InvalidArgumentException('Not a valid array or array is empty');
        }
    }

    public function get_meta_value_prefix(){
        return $this->meta_value_prefix;
    }

    public function get_variation_property($v_id, $property) {
        $method_name = 'get_' . $property;
        $variations_arr = $this->get_variations_arr();
        if (isset($variations_arr[$v_id]) && is_object($variations_arr[$v_id])) {
            return $variations_arr[$v_id]->$method_name();
        } else {
            return '';
        }
    }

    public function set_variation_property($v_id, $property, $value){   
        $method_name = 'set_' . $property;
        $variations_arr = $this->get_variations_arr();
        $variations_arr[$v_id]->$method_name($value);
    }

    public function save() {
        $current_v_id = $this->get_current_variation();
        $variations_ids_arr = $this->get_variation_ids_arr();
        update_post_meta($this->get_post_id(), $this->meta_value_prefix . 'variations', implode(',', $this->get_variation_ids_arr()));
        $variations_arr = $this->get_variations_arr();
        foreach ($this->get_variations_arr() as $v_id => $variations_obj) {
            if (in_array($current_v_id, $variations_ids_arr) && $current_v_id == $v_id) {
                $variations_obj->save();
            }
        }
    }

    public function get_current_variation(){
        return $this->current_variation;
    }

    public function clear_stats(){
        $variation_obj_arr = $this->get_variations_arr();
        foreach ($variation_obj_arr as $v_obj) {
            $v_obj->reset_stats();
        }
    }

    public function pause_variation($post_id, $v_id) {
        $v_status = $this->get_variation_property($v_id, 'status');
        if ((int) $v_status) {
            $this->set_variation_property($v_id, 'status', 0);
        } else {
            $this->set_variation_property($v_id, 'status', 1);
        }
        $this->save();
    }

    public function get_confidence($v_id) {
        $variations_arr = $this->get_variations_arr();
        $conversion_rate_arr = $this->get_conversion_rate_arr();
        $confidence_arr = array();
        if (!empty($variations_arr)) {
            foreach ($variations_arr as $f_v_id => $f_var_obj) {
                $f_visitors = (int) $f_var_obj->get_visitors();
                $f_conversion_rate = (float) $f_var_obj->get_conversion_rate();
                foreach ($variations_arr as $s_v_id => $s_var_obj) {
                    $s_visitors = (int) $s_var_obj->get_visitors();
                    $s_conversion_rate = (float) $s_var_obj->get_conversion_rate();
                    if ($f_v_id != $s_v_id && (int) $f_var_obj->get_conversions()) {
                        if (!isset($confidenceArr[$f_v_id]) && (int) $s_var_obj->get_conversions()) {
                            $confidence_arr[$f_v_id] = number_format($this->mo_lp_get_cumnormdist($this->mo_lp_get_zscore(array(
                                                'visitors' => $s_visitors,
                                                'conversion_rate' => $s_conversion_rate
                                                    ), array(
                                                'visitors' => $f_visitors,
                                                'conversion_rate' => $f_conversion_rate
                                    ))) * 100, 1);
                        } elseif (isset($confidence_arr[$f_v_id]) && $confidence_arr[$f_v_id] > number_format($this->mo_lp_get_cumnormdist($this->mo_lp_get_zscore(array(
                                                    'visitors' => $s_visitors,
                                                    'conversion_rate' => $s_conversion_rate
                                                        ), array(
                                                    'visitors' => $f_visitors,
                                                    'conversion_rate' => $f_conversion_rate
                                        ))) * 100, 1) && (int) $s_var_obj->get_conversions()) {
                            $confidence_arr[$f_v_id] = number_format($this->mo_lp_get_cumnormdist($this->mo_lp_get_zscore(array(
                                                'visitors' => $s_visitors,
                                                'conversion_rate' => $s_conversion_rate
                                                    ), array(
                                                'visitors' => $f_visitors,
                                                'conversion_rate' => $f_conversion_rate
                                    ))) * 100, 1);
                        }
                    }
                }
            }
        }
        $confidence = (isset($confidence_arr[$v_id]) && $confidence_arr[$v_id]) ? $confidence_arr[$v_id] . '%' : 'NEI';
        return $confidence;
    }

    public function mo_lp_get_zscore($c, $t){
        if ($t['visitors'] && $c['visitors'] && $t['conversion_rate'] && $c['conversion_rate']) {
            $z = $t['conversion_rate'] - $c['conversion_rate'];
            $s = ($t['conversion_rate'] * (1 - $t['conversion_rate'])) / $t['visitors'] + ($c['conversion_rate'] * (1 - $c['conversion_rate'])) / $c['visitors'];
            return $z / sqrt($s);
        } else {
            return 0;
        }
    }

    public function mo_lp_get_cumnormdist($x)
    {
        $b1 = 0.319381530;
        $b2 = - 0.356563782;
        $b3 = 1.781477937;
        $b4 = - 1.821255978;
        $b5 = 1.330274429;
        $p = 0.2316419;
        $c = 0.39894228;
        
        if ($x >= 0.0) {
            $t = 1.0 / (1.0 + $p * $x);
            return (1.0 - $c * exp(- $x * $x / 2.0) * $t * ($t * ($t * ($t * ($t * $b5 + $b4) + $b3) + $b2) + $b1));
        } else {
            $t = 1.0 / (1.0 - $p * $x);
            return ($c * exp(- $x * $x / 2.0) * $t * ($t * ($t * ($t * ($t * $b5 + $b4) + $b3) + $b2) + $b1));
        }
    }

    public function get_conversion_rate_arr(){
        $variation_obj_arr = $this->get_variations_arr();
        $conversion_rate_arr = array();
        foreach ($variation_obj_arr as $var_obj) {
            $conversion_rate_arr[$var_obj->get_id()] = $this->get_variation_property($var_obj->get_id(), 'conversion_rate');
        }
        return $conversion_rate_arr;
    }

    public function delete_variation($post_id, $v_id) {
        if (isset($post_id) && isset($v_id)) {
            $variations_arr = $this->get_variations_arr();
            $var_ids_arr = $this->get_variation_ids_arr();
            if (count($var_ids_arr) == 1) {
                wp_delete_post($post_id, true);
            } else {
                $variations_arr[$v_id]->delete();
                $key = array_search($v_id, $var_ids_arr);
                unset($var_ids_arr[$key]);
                $this->set_variation_ids_arr($var_ids_arr);
                $this->save();
            }
        }
    }

    public function mo_is_testing() {
        global $post;
        $post_id = $post->ID;
        $is_testing = false;
        if ($this->post_type == 'page') {
            if (count($this->get_variations_arr()) <= 1) {
                $is_testing = false;
            } else {
                $is_testing = true;
            }
        } elseif ($post->post_type == 'mo_landing_page') {
            if (count($this->get_variations_arr()) < 1) {
                $is_testing = false;
            } else {
                $is_testing = true;
            }
        }
        return $is_testing;
    }

    public function mo_bot_detected() {
        if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }

    public function get_active_variation_count(){
        return $this->active_variation_count;
    }

    public function set_active_variation_count($active_variation_count){
        $this->active_variation_count = $active_variation_count;
    }

    public function _set_current_variation() {
        global $post, $pagenow;

        if (isset($_GET[$this->get_meta_value_prefix() . 'variation_id'])) {
            return (int) $_GET[$this->get_meta_value_prefix() . 'variation_id'];
        } elseif (isset($_GET['v_id'])) {
            return (int) $_GET['v_id'];
        } elseif (isset($_POST[$this->get_meta_value_prefix() . 'open_variation'])) {
            return (int) $_POST[$this->get_meta_value_prefix() . 'open_variation'];
        } elseif (isset($_POST['v_id'])) {
            return (int) $_POST['v_id'];
        } elseif (isset($post) && isset($_COOKIE[$this->get_meta_value_prefix() . 'variation_' . $post->ID]) && $this->get_variation_property($_COOKIE[$this->get_meta_value_prefix() . 'variation_' . $post->ID], 'status') && !is_admin()) {
            return (int) $_COOKIE[$this->get_meta_value_prefix() . 'variation_' . $post->ID];
        } elseif (isset($this->current_variation)) {
            return (int) $this->current_variation;
        }

        if ($this->get_current_variation() === null) {
            $variation_ids_arr = $this->get_variation_ids_arr();
            if (count($variation_ids_arr) > 1) {
                foreach ($this->get_variations_arr() as $v_id => $var_obj) {
                    if ($pagenow == 'edit.php') {
                        $conversion_rate_variation_arr[$v_id] = $var_obj->get_conversion_rate();
                    } elseif ($var_obj->get_status()) {
                        $conversion_rate_variation_arr[$v_id] = $var_obj->get_conversion_rate();
                    }
                }
                if (!empty($conversion_rate_variation_arr)) {
                    arsort($conversion_rate_variation_arr);
                    reset($conversion_rate_variation_arr);
                    $randNum = rand(1, 10);
                    $showPercentage = ((int) get_option('mo_lp_variation_percentage') >= 10) ? (int) get_option('mo_lp_variation_percentage') / 10 : 0;
                    if ($randNum > (int) $showPercentage) {
                        if (count($conversion_rate_variation_arr) > 1) {
                            return (int) array_rand($conversion_rate_variation_arr, 1);
                        } else {
                            return (int) key($conversion_rate_variation_arr);
                        }
                    } else {
                        return (int) key($conversion_rate_variation_arr);
                    }
                }
            } else {
                return 0;
            }
        } else {
            if ($_GET['action'] != $this->get_meta_value_prefix() . 'pause_variation' && !(int) $this->get_variation_property($this->get_current_variation(), 'status')) {
                return 0;
            }
        }
    }

    public function set_current_variation($current_variation){
        $this->current_variation = $current_variation;
        return $this;
    }
}