<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends AdminController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->_pushBreadCrumbs('Admins', route('admin.users.index'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(UserRepository $user, RoleRepository $role)
    {
        if ($this->_request->ajax()) {
            return $user->dataTable($this->_request);
        }

        $this->_data['title'] = 'Admins';
        $this->_data['roles'] = $role->all();

        return view('admin.users.index', $this->_data);
    }

    public function view(UserRepository $user, RoleRepository $role)
    {
        $id = $this->_request->get('id');
        $this->_data['title'] = 'Create a new account';
        if ($id) {
            $this->_data['title'] = 'Edit account';
            $this->_data['data'] = $user->getUser($id);
        }

        $this->_data['roles'] = $role->all();

        $this->_pushBreadCrumbs($this->_data['title']);
        return view('admin.users.view', $this->_data);
    }

    public function store(UserRepository $user)
    {
        $input = $this->_request->all();
        $id = $input['id'] ?? null;
        $rules = [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:users',
            'password' => 'required',
            'role_id' => 'required',
            'active' => 'required'
        ];
        $message = 'The account has been created.';

        if ($id) {
            $rules['email'] = 'required|email|max:100|unique:users,email,' . $input['id'];
            $message = 'The account has been updated.';
        }
        if ($id && $id == Auth::id()) {
            return redirect()->back()
                ->withErrors(['You can not update yourself.'])
                ->withInput();
        }

        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user->createOrUpdate($input, $id);

        return redirect()->route('admin.users.index')->withSuccess($message);
    }

    public function delete(UserRepository $user)
    {
        $ids = $this->_request->get('ids');
        $result = $user->delete($ids);

        return response()->json($result);
    }
}
