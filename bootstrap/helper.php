<?php
function set_active($path, $active = 'active')
{
	return call_user_func_array('Request::is', (array)$path) ? $active : '';
}

function make_option($array, $select = 0, $field = 'name'){
	$result = '';
	foreach ($array as $key => $value) {
		if ($value['id'] == $select) {
			$result .= '<option value="'.$value['id'].'" selected="selected">'.$value[$field].'</option>';
		}else{
			$result .= '<option value="'.$value['id'].'">'.$value[$field].'</option>';
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
			$result .= option_menu($value['children'], $text."___", $select);
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
			
			<input type="checkbox" value="'.$key.'" checked>'.$value['name'];
		}else{
			$result .= '<li class="list-tree-item">
	
			<input type="checkbox" value="'.$key.'">'.$value['name'];
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

function make_list_hierarchy_no_checkbox($array){
	$result = '<ul class="list-tree">';
	foreach ($array as $key => $value) {
		$result .= '<li class="list-tree-item"><label>'.$value['name'].'</label>';
		
		if (count($value['children']) > 0) {
			$result .= make_list_hierarchy_no_checkbox($value['children']);
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

function general_code($string, $id, $number)
{
    $arr = explode(' ', trim($string));
    $code = '';
    foreach($arr as $s)
    {
        $code .= strtoupper(substr($s,0,1));
    }
    return $code . sprintf('%0' . $number . 'd', $id);
}

function parse_status($status){
	$status_parsed = '';
	switch ($status) {
		case EXCUTING:
			$status_parsed = '<span class="label label-primary">'.EXCUTING_TEXT.'</span>';
			break;

		case TRANSPORTING:
			$status_parsed = '<span class="label label-info">'.TRANSPORTING_TEXT.'</span>';
			break;

		case TRANSPORTED:
			$status_parsed = '<span class="label label-warning">'.TRANSPORTED_TEXT.'</span>';
			break;

		case COMPLETED:
			$status_parsed = '<span class="label label-success">'.COMPLETED_TEXT.'</span>';
			break;

		case CANCELED:
			$status_parsed = '<span class="label label-danger">'.CANCELED_TEXT.'</span>';
			break;

		default:
			$status_parsed = '<span class="label label-info">'.'Không xác định'.'</span>';
			break;
	}
	return $status_parsed;
}

function make_cart_status_options($selected = 0){
	$array = [
		array('id' => EXCUTING, 'name' => 'Đang xử lý'),
		array('id' => TRANSPORTING, 'name' => 'Đang giao'),
		array('id' => TRANSPORTED, 'name' => 'Đã giao'),
		array('id' => COMPLETED, 'name' => 'Đã hoàn tất'),
		array('id' => CANCELED, 'name' => 'Đã hủy'),
	];

	return make_option($array, $selected);
}

function make_payment_status_options($selected = 0){
	$array = [
		array('id' => NOT_PAYING, 'name' => 'Chưa thanh toán'),
		array('id' => PAYING_NOT_ENOUGH, 'name' => 'Chưa thanh toán đủ'),
		array('id' => PAYING_OFF, 'name' => 'Đã thanh toán'),
		array('id' => RECEIVED_PAYMENT, 'name' => 'Đã nhận tiền'),
	];

	return make_option($array, $selected);
}