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
			console.log(response);
			if (response.success) {
				$('#cart-icon span.number').html(response.number);
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