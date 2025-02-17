<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class UserRefreshRateLimiter
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = $request->user();

        if(Str::contains($user -> dep, ['꽃파는총각', '위탁운영', '꽃파는사람들']) ) {
            $key = 'rate_limit:user:' . $user->id;

            $limit = RateLimiter::attempt(
                $key,
                $perMinute = 500,     // 횟수
                function() {

                },
                $decaySeconds = 60 * 60 * 24  // 초 기준
            );

            if(!$limit) {
//                $seconds = RateLimiter::availableIn($key);

//                return redirect('/error/404') -> with('message', '너무 많은 횟수를 시도하고 있습니다.' . '\n' . $seconds . '초 뒤에 다시 시도해주세요.');
                return redirect() -> away('https://flabiz.kr/sub/max_traffic_tms.php');
            }
        }

        return $next($request);
    }
}
