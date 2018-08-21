<?php
/**
 * Created by PhpStorm.
 * User: phidangmtv
 * Date: 4/10/18
 * Time: 2:53 PM
 */

namespace App\Repositories;


use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;

class RoleRepository
{
    public function all()
    {
        $data = Role::select('id', 'name', 'alias')->where('active', ACTIVE)->get();
        return $data;
    }

    public function getPermissionByRoleID($id)
    {
        return RolePermission::select('permissions.*')
            ->join('permissions', 'permissions.id' ,'=','role_permission.permission_id')
            ->where('role_id', $id)
            ->get();
    }

    public function dataTable($request)
    {
        $models = Role::selectRaw('roles.id, roles.name, roles.active, created_at');
            //->leftJoin('users', 'roles.id', '=', 'users.role_id');
        $dataTable = DataTables::eloquent($models)
            ->filter(function ($query) use ($request) {
                if (trim($request->get('status')) !== "") {
                    $query->where('roles.active', $request->get('status'));
                }

                if (trim($request->get('keyword')) !== "") {
                    $query->where(function ($sub) use ($request) {
                        $sub->where('roles.name', 'like', '%' . $request->get('keyword') . '%');
                    });

                }
            }, true)
            ->addColumn('action', function ($model) {
                $html = '';
                $html .= '<a href="' . route('admin.roles.view', ['id' => $model->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
                $html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $model->id . '" data-name="' . $model->name . '">';
                $html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';
                
                return $html;
            })
            ->addColumn('status', function ($model) {
                if($model->active) {
                    $html = '<label class="label label-primary">Đã kích hoặc</label>';
                } else {
                    $html = '<label class="label label-default">Chưa kích hoặc</label>';
                }

                return $html;
            })
            ->rawColumns(['action', 'status'])
            ->toJson();

        return $dataTable;
    }

    public function getRole($id)
    {
        $data = Role::find($id);
        $data->permissions = RolePermission::where('role_id', $id)->get();
        return $data;
    }

    public function createOrUpdate($data, $id = null)
    {
        if ($id) {
            $model = Role::find($id);
            RolePermission::where('role_id', $id)->delete();
        } else {
            $model = new Role;
        }
        $model->name = $data['name'];
        $model->alias = $data['name'];
        $model->active = $data['active'];
        $model->save();


        foreach($data['permissions'] as $permission) {
            $rolePermission = new RolePermission;
            $rolePermission->role_id = $model->id;
            $rolePermission->permission_id = $permission;
            $rolePermission->save();
        }
        return $model;
    }

    public function delete($id)
    {
        $result = [
            'success' => true,
            'errors' => []
        ];
        $model = Role::find($id);
        if ($model === null) {
            $result['errors'][] = 'Phòng ban có ID: ' . $id . ' không tồn tại!';
            $result['success'] = false;
            return $result;
        }

        $used = User::where('role_id', $id)->count();
        if($used) {
            $result['errors'][] = 'Để xóa bạn cần phải xóa các user của phòng ban này trước!';
            $result['success'] = false;
            return $result;
        }

        RolePermission::where('role_id', $id)->delete();
        Role::destroy($id);

        return $result;
    }

    public function changeStatus($modelID, $status)
    {
        $model = Role::find($modelID);
        $model->active = $status;
        return $model->save();
    }

    public function getPermissions()
    {
        $data = Permission::all();
        return $data;
    }

    public function getPermissionIDByRole($roleID)
    {
        $data = RolePermission::where('role_id', $roleID)
            ->pluck('permission_id')
            ->toArray();
        return $data;
    }
}