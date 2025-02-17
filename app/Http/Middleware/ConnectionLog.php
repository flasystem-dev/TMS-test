<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class ConnectionLog
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = Auth::check() ? Auth::user()->user_id : $request->ip();
        $name = Auth::check() ? Auth::user()->name : 'Guest';
        $uri = Route::current()->uri();
        $now = Carbon::now()->format('Y-m-d');

        DB::table('connection_log')->updateOrInsert(
            [
                'user_id' => $id,
                'uri' => $uri,
                'log_date' => $now,
            ],
            [
                'name' => $name,
                'hit' => DB::raw('hit + 1'),
            ]
        );

        return $next($request);
    }
}
