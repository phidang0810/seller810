<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\TransportWarehouseRepository;
use App\Repositories\ProductRepository;
use App\Repositories\WarehouseRepository;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class TransportWarehouseController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Danh sách chuyển kho', route('admin.transport_warehouse.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(TransportWarehouseRepository $transportWarehouse){
        if ($this->_request->ajax()){
            return $transportWarehouse->dataTable($this->_request);
        }

        $this->_data['title'] = 'Chuyển kho';

        return view('admin.transport_warehouse.index', $this->_data);
    }

    public function view(TransportWarehouseRepository $transportWarehouse, ProductRepository $product, WarehouseRepository $warehouse)
    {
        if ($this->_request->ajax()){
            if (isset($this->_request['product_id'])) {
                if (isset($this->_request['color_id'])) {
                    if (isset($this->_request['size_id'])) {
                        if (isset($this->_request['warehouse_id'])) {
                            if ($this->_request['get_data'] == true) {
                                return $product->getProductDatas($this->_request);
                            }
                            return $product->getProductDetailquantity($this->_request);
                        }
                        return $product->getProductDetailWarehouseOptions($this->_request);
                    }
                    return $product->getProductDetailSizeOptions($this->_request);
                }
                return $product->getProductDetailColorOptions($this->_request);
            }
        }
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Tạo mới chuyển kho';

        if ($id) {
            $this->_data['title'] = 'Chỉnh sửa chuyển kho';
            $this->_data['data'] = $transportWarehouse->get($id);
            $this->_data['transport_details'] = json_encode($transportWarehouse->getDetails($id));
        }
        $this->_data['transport_staff_options'] = $transportWarehouse->getTransportStaffOptions($id);
        $this->_data['product_options'] = $product->getProductOptions();
        $this->_data['warehouse_options'] = $warehouse->getWarehouseOptions();
        $this->_data['status_options'] = $transportWarehouse->getStatusOptions($id);

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.transport_warehouse.view', $this->_data);
    }

    /**
     * @param TransportWarehouseRepository $transportWarehouse
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(TransportWarehouseRepository $transportWarehouse)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;

        $rules = [
            'transport_staff_id' => 'required',
            'transport_date' => 'required'
        ];
        $message = 'Đơn chuyển kho đã được tạo.';

        if ($id) {
            // $rules['name'] = 'required|string|max:50|unique:carts,name,' . $input['id'];
            $message = 'Đơn chuyển kho đã được cập nhật.';
        }

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {

            return redirect()->back()
            ->withErrors($validator)
            ->withInput();
        }

        $data = $transportWarehouse->createOrUpdate($input, $id);
        // dd($data);
        if($input['action'] === 'save') {
            return redirect()->route('admin.transport_warehouse.index')->withSuccess($message);
        }

        if($input['action'] === 'save_print') {
            // return redirect()->route('admin.carts.view', ['id' => $data->id])->withSuccess($message);
        }

        return redirect()->route('admin.transport_warehouse.index')->withSuccess($message);
    }

    public function delete(TransportWarehouseRepository $transportWarehouse)
    {
        $ids = $this->_request->get('ids');
        $result = $transportWarehouse->delete($ids);

        return response()->json($result);
    }

    public function receive(TransportWarehouseRepository $transportWarehouse)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Nhận hàng chuyển';
        $this->_data['data'] = $transportWarehouse->getReceiveTransport($id);
        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.transport_warehouse.receive', $this->_data);
    }

    public function receiveProduct(TransportWarehouseRepository $transportWarehouse)
    {
        $id = $this->_request->get('id');
        $result = $transportWarehouse->receiveProduct($id);

        return response()->json($result);
    }
}
