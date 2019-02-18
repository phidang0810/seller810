// Define vars
var urlAjaxGetProducts = getDomain() + '/san-pham';
$.ajaxSetup({
	headers: {
		'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	}
});

$(document).ready(function(){
	getProducts();
});

// When radio change
$('input[type=radio][name=categoryRadio]').change(function() {
    getProducts();
});

$('input[type=radio][name=sizeRadio]').change(function() {
    getProducts();
});

$('input[type=radio][name=colorRadio]').change(function() {
    getProducts();
});

$('input[type=radio][name=productPriceRadio]').change(function() {
    getProducts();
});

$('select#select-sorting').change(function() {
    getProducts();
});

function getProducts (page = 1) {
	var data = {
		page: page,
		search_string: $("#search-string").val(),
		category: $('input[type=radio][name=categoryRadio]:checked').val(),
		size: $('input[type=radio][name=sizeRadio]:checked').val(),
		color: $('input[type=radio][name=colorRadio]:checked').val(),
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
				printProducts(response.data);
				updatePageState(data);
			} else {
				alert("failed");
			}
		}
	});
}

function updatePageState(data) {
	var params = "";
	$.each(data, function (key, value) {
		if (value != null && value != "") {
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

function printProducts(data) {
	$("#products-list").html(""); // clear products list
	$.each(data.data, function (key, product) {
		$("#products-list").append(generateProduct(product));
	});
	generatePagination(data);
}

// function generate layout for product
function generateProduct(product) {
	var html = '<div class="col-md-4 col-sm-6 product">\
	<a href="#">\
	<img src="storage/' + product.photo + '" alt="" class="img-fluid">\
	<h6 class="product-name">' + product.name + '</h6>\
	<h6 class="product-price">' + product.sell_price + '</h6>\
	</a>\
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