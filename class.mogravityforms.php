<?php
class mogravityforms {
	public function __construct() {
		add_action ( 'wp_ajax_mo_gf_form_field_mapping', array (
				$this,
				'mo_gf_form_field_mapping' 
		) );
		add_action ( 'gform_after_submission', array (
				$this,
				'mo_post_to_marketing_optimizer' 
		), 10, 2 );
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
	public function mo_gf_form_field_mapping() {
		if (isset ( $_POST ['form_id'] ) && $_POST ['form_id'] > 0) {
			$form_id = $_POST ['form_id'];
			$form_fields_arr = $this->mo_get_form_fields_by_id ( $form_id );
			echo $this->mo_get_form_field_mapping_view ( $form_id, $form_fields_arr );
			die ();
		}
	}
	public function mo_get_form_fields_by_id($form_id) {
		if (isset ( $form_id ) && $form_id > 0) {
			$form_meta_arr = GFFormsModel::get_form_meta ( $form_id );
			$form_fields_arr = array ();
			foreach ( $form_meta_arr ['fields'] as $field ) {
				if (is_array ( $field ['inputs'] )) {
					foreach ( $field ['inputs'] as $v ) {
						if ($field ['label'] == 'Name') {
							$form_fields_arr [( string ) $v ['id']] = $v ['label'] . ' ' . $field ['label'];
						} else {
							$form_fields_arr [( string ) $v ['id']] = $v ['label'];
						}
					}
				} else {
					$form_fields_arr [( string ) $field ['id']] = $field ['label'];
				}
			}
			return $form_fields_arr;
		}
	}
	public function mo_get_form_field_mapping_view($form_id, $form_field_mappings) {
		$formFieldMappingArr = $this->mo_get_form_field_mapping ( $form_id );
		$table = '<table style="margin-bottom:15px;">';
		$table .= '<tr ><td style="width:46%;text-align:left;">Marketing Optimizer Form Id</td><td style="width:30%;text-align:left;"> </td><td style="width:25%;text-align:left;"><input type="text" name="f_id"  value="'.$formFieldMappingArr['f_id'].'" /></td></tr>';
		$table .= '</table>';
		$table .= '<table>';
		$table .= '<tr>';
		$table .= '<th style="width:25%;text-align:left;">Gravity Forms Field</th>';
		$table .= '<th style="width:10%;text-align:left;">To</th>';
		$table .= '<th style="width:25%;text-align:left;">Marketing Optimizer Form Field Id</th>';
		$table .= '</tr>';
		$table .= '<tbody>';
		
		foreach ( $form_field_mappings as $k => $v ) {
			if ($formFieldMappingArr) {
				$table .= '<tr><td>' . $v . '</td><td><img src="'. plugins_url().DS.MO_PLUGIN_DIRECTORY.'/images/move.png" /></td><td><input type="text" name="gfffm[' . $k . ']" value="' . $formFieldMappingArr [$k] . '" /></td></tr>';
			} else {
				$table .= '<tr><td>' . $v . '</td><td>--></td><td><input type="text" name="gfffm[' . $k . ']" /></td></tr>';
			}
		}
		$table .= '</tbody>';
		$table .= '</table>';
		return $table;
	}
	public static function mo_save_form_field_mapping($form_id, $fieldMappingArr) {
		if ($form_id && count ( $fieldMappingArr ) > 0) {
			$form_id = trim ( $form_id );
			$formFieldsArr = array();
			foreach ( $fieldMappingArr['gfffm'] as $k => $v ) {
				$formFieldsArr [$k] = trim ( $v );
			}
			$formFieldsArr ['f_id'] = trim($fieldMappingArr['f_id']);
			update_option ( 'mo_form_field_mapping_' . $form_id, serialize ( $formFieldsArr ) );
		}
	}
	public static function mo_get_form_field_mapping($form_id) {
		if ($form_id) {
			return unserialize ( get_option ( 'mo_form_field_mapping_' . $form_id ) );
		} else {
			return false;
		}
	}
	function mo_post_to_marketing_optimizer($entry, $form) {
		$post_url = 'http://app.aimcrm.com/remote/form_post.php';
		$form_id = $entry ['form_id'];
		$formFieldMappingArr = mogravityforms::mo_get_form_field_mapping($form_id);
		$body = array();
		$body['org_id'] = get_option('mo_account_id');
		$body['action'] = 'feedback_visitor_add';
		if ($formFieldMappingArr) {
			$formFieldMappingArr = array_flip($formFieldMappingArr);
			foreach($formFieldMappingArr as $k =>$v){
				if($v =='f_id' ){
					$body[$v] = $k;
				}else{
					$body['ap_field_'.$k] = $entry[$v];
				}
			}
			$request = new WP_Http ();
			$response = $request->post ( $post_url, array (
					'body' => $body,
					'timeout' =>10 
			) );
		}
	}
}
$moGravityForms = new mogravityforms ();