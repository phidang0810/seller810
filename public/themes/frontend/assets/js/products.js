// Define vars
// var urlAjaxGetProducts = getDomain() + '/san-pham';
var urlAjaxGetProducts = getCurrentUrl();
var productsList;
var display_type = 'grid';
var color;
$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

$(document).ready(function(){
	getProducts();	
});

function clearFilter(type) {
	switch (type) {
		case 'color':
			$('.color-choice').removeClass('active');
			color = null;
			break;
		case 'size':
			$('input[type=radio][name=sizeRadio]').prop("checked", false)
			break;
		case 'price':
			$('input[type=radio][name=productPriceRadio]').prop("checked", false)
			break;
		default:
			// statements_def
			break;
	}
	getProducts();
}

$('input[type=radio][name=sizeRadio]').change(function() {
	getProducts();
});

$('.color-choice').click(function() {
	if($(this).hasClass('active')) {
		$(this).removeClass('active');
		color = null;
	}else{
		$('.color-choice').removeClass('active');
		color = $(this).attr('data-id');
		$(this).addClass('active');
	}
	getProducts();
});

$('input[type=radio][name=productPriceRadio]').change(function() {
	getProducts();
});

$('select#select-sorting').change(function() {
	getProducts();
});

// Set display style
function set_display_type(type) {
	display_type = type;
	printProducts(productsList);
}

// Load products from server
function getProducts (page = 1) {
	var data = {
		page: page,
		search_string: $("#search-string").val(),
		category: category,
		size: $('input[type=radio][name=sizeRadio]:checked').val(),
		color: color,
		price: $('input[type=radio][name=productPriceRadio]:checked').val(),
		sort: $('select#select-sorting').val()
	};

	$.ajax({
		url: urlAjaxGetProducts,
		type: 'GET',
		data: data,
		dataType:'json',
		success: function(response) {
			if (response.success) {
				productsList = response.data;
				printProducts(productsList);
				updatePageState(data);
			} else {
				alert("failed");
			}
		}
	});
}

// update page state: update url
function updatePageState(data) {
	var params = "";
	$.each(data, function (key, value) {
		if (value != null && value != "" && key != 'category') {
			if (params == "") {
				params += key + "=" + value;
			}else{
				params += "&" + key + "=" + value;
			}
		}
	});
	var url = urlAjaxGetProducts + '?' + params;
	window.history.pushState("","", url);
}

// print products list
function printProducts(data) {
	$("#products-list").html(""); // clear products list
	$.each(data.data, function (key, product) {
		if(display_type == 'grid') {
			$("#products-list").append(generateGridProduct(product));
			$('a.grid-icon').addClass('active');
			$('a.list-icon').removeClass('active');
		} else {
			$("#products-list").append(generateListProduct(product));
			$('a.list-icon').addClass('active');
			$('a.grid-icon').removeClass('active');
		}
	});
	generatePagination(data);
}

// function generate grid layout for product
function generateGridProduct(product) {
	var html = '<div class="col-md-4 col-sm-6 product product-grid">\
	<a href="/san-pham/' + product.id + '/' + product.slug + '">\
	<img src="/storage/' + product.thumb + '" alt="" class="img-fluid">\
	<h6 class="product-name">' + product.name + '</h6>';
	if (auth == 1) {
		html += '<h6 class="product-price">' + product.sell_price + '</h6>';
	}
	html += '</a>\
	</div>';
	return html;
}

// function generate list layout for product
function generateListProduct(product) {
	var html = '<div class="col-md-12 product product-list">\
	<div class="row">\
	<div class="col-md-3 col-sm-6 text-center">\
	<a href="/san-pham/' + product.id + '/' + product.slug + '"><img src="/storage/' + product.thumb + '" alt="" class="img-fluid"></a>\
	</div>\
	<div class="col-md-9 col-sm-6 contents">\
	<h6 class="product-name">' + product.name + '</h6>';
	if (auth == 1) {
		html += '<h6 class="product-price">' + product.sell_price + '</h6>';
	}
	html += '<p class="size"><span class="font-weight-bold">Kích thước: </span>' + product.sizes + '</p>\
	<p class="color"><span class="font-weight-bold">Màu sắc: </span>' + product.colors + '</p>\
	<a name="' + product.name + '" class="btn btn-success" href="/san-pham/' + product.id + '/' + product.slug + '" role="button">Xem Chi Tiết</a>\
	</div>\
	</div>\
	</div>';

	return html;
}

// function generate pagination 
function generatePagination(data) {
	var html = '';

	if (data.last_page <= 1) {
		$('#pagination ul.custom-pagination').html(html);
		return true;
	}

	for (var i = 1; i <= data.last_page; i++) {
		if (i == 1 || i == data.last_page || ( i >= data.current_page - 2 && i <= data.current_page + 2 )) {
			if (data.current_page == i) {
				html += '<li class="page-item active"><a class="page-link" href="javascript:;">' + i + '</a></li>';
			}else{
				html += '<li class="page-item"><a class="page-link" href="javascript:;" onclick="getProducts(' + i + ')">' + i + '</a></li>';
			}
		}
	}

	$('#pagination ul.custom-pagination').html(html);
}