<?php

namespace App\Http\Controllers\Excel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use App\Models\Order\OrderData;
use App\Exports\OrderExport;
use Carbon\Carbon;
use Hashids\Hashids;

use App\Services\Order\OrderIndexService;
use App\Jobs\OrderExportJob;

use App\Models\User;
use App\Models\Order\OrderExcelDownload;


class OrderExcelController extends Controller
{

    public function index()
    {
        $data['files'] = OrderExcelDownload::orderBy('updated_at', 'desc') -> get();

        return view('excel.order.index', $data);
    }

    public function download_file($encode_id)
    {
        $decode_id = (new Hashids('flasystem-dev'))->decode($encode_id)[0] ?? null;

        $fileName = DB::table('order_excel_download') -> where('id', $decode_id) -> value('file_name') ?? "";
        $filePath = "excel/order/{$fileName}";

        if (empty($fileName) || !Storage::exists($filePath)) {
            abort(404, '파일을 찾을 수 없습니다.');
        }

        return Storage::download($filePath);
    }

    public function download_order_excel(Request $request) {
        $order_idx = $request -> order_idx;

        $now = Carbon::now() -> format('y-m-d');
        $file_name = "order_".$now.".xls";

        return Excel::download(new OrderExport($order_idx), $file_name,  \Maatwebsite\Excel\Excel::XLSX, [
            'Content-Type' => 'application/octet-stream'
        ]);
    }

    public function download_batch_orderExcel(Request $request) {
        $search = $request -> all();
        $order_idx = OrderIndexService::order_bulk_excelDownload($search);

        count($order_idx);

        $brand = BrandAbbr($search['excel_brand']);
        $now = Carbon::now();
        $formattedTime = $now -> format('ymdHis'). floor($now->millisecond / 100);
        $fileName = $brand."_".$formattedTime.".xlsx";
        $filePath = "order/".$fileName;

        $download = OrderExcelDownload::create([
            'file_name' => $fileName,
            'status' => 'processing',
            'requester' => Auth::user()->name
        ]);

        $download_id = (new Hashids('flasystem-dev'))->encode($download->id);
        $fileUrl = url('order/excel/file/download/') . "/{$download_id}";

        OrderExcelDownload::where('id', $download->id) -> update(['file_url' => $fileUrl ]);

        // 백그라운드 큐 실행
        OrderExportJob::dispatch($order_idx, $fileName, $filePath, $download->id);

        return response()->json([
            'message' => '파일 생성이 시작되었습니다. 다운로드 내역에서 확인하세요.',
        ]);
    }
}
