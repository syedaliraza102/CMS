<?php

namespace App\Http\Middleware;

use Closure;
use \App\User;

class AdminAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $email = str_replace('Basic ', '', str_replace('BiZDE5NTAwIn0%3D', '', \Request::header('Authorization')));
        $adminuser = User::with('admin_role_data')->where('user_name', $email)->first();
        //dd($adminuser->toArray());
        session(['admin_role' => $adminuser['admin_role'] ?? '']);
        session(['user' => !empty($adminuser) ? $adminuser->toArray() : []]);
        session(['admin_role_actions' => $adminuser['admin_role_data']['actions'] ?? []]);
        //dd(session('admin_role_actions'));
        $_SESSION['user'] = !empty($adminuser) ? $adminuser->toArray() : [];
        return $next($request);
    }
}
