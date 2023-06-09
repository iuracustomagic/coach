<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

class TrackLastActiveAt
{
    public function handle(Request $request, Closure $next)
    {
        if (backpack_auth()->guest()) {
            return $next($request);
        }

        $date = new \DateTime(backpack_user()->last_active_at);
        $now = new \DateTime();

        if($date < $now) {
            backpack_user()->last_active_at = date("Y-m-d H:i:s");
            backpack_user()->saveQuietly();
        }

        return $next($request);
    }
}