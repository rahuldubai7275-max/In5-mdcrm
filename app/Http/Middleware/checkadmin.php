<?php

namespace App\Http\Middleware;

use App\Http\Controllers\AdminController;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class checkadmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(Auth::guard('admin')->check()) {
            $adminAuth=\Auth::guard('admin')->user();
            if($adminAuth->status == '2') {
                return redirect('/admin/logout');
            }

            $check=(new AdminController() )->company_info( 'info' );
            if($check==0)
                exit();

            return $next($request);
        }
        return redirect('/admin/login');
//        abort(404);
    }
}
