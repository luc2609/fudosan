<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Permission
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param $permission
     * @return mixed|never
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!$request->user()->hasPermission($permission)) {
            return abort(_error(null, __('message.no_permission'), HTTP_FORBIDDEN));
        }

        return $next($request);
    }
}
