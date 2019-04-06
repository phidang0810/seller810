function search_products() {
	var slugProducts = '/san-pham';
	console.log(location);
	if (location.pathname == slugProducts) {
		getProducts();
	}else{
		window.location.href = getDomain + slugProducts;
	}
}

// Define functions
function getDomain () {
	return location.protocol + "//" + location.host;
}

function getCurrentUrl () {
	return location.origin + location.pathname;
}

function getCartDetailsNumber () {
	var data = {
		user_id: user_id
	};
	$.ajax({
		url: urlGetCartDetailsNumber,
		type: 'GET',
		data: data,
		dataType:'json',
		success: function(response) {
			if (response.success) {
				if (response.number >= 1) {
					if (response.number > 9) {
						$('#cart-icon span.number').html('9+');
					}else{
						$('#cart-icon span.number').html(response.number);
					}
				}else{
					$('#cart-icon span.number').html('');
				}
			}else{
			}
		}
	});
}

$(document).ready(function(){
	// Get total cart detail of current user with cart status in cart
	if (auth == 1) {
		getCartDetailsNumber();
	}
});