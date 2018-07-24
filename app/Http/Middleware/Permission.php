<?php

namespace App\Http\Middleware;

use App\Repositories\RoleRepository;
use Closure;
use Illuminate\Support\Facades\Auth;

class Permission
{
    protected $role;
    public function __construct(RoleRepository $role)
    {
        $this->role = $role;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, ... $permissionRequired)
    {
        if (!Auth::check()) return redirect('login');
        $user = Auth::user();

        $permissionUser = $this->role->getPermissionByRoleID($user->role_id);
        foreach($permissionUser as $item) {
            if (in_array($item->alias, $permissionRequired)) return $next($request);
        }
        return redirect()->route('error', [403]);
    }
}
