<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 10/06/2018
 * Time: 12:04 AM
 */

namespace App\Repositories;

use App\Models\ImportProduct;
use App\Models\ImportProductDetail;
use App\Models\Permission;
use App\Models\Warehouse;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;
use App\Libraries\Photo;
use Illuminate\Support\Facades\Storage;
use Response;
use DNS1D;

Class ImportProductRepository
{
	const CACHE_NAME_PRODUCTS = 'import_products';

	public function dataTable($request){
		$importProducts = ImportProduct::select(['id', 'code', 'product_id', 'quantity', 'total_price', 'status', 'created_at']);

		$dataTable = DataTables::eloquent($importProducts)
		->filter(function ($query) use ($request) {
			if (trim($request->get('status')) !== "") {
				$query->where('active', $request->get('status'));
			}
		}, true)
		->addColumn('action', function ($importProduct) {
			$html = '';
			$html .= '<a href="' . route('admin.import_products.view', ['id' => $importProduct->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
			return $html;
		})
		->addColumn('product_name', function ($importProduct) {
			$product = $importProduct->product;
			$html = $product->name;
			return $html;
		})
		->addColumn('supplier_name', function ($importProduct) {
			$product = $importProduct->product;
			$supplier = $product->supplier;
			if ($supplier) {
				$html = $supplier->name;
				return $html;
			}
			return null;
		})
		->addColumn('product_category', function ($importProduct) {
			$productRepository = new ProductRepository;
			$product = $importProduct->product;
			$category =  $productRepository->lowestLevelCategory($product->id);
			return ($category) ? $category->name : "";
		})
		->rawColumns(['action', 'product_name', 'supplier_name', 'product_category'])
		->toJson();

		return $dataTable;
	}

	public function get($id)
	{
		$data = ImportProduct::find($id);
		return $data;
	}

	public function createOrUpdate($data, $id = null)
	{
		if ($id) {
			$model = ImportProduct::find($id);
		} else {
			$model = new ImportProduct;
		}

		$productRepository = new ProductRepository;
		$product_model = $productRepository->createOrUpdate($data, $data['product_id']);

		$model->product_id = $product_model->id;
		$model->code = general_code('N H', $id, 6);
		$model->quantity = $data['import_quantity'];
		$model->import_staff_id = $data['import_staff_id'];
		$model->warehouse_id = $data['warehouse_id'];
		if(key_exists('price', $data)) {
			$model->price = preg_replace('/[^0-9]/', '', $data['price']);
			$model->total_price = $model->price * $model->quantity;
		}
		$model->note = $data['note'];
		$model->status = IMPORT_IMPORTING;

		$model->save();

		if (isset($data['importDetails'])) {
			$importDetails = json_decode($data['importDetails']);

			// Delete import detail if delete
			foreach ($importDetails as $detail) {
				if (isset($detail->id)) {
					$modelDetail = ImportProductDetail::find($detail->id);
					if ($modelDetail) {
						if (isset($detail->delete) && $detail->delete == true) {
							$modelDetail->delete();
						}
					}
				}
			}

			// Add/update import detail
			foreach ($importDetails as $key => $importDetail) {
				if (!isset($importDetail->delete) || $importDetail->delete != true) {
					$productDetail = ProductDetail::where("product_id", $model->product_id)
					->where("color_id", $importDetail->color_code->id)
					->where("size_id", $importDetail->size->id)
					->first();

					if (!$productDetail) {
						$productDetail = new ProductDetail([
							'color_id' => ($importDetail->color_code) ? $importDetail->color_code->id : 0,
							'size_id' => ($importDetail->size) ? $importDetail->size->id : 0,
							'quantity' => 0
						]);
						$product = Product::find($model->product_id);
						$product->details()->save($productDetail);
					}

					if ($productDetail) {
						$detail = ImportProductDetail::where("import_product_id", $model->id)
						->where("product_detail_id", $productDetail->id)->first();
						if ($detail) {
							$detail['quantity'] = $importDetail->quantity;
							$detail->save();
						}else{
							$detail = new ImportProductDetail([
								'import_product_id' => $model->id,
								'product_id' => $model->product_id,
								'product_detail_id' => $productDetail->id,
								'quantity' => $importDetail->quantity,
								'status' => IMPORT_DETAIL_UNCONFIMRED
							]);
							$model->details()->save($detail);
						}
					}
				}
			}
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
			$model = ImportProduct::find($id);
			if ($model === null) {
				$result['errors'][] = 'ID nhập hàng: ' . $id . ' không tồn tại';
				$result['success'] = false;
				continue;
			}
			$model->delete();
		}

		return $result;
	}

	public function getImportStaffOptions($id){
		$model = ImportProduct::find($id);
		$permission = Permission::where('alias', 'product_importer')->first();
		$roles = $permission->roles;
		$staffs = [];
		foreach ($roles as $role) {
			$users = $role->users;
			foreach ($users as $user) {
				$staffs[] = $user;
			}
		}
		if ($model && $model->import_staff_id) {
			$result = make_option($staffs, $model->import_staff_id, "full_name");
		}else{
			$result = make_option($staffs, 0, "full_name");
		}
		
		return $result;
	}

	public function getWarehouseOptions($id){
		$model = ImportProduct::find($id);
		$warehouses = Warehouse::select(['id', 'name'])->get();
		if ($model && $model->warehouse) {
			$result = make_option($warehouses, $model->warehouse->id);
		}else{
			$result = make_option($warehouses);
		}
		
		return $result;
	}

	public function getDetails($id){
		$model = ImportProduct::find($id);
		foreach ($model->details as $detail) {
			$detail->productDetail;
			$detail->productDetail->color;
			$detail->productDetail->size;
		}
		$return = [];
		foreach ($model->details as $key => $value) {
			$return[] = [
				'id' => $value->id,
				'color_code' => [
					'id' => ($value->productDetail->color) ? $value->productDetail->color->id : 0,
					'name'	=>	($value->productDetail->color) ? $value->productDetail->color->name : ""
				],
				'size' => [
					'id' => ($value->productDetail->size) ? $value->productDetail->size->id : 0,
					'name'	=>	($value->productDetail->size) ? $value->productDetail->size->name : ""
				],
				'quantity' => ($value->quantity) ? $value->quantity : 0
			];
		}
		return $return;
	}
}