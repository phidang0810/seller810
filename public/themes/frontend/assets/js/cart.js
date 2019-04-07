$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

$(document).ready(function() {
	// get data of page
	// get cart data
	getInCartCart();
});

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
			if (response.success) {
				printCart(response.cart);
			}else{
				$('#submit-cart').hide();
				showAlert('warning', response.error);
			}
		}
	});
}

function printCart(cart) {
	$('#cart-details').html('');
	printCartInfo(cart);
	$.each(cart.details, function (key, detail) {
		printCartDetail(detail);
	});
	getCartDetailsNumber();

	setTimeout(function(){ 
		$('#loading-indicator').hide(); 
	}, 1000);
	
}

function printCartDetail(detail) {
	html = '<div class="row detail">\
	<div class="col-12">\
	<div class="row">\
	<div class="col-md-2 image">\
	<img src="storage/'+detail.photo+'" alt="'+detail.name+'" class="img-fluid">\
	</div>\
	<div class="col-md-6 info">\
	<h6 class="title">'+detail.name+'</h6>\
	<p>Cung cấp bởi: '+detail.supplier.name+'</p>\
	<a href="javascript:;" title="delete" onclick="deleteCartDetail('+detail.id+');">Xóa</a>\
	</div>\
	<div class="col-md-2">\
	<p class="price">'+detail.total_price+'</p>\
	</div>\
	<div class="col-md-2 quantity-section">\
	<div class="number-input">\
	<button onclick="down(this)" ></button>\
	<input data-id="'+detail.id+'" class="quantity" min="'+detail.min_quantity_sell+'" max="'+detail.product_detail.quantity_available+'" name="quantity-'+detail.id+'" value="'+detail.quantity+'" type="number">\
	<button onclick="up(this)" class="plus"></button>\
	</div>\
	</div>\
	</div>\
	</div>\
	</div>';
	$('#cart-details').append(html);
}

function printCartInfo(cart) {
	$('#pre-cal').html(cart.price);
	$('#total-price').html(cart.total_price);
}

function deleteCartDetail(id) {
	$('#loading-indicator').show();
	updateCartDetail(id, 0);
}

function up(a) {
	$('#loading-indicator').show();
	input = a.parentNode.querySelector('input[type=number]');
	input.stepUp();
	var id = $(input).attr('data-id');
	var quantity = $(input).val();
	updateCartDetail(id, quantity);
}

function down(a) {
	$('#loading-indicator').show();
	input = a.parentNode.querySelector('input[type=number]');
	input.stepDown();
	var id = $(input).attr('data-id');
	var quantity = $(input).val();
	updateCartDetail(id, quantity);
}

function updateCartDetail(id, quantity) {
	var data = {
		status: "in_cart",
		user_id: user_id,
		id: id,
		quantity: quantity,
	};

	$.ajax({
		url: urlGetInCartCart,
		type: 'GET',
		data: data,
		dataType:'json',
		success: function(response) {
			printCart(response.cart);
		}
	});
}

function showAlert(type, message) {
	var html = '<div class="col"><div class="alert alert-'+type+'" role="alert">\
	'+message+'\
	</div></div>';
	$('#cart-details').html(html);
}