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

    public function download_file($id)
    {

        $fileName = DB::table('order_excel_download') -> where('id', $id) -> value('file_name');
        $filePath = "excel/order/{$fileName}";

        if (!Storage::exists($filePath)) {
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

        $now = Carbon::now() -> format('ymdHis');
        $fileName = "order_".$now.".xlsx";
        $filePath = "order/".$fileName;

        $download = OrderExcelDownload::create([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'status' => 'processing',
            'requester' => Auth::user()->name
        ]);

        // 백그라운드 큐 실행
        OrderExportJob::dispatch($order_idx, $fileName, $filePath, $download->id);

        return response()->json([
            'message' => '파일 생성이 시작되었습니다. 다운로드 내역에서 확인하세요.',
        ]);
    }
}
