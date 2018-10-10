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
			switch ($importProduct->status) {
				case '1':
				$html .= '<a href="' . route('admin.import_products.check', ['id' => $importProduct->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"> Kiểm hàng</a>';
				break;

				case '2':
				$html .= '<a href="#" class="btn btn-xs btn-success bt-importwarehouse" style="margin-right: 5px" data-id="' . $importProduct->id . '" data-name="' . $importProduct->code . '"> Nhập kho</a>';
				break;
				
				default:
				break;
			}
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
		if ($data['product_option'] == 'new') {
			$product_model = $productRepository->createOrUpdate($data);
		}else{
			$product_model = $productRepository->getProduct($data['product_id']);
		}
		
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
		// $model->supplier_id = $data['supplier_id'];
		$model->brand_id = $data['brand_id'];
		$model->sell_price = preg_replace('/[^0-9]/', '', $data['sell_price']);
		$model->description = $data['description'];
		$model->content = $data['content'];
		$model->active = $data['active'];
		$model->order = $data['order'];

		if($data['product_option'] == 'old' && isset($data['photo'])) {
			if ($model->photo) {
				Storage::delete($model->photo);
			}
			$upload = new Photo($data['photo']);
			$model->photo = $upload->uploadTo('products');
		}

		$model->save();
		if (!$id) {
			$model->code = general_code('N H', $model->id, 6);
			$model->save();
		}


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
				$sizes = [];
				$colors = [];
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

				if ($importDetail->size && !in_array($importDetail->size->name, $sizes)) {
					$sizes[] = $importDetail->size->name;
				}

				if ($importDetail->color_code && !in_array($importDetail->color_code->name, $colors)) {
					$colors[] = $importDetail->color_code->name;
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

	public function getCheckImport($id){
		$data = ImportProduct::find($id);
		return $data;
	}

	public function confirmDetail($id)
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
		$model->status = IMPORT_DETAIL_CONFIMRED;
		$model->save();

		if ($this->areAllDetailsConfirmed($model->importProduct->id)) {
			$modelImportProduct = ImportProduct::find($model->importProduct->id);
			$modelImportProduct->status = IMPORT_CHECKED;
			$modelImportProduct->save();
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

	public function importWarehouse($id)
	{
		$result = [
			'success' => true,
			'errors' => [],
			'id' => $id
		];

		$importProduct = ImportProduct::find($id);
		$product = Product::find($importProduct->product_id);

		// Push data from import product to product
		$product->price = $importProduct->price;
		$product->supplier_id = $importProduct->supplier_id;
		$product->brand_id = $importProduct->brand_id;
		$product->sell_price = $importProduct->sell_price;
		$product->photo = $importProduct->photo;
		$product->description = $importProduct->description;
		$product->content = $importProduct->content;
		$product->sizes = implode(',', array_unique(array_merge(explode(',', $importProduct->sizes), explode(',', $product->sizes))));
		$product->colors = implode(',', array_unique(array_merge(explode(',', $importProduct->colors), explode(',', $product->colors))));
		$product->save();

		// Push details quantity to warehouse product detail & product detail, update product quantity
		if ($importProduct->details) {
			foreach ($importProduct->details as $importProductDetail) {
				$warehouseProduct = WarehouseProduct::where('warehouse_id', $importProduct->warehouse_id)
				->where('product_id', $importProduct->product_id)
				->where('product_detail_id', $importProductDetail->product_detail_id)
				->first();

				if ($warehouseProduct) {
					$warehouseProduct->quantity += $importProductDetail->quantity;
				}else{
					$warehouseProduct = new WarehouseProduct;
					$warehouseProduct->warehouse_id = $importProduct->warehouse_id;
					$warehouseProduct->product_id = $importProduct->product_id;
					$warehouseProduct->product_detail_id = $importProductDetail->product_detail_id;
					$warehouseProduct->quantity = $importProductDetail->quantity;
				}
				$warehouseProduct->save();

				if ($productDetail = ProductDetail::find($importProductDetail->product_detail_id)) {
					$productDetail->quantity += $importProductDetail->quantity;
					$productDetail->save();
					$product->quantity += $importProductDetail->quantity;
					$product->quantity_available += $importProductDetail->quantity;
					$product->save();
				}
			}
		}

		$importProduct->status = IMPORT_IMPORTED;
		$importProduct->save();

		return $result;
	}

	public function getStaticDataTableObj($request)
    {
        $builder = ImportProduct::selectRaw('import_products.price, import_products.created_at, products.main_cate, warehouses.name as warehouse_name, products.name as product_name, products.barcode_text as product_code, SUM(import_products.quantity) as quantity, suppliers.name as supplier_name')
            ->join('warehouses', 'warehouses.id', '=', 'import_products.warehouse_id')
            ->join('products', 'products.id', '=', 'import_products.product_id')
            ->join('suppliers', 'suppliers.id', '=', 'products.supplier_id')
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
                        $sub->where('products.name', 'like', '%' . $request->get('keyword') . '%')
                            ->orWhere('products.barcode_text', 'like', '%' . $request->get('keyword') . '%');
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
}