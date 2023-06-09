<?php

namespace App\Http\Middleware;

use Closure;

class CheckRoles
{
    public function handle($request, Closure $next)
    {
        /* Check user permissions */
        /* Permissions should be defined as user.index, user.store etc. and assigned to role  */
        /*if (!backpack_user()->can($request->route()->getName())) {
            abort(403);
        }*/

        return $next($request);
    }
}
