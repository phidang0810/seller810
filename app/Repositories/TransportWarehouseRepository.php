<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 10/06/2018
 * Time: 12:04 AM
 */

namespace App\Repositories;

use App\Models\TransportWarehouse;
use App\Models\TransportWarehouseDetail;
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

Class TransportWarehouseRepository
{
	const CACHE_NAME_PRODUCTS = 'transport_warehouse';

	public function dataTable($request){
		$transportWarehouse = TransportWarehouse::select(['id', 'code', 'transport_staff_id', 'transport_date', 'status', 'created_at']);

		$dataTable = DataTables::eloquent($transportWarehouse)
		->filter(function ($query) use ($request) {
			if (trim($request->get('status')) !== "") {
				$query->where('active', $request->get('status'));
			}
		}, true)
		->addColumn('action', function ($transportWarehouse) {
			$html = '';
			$html .= '<a href="#" class="btn btn-xs btn-success bt-print" style="margin-right: 5px" data-id="' . $transportWarehouse->id . '" data-name="' . $transportWarehouse->code . '"> In</a>';
			switch ($transportWarehouse->status) {
				case TRANSPORT_TRANSPORTING:
				$html .= '<a href="' . route('admin.transport_warehouse.receive', ['id' => $transportWarehouse->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"> Nhận hàng</a>';
				break;
				
				default:
				break;
			}
			return $html;
		})
		->addColumn('staff_name', function ($transportWarehouse) {
			$staff = $transportWarehouse->staff;
			$html = $staff->full_name;
			return $html;
		})
		->addColumn('status', function ($transportWarehouse) {
			$html = TRANSPORT_TEXT[$transportWarehouse->status];
			return $html;
		})
		->rawColumns(['action', 'staff_name'])
		->toJson();

		return $dataTable;
	}

	public function get($id)
	{
		$data = TransportWarehouse::find($id);
		return $data;
	}

	public function createOrUpdate($data, $id = null)
	{
		if ($id) {
			$model = TransportWarehouse::find($id);
		} else {
			$model = new TransportWarehouse;
		}
		
		$model->code = general_code('C K', $id, 6);
		$model->transport_staff_id = $data['transport_staff_id'];
		$model->transport_date = date('Y-m-d H:i:s', strtotime($data['transport_date']));
		$model->status = TRANSPORT_TRANSPORTING;

		$model->save();
		if (!$id) {
			$model->code = general_code('C K', $model->id, 6);
			$model->save();
		}
		$data_dd = [];

		if (isset($data['transport_details'])) {
			$transport_details = json_decode($data['transport_details']);

			// Delete import detail if delete
			foreach ($transport_details as $transportDetail) {
				if (isset($transportDetail->id)) {
					$transportDetailModel = TransportWarehouseDetail::find($transportDetail->id);
					// $productModel = Product::find($transportDetail->product_name->id);
					// $productDetailModel = ProductDetail::find($transportDetail->product_detail->id);
					$warehouseProduct = WarehouseProduct::where('warehouse_id', $transportDetail->from_warehouse->id)
					->where('product_id', $transportDetail->product_name->id)
					->where("product_detail_id", $transportDetail->product_detail->id)
					->first();

					$changeQuantity = $transportDetailModel->quantity;

					if ($transportDetailModel) {
						if (isset($transportDetail->delete) && $transportDetail->delete == true) {
							$transportDetailModel->delete();

							// Add product quantity, product detail quantity, warehouse product quantity
							// $productModel->quantity_available += $changeQuantity;
							// $productModel->save();
							// $productDetailModel->quantity += $changeQuantity;
							// $productDetailModel->save();
							$warehouseProduct->quantity += $changeQuantity;
							$warehouseProduct->quantity_available += $changeQuantity;
							$warehouseProduct->save();
						}
					}
				}
			}

			// Add/update import detail
			// Update quantity for product quantity, product detail quantity, warehouse product quantity
			foreach ($transport_details as $key => $transportDetail) {
				if (!isset($transportDetail->delete) || $transportDetail->delete != true) {
					$transportDetailModel = TransportWarehouseDetail::where("transport_warehouse_id", $model->id)
					->where("from_warehouse_id", $transportDetail->from_warehouse->id)
					->where("receive_warehouse_id", $transportDetail->receive_warehouse->id)
					->where("product_id", $transportDetail->product_name->id)
					->where("product_detail_id", $transportDetail->product_detail->id)
					->first();

					// $productModel = Product::find($transportDetail->product_name->id);
					// $productDetailModel = ProductDetail::find($transportDetail->product_detail->id);
					$warehouseProduct = WarehouseProduct::where('warehouse_id', $transportDetail->from_warehouse->id)
					->where('product_id', $transportDetail->product_name->id)
					->where("product_detail_id", $transportDetail->product_detail->id)
					->first();

					if (!$transportDetailModel) {
						$transportDetailModel = new TransportWarehouseDetail([
							'from_warehouse_id' => (isset($transportDetail->from_warehouse)) ? $transportDetail->from_warehouse->id : 0,
							'receive_warehouse_id' => (isset($transportDetail->receive_warehouse)) ? $transportDetail->receive_warehouse->id : 0,
							'product_id' => (isset($transportDetail->product_name)) ? $transportDetail->product_name->id : 0,
							'product_detail_id' => (isset($transportDetail->product_detail)) ? $transportDetail->product_detail->id : 0,
							'quantity' => $transportDetail->product_quantity,
							'status'	=>	TRANSPORT_DETAIL_UNRECEIVE
						]);
						$model->details()->save($transportDetailModel);
						// $productModel->quantity_available -= $transportDetail->product_quantity;
						// $productModel->save();
						// $productDetailModel->quantity -= $transportDetail->product_quantity;
						// $productDetailModel->save();
						$warehouseProduct->quantity -= $transportDetail->product_quantity;
						$warehouseProduct->quantity_available -= $transportDetail->product_quantity;
						$warehouseProduct->save();
					}

					$changeQuantity = $transportDetail->product_quantity - $transportDetailModel->quantity;

					if ($transportDetailModel) {
						$transportDetailModel->quantity = $transportDetail->product_quantity;
						$transportDetailModel->save();
						// $productModel->quantity_available -= $changeQuantity;
						// $productModel->save();
						// $productDetailModel->quantity -= $changeQuantity;
						// $productDetailModel->save();
						$warehouseProduct->quantity -= $changeQuantity;
						$warehouseProduct->quantity_available -= $changeQuantity;
						$warehouseProduct->save();
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
			$model = TransportWarehouse::find($id);
			if ($model === null) {
				$result['errors'][] = 'ID nhập hàng: ' . $id . ' không tồn tại';
				$result['success'] = false;
				continue;
			}
			$model->delete();
		}

		return $result;
	}

	public function getTransportStaffOptions($id){
		$model = TransportWarehouse::find($id);
		$permission = Permission::where('alias', 'warehouse_transporter')->first();
		$roles = $permission->roles;
		$staffs = [];
		foreach ($roles as $role) {
			$users = $role->users;
			foreach ($users as $user) {
				$staffs[] = $user;
			}
		}
		if ($model && $model->transport_staff_id) {
			$result = make_option($staffs, $model->transport_staff_id, "full_name");
		}else{
			$result = make_option($staffs, 0, "full_name");
		}
		
		return $result;
	}

	public function getStatusOptions($id){
		$model = TransportWarehouse::find($id);
		$staffs = [];
		$staffs[] = ["id" => TRANSPORT_TRANSPORTING, "name" => TRANSPORT_TEXT[TRANSPORT_TRANSPORTING]];
		$staffs[] = ["id" => TRANSPORT_TRANSPORTED, "name" => TRANSPORT_TEXT[TRANSPORT_TRANSPORTED]];
		if ($model && $model->status) {
			$result = make_option($staffs, $model->status);
		}else{
			$result = make_option($staffs);
		}
		
		return $result;
	}

	public function getWarehouseOptions($id){
		$model = TransportWarehouse::find($id);
		$warehouses = Warehouse::select(['id', 'name'])->get();
		if ($model && $model->warehouse) {
			$result = make_option($warehouses, $model->warehouse->id);
		}else{
			$result = make_option($warehouses);
		}
		
		return $result;
	}

	public function getDetails($id){
		$model = TransportWarehouse::find($id);
		foreach ($model->details as $detail) {
			$detail->product;
			$detail->productDetail;
			$detail->fromWarehouse;
			$detail->receiveWarehouse;
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
				'from_warehouse' => [
					'id' => ($value->fromWarehouse) ? $value->fromWarehouse->id : 0,
					'name' => ($value->fromWarehouse) ? $value->fromWarehouse->name : ''
				],
				'receive_warehouse' => [
					'id' => ($value->receiveWarehouse) ? $value->receiveWarehouse->id : 0,
					'name' => ($value->receiveWarehouse) ? $value->receiveWarehouse->name : ''
				]
			];
		}
		return $return;
	}

	public function getReceiveTransport($id){
		$data = TransportWarehouse::find($id);
		return $data;
	}

	public function receiveProduct($id)
	{
		$result = [
			'success' => true,
			'errors' => []
		];
		$model = TransportWarehouseDetail::find($id);
		if ($model === null) {
			$result['errors'][] = 'ID chuyển kho chi tiết: ' . $id . ' không tồn tại';
			$result['success'] = false;
		}

		// Plus product quantity to product, product detail, product warehouse
		// $productModel = Product::find($model->product_id);
		// $productDetailModel = ProductDetail::find($model->product_detail_id);
		$warehouseProduct = WarehouseProduct::where('warehouse_id', $model->receive_warehouse_id)
		->where('product_id', $model->product_id)
		->where("product_detail_id", $model->product_detail_id)
		->first();
		if (!$warehouseProduct) {
			$warehouseProduct = new WarehouseProduct;
			$warehouseProduct->warehouse_id = $model->receive_warehouse_id;
			$warehouseProduct->product_id = $model->product_id;
			$warehouseProduct->product_detail_id = $model->product_detail_id;
			$warehouseProduct->quantity = $model->quantity;
			$warehouseProduct->quantity_available = $model->quantity;
			$warehouseProduct->save();
		}else{
			$warehouseProduct->quantity += $model->quantity;
			$warehouseProduct->quantity_available += $model->quantity;
			$warehouseProduct->save();
		}
		// $productModel->quantity_available += $model->quantity;
		// $productModel->save();
		// $productDetailModel->quantity += $model->quantity;
		// $productDetailModel->save();

		$model->status = TRANSPORT_DETAIL_RECEIVED;
		$model->save();

		if ($this->areAllDetailsReceived($model->transportWarehouse->id)) {
			$modelTransportWarehouse = TransportWarehouse::find($model->transportWarehouse->id);
			$modelTransportWarehouse->status = TRANSPORT_TRANSPORTED;
			$modelTransportWarehouse->save();
		}

		return $result;
	}

	public function areAllDetailsReceived($id){
		$model = TransportWarehouse::find($id);
		if ($model) {
			if($model->details){
				foreach ($model->details as $detail) {
					if($detail->status == TRANSPORT_DETAIL_UNRECEIVE){
						return false;
					}
				}
				return true;
			}
		}
		return false;
	}

	public function getPrintDatas($id){
		$result = [
			'success' => true
		];
		$model = TransportWarehouse::find($id);
		if ($model) {
			$model->staff;
			$model->details;
			if ($model->details) {
				foreach ($model->details as $detail) {
					$detail->receiveWarehouse;
					$detail->product;
				}
			}
		}
		$result['transportWarehouse'] = $model;
		return $result;
	}
}