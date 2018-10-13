<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 10/06/2018
 * Time: 12:04 AM
 */

namespace App\Repositories;

use App\Models\Category;
use App\Models\ImportProduct;
use App\Models\ImportProductDetail;
use App\Models\Permission;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\Supplier;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;
use App\Libraries\Photo;
use Illuminate\Support\Facades\Storage;
use Response;
use DNS1D;
use Carbon\Carbon;

Class ImportProductRepository
{
	const CACHE_NAME_PRODUCTS = 'import_products';

	public function dataTable($request){
		$importProducts = ImportProduct::select(['id', 'code', 'name', 'supplier_id', 'product_id', 'quantity', 'total_price', 'status', 'created_at'])
		->where('status', IMPORT_IMPORTING);

		$dataTable = DataTables::eloquent($importProducts)
		->filter(function ($query) use ($request) {
			if (trim($request->get('status')) !== "") {
				$query->where('status', $request->get('status'));
			}

			if (trim($request->get('code')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('code', 'like', '%' . $request->get('code') . '%');
				});
			}

			if (trim($request->get('start_date')) !== "") {
				$fromDate = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('start_date') . ' 00:00:00')->toDateTimeString();

				if (trim($request->get('end_date')) !== "") {

					$toDate = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('end_date') . ' 23:59:59')->toDateTimeString();
					$query->whereBetween('created_at', [$fromDate, $toDate]);
				} else {
					$query->whereDate('created_at', '>=', $fromDate);
				}
			}
		}, true)
		->addColumn('action', function ($importProduct) {
			$html = '';
			switch ($importProduct->status) {
				case IMPORT_IMPORTING:
				$html .= '<a href="' . route('admin.import_products.view', ['id' => $importProduct->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"> Sửa</a>';
				break;
				
				default:
				break;
			}
			if ($importProduct->status != IMPORT_COMPLETED) {
				$html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $importProduct->id . '" data-name="' . $importProduct->code . '"><i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';
			}
			return $html;
		})
		->addColumn('status', function ($importProduct) {
			$html = '<span class="label label-'.IMPORT_LABEL[$importProduct->status].'">'.IMPORT_TEXT[$importProduct->status].'</span>';
			return $html;
		})
		->addColumn('product_name', function ($importProduct) {
			$html = $importProduct->name;
			return $html;
		})
		->addColumn('supplier_name', function ($importProduct) {
			if ($importProduct->supplier) {
				$html = $importProduct->supplier->name;
				return $html;
			}
			return null;
		})
		->addColumn('product_category', function ($importProduct) {
			$category =  $this->lowestLevelCategory($importProduct->id);
			return ($category) ? $category->name : "";
		})
		->rawColumns(['action', 'product_name', 'supplier_name', 'product_category', 'status'])
		->toJson();

		return $dataTable;
	}

	public function dataTableReceive($request){
		$importProducts = ImportProduct::select(['id', 'code', 'name', 'supplier_id', 'product_id', 'quantity', 'total_price', 'status', 'created_at'])
		->where('status', '<>', IMPORT_IMPORTING);

		$dataTable = DataTables::eloquent($importProducts)
		->filter(function ($query) use ($request) {
			if (trim($request->get('status')) !== "") {
				$query->where('status', $request->get('status'));
			}

			if (trim($request->get('code')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('code', 'like', '%' . $request->get('code') . '%');
				});
			}

			if (trim($request->get('start_date')) !== "") {
				$fromDate = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('start_date') . ' 00:00:00')->toDateTimeString();

				if (trim($request->get('end_date')) !== "") {

					$toDate = Carbon::createFromFormat('d/m/Y H:i:s', $request->get('end_date') . ' 23:59:59')->toDateTimeString();
					$query->whereBetween('created_at', [$fromDate, $toDate]);
				} else {
					$query->whereDate('created_at', '>=', $fromDate);
				}
			}
		}, true)
		->addColumn('action', function ($importProduct) {
			$html = '';
			switch ($importProduct->status) {
				case IMPORT_IMPORTED:
				$html .= '<a href="' . route('admin.import_products.check', ['id' => $importProduct->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"> Kiểm hàng</a>';
				break;

				case IMPORT_CHECKED:
				case IMPORT_COMPLETING:
				// $html .= '<a href="#" class="btn btn-xs btn-success bt-importwarehouse" style="margin-right: 5px" data-id="' . $importProduct->id . '" data-name="' . $importProduct->code . '"> Nhập kho</a>';
				$html .= '<a href="' . route('admin.import_products.import', ['id' => $importProduct->id]) . '" class="btn btn-xs btn-success" style="margin-right: 5px"> Nhập kho</a>';
				break;
				
				default:
				break;
			}
			if ($importProduct->status != IMPORT_COMPLETED) {
				$html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $importProduct->id . '" data-name="' . $importProduct->code . '"><i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';
			}
			return $html;
		})
		->addColumn('status', function ($importProduct) {
			$html = '<span class="label label-'.IMPORT_LABEL[$importProduct->status].'">'.IMPORT_TEXT[$importProduct->status].'</span>';
			return $html;
		})
		->addColumn('product_name', function ($importProduct) {
			$html = $importProduct->name;
			return $html;
		})
		->addColumn('supplier_name', function ($importProduct) {
			if ($importProduct->supplier) {
				$html = $importProduct->supplier->name;
				return $html;
			}
			return null;
		})
		->addColumn('product_category', function ($importProduct) {
			$category =  $this->lowestLevelCategory($importProduct->id);
			return ($category) ? $category->name : "";
		})
		->rawColumns(['action', 'product_name', 'supplier_name', 'product_category', 'status'])
		->toJson();

		return $dataTable;
	}

	public function get($id)
	{
		$data = ImportProduct::find($id);
		return $data;
	}

	public function createOrUpdate($data, $id = null, $import_complete = false)
	{
		if ($id) {
			$model = ImportProduct::find($id);
		} else {
			$model = new ImportProduct;
		}

		$model->name = $data['name'];

		if ($data['product_option'] == 'old') {
			$model->product_id = $data['product_id'];
			$product = Product::find($data['product_id']);
			$model->name = $product->name;
		}
		
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
		if ($import_complete) {
			$model->status = IMPORT_IMPORTED;
		}
		
		$model->supplier_id = $data['supplier_id'];
		$model->brand_id = $data['brand_id'];
		$model->sell_price = preg_replace('/[^0-9]/', '', $data['sell_price']);
		$model->description = $data['description'];
		$model->content = $data['content'];
		$model->active = INACTIVE;
		$model->order = $data['order'];

		if(isset($data['photo'])) {
			if ($model->photo) {
				Storage::delete($model->photo);
			}
			$upload = new Photo($data['photo']);
			$model->photo = $upload->uploadTo('products');
		}

		$model->save();

		if (!$id) {
			$model->code = general_code('N H', $model->id, 6);
		}

		if (isset($data['categories'])) {
			$model->category_ids = $data['categories'];

			// Generate product code based on category code
			$category = $this->lowestLevelCategory($model->id, $data['categories']);
			$model->main_cate = $category->id;
			$old_barcode_text = $model->barcode_text;
			$model->barcode_text = general_product_code($category->code, $model->id, 7);

			if ($model->barcode) {
				if ($old_barcode_text != $model->barcode_text) {
					Storage::delete($model->barcode);
				}
			}
			Storage::disk('public')->put('barcodes/'.$model->barcode_text.'.png', base64_decode(DNS1D::getBarcodePNG($model->barcode_text, 'C128',2,33)));
			$model->barcode = 'public/barcodes/'.$model->barcode_text.'.png';
			
			$model->save();
		}else{
			$old_barcode_text = $model->barcode_text;
			$model->barcode_text = general_product_code('SP', $model->id, 7);

			if ($model->barcode) {
				if ($old_barcode_text != $model->barcode_text) {
					Storage::delete($model->barcode);
				}
			}
			Storage::disk('public')->put('barcodes/'.$model->barcode_text.'.png', base64_decode(DNS1D::getBarcodePNG($model->barcode_text, 'C128',2,33)));
			$model->barcode = 'public/barcodes/'.$model->barcode_text.'.png';
			
			$model->save();
		}

		$model->save();

		if (isset($data['importDetails'])) {
			$importDetails = json_decode($data['importDetails']);

			$sizes = [];
			$colors = [];

			foreach ($importDetails as $detail) {
				if (isset($detail->id)) {
					$detailModel = ImportProductDetail::find($detail->id);

					if ($detailModel) {
						if (isset($detail->delete) && $detail->delete == true) {
							$detailModel->delete();
						}
					}
				}
			}

			foreach ($importDetails as $key => $detail) {
				if (!isset($detail->delete) || $detail->delete != true) {
					if (isset($detail->id)) {
						$detailModel = ImportProductDetail::find($detail->id);
					}else{
						$detailModel = ImportProductDetail::where("import_product_id", $model->id)
						->where("color_id", $detail->color_code->id)
						->where("size_id", $detail->size->id)
						->first();
					}

					if (!$detailModel) {
						$detailModel = new ImportProductDetail([
							'import_product_id' => $model->id,
							'color_id' => (isset($detail->color_code)) ? $detail->color_code->id : 0,
							'size_id' => (isset($detail->size)) ? $detail->size->id : 0,
							'quantity' => $detail->quantity,
							'status'	=>	IMPORT_DETAIL_UNCONFIMRED
						]);
						$model->details()->save($detailModel);
					}else{
						$detailModel->color_id = (isset($detail->color_code)) ? $detail->color_code->id : 0;
						$detailModel->size_id = (isset($detail->size)) ? $detail->size->id : 0;
						$detailModel->quantity = $detail->quantity;
						$detailModel->save();
					}

					if ($detail->size && !in_array($detail->size->name, $sizes)) {
						$sizes[] = $detail->size->name;
					}

					if ($detail->color_code && !in_array($detail->color_code->name, $colors)) {
						$colors[] = $detail->color_code->name;
					}
				}
			}

			$model->colors = implode($colors, ',');
			$model->sizes = implode($sizes, ',');
			$model->save();
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
			if ($model->details) {
				foreach ($model->details as $detail) {
					$detail->delete();
				}
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

	public function getSupplierOptions($id){
		$model = ImportProduct::find($id);
		$suppliers = Supplier::where('active', 1)->get();

		if ($model && $model->supplier_id) {
			$result = make_option($suppliers, $model->supplier_id);
		}else{
			$result = make_option($suppliers, 0);
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

	public function getCheckImport($id){
		$data = ImportProduct::find($id);
		return $data;
	}

	public function confirmDetail($id, $quantity)
	{
		$result = [
			'success' => true,
			'errors' => []
		];
		$model = ImportProductDetail::find($id);
		if ($model === null) {
			$result['errors'][] = 'ID nhập hàng chi tiết: ' . $id . ' không tồn tại';
			$result['success'] = false;
		}
		$importProduct = ImportProduct::find($model->import_product_id);
		$changeQuantity = $model->quantity - $quantity;

		$importProduct->quantity -= $changeQuantity;
		$importProduct->total_price = $importProduct->quantity * $importProduct->price;
		$importProduct->save();
		$model->quantity -= $changeQuantity;
		$model->status = IMPORT_DETAIL_CONFIMRED;
		$model->save();

		$result['all_confirmed'] = 'false';

		if ($this->areAllDetailsConfirmed($model->importProduct->id)) {
			$result['all_confirmed'] = 'true';
		}

		return $result;
	}

	public function confirmImportDetail($id, $quantity)
	{
		$result = [
			'success' => true,
			'errors' => []
		];
		$importProductDetail = ImportProductDetail::find($id);
		if ($importProductDetail === null) {
			$result['errors'][] = 'ID nhập hàng chi tiết: ' . $id . ' không tồn tại';
			$result['success'] = false;
		}

		$importProduct = ImportProduct::find($importProductDetail->import_product_id);
		$model = Product::find($importProduct->product_id);
		
		$changeQuantity = $importProductDetail->quantity - $quantity;

		$importProduct->quantity -= $changeQuantity;
		$importProduct->total_price = $importProduct->quantity * $importProduct->price;
		$importProduct->save();
		$importProductDetail->quantity -= $changeQuantity;
		$importProductDetail->save();

		// Add to product & product detail
		$productDetail = ProductDetail::where('product_id', $model->id)
		->where('color_id', $importProductDetail->color_id)
		->where('size_id', $importProductDetail->size_id)
		->first();

		if (!$productDetail) {
			$productDetail = new ProductDetail([
				'color_id' => ($importProductDetail->color) ? $importProductDetail->color->id : 0,
				'size_id' => ($importProductDetail->size) ? $importProductDetail->size->id : 0,
				'quantity' => 0,
				'quantity_available' => 0
			]);
			$model->details()->save($productDetail);
		}

		$warehouseProduct = WarehouseProduct::where('warehouse_id', $importProduct->warehouse_id)
		->where('product_id', $importProduct->product_id)
		->where('product_detail_id', $productDetail->id)
		->first();

		if ($warehouseProduct) {
			$warehouseProduct->quantity += $importProductDetail->quantity;
			$warehouseProduct->quantity_available += $importProductDetail->quantity;
		}else{
			$warehouseProduct = new WarehouseProduct;
			$warehouseProduct->warehouse_id = $importProduct->warehouse_id;
			$warehouseProduct->product_id = $model->id;
			$warehouseProduct->product_detail_id = $productDetail->id;
			$warehouseProduct->quantity = $importProductDetail->quantity;
			$warehouseProduct->quantity_available = $importProductDetail->quantity;
		}
		$warehouseProduct->save();

		$productDetail->quantity += $importProductDetail->quantity;
		$productDetail->quantity_available += $importProductDetail->quantity;
		$productDetail->save();
		$model->quantity += $importProductDetail->quantity;
		$model->quantity_available += $importProductDetail->quantity;
		$model->save();

		$importProductDetail->status = IMPORT_DETAIL_IMPORTED;
		$importProductDetail->save();

		$result['all_imported'] = 'false';

		if ($this->areAllDetailsImported($importProductDetail->importProduct->id)) {
			$result['all_imported'] = 'true';
		}

		return $result;
	}

	public function areAllDetailsConfirmed($id){
		$model = ImportProduct::find($id);
		if ($model) {
			if($model->details){
				foreach ($model->details as $detail) {
					if($detail->status == IMPORT_DETAIL_UNCONFIMRED){
						return false;
					}
				}
				return true;
			}
		}
		return false;
	}

	public function areAllDetailsImported($id){
		$model = ImportProduct::find($id);
		if ($model) {
			if($model->details){
				foreach ($model->details as $detail) {
					if($detail->status == IMPORT_DETAIL_CONFIMRED){
						return false;
					}
				}
				return true;
			}
		}
		return false;
	}

	public function importWarehouse($id)
	{
		$result = [
			'success' => true,
			'errors' => [],
			'id' => $id
		];

		$productRepository = new ProductRepository;
		$importProduct = ImportProduct::find($id);
		if ($importProduct->product_id) {
			$model = Product::find($importProduct->product_id);
		}else{
			$model = new Product;
		}

		$model->supplier_id = $importProduct->supplier_id;
		$model->brand_id = $importProduct->brand_id;
		$model->barcode_text = $importProduct->barcode_text;
		$model->barcode = $importProduct->barcode;
		$model->name = $importProduct->name;
		$model->colors = $importProduct->colors;
		$model->sizes = $importProduct->sizes;
		$model->price = $importProduct->price;
		$model->sell_price = $importProduct->sell_price;
		$model->photo = $importProduct->photo;
		$model->description = $importProduct->description;
		$model->content = $importProduct->content;
		$model->active = $importProduct->active;
		$model->order = $importProduct->order;
		$model->main_cate = $importProduct->main_cate;
		$model->category_ids = $importProduct->category_ids;

		$model->save();
		$data['categories'] = $importProduct->category_ids;

		if (isset($data['categories'])) {
			$productRepository->addCategories($model->id, $data['categories']);

			// Generate product code based on category code
			$category = $productRepository->lowestLevelCategory($model->id);
			$model->main_cate = $category->id;
			$old_barcode_text = $model->barcode_text;
			$model->barcode_text = general_product_code($category->code, $model->id, 7);

			if ($model->barcode) {
				if ($old_barcode_text != $model->barcode_text) {
					Storage::delete($model->barcode);
				}
			}
			Storage::disk('public')->put('barcodes/'.$model->barcode_text.'.png', base64_decode(DNS1D::getBarcodePNG($model->barcode_text, 'C128',2,33)));
			$model->barcode = 'public/barcodes/'.$model->barcode_text.'.png';
			
			$model->save();
		}else{
			$old_barcode_text = $model->barcode_text;
			$model->barcode_text = general_product_code('SP', $model->id, 7);

			if ($model->barcode) {
				if ($old_barcode_text != $model->barcode_text) {
					Storage::delete($model->barcode);
				}
			}
			Storage::disk('public')->put('barcodes/'.$model->barcode_text.'.png', base64_decode(DNS1D::getBarcodePNG($model->barcode_text, 'C128',2,33)));
			$model->barcode = 'public/barcodes/'.$model->barcode_text.'.png';
			
			$model->save();
		}

		// // Push details quantity to warehouse product detail & product detail, update product quantity
		// if ($importProduct->details) {
		// 	foreach ($importProduct->details as $importProductDetail) {
		// 		// Add to product & product detail
		// 		$productDetail = ProductDetail::where('product_id', $model->id)
		// 		->where('color_id', $importProductDetail->color_id)
		// 		->where('size_id', $importProductDetail->size_id)
		// 		->first();

		// 		if (!$productDetail) {
		// 			$productDetail = new ProductDetail([
		// 				'color_id' => ($importProductDetail->color) ? $importProductDetail->color->id : 0,
		// 				'size_id' => ($importProductDetail->size) ? $importProductDetail->size->id : 0,
		// 				'quantity' => 0,
		// 				'quantity_available' => 0
		// 			]);
		// 			$model->details()->save($productDetail);
		// 		}




		// 		$warehouseProduct = WarehouseProduct::where('warehouse_id', $importProduct->warehouse_id)
		// 		->where('product_id', $importProduct->product_id)
		// 		->where('product_detail_id', $productDetail->id)
		// 		->first();

		// 		if ($warehouseProduct) {
		// 			$warehouseProduct->quantity += $importProductDetail->quantity;
		// 			$warehouseProduct->quantity_available += $importProductDetail->quantity;
		// 		}else{
		// 			$warehouseProduct = new WarehouseProduct;
		// 			$warehouseProduct->warehouse_id = $importProduct->warehouse_id;
		// 			$warehouseProduct->product_id = $model->id;
		// 			$warehouseProduct->product_detail_id = $productDetail->id;
		// 			$warehouseProduct->quantity = $importProductDetail->quantity;
		// 			$warehouseProduct->quantity_available = $importProductDetail->quantity;
		// 		}
		// 		$warehouseProduct->save();

		// 		$productDetail->quantity += $importProductDetail->quantity;
		// 		$productDetail->quantity_available += $importProductDetail->quantity;
		// 		$productDetail->save();
		// 		$model->quantity += $importProductDetail->quantity;
		// 		$model->quantity_available += $importProductDetail->quantity;
		// 		$model->save();
		// 	}
		// }

		$importProduct->product_id = $model->id;
		$importProduct->status = IMPORT_COMPLETING;
		$importProduct->save();

		return $result;
	}

	public function getStaticDataTableObj($request)
	{
		$builder = ImportProduct::selectRaw('import_products.price, import_products.created_at, import_products.main_cate, warehouses.name as warehouse_name, import_products.name as product_name, import_products.barcode_text as product_code, SUM(import_products.quantity) as quantity, suppliers.name as supplier_name')
		->join('warehouses', 'warehouses.id', '=', 'import_products.warehouse_id')
		->leftJoin('suppliers', 'suppliers.id', '=', 'import_products.supplier_id')
		->where('import_products.status', IMPORT_COMPLETED)
		->groupBy('import_products.product_id');

		$categories = Category::get()->pluck('name', 'id')->toArray();
		$dataTable = DataTables::eloquent($builder)
		->filter(function ($query) use ($request) {
			if (trim($request->get('status')) !== "") {
				$query->where('active', $request->get('status'));
			}

			if (trim($request->get('category_id')) !== "") {
				$query->join('product_category', 'products.id', '=', 'product_category.product_id')
				->where('product_category.category_id', $request->get('category_id'));
			}

			if (trim($request->get('warehouse_id')) !== "") {
				$query->where('warehouse_product.warehouse_id', $request->get('warehouse_id'));
			}
			if (trim($request->get('date_from')) !== "") {
				$dateFrom = \DateTime::createFromFormat('d/m/Y', $request->get('date_from'));
				$dateFrom = $dateFrom->format('Y-m-d 00:00:00');
				$query->where('import_products.created_at', '>=', $dateFrom);
			}

			if (trim($request->get('date_to')) !== "") {
				$dateTo = \DateTime::createFromFormat('d/m/Y', $request->get('date_to'));
				$dateTo = $dateTo->format('Y-m-d 23:59:50');
				$query->where('import_products.created_at', '<=', $dateTo);
			}
			if (trim($request->get('keyword')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('import_products.name', 'like', '%' . $request->get('keyword') . '%')
					->orWhere('import_products.barcode_text', 'like', '%' . $request->get('keyword') . '%');
				});

			}
		}, true)
		->addColumn('category', function($product) use ($categories) {
			return $categories[$product->main_cate] ?? '';
		})
		->addColumn('quantity', function($product) {
			return format_number($product->quantity);
		})
		->addColumn('total_price', function($product) {
			return format_number($product->quantity * $product->price);
		});

		return $dataTable;
	}

	public function getStaticDataTable($request)
	{
		$data = $this->getStaticDataTableObj($request)
		->rawColumns(['category', 'quantity'])
		->toJson();

		return $data;
	}

	public function idCategories($id){
		$model = ImportProduct::find($id);
		return list_ids(Category::whereIn('id', explode(',', $model->category_ids))->get());
	}

	public function lowestLevelCategory($id, $categories = null){
		$model = ImportProduct::find($id);
		if ($categories == null) {
			$categories = $model->category_ids;
		}
		$category = Category::whereIn('id', explode(',', $categories))->orderBy('level', 'desc')->first();
		return $category;
	}

	public function checkCompleted($id){
		if ($this->areAllDetailsConfirmed($id)) {
			$model = ImportProduct::find($id);
			$model->status = IMPORT_CHECKED;
			$model->save();

			return true;
		}
		return false;
	}

	public function checkImportCompleted($id){
		if ($this->areAllDetailsImported($id)) {
			$model = ImportProduct::find($id);
			$model->status = IMPORT_COMPLETED;
			$model->save();

			return true;
		}
		return false;
	}

	public function getPrintDatas($id){
		$result = [
			'success' => true
		];
		$model = ImportProduct::find($id);
		if ($model) {
			$model->staff;
			$model->details;
			$model->supplier;
			// $model->product;
			if ($model->details) {
				foreach ($model->details as $detail) {
					$detail->color;
					$detail->size;
				}
			}
		}
		$result['import_product'] = $model;
		return $result;
	}
}