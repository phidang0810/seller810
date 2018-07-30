<?php
function set_active($path, $active = 'active')
{
	return call_user_func_array('Request::is', (array)$path) ? $active : '';
}

function make_option($array, $select = 0){
	$result = '';
	foreach ($array as $key => $value) {
		if ($value['id'] == $select) {
			$result .= '<option value="'.$value['id'].'" selected="selected">'.$value['name'].'</option>';
		}else{
			$result .= '<option value="'.$value['id'].'">'.$value['name'].'</option>';
		}
	}
	return $result;
}

function make_tree($array, $parent = 0){
	if (count($array) <= 0) {
		return false;
	}

	$result = [];
	foreach ($array as $key => $value) {
		if ($value->parent_id == $parent) {
			$result[$value->id] = [
				'name'	=>	$value['name'],
				'level'	=>	$value['level'],
				'children'	=>	make_tree($array, $value->id)
			];
		}
	}
	return $result;
}

function option_menu($array, $text = "", $select = 0, $result = ''){
	foreach ($array as $key => $value) {
		if ($select != 0 && $select == $key) {
			$result .= "<option value='".$key."' selected='selected'>".$text.$value['name']."</option>";
		}else{
			$result .= "<option value='".$key."'>".$text.$value['name']."</option>";
		}
		
		if (count($value['children']) > 0) {
			$result .= option_menu($value['children'], $text."|_", $select);
		}

		unset($array[$key]);
	}
	return $result;
}

function make_list_hierarchy($array, $checked = array(), $result = ''){
	$result .= '<ul class="list-tree">';
	foreach ($array as $key => $value) {
		if (array_key_exists($key, $checked)) {
			$result .= '<li class="list-tree-item">
			
			<input type="checkbox" value="'.$key.'" checked><label>'.$value['name'].'</label>';
		}else{
			$result .= '<li class="list-tree-item">
	
			<input type="checkbox" value="'.$key.'"><label>'.$value['name'].'</label>';
		}
		
		if (count($value['children']) > 0) {
			$result .= make_list_hierarchy($value['children'], $checked);
		}
		$result .= '</li>';

		unset($array[$key]);
	}
	$result .= '</ul>';
	return $result;
}

function format_price($price)
{
	return number_format($price) . ' VND';
}

function list_ids($array){
	$return = array();
	foreach ($array as $key => $value) {
		$return[$value['id']] = $value['name'];
	}
	return $return;
}

function array_to_string($array){
	$return = '';
	foreach ($array as $key => $value) {
		$return .= $key.',';
	}
	$return = rtrim($return, ',');
	return $return;
}