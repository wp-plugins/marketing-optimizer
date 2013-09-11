<?php
class mogravityforms {
	
	public function __construct(){
		add_action ( 'wp_ajax_mo_gf_form_field_mapping', array($this,'mo_gf_form_field_mapping' ));
	}
	public static function mo_get_gf_dropdown() {
		$forms = GFFormsModel::get_forms ( true );
		$gf_dropdown = '<select name="mo_gf_form" id="mo_gf_form">';
		$gf_dropdown .= '<option value="0" >Select a Form</option>';
		foreach ( $forms as $form ) {
			$gf_dropdown .= '<option value="' . $form->id . '" >' . $form->title . '</option>';
		}
		$gf_dropdown .= '</select>';
		return $gf_dropdown;
	}
	
	public function mo_gf_form_field_mapping(){
		if(isset($_POST['form_id']) && $_POST['form_id'] > 0){
			$form_id = $_POST['form_id'];
			$form_fields_arr = $this->mo_get_form_fields_by_id($form_id);
			echo json_encode($form_fields_arr);
		}
	}
	public function mo_get_form_fields_by_id($form_id){
		if(isset($form_id) && $form_id > 0){
			$form_meta_arr = GFFormsModel::get_form_meta($form_id);
			$form_fields_arr = array();
			foreach($form_meta_arr['fields'] as $field){
				if(is_array($field['inputs'])){
					foreach($field['inputs'] as $v ){
						$form_fields_arr[(string)$v['id']] = $v['label'] . ' ' . $field['label'];
					}
				}else{
					$form_fields_arr[(string)$field['id']]= $field['label'];
				}
			}
			return $form_fields_arr;
		}
	}
}
$moGravityForms = new mogravityforms();