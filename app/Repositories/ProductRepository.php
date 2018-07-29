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
use App\Models\Size;
use App\Models\Color;
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
			$categories = $this->categories($product->id);
			$cat_names = [];
			foreach ($categories as $category) {
				$cat_names[] = $category->name;
			}
			return implode($cat_names, ', ');
		})
		->rawColumns(['category', 'photo', 'status', 'action'])
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
		if(isset($data['photo'])) {

			if ($model->photo) {
				Storage::delete($model->photo);
			}
			$upload = new Photo($data['photo']);
			$model->photo = $upload->uploadTo('products');
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

		if (isset($data['product_photos'])) {
			$this->addPhotos($model->id, $data['product_photos']);
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
			$modelDetail = ProductDetail::where('product_id', $id)
			->where('color_id', $detail->color->id)
			->where('size_id', $detail->size->id)->first();
			if (!empty($modelDetail)) {
				$modelDetail->quantity = $detail->quantity;
				$modelDetail->save();
			}else{
				$modelDetail = new ProductDetail([
					'color_id' => $detail->color->id,
					'size_id' => $detail->size->id,
					'quantity' => $detail->quantity
				]);
				$model->details()->save($modelDetail);
			}

			if (!in_array($detail->size->name, $sizes)) {
				$sizes[] = $detail->size->name;
			}

			if (!in_array($detail->color->name, $colors)) {
				$colors[] = $detail->color->name;
			}
		}
		$data = [
			'sizes' => implode($sizes, ','),
			'colors' => implode($colors, ',')
		];

		return $data;
	}

	public function addPhotos($id, $photos){
		$model = Product::find($id);
		dd($photos);
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
				'color' => [
					'id' => $value->color->id,
					'name'	=>	$value->color->name
				],
				'size' => [
					'id' => $value->size->id,
					'name'	=>	$value->size->name
				],
				'quantity' => $value->quantity
			];
		}
		return $return;
	}

	public function getSizeOptions(){
		$sizes = Size::select(['sizes.id', 'sizes.name'])->get();
		$result = make_option($sizes);

		return $result;
	}

	public function getColorOptions(){
		$colors = Color::select(['colors.id', 'colors.name'])->get();
		$result = make_option($colors);

		return $result;
	}
}