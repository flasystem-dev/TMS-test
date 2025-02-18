<?php
namespace App\Services\Statistics;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorService
{
    // 당일 매출, 건수 (데이터테이블)
    public static function today_sales_vendor($brand, $dateType) {
        $cartQuery = DB::table('order_cart')
            ->select('order_idx', DB::raw('SUM(IFNULL(vendor_option_price, 0)) as total_option_price'))
            ->groupBy('order_idx');

        $list = DB::table('order_data as o')
            -> join('order_delivery as d', 'o.order_idx', '=', 'd.order_idx')
            -> leftJoinSub($cartQuery,'c', 'o.order_idx', '=', 'c.order_idx')
            -> join('vendors as v', 'o.mall_code', '=', 'v.idx')
            -> select(
                'o.brand_type_code as brand',
                'v.mall_name as mall_name', 'v.rep_name as rep_name', 'v.partner_name as partner_name', 'v.service_percent as service_percent',
                DB::raw('IFNULL(COUNT(o.order_idx), 0) as order_count'),
                DB::raw('IFNULL(SUM(o.total_amount - o.discount_amount), 0) as order_amount'),
                DB::raw('IFNULL(SUM(o.vendor_amount), 0) as vendor_amount'),
            )
            ->where('o.brand_type_code', $brand)
            ->where('o.is_view', 1)
            ->where('d.is_balju', 1)
            ->whereNot('d.delivery_state_code', 'DLCC')
            ->where('v.is_jungsan', 1)
            ->where('v.is_valid', 'Y')

            ->groupBy('o.mall_code');

        if($dateType==="order") {
            $list = $list
                ->whereBetween('o.order_time', [date('Y-m-d'), date('Y-m-d')." 23:59:59"]);
        }elseif ($dateType==="delivery") {
            $list = $list
                ->whereBetween('d.delivery_date', [date('Y-m-d'), date('Y-m-d')." 23:59:59"]);
        }
        return $list -> get();
    }

    // 사업자 카운트 정보 (활성화 사업자, 신규)
    public static function vendors_count_data() {
        $data1 = self::valid_vendors();
        $data2 = self::new_vendors();

        $data1 = collect($data1)->keyBy('brand_type')->toArray();
        $data2 = collect($data2)->keyBy('brand_type')->toArray();

        $vendors = [];

        foreach ($data1 as $key => $value) {
            $type = [];
            $type['brand'] = $value -> brand_type;
            $type['count'] = $value ->count;
            $type['new'] = $data2[$key] -> count ?? 0;
            $vendors[] = $type;
        }

        return $vendors;
    }

    // 사업자 리스트 - 정산 정보
    public static function vendor_specification_data($year, $month, $brand, $dateType) {
        return DB::table('vendor as v')
            ->leftJoin('total_information as t', function($join) use ($year, $month, $dateType) {
                $join->on('v.idx', '=', 't.mall_code')
                    ->where('t.year', $year)
                    ->where('t.month', $month)
                    ->where('t.date_type', $dateType);
            })
            ->select(
                'idx', 'brand_type', 'mall_name', 'rep_name', 'rep_tel', 'registered_date',
                DB::raw("(SELECT COUNT(*) FROM vendor WHERE recommend_person = v.idx AND is_valid = 'Y') as recommend_count"),
                DB::raw("(SELECT COUNT(*) FROM vendor WHERE recommend_person = v.recommend_person AND is_valid = 'Y') as recommend_person_count"),
                DB::raw("(SELECT rep_name FROM vendor WHERE idx = v.recommend_person) as recommend_name"),
                DB::raw('IFNULL(t.order_count, 0) as order_count'),
                DB::raw('IFNULL(t.order_amount, 0) as order_amount')
            )
            ->where('v.brand_type', $brand)
            ->where('v.is_valid', 'Y')
            ->where('v.is_jungsan', 1)
            ->groupBy('v.idx')
            ->orderBy('t.order_amount', 'desc')
            ->get();
    }

    // 추천인 정산 정보
    public static function recommendPerson_specification($year, $month, $recommend, $dateType) {
        return DB::table('vendor as v')
            ->leftJoin('total_information as t', function($join) use ($year, $month, $dateType) {
                $join->on('v.idx', '=', 't.mall_code')
                    ->where('t.year', $year)
                    ->where('t.month', $month)
                    ->where('t.date_type', $dateType);
            })
            ->select(
                'idx', 'brand_type', 'mall_name', 'rep_name', 'rep_tel', 'registered_date',
                DB::raw("(SELECT COUNT(*) FROM vendor WHERE recommend_person = v.idx AND is_valid = 'Y') as recommend_count"),
                DB::raw("(SELECT COUNT(*) FROM vendor WHERE recommend_person = v.recommend_person AND is_valid = 'Y') as recommend_person_count"),
                DB::raw("(SELECT rep_name FROM vendor WHERE idx = v.recommend_person) as recommend_name"),
                DB::raw('IFNULL(t.order_count, 0) as order_count'),
                DB::raw('IFNULL(t.order_amount, 0) as order_amount')
            )
            ->where('v.recommend_person', $recommend)
            ->where('v.is_valid', 'Y')
            ->where('v.is_jungsan', 1)
            ->groupBy('v.idx')
            ->orderBy('t.order_amount', 'desc')
            ->get();
    }

    // 사업자 매출 캘린더 api 정보
    public static function vendorSales_calenderAPI($request)
    {
        $start = $request->start;
        $end = $request->end;
        $vendor = $request -> vendor;
        $dateType = $request -> dateType;

        $select_month = self::find_select_month($start, $end);
        $year = $select_month['year'];
        $month = $select_month['month'];

        $startDate = Carbon::create($year, $month, 1)->format('Y-m-d');
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');

        $list = self::vendor_daily_sales($startDate, $endDate, $vendor, $dateType);
        $sales_data = self::vendor_month_sales($year, $month, $vendor, $dateType);

        foreach ($list as $item) {
            if($dateType==="order") {
                $item->date = $item->order_date;
            }elseif ($dateType==="delivery") {
                $item->date = $item->delivery_date;
            }
        }

        $events = $list->map(function ($sale) {
            return [
                'title' =>  "<span class='order_amount'>".number_format($sale->order_amount) . " 원</span>"."<br><span class='order_count'>{$sale->order_count} 건</span>",
                'start' => $sale->date,
                'allDay' => true, // 하루 단위로 표시
                'color' => "#fff",
                'textColor' => "#444444"
            ];
        });

        return [
            'events' => $events,
            'month_sales' => [
                'order_count' => $sales_data -> order_count ?? 0,
                'order_amount' => $sales_data -> order_amount ?? 0,
            ]
        ];
    }

########################################################################################################################
########################################################################################################################

    // 사업자 카운트 ( 활성화 된 )
    protected static function valid_vendors() {
        $types = ['BTFCB', 'BTFCC', 'BTCS'];

        return DB::table('vendor')
            -> select('brand_type', DB::raw('count(*) as count'))
            -> where('is_valid', 'Y')
            -> where('is_jungsan', 1)
            -> groupBy('brand_type')
            -> orderByRaw('FIELD(brand_type, ' . implode(',', array_map(function($type) {
                    return "'" . $type . "'";
                }, $types)) . ')')
            -> get();
    }

    // 신규 사업자 카운트 ( 한달 이내 )
    protected static function new_vendors() {
        $monthAgo = Carbon::now()->subMonths(1)->toDateString();

        return DB::table('vendor')
            -> select('brand_type', DB::raw('count(*) as count'))
            -> where('is_valid', 'Y')
            -> where('is_jungsan', 1)
            -> where('registered_date', '>', $monthAgo)
            -> groupBy('brand_type')
            -> get();
    }

    // 하루 주문 건수, 매출 ( 풀캘린더 )
    protected static function vendor_daily_sales($start, $end, $idx, $dateType) {
        $list = DB::table('order_data as o')
            -> join('order_delivery as d', 'o.order_idx', '=', 'd.order_idx')
            -> select(
                'o.brand_type_code as brand',
                DB::raw('COUNT(o.order_idx) as order_count'),
                DB::raw('SUM(o.total_amount - o.discount_amount) as order_amount'),
                DB::raw('DATE(o.order_time) as order_date'),
                DB::raw('DATE(d.delivery_date) as delivery_date')
            )
            ->where('o.is_view', 1)
            ->where('o.mall_code', $idx)
            ->where('d.is_balju', 1)
            ->whereNot('d.delivery_state_code', 'DLCC');

        if($dateType==="order") {
            $list = $list
                ->whereBetween('o.order_time', [$start, $end." 23:59:59"])
                ->groupBy('o.brand_type_code', DB::raw('DATE(o.order_time)'));
        }elseif ($dateType==="delivery") {
            $list = $list
                ->whereBetween('d.delivery_date', [$start, $end." 23:59:59"])
                ->groupBy('o.brand_type_code', DB::raw('DATE(d.delivery_date)'));
        }
        return $list -> get();
    }

    // 월 매출 데이터 (풀캘린더)
    protected static function vendor_month_sales($year, $month, $idx, $dateType) {
        return DB::table('total_information')
            -> select(
                DB::raw('SUM(order_count) as order_count'),
                DB::raw('SUM(order_amount) as order_amount')
            )
            -> where('year', $year)
            -> where('month', $month)
            -> where('mall_code', $idx)
            -> where('date_type', $dateType)
            -> first();
    }

    // 풀캘린더 선택 된 연월 찾기
    protected static function find_select_month($start, $end) {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);

        $firstDayOfMonth = $start->copy()->startOfMonth();
        // 시작 날짜부터 종료 날짜까지 월을 배열에 추가
        $months = [];
        $current = $start->copy();
        $selectedMonth = "";

        while ($current->lte($end)) {
            $months[] = $current->format('Y-m');  // "YYYY-MM" 형식으로 추가
            $current->addMonth();                 // 다음 월로 이동
        }

        if (count($months) === 2) {
            // 월 차이가 2개인 경우, 시작이 첫째 날 인 경우 첫번째 월이 선택 월
            if($start->equalTo($firstDayOfMonth)) {
                $selectedMonth = $months[0];
                // 월 차이가 2개인 경우, 시작이 첫째 날이 아닌 경우 두번째 월이 선택 월
            }else {
                $selectedMonth = $months[1];
            }
        } elseif (count($months) === 3) {
            // 월 차이가 3개인 경우, 가운데 월이 선택된 월
            $selectedMonth = $months[1];
        }

        [$year, $month] = explode('-', $selectedMonth);
        $year = (int)$year;
        $month = (int)$month;

        return [
            'year' => $year,
            'month' => $month,
        ];
    }
}