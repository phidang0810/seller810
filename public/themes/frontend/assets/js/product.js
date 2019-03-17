$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

var color = 0, size = 0;

function selectColor (id) {
	$('.quantity-section .number-input').hide();
	$('#colors a').removeClass('active');
	$('#color-'+id).addClass('active');
	color = id;
	getQuantityMinMax();
}

function selectSize (id) {
	$('.quantity-section .number-input').hide();
	$('#sizes a').removeClass('active');
	$('#size-'+id).addClass('active');
	size = id;
	getQuantityMinMax();
}

function getQuantityMinMax () {
	if (color != 0 && size != 0) {
		var data = {
			id: product_id,
			size: size,
			color: color,
		};
		$.ajax({
			url: urlGetQuantity,
			type: 'GET',
			data: data,
			dataType:'json',
			success: function(response) {
				$('input[name=quantity]').attr('max', response.quantity_available);
				if ($('input[name=quantity]').val() > response.quantity_available) {
					$('input[name=quantity]').val(response.quantity_available);
				}
				$('.quantity-section .number-input').css('display', 'inline-flex');
			}
		});
	}else{
		return false;
	}
}

function addToCart() {
	var quantity = $('input[name=quantity]').val();
	var data = {
		id: product_id,
		size: size,
		color: color,
		quantity: quantity,
		user_id: user_id
	};
	$.ajax({
		url: urlAddCartDetail,
		type: 'POST',
		data: data,
		dataType:'json',
		success: function(response) {
			if (response.success) {
				$('.alert').addClass('alert-success');
				$('h4.alert-heading').html("Đặt hàng thành công.");
				$('.alert p').html(response.message);
				getCartDetailsNumber();
			}else{
				$('.alert').addClass('alert-danger');
				$('h4.alert-heading').html("Đặt hàng không thành công.");
				$('.alert p').html(response.error);
			}
			$('#alert').show();
		}
	});
}

$(document).ready(function() {
	$('#alert').hide();
});