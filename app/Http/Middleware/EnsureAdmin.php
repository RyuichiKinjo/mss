<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->session()->get('admin') !== true) {
            return response()->json(['message' => '管理モードではありません'], 403);
        }

        return $next($request);
    }
}
