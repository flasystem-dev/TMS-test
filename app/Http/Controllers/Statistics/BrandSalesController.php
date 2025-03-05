<?php

namespace App\Http\Controllers\Statistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Services\Statistics\BrandService;

use App\Charts\SalesChart;

class BrandSalesController extends Controller
{
    // 브랜드 매출 페이지
    public function index(Request $request) {

        $brands = ['BTCP', 'BTCC', 'BTSP', 'BTBR', 'BTOM', 'BTCS', 'BTFC'];

        // 차트 데이터
        $chart = new SalesChart;
        $month_name = array();

        for($i = 1; $i <= 12; $i++) {
            $month_name[] = $i . "월";
        }

        $api = url("/api/statistics/brand/sales/chart-data");

        $chart->labels($month_name);
        $chart->load($api);

        $data['brands'] = $brands;
        $data['chart'] = $chart;

        return view('statistics.brand-sales', $data);
    }

    // 풀캘린더 이벤트 데이터 보내기 API
    public static function sales_calendar_data(Request $request) {
        $start = $request->start;
        $end = $request->end;
        $brand = $request -> brand;
        $dateType = $request -> dateType;

        $select_month = self::find_select_month($start, $end);
        $year = $select_month['year'];
        $month = $select_month['month'];

        $startDate = Carbon::create($year, $month, 1)->format('Y-m-d');
        $endDate = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');

        $list = BrandService::day_sales($startDate, $endDate, $brand, $dateType);
        $sales_data = BrandService::month_sales($year, $month, $brand, $dateType);


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

        $response = [
            'events' => $events,
            'month_sales' => [
                'order_count' => $sales_data -> order_count ?? 0,
                'order_amount' => $sales_data -> order_amount ?? 0,
            ]
        ];

        return response()->json($response);
    }

    // 데이터테이블 매출 데이터
    public static function dateType_sales_data(Request $request) {
        $dateType = $request -> dateType;

        $customOrderBy = ['BTCP', 'BTCC', 'BTSP', 'BTBR', 'BTOM', 'BTCS', 'BTFC'];

        // 당일 매출
        $list = BrandService::today_sales($dateType);

        // 당월 매출
        $list2 = BrandService::monthly_sales($dateType);

        // 금년 매출
        $list3 = BrandService::yearly_sales($dateType);

        // 작년 매출
        $list4 = BrandService::yearAgo_sales($dateType);

        $list = collect($list)->keyBy('brand');
        $list2 = collect($list2)->keyBy('brand');
        $list3 = collect($list3)->keyBy('brand');
        $list4 = collect($list4)->keyBy('brand');

        // 매출 데이터테이블
        $sales_data = array();
        foreach ($customOrderBy as $brand) {
            $data = [];
            $data['brand']         = CommonCodeName($brand);
            $data['today_cnt']     = isset($list[$brand]->order_count)   ? number_format($list[$brand]->order_count)  : 0;
            $data['today_sales']   = isset($list[$brand]->order_amount)  ? number_format($list[$brand]->order_amount) : 0;
            $data['monthly_cnt']   = isset($list2[$brand]->order_count)  ? number_format($list2[$brand]->order_count) : 0;
            $data['monthly_sales'] = isset($list2[$brand]->order_amount) ? number_format($list2[$brand]->order_amount) : 0;
            $data['yearly_cnt']    = isset($list3[$brand]->order_count)  ? number_format($list3[$brand]->order_count) : 0;
            $data['yearly_sales']  = isset($list3[$brand]->order_amount) ? number_format($list3[$brand]->order_amount) : 0;
            $data['yearAgo_cnt']   = isset($list4[$brand]->order_count)  ? number_format($list4[$brand]->order_count) : 0;
            $data['yearAgo_sales'] = isset($list4[$brand]->order_amount) ? number_format($list4[$brand]->order_amount) : 0;
            $sales_data[] = $data;
        }
        return response()->json($sales_data);
    }

    // 차트 데이터 가져오기
    public static function update_chartData(Request $request) {
        $brand = $request -> brand ?? "BTCP";
        $year = $request -> year ?? date('Y');
        $dateType = $request -> dateType ?? 'order';

        $brand_sales = BrandService::monthly_sales_brand($brand, $year, $dateType);

        $list = collect($brand_sales)->keyBy('month');

        $month_sales = [];
        for($i = 1; $i <= 12; $i++) {
            $month_sales[] = (int) ($list[$i]->order_amount ?? 0);
        }

        $chart = new SalesChart;
        $chart -> dataset('매출', 'line' , $month_sales)->options([
            'pointRadius' => 5,
            'pointHoverRadius' => 7,
            'pointHitRadius' => 50,
            'borderWidth' => 2,
            'borderColor' => '#007bff',
            'backgroundColor' => 'rgba(0, 123, 255, 0.1)',
        ]);
        return $chart -> api();
    }

########################################################################################################################

    // 풀캘린더 선택 된 연월 찾기
    public static function find_select_month($start, $end) {
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


