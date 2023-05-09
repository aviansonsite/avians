<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Session;

class CheckMiddleware
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
        $roles=Session::get('ROLES');
        $role=explode(',',$roles);
        $count=count($role);
       
        $route = \Route::current()->action['prefix'];
        $url= Route::getFacadeRoot()->current()->uri();
        $name= \Request::route()->getName();
        $prefix=explode('/', $route);
        //dd($prefix);
        $match=0;
        // dd($role);
        for($i=0;$i<$count;$i++)
        {
            if($role[$i]==2 || $role[$i]==3)
            {
                if(($url=='users' || $name=='user.page' || $url=='add_users' || $url=='edit_users' || $url=='change_status' || $url=='user_del' || $url=='company' ||  $url=='edit_company') && ($role[$i]==2  || $role[$i]==3))
                {
                    return redirect()->back();
                }
                else
                {
                    break;
                }
            }
        }
        return $next($request);   
    }
    
}
