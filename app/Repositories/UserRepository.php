<?php
/**
 * Created by PhpStorm.
 * User: phidangmtv
 * Date: 4/10/18
 * Time: 2:53 PM
 */

namespace App\Repositories;


use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserRepository
{
    const CACHE_NAME_USERS = 'users';

    public function dataTable($request)
    {
        $users = User::select(['users.id', 'roles.name as role', 'full_name', 'email', 'users.active', 'users.created_at'])
            ->join('roles', 'roles.id', '=', 'users.role_id');

        $dataTable = DataTables::eloquent($users)
            ->filter(function ($query) use ($request) {
                if (trim($request->get('role')) !== "") {
                    $query->where('role_id', $request->get('role'));
                }

                if (trim($request->get('status')) !== "") {
                    $query->where('users.active', $request->get('status'));
                }

                if (trim($request->get('keyword')) !== "") {
                    $query->where(function ($sub) use ($request) {
                        $sub->where('users.full_name', 'like', '%' . $request->get('keyword') . '%')
                            ->orWhere('users.email', 'like', '%' . $request->get('keyword') . '%');
                    });

                }
            }, true)
            ->addColumn('action', function ($user) {
                $html = '';
                if ($user->id != Auth::id()) {
                    $html .= '<a href="' . route('admin.users.view', ['id' => $user->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Edit</a>';
                    $html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $user->id . '" data-email="' . $user->email . '">';
                    $html .= '<i class="glyphicon glyphicon-edit"></i> Delete</a>';
                }
                return $html;
            })
            ->addColumn('status', function ($user) {
                if ($user->active === ACTIVE) {
                    $html = '<span class="label label-primary">Active</span>';
                } else {
                    $html = '<span class="label">Inactive</span>';
                }
                return $html;
            })
            ->rawColumns(['status', 'action'])
            ->toJson();

        return $dataTable;
    }

    public function addUser($user)
    {
        $users = $this->getUsers();
        array_push($users, $user);
        Cache::forever(self::CACHE_NAME_USERS, $users);

        return $user;
    }

    public function getUsers()
    {
        return Cache::get(self::CACHE_NAME_USERS, []);
    }

    public function getUser($id)
    {
        $data = User::find($id);
        return $data;
    }

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = User::find($id);
        } else {
            $model = new User;
        }
        $model->email = $data['email'];
        $model->role_id = $data['role_id'];
        $model->active = $data['active'];
        $model->password = Hash::make($data['password']);
        $model->first_name = $data['first_name'];
        $model->last_name = $data['last_name'];
        $model->full_name = $data['first_name'] . ' ' . $data['last_name'];

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
            $user = User::find($id);
            if ($user === null) {
                $result['errors'][] = 'User ID: ' . $id . ' is not exists';
                $result['success'] = false;
                continue;
            }
            if (Auth::id() == $id) {
                $result['errors'][] = 'You can not delete yourself';
                $result['success'] = false;
                continue;
            }
            $user->delete();
        }

        return $result;
    }
}