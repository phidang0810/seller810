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
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;
use App\Libraries\Photo;
use Illuminate\Support\Facades\Storage;

Class ProductRepository
{
	const CACHE_NAME_PRODUCTS = 'products';

	public function dataTable($request)
	{
		$products = Product::select(['products.id', 'products.photo', 'products.code','products.name', 'products.quantity_available', 'products.price', 'products.sell_price', 'products.sizes', 'products.active', 'products.created_at']);

		$dataTable = DataTables::eloquent($products)
		->filter(function ($query) use ($request) {
			if (trim($request->get('category')) !== "") {
				$query->join('product_category', 'products.id', '=', 'product_category.product_id')
				->where('product_category.category_id', $request->get('category'));
			}
			
			if (trim($request->get('status')) !== "") {
				$query->where('products.active', $request->get('status'));
			}

			if (trim($request->get('keyword')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('products.name', 'like', '%' . $request->get('keyword') . '%');
				});

			}
		}, true)
		->addColumn('action', function ($product) {
			$html = '';
			$html .= '<a href="' . route('admin.products.view', ['id' => $product->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
			$html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $product->id . '" data-name="' . $product->name . '">';
			$html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';
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
				$html = '<img style="width: 80px; height: 60px;" class="img-thumbnail" src="' . asset('storage/' . $product->photo). '" />';
			} else {
				$html = ' <img alt="No Photo" style="width: 80px; height: 60px;" class="img-thumbnail" src="'.asset(NO_PHOTO).'" >';
			}
			return $html;
		})
		->addColumn('category', function ($product) {
			$category = $this->lowestLevelCategory($product->id);
			return ($category) ? $category->name : "";
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
		$model->quantity = $data['quantity'];
		$model->brand_id = $data['brand_id'];
		$model->content = $data['content'];
		$model->code = $data['code'];
		$model->barcode = $data['barcode'];
		$model->price = preg_replace('/[^0-9]/', '', $data['price']);
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

		$model->save();

		if (isset($data['categories'])) {
			$this->addCategories($model->id, $data['categories']);
		}

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
				Storage::delete($user->photo);
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
					}else{
						$modelDetail->quantity = $detail->quantity;
						$modelDetail->color_id = $detail->color_code->id;
						$modelDetail->size_id = $detail->size->id;
						$modelDetail->save();
					}
				}
			}else{
				if (!isset($detail->delete) || $detail->delete != true) {
					$modelDetail = new ProductDetail([
						'color_id' => $detail->color_code->id,
						'size_id' => $detail->size->id,
						'quantity' => $detail->quantity
					]);
					$model->details()->save($modelDetail);
				}
			}

			if (!in_array($detail->size->name, $sizes)) {
				$sizes[] = $detail->size->name;
			}

			if (!in_array($detail->color_code->name, $colors)) {
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
						Storage::delete($modelPhoto->origin);
	                // Storage::delete($modelPhoto->large);
	                // Storage::delete($modelPhoto->thumb);
						$modelPhoto->delete();
					}else{
						$modelPhoto->name = $photo->name;
						$modelPhoto->color_code = ($photo->color_code->id != 0 ) ? $photo->color_code->id : null;
						$modelPhoto->order = $photo->order;
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
									'name' => $photo->name,
									'color_code' => ($photo->color_code->id != 0 ) ? $photo->color_code->id : null,
									'order' => $photo->order,
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
					'id'	=>	$value->color->id,
					'name'	=>	$value->color->name
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
					'id' => (isset($value->color)) ? $value->color->id : "",
					'name'	=>	(isset($value->color)) ? $value->color->name : ""
				],
				'size' => [
					'id' => (isset($value->size)) ? $value->size->id : "",
					'name'	=>	(isset($value->size)) ? $value->size->name : ""
				],
				'quantity' => $value->quantity
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

	public function getBrandOptions($id){
		$model = Product::find($id);
		$brands = Brand::select(['brands.id', 'brands.name'])->get();
		if ($model && isset($model->brand->id)) {
			$result = make_option($brands, $model->brand->id);
		}else{
			$result = make_option($brands);
		}
		
		return $result;
	}
}