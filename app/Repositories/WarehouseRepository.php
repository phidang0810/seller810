<?php
/**
 * Created by PhpStorm.
 * User: Dinh Thien Phuoc
 * Date: 10/06/2018
 * Time: 12:04 AM
 */

namespace App\Repositories;

use App\Models\Category;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;
use App\Libraries\Photo;
use Illuminate\Support\Facades\Storage;
use Response;
use DNS1D;

Class WarehouseRepository
{
	const CACHE_NAME_PRODUCTS = 'warehouses';

	public function dataTable($request)
    {
		$warehouses = Warehouse::select(['id', 'name', 'code', 'address', 'phone', 'email', 'active', 'order', 'created_at']);

		$dataTable = DataTables::eloquent($warehouses)
		->filter(function ($query) use ($request) {
			if (trim($request->get('status')) !== "") {
				$query->where('active', $request->get('status'));
			}

			if (trim($request->get('keyword')) !== "") {
				$query->where(function ($sub) use ($request) {
					$sub->where('name', 'like', '%' . $request->get('keyword') . '%');
				});

			}
		}, true)
		->addColumn('action', function ($warehouses) {
			$html = '';
			$html .= '<a href="' . route('admin.warehouses.view', ['id' => $warehouses->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
			$html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $warehouses->id . '" data-name="' . $warehouses->name . '">';
			$html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';
			return $html;
		})
		->addColumn('status', function ($warehouses) {
			$active = '';
			$disable = '';
			if ($warehouses->active === ACTIVE) {
				$active  = 'checked';
			}
			$html = '<input type="checkbox" '.$disable.' data-name="'.$warehouses->name.'" data-id="'.$warehouses->id.'" name="social' . $warehouses->active . '" class="js-switch" value="' . $warehouses->active . '" ' . $active . ' ./>';
			return $html;
		})
		->rawColumns(['status', 'action'])
		->toJson();

		return $dataTable;
	}

	public function getWarehouse($id)
	{
		$data = Warehouse::find($id);
		return $data;
	}

	public function createOrUpdate($data, $id = null)
	{
		if ($id) {
			$model = Warehouse::find($id);
		} else {
			$model = new Warehouse;
		}
		
		$model->name = $data['name'];
		$model->code = general_code('K H', $id, 2);
		$model->address = $data['address'];
		$model->phone = $data['phone'];
		$model->email = $data['email'];
		$model->order = $data['order'];
		$model->active = $data['active'];

		$model->save();

		return $model;
	}


	public function delete($ids)
	{
		$result = [
			'success' => true,
			'errors' => []
		];
		foreach ($ids as $id) {
			$warehouse = Warehouse::find($id);
			if ($warehouse === null) {
				$result['errors'][] = 'ID kho hàng: ' . $id . ' không tồn tại';
				$result['success'] = false;
				continue;
			}
			$warehouse->delete();
		}

		return $result;
	}

	public function changeStatus($warehouseID, $status)
	{
		$model = Warehouse::find($warehouseID);
		$model->active = $status;
		return $model->save();
	}

	public function getWarehouseOptions(){
		return make_option($this->getWarehouses());
	}

	public function getWarehouses()
	{
		$data = Warehouse::get();
		return $data;
	}

	public function getProductQuantityObj($request)
    {
        $warehouses = WarehouseProduct::selectRaw('products.main_cate, warehouses.name as warehouse_name, products.name as product_name, products.barcode_text as product_code, SUM(warehouse_product.quantity) as quantity, SUM(warehouse_product.quantity_available) as quantity_available')
        ->join('warehouses', 'warehouses.id', '=', 'warehouse_product.warehouse_id')
        ->join('products', 'products.id', '=', 'warehouse_product.product_id')
        ->groupBy('warehouse_product.product_id');

        $categories = Category::get()->pluck('name', 'id')->toArray();
        $dataTable = DataTables::eloquent($warehouses)
            ->filter(function ($query) use ($request) {
                if (trim($request->get('status')) !== "") {
                    $query->where('active', $request->get('status'));
                }

                if (trim($request->get('category_id')) !== "") {
                    $query->where('products.main_cate', $request->get('category_id'));
                }

                if (trim($request->get('warehouse_id')) !== "") {
                    $query->where('warehouse_product.warehouse_id', $request->get('warehouse_id'));
                }

                if (trim($request->get('name')) !== "") {
                    $query->where(function ($sub) use ($request) {
                        $sub->where('products.name', 'like', '%' . $request->get('name') . '%');
                    });

                }
            }, true)
            ->addColumn('category', function($product) use ($categories) {
                return $categories[$product->main_cate] ?? '';
            })
            ->addColumn('quantity', function($product) {
                return format_number($product->quantity);
            })
            ->addColumn('quantity_available', function($product) {
                return format_number($product->quantity_available);
            })
            ->addColumn('quantity_sell', function($product) {
                return format_number($product->quantity - $product->quantity_available);
            });

        return $dataTable;
    }

    public function getProductQuantityTable($request)
    {
        $data = $this->getProductQuantityObj($request)
            ->rawColumns(['category', 'platform', 'photo'])
            ->toJson();

        return $data;
    }

    public function getDataList()
    {
        $data = Warehouse::where('active', TRUE)->get();
        return $data;
    }
}