<?php
/**
 * Created by PhpStorm.
 * User: phidangmtv
 * Date: 4/10/18
 * Time: 2:53 PM
 */

namespace App\Repositories;


use App\Libraries\Photo;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Response;

class UserRepository
{
    const CACHE_NAME_USERS = 'users';

    public function dataTable($request)
    {
        $users = User::select(['users.id', 'users.avatar', 'roles.name as role', 'full_name', 'email', 'users.active', 'users.created_at'])
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
        ->addColumn('avatar', function ($user) {
            if ($user->avatar) {
                $html = '<img style="width: 80px; height: 60px;" class="img-thumbnail" src="' . asset('storage/' . $user->avatar). '" />';
            } else {
                $html = ' <img alt="No Photo" style="width: 80px; height: 60px;" class="img-thumbnail" src="'.asset(NO_PHOTO).'" >';
            }
            return $html;
        })
        ->addColumn('action', function ($user) {
            $html = '';
            if ($user->id != Auth::id()) {
                $html .= '<a href="' . route('admin.users.view', ['id' => $user->id]) . '" class="btn btn-xs btn-primary" style="margin-right: 5px"><i class="glyphicon glyphicon-edit"></i> Sửa</a>';
                //$html .= '<a href="#" class="bt-delete btn btn-xs btn-danger" data-id="' . $user->id . '" data-email="' . $user->email . '">';
                //$html .= '<i class="fa fa-trash-o" aria-hidden="true"></i> Xóa</a>';
            }
            return $html;
        })
        ->addColumn('status', function ($user) {
            $active = '';
            $disable = '';
            if ($user->id === Auth::id()) {
                $disable = 'disabled';
            }
            if ($user->active === ACTIVE) {
                $active  = 'checked';
            }
            $html = '<input type="checkbox" '.$disable.' data-email="'.$user->email.'" data-id="'.$user->id.'" name="social' . $user->active . '" class="js-switch" value="' . $user->active . '" ' . $active . ' ./>';
            return $html;
        })
        ->rawColumns(['avatar','status', 'action'])
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

    public function checkAccountExist($user, $social)
    {
        $model =  SocialUser::where('social_alias', $social);
        if($user->getEmail()) {
            $model->where('email', $user->getEmail());
        }

        return $model->first();
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
        $model->full_name = $data['full_name'];

        if(isset($data['avatar'])) {
            if ($model->avatar) {
                Storage::delete($model->avatar);
            }
            $upload = new Photo($data['avatar']);
            $model->avatar = $upload->uploadTo('users');
        }
        $model->save();

        return $model;
    }

    public function createOrUpdateSocialUser($social, $user, $id = null)
    {
        if($id) {
            $model = User::find($id);
        } else {
            $model = new User;
        }

        $model->full_name = $user->getName();
        $model->email = $user->getEmail();
        $model->avatar = $user->getAvatar();
        $model->data = json_encode($user);

        $model->social_alias = $social;

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
                $result['errors'][] = 'Tài khoản có ID: ' . $id . ' không tồn tại!';
                $result['success'] = false;
                continue;
            }
            if (Auth::id() == $id) {
                $result['errors'][] = 'Không thể xóa chính bạn!';
                $result['success'] = false;
                continue;
            }

            if ($user->avatar) {
                Storage::delete($user->avatar);
            }
            $user->delete();
        }

        return $result;
    }

    public function changeStatus($userID, $status)
    {
        $model = User::find($userID);
        $model->active = $status;
        return $model->save();
    }
    public function getTotalUser()
    {
        $data = User::where('active', ACTIVE)->count();
        return $data;
    }

    public function getStaff($id)
    {
        $return = [
            'status'    =>  false,
            'message'   =>  'Id staff không tồn tại',
        ];

        $staff = User::find($id);
        if ($staff) {
            $return['status'] = true;
            $return['staff'] = $staff;
        }

        return $return;
    }

    public function createCustomer($data)
    {
        $model = new User;
        $model->email = $data['email'];
        $model->role_id = 2; // customer
        $model->active = true;
        $model->password = Hash::make($data['password']);
        $model->full_name = $data['name'];

        if(isset($data['phone'])) {
            $model->phone = $data['phone'];
        }

        if(isset($data['address'])) {
            $model->address = $data['address'];
        }

        $model->save();


        // create customer
        $customer = new Customer;
        $customer->name = $data['name'];
        $customer->email = $data['email'];
        $customer->user_id = $model->id;
        $customer->active = true;
        $customer->group_customer_id = 1;

        if(isset($data['phone'])) {
            $customer->phone = $data['phone'];
        }

        if(isset($data['address'])) {
            $customer->address = $data['address'];
        }

        $customer->save();
        $customer->code = general_code($data['name'], $customer->id, 5);
        $customer->save();

        return $model;
    }
}