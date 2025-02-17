<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cache;
use App\Models\CommonCode;
use App\Models\Product\ProductOptionType;
use App\Models\Product\ProductPriceType;

class UserCache
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // 마지막 업데이트 시간 확인
        $lastUpdated = Cache::get('user_cache_updated_at');

        if (!$lastUpdated) { // 3시간 기준
            Cache::put('user_cache_updated_at', now(), 3600 * 4);

            Cache::put('common_codes', CommonCode::all()->keyBy('code'), 3600 * 6);

            Cache::put('product_option_type', ProductOptionType::all()->keyBy('id'), 3600 * 6);

            Cache::put('product_price_type', ProductPriceType::all()->keyBy('id'), 3600 * 6);
        }

        return $next($request);
    }
}
