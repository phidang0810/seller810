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