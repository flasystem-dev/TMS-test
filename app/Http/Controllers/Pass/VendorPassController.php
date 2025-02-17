<?php
namespace App\Http\Controllers\Pass;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Models\VendorPass;
use App\Models\User;

class VendorPassController extends Controller
{

    public function index(Request $request) {
        $search = $request -> except('page');
        $data['passes'] = VendorPass::index_passList($search);
        return view('pass.index', $data);
    }

    public function passForm($id) {
        $data['pass'] = VendorPass::find($id);
        return view('pass.pass-form', $data);
    }

    // 1년 주문건
    public function all_orders_cnt() {
        $oneYearAgo = Carbon::now()->subYear();
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $brand = substr($this->brand_type, 0, 4);

        return DB::table('total_information')
            ->select(DB::raw('IFNULL(SUM(order_count), 0) as order_count'))
            ->where('brand', $brand)
            ->where('mall_code', $this->idx)
            ->where('date_type', 'order')
            ->where(function($query) use ($oneYearAgo, $currentYear, $currentMonth) {
                // 과거 1년과 현재 연도에 대해 조건을 추가
                $query->where(function($q) use ($oneYearAgo) {
                    $q->where('year', $oneYearAgo->year)
                        ->where('month', '>=', $oneYearAgo->month);
                })->orWhere(function($q) use ($currentYear, $currentMonth) {
                    $q->where('year', $currentYear)
                        ->where('month', '<=', $currentMonth);
                });
            })
            ->first()->order_count;
    }

    // 1년 금액
    public function all_orders_sum_amount() {
        $oneYearAgo = Carbon::now()->subYear();
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $brand = substr($this->brand_type, 0, 4);

        return DB::table('total_information')
            ->select(DB::raw('IFNULL(SUM(order_amount), 0) as order_amount'))
            ->where('brand', $brand)
            ->where('mall_code', $this->idx)
            ->where('date_type', 'order')
            ->where(function($query) use ($oneYearAgo, $currentYear, $currentMonth) {
                // 과거 1년과 현재 연도에 대해 조건을 추가
                $query->where(function($q) use ($oneYearAgo) {
                    $q->where('year', $oneYearAgo->year)
                        ->where('month', '>=', $oneYearAgo->month);
                })->orWhere(function($q) use ($currentYear, $currentMonth) {
                    $q->where('year', $currentYear)
                        ->where('month', '<=', $currentMonth);
                });
            })
            ->first()->order_amount;
    }

    // 당월 주문건
    public function orders_for_this_month_cnt() {
        $brand = substr($this->brand_type, 0, 4);
        return DB::table('total_information')
            ->select(DB::raw('IFNULL(SUM(order_count), 0) as order_count'))
            ->where('brand', $brand)
            ->where('mall_code', $this->idx)
            ->where('date_type', 'delivery')
            ->where('year', date('Y'))
            ->where('month', date('m'))
            ->first()->order_count;
    }

    // 당월 주문 금액
    public function orders_for_this_month_amount() {
        $brand = substr($this->brand_type, 0, 4);
        return DB::table('total_information')
            ->select(DB::raw('IFNULL(SUM(order_amount), 0) as order_amount'))
            ->where('brand', $brand)
            ->where('mall_code', $this->idx)
            ->where('date_type', 'delivery')
            ->where('year', date('Y'))
            ->where('month', date('m'))
            ->first()->order_amount;
    }

    // 오늘 주문건
    public function orders_for_today_cnt() {

        return DB::table('order_data')
            ->join('order_delivery', 'order_delivery.order_idx', '=', 'order_data.order_idx')
            ->whereDate('order_delivery.delivery_date', date('Y-m-d'))
            ->where('order_data.brand_type_code', config('brand'))
            ->where('order_data.mall_code', $this->idx)
            ->where('order_data.is_view', 1)
            ->where('order_delivery.is_balju', 1)
            -> whereNot(function($query){
                $query -> where('order_data.payment_state_code', 'PSCC')
                    -> orWhere('order_delivery.delivery_state_code', 'DLCC');
            })
            -> count();
    }

    // 오늘 주문 금액
    public function orders_for_today_amount() {
        return DB::table('order_data')
            ->join('order_delivery', 'order_delivery.order_idx', '=', 'order_data.order_idx')
            ->selectRaw('sum(total_amount-discount_amount) as total_amount')
            ->whereDate('order_delivery.delivery_date', date('Y-m-d'))
            ->where('order_data.brand_type_code', config('brand'))
            ->where('order_data.mall_code', $this->idx)
            ->where('order_data.is_view', 1)
            ->where('order_delivery.is_balju', 1)
            ->whereNot(function($query){
                $query -> where('order_data.payment_state_code', 'PSCC')
                    -> orWhere('order_delivery.delivery_state_code', 'DLCC');
            })
            -> first() -> total_amount;
    }

########################################################################################################################
#####################################################  수정  ############################################################

    ###########################################  Pass - 업데이트  #######################################################
    public function passUpsert(Request $request) {
        $input = $request -> all();
        $id = $input['id'];

        if(DB::table('vendor_pass')->where('id', $id)->exists()) {
            $pass = VendorPass::find($id);
        }else{
            $pass = new VendorPass();
            $id = VendorPass::max('id') + 1;
            $input['id'] = $id;
        }
        $pass -> fill($input);
        write_table_log($pass);
        $pass -> save();

        if(!empty($input['vendor_id'])) {
            if(User::where('user_id', $input['vendor_id'])->where('brand', $pass->brand)->where('vendor_idx', $id)->exists()) {
                $user = User::where('user_id', $input['vendor_id'])->where('brand', $pass->brand)->where('vendor_idx', $id) -> first();
            }else {
                $user = new User();
                $user -> id = User::max('id') + 1;
            }

            if(!empty($input['vendor_pw'])) {
                $user -> password = bcrypt($input['vendor_pw']);
            }
            $user -> vendor_idx = $id;
            $user -> brand = $input['brand'];
            $user -> user_id = $input['vendor_id'];
            $user -> name = $input['name'];
            $user -> status = 1;
            $user -> is_vendor = 2;
            $user -> is_credit = $input['is_credit'];
            $user -> auth = 9;
            write_table_log($user);
            $user -> save();
        }

        session() -> flash("update", 1);
        return response() -> json(['status'=>true, 'url'=>url("/pass/pass-form/$id")]);
    }

    ############################################## Pass 리스트 간단 정보 변경 #############################################
    public function simple_update(Request $request) {
        $column = $request -> column;
        $id = $request -> id;
        $check = $request -> check;

        $pass = VendorPass::find($id);
        $pass -> $column = $check;
        write_table_log($pass);
        $pass -> save();

        session()->flash('update', 1);
        return response() -> json(true);
    }

########################################################################################################################
#####################################################  조회  ############################################################

    public function checkDomain(Request $request) {
        return DB::table('vendor_pass')->where('domain', $request -> domain) -> exists();
    }
}
