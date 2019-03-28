$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

var color = 0, size = 0;

$('.color-choice').click(function() {
	if($(this).hasClass('active')) {
		$(this).removeClass('active');
		color = 0;
	}else{
		$('.color-choice').removeClass('active');
		color = $(this).attr('data-id');
		$(this).addClass('active');
		getQuantityMinMax();
	}
});

$('.size-choice').click(function() {
	if($(this).hasClass('active')) {
		$(this).removeClass('active');
		size = 0;
	}else{
		$('.size-choice').removeClass('active');
		size = $(this).attr('data-id');
		$(this).addClass('active');
		getQuantityMinMax();
	}
});

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
			}
		});
	}else{
		return false;
	}
}

function addToCart() {
	if (color != 0 && size != 0) {
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
					showAlert('alert-success', 'Đặt hàng thành công', response.message);
					getCartDetailsNumber();
				}else{
					showAlert('alert-danger', 'Đặt hàng không thành công', response.error);
				}
			}
		});
	}else{
		showAlert('alert-warning', 'Đặt hàng không thành công', 'Bạn cần phải chọn size, color và nhập số lượng mới có thể đặt hàng');
		return false;
	}
}

function showAlert(className, title, message) {
	$('.alert').addClass(className);
	$('h4.alert-heading').html(title);
	$('.alert p').html(message);
	$('#alert').show();
	hideAlert(className);
}

function hideAlert(className) {
	setTimeout(function() {
		$('.alert').removeClass(className);
		$('#alert').hide();
	}, 2000);
}

$(document).ready(function() {
	$('#alert').hide();
});