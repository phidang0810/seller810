<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 10/06/2018
 * Time: 12:04 AM
 */

namespace App\Repositories;

use App\Models\ReturnProduct;
use App\Models\ReturnProductDetail;
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
use Carbon\Carbon;

Class ReturnProductRepository
{
	const CACHE_NAME_PRODUCTS = 'return_product';

	public function dataTable($request){
		$returnProduct = ReturnProduct::select(['id', 'code', 'return_staff_id', 'return_date', 'status', 'quantity', 'created_at']);

		$dataTable = DataTables::eloquent($returnProduct)
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
                    $query->whereBetween('return_date', [$fromDate, $toDate]);
                } else {
                    $query->whereDate('return_date', '>=', $fromDate);
                }
            }
		}, true)
		->addColumn('action', function ($returnProduct) {
			$html = '';
			switch ($returnProduct->status) {
				case RETURN_RETURNING:
				$html .= '<a href="#" class="btn btn-xs btn-primary bt-returned" style="margin-right: 5px" data-id="' . $returnProduct->id . '" data-name="' . $returnProduct->code . '"> '.RETURN_ACTION_TEXT[RETURN_RETURNING].'</a>';
				break;
				
				default:
				break;
			}
			return $html;
		})
		->addColumn('staff_name', function ($returnProduct) {
			$staff = $returnProduct->staff;
			$html = $staff->full_name;
			return $html;
		})
		->addColumn('status', function ($returnProduct) {
			$html = '<span class="label label-'.RETURN_LABEL[$returnProduct->status].'">'.RETURN_TEXT[$returnProduct->status].'</span>';
			return $html;
		})
		->rawColumns(['action', 'staff_name', 'status'])
		->toJson();

		return $dataTable;
	}

	public function get($id)
	{
		$data = ReturnProduct::find($id);
		return $data;
	}

	public function createOrUpdate($data, $id = null)
	{
		if ($id) {
			$model = ReturnProduct::find($id);
		} else {
			$model = new ReturnProduct;
		}
		
		$model->code = general_code('Trả hàng', $id, 6);
		$model->return_staff_id = $data['return_staff_id'];
		$model->reason = $data['reason'];
		$model->return_date = date('Y-m-d H:i:s', strtotime($data['return_date']));
		$model->status = RETURN_RETURNING;
		$model->quantity = 0;

		$model->save();
		if (!$id) {
			$model->code = general_code('Trả hàng', $model->id, 6);
			$model->save();
		}

		if (isset($data['return_details'])) {
			$return_details = json_decode($data['return_details']);

			// Delete import detail if delete
			foreach ($return_details as $returnDetail) {
				if (isset($returnDetail->id)) {
					$returnDetailModel = ReturnProductDetail::find($returnDetail->id);
					$productModel = Product::find($returnDetail->product_name->id);
					$productDetailModel = ProductDetail::find($returnDetail->product_detail->id);
					$warehouseProduct = WarehouseProduct::where('warehouse_id', $returnDetail->warehouse->id)
					->where('product_id', $returnDetail->product_name->id)
					->where("product_detail_id", $returnDetail->product_detail->id)
					->first();

					$changeQuantity = $returnDetailModel->quantity;

					if ($returnDetailModel) {
						if (isset($returnDetail->delete) && $returnDetail->delete == true) {
							$returnDetailModel->delete();

							// Add product quantity, product detail quantity, warehouse product quantity
							$productModel->quantity += $changeQuantity;
							$productModel->quantity_available += $changeQuantity;
							$productModel->save();
							$productDetailModel->quantity += $changeQuantity;
							$productDetailModel->quantity_available += $changeQuantity;
							$productDetailModel->save();
							$warehouseProduct->quantity += $changeQuantity;
							$warehouseProduct->quantity_available += $changeQuantity;
							$warehouseProduct->save();
						}
					}
				}
			}

			// Add/update import detail
			// Update quantity for product quantity, product detail quantity, warehouse product quantity
			foreach ($return_details as $key => $returnDetail) {
				if (!isset($returnDetail->delete) || $returnDetail->delete != true) {
					$returnDetailModel = ReturnProductDetail::where("return_product_id", $model->id)
					->where("warehouse_id", $returnDetail->warehouse->id)
					->where("product_id", $returnDetail->product_name->id)
					->where("product_detail_id", $returnDetail->product_detail->id)
					->first();

					$productModel = Product::find($returnDetail->product_name->id);
					$productDetailModel = ProductDetail::find($returnDetail->product_detail->id);
					$warehouseProduct = WarehouseProduct::where('warehouse_id', $returnDetail->warehouse->id)
					->where('product_id', $returnDetail->product_name->id)
					->where("product_detail_id", $returnDetail->product_detail->id)
					->first();

					if (!$returnDetailModel) {
						$returnDetailModel = new ReturnProductDetail([
							'warehouse_id' => (isset($returnDetail->warehouse)) ? $returnDetail->warehouse->id : 0,
							'product_id' => (isset($returnDetail->product_name)) ? $returnDetail->product_name->id : 0,
							'product_detail_id' => (isset($returnDetail->product_detail)) ? $returnDetail->product_detail->id : 0,
							'quantity' => $returnDetail->product_quantity
							// 'status'	=>	TRANSPORT_DETAIL_UNRECEIVE
						]);
						$model->details()->save($returnDetailModel);
						$productModel->quantity -= $returnDetail->product_quantity;
						$productModel->quantity_available -= $returnDetail->product_quantity;
						$productModel->save();
						$productDetailModel->quantity -= $returnDetail->product_quantity;
						$productDetailModel->quantity_available -= $returnDetail->product_quantity;
						$productDetailModel->save();
						$warehouseProduct->quantity -= $returnDetail->product_quantity;
						$warehouseProduct->quantity_available -= $returnDetail->product_quantity;
						$warehouseProduct->save();
						$model->quantity += $returnDetail->product_quantity;
						$model->save();
					}

					$changeQuantity = $returnDetail->product_quantity - $returnDetailModel->quantity;

					if ($returnDetailModel) {
						$returnDetailModel->quantity = $returnDetail->product_quantity;
						$returnDetailModel->save();
						$productModel->quantity -= $changeQuantity;
						$productModel->quantity_available -= $changeQuantity;
						$productModel->save();
						$productDetailModel->quantity -= $changeQuantity;
						$productDetailModel->quantity_available -= $changeQuantity;
						$productDetailModel->save();
						$warehouseProduct->quantity -= $changeQuantity;
						$warehouseProduct->quantity_available -= $changeQuantity;
						$warehouseProduct->save();
						$model->quantity += $changeQuantity;
						$model->save();
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
			$model = ReturnProduct::find($id);
			if ($model === null) {
				$result['errors'][] = 'ID nhập hàng: ' . $id . ' không tồn tại';
				$result['success'] = false;
				continue;
			}
			$model->delete();
		}

		return $result;
	}

	public function getReturnStaffOptions($id){
		$model = ReturnProduct::find($id);
		$permission = Permission::where('alias', 'product_returner')->first();
		$roles = $permission->roles;
		$staffs = [];
		foreach ($roles as $role) {
			$users = $role->users;
			foreach ($users as $user) {
				$staffs[] = $user;
			}
		}
		if ($model && $model->return_staff_id) {
			$result = make_option($staffs, $model->return_staff_id, "full_name");
		}else{
			$result = make_option($staffs, 0, "full_name");
		}
		
		return $result;
	}

	public function getWarehouseOptions($id){
		$model = ReturnProduct::find($id);
		$warehouses = Warehouse::select(['id', 'name'])->get();
		if ($model && $model->warehouse) {
			$result = make_option($warehouses, $model->warehouse->id);
		}else{
			$result = make_option($warehouses);
		}
		
		return $result;
	}

	public function getDetails($id){
		$model = ReturnProduct::find($id);
		foreach ($model->details as $detail) {
			$detail->product;
			$detail->productDetail;
			$detail->warehouse;
		}
		$return = [];
		foreach ($model->details as $key => $value) {
			$return[] = [
				'id' => $value->id,
				'product_image' => ($value->product->photo) ? asset('storage/' . $value->product->photo) : asset(NO_PHOTO),
				'product_name' => [
					'id' => ($value->product->id) ? $value->product->id : 0,
					'name' => ($value->product->name) ? $value->product->name : ''
				],
				'product_detail' => ($value->productDetail) ? $value->productDetail : [],
				'product_quantity' => ($value->quantity) ? $value->quantity : 0,
				'product_size' => [
					'id' => ($value->productDetail->size) ? $value->productDetail->size->id : 0,
					'name' => ($value->productDetail->size) ? $value->productDetail->size->name : ''
				],
				'product_color' => [
					'id' => ($value->productDetail->color) ? $value->productDetail->color->id : 0,
					'name' => ($value->productDetail->color) ? $value->productDetail->color->name : ''
				],
				'warehouse' => [
					'id' => ($value->warehouse) ? $value->warehouse->id : 0,
					'name' => ($value->warehouse) ? $value->warehouse->name : ''
				]
			];
		}
		return $return;
	}

	public function returned($id){
		$result = [
			'success' => true,
			'errors' => [],
			'id' => $id
		];

		$model = ReturnProduct::find($id);
		$model->status = RETURN_RETURNED;
		$model->save();

		return $result;
	}

	public function getStatusOptions($id){
		$model = ReturnProduct::find($id);
		$staffs = [];
		$staffs[] = ["id" => RETURN_RETURNING, "name" => RETURN_TEXT[RETURN_RETURNING]];
		$staffs[] = ["id" => RETURN_RETURNED, "name" => RETURN_TEXT[RETURN_RETURNED]];
		if ($model && $model->status) {
			$result = make_option($staffs, $model->status);
		}else{
			$result = make_option($staffs);
		}
		
		return $result;
	}
}