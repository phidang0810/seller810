<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 7/26/2018
 * Time: 12:04 AM
 */

namespace App\Repositories;

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

	public function dataTable($request)
	{
	$products = Product::select(['products.id', 'products.category_ids', 'products.photo', 'products.barcode_text','products.name', 'products.quantity_available', 'products.quantity', 'products.price', 'products.sell_price', 'products.sizes', 'products.active', 'products.created_at'/*, 'price*quantity as total_price'*/]);
	$categories = Category::get()->pluck('name', 'id')->toArray();
	$dataTable = DataTables::eloquent($products)
	->filter(function ($query) use ($request) {
		if (trim($request->get('category')) !== "") {
			$query->join('product_category', 'products.id', '=', 'product_category.product_id')
			->where('product_category.category_id', $request->get('category'));
		}

		if (trim($request->get('status')) !== "") {
			$query->where('products.active', $request->get('status'));
		}

		if (trim($request->get('private_search')) == "out_of_stock") {
			$query->where('products.quantity_available', 0);
		}

		if (trim($request->get('private_search')) == "need_import") {
			$query->where('products.quantity_available', '<=', 10);
			$query->where('products.quantity_available', '>', 0);
		}

		if (trim($request->get('keyword')) !== "") {
			$query->where(function ($sub) use ($request) {
				$sub->where('products.name', 'like', '%' . $request->get('keyword') . '%');
			});

		}
	}, true)
        // ->addColumn('category', function($product) use ($categories) {
        //     $html = '';
        //     $categoryIDs = explode(',', $product->category_ids);
        //     foreach ($categoryIDs as $categoryID) {
        //         $html .= '<label class="label label-default">'.$categories[$categoryID].'</label>';
        //     }
        //     return $html;
        // })
	->addColumn('action', function ($product) {
		$html = '';
		$html .= '<a href="' . route('admin.products.view', ['id' => $product->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
		// $html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $product->id . '" data-name="' . $product->name . '">';
		// $html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';
		return $html;
	})
	->addColumn('status', function ($product) {
		$active = '';
		$disable = '';
		if ($product->active === ACTIVE) {
			$active  = 'checked';
		}
		$html = '<input type="checkbox" '.$disable.' data-name="'.$product->name.'" data-id="'.$product->id.'" name="social' . $product->active . '" class="js-switch" value="' . $product->active . '" ' . $active . ' ./>';
		return $html;
	})
	->addColumn('photo', function ($product) {
		if ($product->photo) {
			$html = '<a class="fancybox" href="' . asset('storage/' . $product->photo). '" title="'.$product->name.'"><img style="width: 80px; height: 60px;" class="img-thumbnail" src="' . asset('storage/' . $product->photo). '" /></a>';
		} else {
			$html = ' <img alt="No Photo" style="width: 80px; height: 60px;" class="img-thumbnail" src="'.asset(NO_PHOTO).'" >';
		}
		return $html;
	})
	->addColumn('category', function ($product) {
		$category = $this->lowestLevelCategory($product->id);
		return ($category) ? $category->name : "";
	})
	->addColumn('quantity_available', function ($product) {
		return number_format($product->quantity_available);
	})
	->addColumn('sell_price', function ($product) {
		return format_price($product->sell_price);
	})
	->addColumn('price', function ($product) {
		return format_price($product->price);
	})
	->addColumn('name', function($product){
		$html = '';
		$html .= '<p>'.$product->name.'</p>';
		if ($product->sizes) {
			$html .= '<p>'.$product->sizes.'</p>';
		}
		return $html;
	})
	->rawColumns(['category', 'photo', 'status', 'action', 'name'])
	->toJson();

	return $dataTable;
}

public function getProduct($id)
{
	$data = Product::find($id);
	return $data;
}

public function getProducts()
{
	$data = Product::get();
	return $data;
}

public function createOrUpdate($data, $id = null)
{
	if ($id) {
		$model = Product::find($id);
	} else {
		$model = new Product;
	}

	$model->name = $data['name'];
	$model->active = $data['active']; 
	$model->order = $data['order'];
	$model->description = $data['description'];
	if (isset($data['quantity'])) {
		$model->quantity = $data['quantity'];
		$model->quantity_available = $data['quantity'];
	}
	$model->brand_id = $data['brand_id'];
	$model->supplier_id = $data['supplier_id'];
	$model->content = $data['content'];
		// $model->code = $data['code'];
		// $model->barcode = $data['barcode'];
	$model->meta_keyword = $data['meta_keyword'];
	$model->meta_description = $data['meta_description'];
	$model->meta_robot = $data['meta_robot'];
	if(key_exists('price', $data)) {
		$model->price = preg_replace('/[^0-9]/', '', $data['price']);
	}

	$model->sell_price = preg_replace('/[^0-9]/', '', $data['sell_price']);
	if(isset($data['photo'])) {

		if ($model->photo) {
			Storage::delete($model->photo);
		}
		$upload = new Photo($data['photo']);
		$model->photo = $upload->uploadTo('products');
	}

	if(isset($data['delete_photo']) && $data['delete_photo'] == true) {
		if ($model->photo) {
			Storage::delete($model->photo);
			$model->photo = null;
		}
	}
	if (isset($data['categories'])) {
		$model->category_ids = $data['categories'];
	}
	$model->save();

	if (isset($data['categories'])) {
		$this->addCategories($model->id, $data['categories']);

			// Generate product code based on category code
		$category = $this->lowestLevelCategory($model->id);
		$model->main_cate = $category->id;

	}

	$model->save();

	if (isset($data['details'])) {
		$tmp_data = $this->addDetails($model->id, $data['details']);
		$model->colors = $tmp_data['colors'];
		$model->sizes = $tmp_data['sizes'];
		$model->save();
	}
	if (isset($data['product_photos']) || isset($data['photos'])) {
		$data['product_photos'] = (isset($data['product_photos'])) ? $data['product_photos'] : null;
		$this->addPhotos($model->id, $data['product_photos'], $data['photos']);
	}

	return $model;
}

public function delete($ids)
{
	$result = [
		'success' => true,
		'errors' => []
	];
	foreach ($ids as $id) {
		$product = Product::find($id);
		if ($product === null) {
			$result['errors'][] = 'ID sản phẩm: ' . $id . ' không tồn tại';
			$result['success'] = false;
			continue;
		}

		if ($product->photo) {
			Storage::delete($product->photo);
		}

		if ($product->details) {
			$product->details()->delete();
		}

		if ($product->photos) {
			foreach ($product->photos as $photo) {
				$photo->deleteImageOnStorage();
			}
			$product->photos()->delete();
		}

		if ($product->categories) {
			$product->categories()->detach();
		}
		$product->delete();
	}

	return $result;
}

public function changeStatus($productID, $status)
{
	$model = Product::find($productID);
	$model->active = $status;
	return $model->save();
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

public function addCategories($id, $categories_string){
	$categories = explode(',', $categories_string);
	$model = Product::find($id);
	$model->categories()->sync($categories);
}

public function addDetails($id, $details){
	$details = json_decode($details);
	$model = Product::find($id);
	$sizes = [];
	$colors = [];
	foreach ($details as $detail) {
		if (isset($detail->id)) {
			$modelDetail = ProductDetail::find($detail->id);
			if ($modelDetail) {
				if (isset($detail->delete) && $detail->delete == true) {
					$modelDetail->delete();
				}
			}
		}
	}
	foreach ($details as $detail) {

		if (isset($detail->id)) {
			$modelDetail = ProductDetail::find($detail->id);
			if ($modelDetail) {
				if (!isset($detail->delete) || $detail->delete != true) {
					$modelDetail->quantity = ($detail->quantity) ? $detail->quantity : 0;
					$modelDetail->quantity_available = ($detail->quantity) ? $detail->quantity : 0;
					$modelDetail->color_id = ($detail->color_code) ? $detail->color_code->id : 0;
					$modelDetail->size_id = ($detail->size) ? $detail->size->id : 0;
					$modelDetail->save();
				}
			}
		}else{
			if (!isset($detail->delete) || $detail->delete != true) {
				$modelDetail = new ProductDetail([
					'color_id' => ($detail->color_code) ? $detail->color_code->id : 0,
					'size_id' => ($detail->size) ? $detail->size->id : 0,
					'quantity' => ($detail->quantity) ? $detail->quantity : 0,
					'quantity_available' => ($detail->quantity) ? $detail->quantity : 0
				]);
				$model->details()->save($modelDetail);
			}
		}

		if ($detail->size && !in_array($detail->size->name, $sizes)) {
			$sizes[] = $detail->size->name;
		}

		if ($detail->color_code && !in_array($detail->color_code->name, $colors)) {
			$colors[] = $detail->color_code->name;
		}
	}
	$data = [
		'sizes' => implode($sizes, ','),
		'colors' => implode($colors, ',')
	];

	return $data;
}

public function addPhotos($id, $files = null, $photos){
	$photos = json_decode($photos);
	$model = Product::find($id);
	foreach ($photos as $key => $photo) {

		if (isset($photo->id)) {
			$modelPhoto = ProductPhoto::find($photo->id);
			if ($modelPhoto) {
				if (isset($photo->delete) && $photo->delete == true) {
					if ($modelPhoto->origin) {
						Storage::delete($modelPhoto->origin);
					}
		                // Storage::delete($modelPhoto->large);
		                // Storage::delete($modelPhoto->thumb);
					$modelPhoto->delete();
				}else{
					$modelPhoto->name = ($photo->name) ? $photo->name : null;
					$modelPhoto->color_code = ($photo->color_code && $photo->color_code->id != 0 ) ? $photo->color_code->id : null;
					$modelPhoto->order = ($photo->order) ? $photo->order : 0;
					$modelPhoto->save();
				}
			}
		}else{
			if ($photo->file_name) {
				if ($files) {
					foreach ($files as $file) {
						if ($photo->file_name == $file->getClientOriginalName() && $photo->delete != true) {
							$upload = new Photo($file);
							$modelPhoto = new ProductPhoto([
								'name' => ($photo->name) ? $photo->name : null,
								'color_code' => ($photo->color_code && $photo->color_code->id != 0 ) ? $photo->color_code->id : null,
								'order' => ($photo->order) ? $photo->order : 0,
								'origin' => $upload->uploadTo('product_photos'),
									// 'large' => $upload->resizeTo('product_photos', Product::LARGE_WIDTH, Product::LARGE_HEIGHT),
									// 'thumb' => $upload->resizeTo('product_photos', Product::THUMB_WIDTH, Product::THUMB_HEIGHT)
							]);
							$model->photos()->save($modelPhoto);
						}
					}
				}
			}
		}
	}
}

public function getPhotos($id){
	$model = Product::find($id);
	$return = [];
	foreach ($model->photos as $key => $value) {
		$value->color;
		$return[] = [
			'id' => $value->id,
			'name'	=>	$value->name,
			'color_code' => [
				'id'	=>	($value->color) ? $value->color->id : 0,
				'name'	=>	($value->color) ? $value->color->name : ''
			],
			'origin' => $value->origin,
			'origin_url' => asset('storage/' . $value->origin),
			'order' => $value->order
		];
	}
	return $return;
}

public function getDetails($id){
	$model = Product::find($id);
	foreach ($model->details as $detail) {
		$detail->size;
		$detail->color;
	}
	$return = [];
	foreach ($model->details as $key => $value) {
		$return[] = [
			'id' => $value->id,
			'color_code' => [
				'id' => ($value->color) ? $value->color->id : 0,
				'name'	=>	($value->color) ? $value->color->name : ""
			],
			'size' => [
				'id' => ($value->size) ? $value->size->id : 0,
				'name'	=>	($value->size) ? $value->size->name : ""
			],
			'quantity' => ($value->quantity) ? $value->quantity : 0
		];
	}
	return $return;
}

public function getColors(){
	$colors = Color::select(['colors.id', 'colors.name'])->get();

	return $colors;
}

public function getSizes(){
	$sizes = Size::select(['sizes.id', 'sizes.name'])->get();

	return $sizes;
}

public function getSizeOptions($id){
	$sizes = Size::select(['sizes.id', 'sizes.name'])->get();
	$result = make_option($sizes);

	return $result;
}

public function getColorOptions($id){
	$colors = Color::select(['colors.id', 'colors.name'])->get();
	$result = make_option($colors);

	return $result;
}

	//---> Get Warehouses Options
public function getWarehouseOptions(){
	$warehouses = Warehouse::select(['warehouses.id', 'warehouses.name'])->get();
	$result = make_option($warehouses);

	return $result;
}

public function getProductOptions(){
	return make_option($this->getProducts());
}

public function getProductDatas($request){
	$product_id = $request->get('product_id');
	$color_id = $request->get('color_id');
	$size_id = $request->get('size_id');
	$warehouse_id = $request->get('warehouse_id');

	$return = [
		'product_id' => $product_id,
		'message'	=>	'Lấy datas cho product thành công',
	];

	if ($product_id) {
		$product = Product::find($product_id);
		$return['product'] = $product;
	}

	if ($warehouse_id) {
		$warehouse = Warehouse::find($warehouse_id);
		$return['warehouse'] = $warehouse;
	}

	if ($product_id && $color_id && $size_id && $warehouse_id) {
		$product_detail = ProductDetail::having('product_id', '=', $product_id)
		->having('color_id', '=', $color_id)
		->having('size_id', '=', $size_id)
		->first();

		$return['product_detail'] = $product_detail;

		$warehouse_product = WarehouseProduct::where('warehouse_id', $warehouse->id)
		->where('product_detail_id', $product_detail->id)
		->first();

		$return['warehouse_product_id'] = $warehouse_product->id;

		$return['max_quantity'] = $warehouse_product->quantity_available;
	}

	return Response::json($return);

}

public function getProductByBarcode($request){
	$barcode_text = $request->get('barcode_text');

	$return = [
		'result' => 'success',
		'message'	=>	'Lấy datas cho product thành công',
	];

	if (!$barcode_text) {
		$return['message'] = 'Phải nhập vào barcode';
		$return['result'] = 'fail';
		return Response::json($return);
	}

	$warehouse_product = WarehouseProduct::where('barcode_text', $barcode_text)->first();

	if (!$warehouse_product) {
		$return['message'] = 'Sản phẩm có barcode '.$barcode_text.' không tồn tại';
		$return['result'] = 'fail';
		return Response::json($return);
	}

	$return['warehouseProduct'] = $warehouse_product;
	$return['product'] = $warehouse_product->product;
	$return['warehouse'] = $warehouse_product->warehouse;
	$return['product_detail'] = $warehouse_product->productDetail;
	$return['size'] = $warehouse_product->productDetail->size;
	$return['color'] = $warehouse_product->productDetail->color;
	$return['warehouse_product_id'] = $warehouse_product->id;
	$return['max_quantity'] = $warehouse_product->quantity_available;

	return Response::json($return);
}

public function getProductDetailColorOptions($request){
	$product_id = $request->get('product_id');

	$return = [
		'product_id' => $product_id,
		'message'	=>	'Lấy color options cho product detail thành công',
	];

	if ($product_id) {
		$product_details = ProductDetail::having('product_id', '=', $product_id)->having('quantity', '>', 0)
		->get();


		$color_ids = [];
		foreach ($product_details as $key => $value) {
			$color_ids[] = $value['color_id'];
		}

		$colors = Color::whereIn('id', $color_ids)->get();

		$return['colors'] = $colors;
	}

	return Response::json($return);
}

public function getProductDetailWarehouseOptions($request){
	$product_id = $request->get('product_id');
	$color_id = $request->get('color_id');
	$size_id = $request->get('size_id');

	$return = [
		'product_id' => $product_id,
		'color_id' => $color_id,
		'size_id' => $size_id,
		'message'	=>	'Lấy warehouses options cho product detail thành công',
	];

	if ($product_id) {
		$product_detail = ProductDetail::having('product_id', '=', $product_id)
		->having('color_id', '=', $color_id)
		->having('size_id', '=', $size_id)
		->having('quantity', '>', 0)
		->first();

		$product_details = WarehouseProduct::where('product_id', '=', $product_id)
		->where('product_detail_id', $product_detail->id)
		->where('quantity', '>', 0)->get();

		$return['product_details'] = $product_details;
		$return['product_detail'] = $product_detail;

		$warehouse_ids = [];
		foreach ($product_details as $key => $value) {
			$warehouse_ids[] = $value['warehouse_id'];
		}

			// $return['warehouses_id'] = $product_details;

		$warehouses = Warehouse::whereIn('id', $warehouse_ids)->get();

		$return['warehourses'] = $warehouses;
	}

	return Response::json($return);

}

public function getProductDetailSizeOptions($request){
	$product_id = $request->get('product_id');
	$color_id = $request->get('color_id');

	$return = [
		'product_id' => $product_id,
		'color_id' => $color_id,
		'message'	=>	'Lấy size options cho product detail thành công',
	];

	if ($product_id && $color_id) {
		$product_details = ProductDetail::having('product_id', '=', $product_id)->having('quantity', '>', 0)
		->having('color_id', '=', $color_id)
		->get();

		$return['product_details'] = $product_details;

		$size_ids = [];
		foreach ($product_details as $key => $value) {
			$size_ids[] = $value['size_id'];
		}

		$sizes = Size::whereIn('id', $size_ids)->get();

		$return['sizes'] = $sizes;
	}

	return Response::json($return);
}

function getProductDetailquantity_bk($request){
	$product_id = $request->get('product_id');
	$color_id = $request->get('color_id');
	$size_id = $request->get('size_id');

	$return = [
		'product_id' => $product_id,
		'color_id' => $color_id,
		'size_id' => $size_id,
		'message'	=>	'Lấy quantity cho product detail thành công',
	];

	if ($product_id && $color_id && $size_id) {
		$product_detail = ProductDetail::having('product_id', '=', $product_id)
		->having('color_id', '=', $color_id)
		->having('size_id', '=', $size_id)
		->having('quantity', '>', 0)
		->first();

		$return['quantity'] = $product_detail->quantity;
		$return['detail_id'] = $product_detail->id;
	}

	return Response::json($return);
}

function getProductDetailquantity($request){
	$product_id = $request->get('product_id');
	$color_id = $request->get('color_id');
	$size_id = $request->get('size_id');
	$warehouse_id = $request->get('warehouse_id');

	$return = [
		'product_id' => $product_id,
		'color_id' => $color_id,
		'size_id' => $size_id,
		'warehouse_id' => $warehouse_id,
		'message'	=>	'Lấy quantity cho product detail thành công',
	];

	if ($product_id && $color_id && $size_id && $warehouse_id) {
		$product_detail = ProductDetail::having('product_id', '=', $product_id)
		->having('color_id', '=', $color_id)
		->having('size_id', '=', $size_id)
		->having('quantity_available', '>', 0)
		->first();

		$warehouse_products = WarehouseProduct::having('product_detail_id', '=', $product_detail->id)
		->having('warehouse_id', '=', $warehouse_id)
		->having('quantity_available', '>', 0)
		->first();

		$return['quantity'] = $warehouse_products->quantity_available;
		$return['warehouse_product_id'] = $warehouse_products->id;
	}

	return Response::json($return);
}

public function getBrandOptions($id){
	$model = Product::find($id);
	$brands = Brand::select(['brands.id', 'brands.name'])->get();
	if ($model && $model->brand) {
		$result = make_option($brands, $model->brand->id);
	}else{
		$result = make_option($brands);
	}

	return $result;
}

public function validateAjax($request, $id = null){
	$name = $request->get('name');
	$value = $request->get('value');
	$id = $request->get('id');
	$return = [
		'result' => true,
		'message'	=>	$value.' khả dụng',
		'id'	=>	$id
	];

	$model = Product::where($name,$value)->first();
	if ($model) {
		if ($id == null || $id != $model->id) {
			$return['result'] = false;
			$return['message'] = $value.' đã được sử dụng';
		}
	}
	return Response::json($return);
}

public function updateProductQuantityAvaiable(){

}

public function getProductsV2($request)
{
	$formatted_products = [];
	$term = trim($request->q);

	$products_list = Product::where('name','LIKE', '%'.$term.'%')->where('quantity_available', '>', 0)->get();
	foreach ($products_list as $product) {
		$formatted_products[] = ['id' => $product->id, 'text' => $product->name];
	}

	return $formatted_products;
}

public function getProductsEmptiable($request)
{
	$formatted_products = [];
	$term = trim($request->q);

	$products_list = Product::where('name','LIKE', '%'.$term.'%')->get();
	foreach ($products_list as $product) {
		$formatted_products[] = ['id' => $product->id, 'text' => $product->name];
	}

	return $formatted_products;
}

public function getTotalProductNeedImport()
{
	$data = Product::where('active', ACTIVE)
	->where('quantity_available', '<=', 10)
	->where('quantity_available', '>', 0)
	->count();
	return $data;
}

public function getTotalProductUnAvailable()
{
	$data = Product::where('active', ACTIVE)
	->where('quantity_available', '=', 0)
	->count();

	return $data;
}

public function retrieveProduct($request){
	$product_id = $request->get('product_id');

	$return = [
		'product_id' => $product_id,
		'message'	=>	'Lấy datas cho product thành công',
	];

	if ($product_id) {
		$product = Product::find($product_id);
		$return['product'] = $product;
		$return['product']['photos'] = $this->getPhotos($product_id);
	}

	return Response::json($return);
}

public function getSupplierOptions($id){
	$model = Product::find($id);
	$suppliers = Supplier::where('active', 1)->get();

	if ($model && $model->supplier_id) {
		$result = make_option($suppliers, $model->supplier_id);
	}else{
		$result = make_option($suppliers, 0);
	}

	return $result;
}

public function getDetailsByWarehouses($id){
	$warehouses = Warehouse::get();
	$model = Product::find($id);
	$return = [];
	if ($model) {
		foreach ($warehouses as $warehouse) {
			$return[] = [
				'details' => $this->getDetailsByWarehouse($model->id, $warehouse->id),
				'name' => $warehouse->name,
				'code' => $warehouse->code
			];
		}
	}

	return $return;
}

public function getDetailsByWarehouse($id, $warehouse_id){
	$warehouseProducts = WarehouseProduct::where('warehouse_id', $warehouse_id)->where('product_id', $id)->get();
	$return = [];
	foreach ($warehouseProducts as $warehouseProduct) {
		$return[] = [
			'id' => $warehouseProduct->id,
			'color_code' => [
				'id' => ($warehouseProduct->productDetail && $warehouseProduct->productDetail->color) ? $warehouseProduct->productDetail->color->id : 0,
				'name'	=>	($warehouseProduct->productDetail && $warehouseProduct->productDetail->color) ? $warehouseProduct->productDetail->color->name : ""
			],
			'size' => [
				'id' => ($warehouseProduct->productDetail && $warehouseProduct->productDetail->size) ? $warehouseProduct->productDetail->size->id : 0,
				'name'	=>	($warehouseProduct->productDetail && $warehouseProduct->productDetail->size) ? $warehouseProduct->productDetail->size->name : ""
			],
			'quantity' => ($warehouseProduct->quantity) ? $warehouseProduct->quantity : 0,
			'quantity_available' => ($warehouseProduct->quantity_available) ? $warehouseProduct->quantity_available : 0,
			'barcode' => ($warehouseProduct->barcode) ? $warehouseProduct->barcode : ""
		];
	}
	return $return;
}

public function getProductsByFilters($request) {
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
	$data =	$data->paginate(2);

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
}