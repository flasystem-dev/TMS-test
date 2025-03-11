<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\CodeOfCompanyInfo;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/order/ecommerce_orders';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

        $this->middleware('dep-check');
        $this->middleware('guest')->except('logout');
    }
    public function username()
    {
        return 'user_id';
    }

    // 로그인 후 부서 별 주문 다르게 보이게
    protected function authenticated(Request $request , $user) {

        $brands = DB::table('code_of_company_info') -> select('brand_type_code')->where('is_used', 1)-> get();

        switch($user -> dep) {
            case '꽃파는총각':
                foreach ($brands as $brand) {
                    switch ($brand->brand_type_code) {
                        case 'BTCP':
                            session([$brand->brand_type_code => 'Y']);
                            break;
                        case 'BTCC':
                        case 'BTSP':
                        case 'BTCS':
                        case 'BTFC':
                        case 'BTBR':
                        case 'BTOM':
                            session([$brand->brand_type_code => 'N']);
                            break;
                    }
                }
                break;
            case '위탁운영':
                foreach ($brands as $brand) {
                    switch ($brand->brand_type_code) {
                        case 'BTCC':
                        case 'BTSP':
                            session([$brand->brand_type_code => 'Y']);
                            break;
                        case 'BTCP':
                        case 'BTCS':
                        case 'BTFC':
                        case 'BTBR':
                        case 'BTOM':
                            session([$brand->brand_type_code => 'N']);
                            break;
                    }
                }
                break;
            case '꽃파는사람들':
                foreach ($brands as $brand) {
                    switch ($brand->brand_type_code) {
                        case 'BTCP':
                        case 'BTCC':
                        case 'BTSP':
                            session([$brand->brand_type_code => 'N']);
                            break;
                        case 'BTCS':
                        case 'BTFC':
                        case 'BTBR':
                        case 'BTOM':
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
    }
}
