$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

// Define vars
var payment_method;
var transport_method;
var transport_info_name;
var transport_info_phone;
var cart_id;

$(document).ready(function() {
	// get data of page
	// get cart data
	getInCartCart();
	getCustomerInfo();
});

function getCustomerInfo() {
	var data = {
		user_id: user_id,
		type: "get_customer"
	};

	$.ajax({
		url: urlGetInCartCart,
		type: 'GET',
		data: data,
		dataType:'json',
		success: function(response) {
			printCustomerInfo(response.customer);
		}
	});
}

function getInCartCart() {
	var data = {
		status: "in_cart",
		user_id: user_id
	};

	$.ajax({
		url: urlGetInCartCart,
		type: 'GET',
		data: data,
		dataType:'json',
		success: function(response) {
			cart_id = response.cart.id;
			printCart(response.cart);
		}
	});
}

function printCustomerInfo(customer) {
	$('input[name=name]').val(customer.name);
	$('input[name=email]').val(customer.email);
	$('input[name=phone]').val(customer.phone);
	$('input[name=address]').val(customer.address);
	$('select[name=city]').val(customer.city_id);
	if (customer.default_payment) {
		$('#is_payment_default').prop('checked', true);
	}else{
		$('#is_payment_default').prop('checked', false);
	}
	showHideSections();
}

function printCart(cart) {
	$('#cart-details').html('');
	$.each(cart.details, function (key, detail) {
		printCartDetail(detail);
	});

	$('#price').html(cart.price);
	if (cart.shipping_fee > 0) {
		$('#shipping-fee').html(cart.shipping_fee_text);
	}
	$('#total_price').html(cart.total_price);
}

function printCartDetail(detail) {
	html = '<div class="col-12 cart-detail">\
	<div class="row">\
	<div class="col-3 image"><img src="storage/'+detail.photo+'" alt="'+detail.name+'" class="img-fluid"></div>\
	<div class="col-4 content">\
	<h6 class="title">'+detail.name+'</h6>\
	<p>Size: <span>'+detail.size.name+'</span></p>\
	<p>Màu: <span>'+detail.color.name+'</span></p>\
	</div>\
	<div class="col-1 quantity">'+detail.quantity+'</div>\
	<div class="col-4 price text-right">'+detail.total_price+'</div>\
	</div>\
	</div>';
	$('#cart-details').append(html);
}

function bankPayment() {
	payment_method = 1;
}

function codPayment() {
	payment_method = 2;
}

function truckTransport() {
	$('#truck-transport-info').css('display', 'flex');
	transport_method = 2;
}

function postOfficeTransport() {
	$('#truck-transport-info').css('display', 'none');
	transport_method = 1;
}

$('select[name=city]').on('change', function() {
	showHideSections();
});

function showHideSections() {
	if ($('select[name=city]').val() != 1) {
		$('#payment-cod').hide();
		$('#transport-info').show();
	}else{
		$('#payment-cod').show();
		$('#transport-info').hide();
	}
}

function storePayment() {
	$('#loading-indicator').show();
	var data = {
		user_id: user_id,
		cart_id: cart_id,
		payment_method: payment_method,
		transport_method: transport_method,
		customer_name: $('input[name=name]').val(),
		customer_email: $('input[name=email]').val(),
		customer_phone: $('input[name=phone]').val(),
		customer_address: $('input[name=address]').val(),
		customer_city: $('select[name=city]').val(),
		is_payment_default: $('#is_payment_default').prop('checked'),
		transport_info_name: $('input[name=garage-name]').val(),
		transport_info_phone: $('input[name=garage-phone]').val()
	};

	$.ajax({
		url: urlPostStorePayment,
		type: 'POST',
		data: data,
		dataType:'json',
		success: function(response) {
			if (response.redirect_to) {
				setTimeout(function(){ 
					window.location.href = response.redirect_to;
				}, 1000);
			}
		}
	});
}