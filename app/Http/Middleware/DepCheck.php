<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user_dep = isset(Auth::user() -> dep)? Auth::user() -> dep : '' ;
        $brand_code = DB::table('code_of_company_info')->select('brand_type_code')-> where('shop_name', '=', $user_dep) -> first();

        $brand_code = isset($brand_code)? $brand_code -> brand_type_code: '';
        $brands     = DB::table('code_of_company_info')->select('brand_type_code')->get();


        switch($brand_code){
            case 'BTCP':
                foreach ($brands as $brand) {
                    switch($brand->brand_type_code){
                        case 'BTCP': case 'BTCC': case 'BTSP':
                            session([$brand->brand_type_code => 'Y']);
                            break;
                        case 'BTCS': case 'BTFC': case 'BTBR':case 'BTOM':
                            session([$brand->brand_type_code => 'N']);
                            break;
                    }
                }
                break;
            case 'BTCS':
                foreach ($brands as $brand) {
                    switch($brand->brand_type_code){
                        case 'BTCP': case 'BTCC': case 'BTSP':
                            session([$brand->brand_type_code => 'N']);
                            break;
                        case 'BTCS': case 'BTFC': case 'BTBR':case 'BTOM':
                            session([$brand->brand_type_code => 'Y']);
                            break;
                    }
                }
                break;
            default :
                foreach ($brands as $brand) {
                    session([$brand->brand_type_code => 'Y']);
                }
        }
        
        return $next($request);
    }
}
