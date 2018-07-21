<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\IconRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
    }

    public function index(IconRepository $iconRepo)
    {
        if ($this->_request->ajax()) {
            return $iconRepo->dataTable($this->_request);
        }

        $this->_data['title'] = 'Video';
        $this->_data['status'] = $iconRepo->getStatus();

        return view('admin.video.index', $this->_data);
    }

    public function store(IconRepository $iconRepo)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;
        $rules = [
            'name' => 'required',
            'panorama_id' => 'required|exists:panoramas,id',
            'file' => 'required'
        ];

        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $iconRepo->createOrUpdate($this->_request->all(), $id);

        return response()->json([
            'msg' => 'The icon has been uploaded.'
        ]);
    }

    public function dataList(IconRepository $iconRepo)
    {
        $panoramaId = $this->_request->get('panorama_id');
        $data = $iconRepo->dataList(['panorama_id' => $panoramaId]);

        return response()->json([
            'results' => $data
        ]);
    }
}
