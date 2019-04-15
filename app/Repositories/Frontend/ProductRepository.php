<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 7/26/2018
 * Time: 12:04 AM
 */

namespace App\Repositories\Frontend;

use App\Models\CartDetail;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductPhoto;
use App\Models\Category;
use App\Models\Size;
use App\Models\Color;
use App\Models\Brand;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use JFilla\Barcode\BarcodeGeneratorPNG;
use Yajra\DataTables\Facades\DataTables;
use App\Libraries\Photo;
use Illuminate\Support\Facades\Storage;
use Response;
use DNS1D;
use Zend\Barcode\Barcode;

Class ProductRepository
{
	const CACHE_NAME_PRODUCTS = 'products';

	public function getProduct($id)
	{
		$data = Product::find($id);
		$data->sell_price = format_price($data->sell_price);

		// Get colors
		$product_details = ProductDetail::having('product_id', '=', $data->id)->having('quantity', '>', 0)
		->get();

		$color_ids = [];
		$size_ids = [];
		foreach ($product_details as $key => $value) {
			$color_ids[] = $value['color_id'];
			$size_ids[] = $value['size_id'];
		}

		$data->colorObjects = Color::whereIn('id', $color_ids)->get();
		$data->sizeObjects = Size::whereIn('id', $size_ids)->get();

		$data->category = $this->lowestLevelCategory($id);
		$data->relatedProducts = $this->getRelatedProducts($data->category->id);

		return $data;
	}

	public function getProductBySlug($slug)
	{
		$data = Product::where('slug', $slug)->first();
		$data->sell_price = format_price($data->sell_price);

		// Get colors
		$product_details = ProductDetail::having('product_id', '=', $data->id)->having('quantity', '>', 0)
		->get();

		$color_ids = [];
		$size_ids = [];
		foreach ($product_details as $key => $value) {
			$color_ids[] = $value['color_id'];
			$size_ids[] = $value['size_id'];
		}

		$data->colorObjects = Color::whereIn('id', $color_ids)->get();
		$data->sizeObjects = Size::whereIn('id', $size_ids)->get();

		$data->category = $this->lowestLevelCategory($data->id);
		$data->relatedProducts = $this->getRelatedProducts($data->category->id);
		$data->hotProducts = $this->getHotProducts();

		return $data;
	}

	public function getHotProducts ($limit = 4) {
		return Product::having('is_hot', ACTIVE)->limit($limit)->get();
	}

	public function getRelatedProducts ($category_id, $limit = 3) {
		return Product::having('category_ids', 'like',  "%$category_id%")->limit($limit)->get();
	}

	public function categories($id){
		$model = Product::find($id);
		$categories = $model->categories;
		return $categories;
	}

	public function lowestLevelCategory($id){
		$category = Category::whereHas('products', function($q) use($id)
		{
			$q->where('id', '=', $id);

		})->orderBy('level', 'desc')->first();
		return $category;
	}

	public function idCategories($id){
		$categories = $this->categories($id);
		$idCategories = list_ids($categories);
		return $idCategories;
	}

	public function getProductPriceRanges() {
		return [
			"0" => [
				"value" => "0-100000",
				"label" => "Dưới 100.000 VND"
			],
			"1" => [
				"value" => "100000-300000",
				"label" => "Từ 100.000 VND đến 300.000 VND"
			],
			"3" => [
				"value" => "300000",
				"label" => "Hơn 300.000 VND"
			]
		];
	}

	public function getMaxQuantity($request) {
		$product_details = ProductDetail::having('product_id', '=', $request->get('id'))->having('color_id', '=', $request->get('color'))->having('size_id', '=', $request->get('size'))->first();
		return $product_details;
	}

	public function getProductsByFilters($request, $category = null) {
	// Filter with search string
		$data = Product::where('products.name', 'like', '%' . $request->get('search_string') . '%');

	// Filter category
		if ($request->get('category') != "") {
			$data->join('product_category', 'products.id', '=', 'product_category.product_id')
			->where('product_category.category_id', $request->get('category'));
		}

	// Filter size
		if ($request->get('size') != "") {
			$data->where('products.sizes', 'like', '%' . $request->get('size') . '%');
		}

	// Filter size
		if ($request->get('color') != "") {
			$data->where('products.colors', 'like', '%' . $request->get('color') . '%');
		}

	// Filter with price
		if ($request->get('price') != "") {
			$prices = explode("-", $request->get('price'));
			if (count($prices) > 1) {
				$data->where('products.sell_price', '>=', $prices[0])
				->where('products.sell_price', '<', $prices[1]);
			}else{
				$data->where('products.sell_price', '>', $prices[0]);
			}
		}

	// Sorting
		if ($request->get('sort') != "") {
			$data->orderBy('sell_price', $request->get('sort'));
		}

	// Get products with paginate
		$data =	$data->paginate(9);

	// Format price for all products got
		foreach ($data as $key => $value) {
			$data[$key]->sell_price = format_price($value->sell_price);
		}

		$return = [
			'success' => true,
			'data' => $data
		];

		return Response::json($return);
	}

	public function getProductByID($id)
	{
		$data = Product::find($id);
		$data->sell_price = format_price($data->sell_price);

		// Get colors
		$product_details = ProductDetail::having('product_id', '=', $data->id)->having('quantity', '>', 0)
		->get();

		$color_ids = [];
		$size_ids = [];
		foreach ($product_details as $key => $value) {
			$color_ids[] = $value['color_id'];
			$size_ids[] = $value['size_id'];
		}

		$data->colorObjects = Color::whereIn('id', $color_ids)->get();
		$data->sizeObjects = Size::whereIn('id', $size_ids)->get();

		$data->category = $this->lowestLevelCategory($data->id);
		$data->relatedProducts = $this->getRelatedProducts($data->category->id);
		$data->hotProducts = $this->getHotProducts();

		return $data;
	}

}