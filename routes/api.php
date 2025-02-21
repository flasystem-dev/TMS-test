<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// PlayAuto API
//Route::get('/PlayAuto/Order', [App\Http\Controllers\API\PlayAutoAPIController::class, 'getData']);
//Route::get('/PlayAuto/Cancel', [App\Http\Controllers\API\PlayAutoAPIController::class, 'getCancelData']);
//Route::get('/PlayAuto/Update', [App\Http\Controllers\API\PlayAutoAPIController::class, 'updateMallCode']);

//Route::get('/test', [App\Http\Controllers\API\PlayAutoAPIController::class, 'getDeliveryCode']);
Route::post('/PlayAuto/test', [App\Http\Controllers\API\PlayAuto2APIController::class, 'set_api_result']);
Route::get('/PlayAuto/Account/Check', [App\Http\Controllers\Shop\OpenMarketController::class, 'check_account_info']);
Route::post('/PlayAuto/Account/Info', [App\Http\Controllers\Shop\OpenMarketController::class, 'update_account_info']);
Route::post('/PlayAuto/Account/Memo', [App\Http\Controllers\Shop\OpenMarketController::class, 'set_admin_memo']);


// Newrun API
Route::get('/Newrun/Order', [App\Http\Controllers\API\NewrunAPIController::class, 'sendOrderToNewrun']) -> name('NewrunAPI');
Route::post('/Newrun/Delivery', [App\Http\Controllers\API\NewrunAPIController::class, 'getDeliveryAPI']) -> name('NewrunDeliveryAPI');
Route::post('Newrun/Goods', [App\Http\Controllers\API\NewrunAPIController::class , 'getGoodsList']);
Route::post('Newrun/GoodsCTGY', [App\Http\Controllers\API\NewrunAPIController::class , 'updateGoodsCTRY']);

Route::get('Newrun/test', [App\Http\Controllers\API\NewrunAPIController::class , 'test']);

// Tosspayments API
Route::get('/Tosspayments/Success', [App\Http\Controllers\Payment\TosspaymentsController::class, 'success']) -> name('toss-success');
Route::post('/Tosspayments/Webhook', [App\Http\Controllers\Payment\TosspaymentsController::class, 'webhook']);
//Route::post('/Tosspayments/Complain', [App\Http\Controllers\Payment\TosspaymentsController::class, 'complain']) -> name('toss-complain');
Route::post('/Tosspayments/CashReceipt', [App\Http\Controllers\Payment\TosspaymentsController::class, 'cashReceipt']) -> name('toss-cashReceipt');

// NicePay API
Route::post('/payments/check/nicePay', [App\Http\Controllers\Payment\NicePayController::class, 'webhook']);

// PopBill API
Route::get('/PopBill/Talk', [App\Http\Controllers\API\PopBillController::class , 'SendATS']);

// Google Test API
Route::get('/google-test', [App\Http\Controllers\API\GoogleBardController::class , 'bard_api']);

// Order API
Route::post('/TMS/Order', [App\Http\Controllers\API\OrderApiController::class , 'OrderSaveApi']);

// 카카오톡
Route::post('/KakaoTalk/ATS-Log', [App\Http\Controllers\Message\KaKaoTalkPageController::class, 'SendATSLog']); // 로그 남기기
Route::post('/KakaoTalk/SendATS', [App\Http\Controllers\Message\KakaoTalkController::class, 'SendATS_one']) -> name('KakaoTalk-Send'); // 알림톡 단건 보내기

// Fla-app
Route::post('/App/Goods-list',[App\Http\Controllers\API\FlaAppController::class, 'GoodsList']);
Route::post('/App/Send-Link',[App\Http\Controllers\API\FlaAppController::class, 'send_data']);

// FlaChain
Route::post('/flaChain/Vendor/Upload',[App\Http\Controllers\Vendor\FlaBusinessController::class, 'upload_file']) -> name('Vendor-Banner');

// Board
Route::post('/Board/Notification/Check', [App\Http\Controllers\Board\NotificationController::class, 'check_notification']) -> name('Notification-Check');

// Shop
Route::post('/Shop/fileUpload', [App\Http\Controllers\fileUploadController::class, 'fileUpload']);
Route::get('/Shop/Ctgy', [App\Http\Controllers\Shop\ProductController::class, 'get_category']);
Route::patch('/Shop/Product', [App\Http\Controllers\Shop\ProductController::class, 'simple_update_product']);
Route::post('/Shop/Product/Excel', [App\Http\Controllers\Shop\ProductController::class, 'excelFile_upload']);
Route::post('/Shop/Account/Check', [App\Http\Controllers\Auth\AccountInfoController::class, 'check_admin_pw']);

Route::get('/Shop/faqDel/{id}', [App\Http\Controllers\Board\faqController::class, 'faqDelete']);
Route::get('/Shop/boardDel/{id}', [App\Http\Controllers\Board\boardController::class, 'boardDelete']);

// Test
Route::post('ETC/Test', [App\Http\Controllers\Test\TestController::class, 'get_file']);
Route::get('test/bmsv2', [App\Http\Controllers\Test\TestController::class, 'BMSv2_response_api']);
Route::post('test/bmsv2/delivery', [App\Http\Controllers\Test\TestController::class, 'test_api_data']);
Route::post('test/api', [App\Http\Controllers\Test\TestController::class, 'get_api']);


// Document
Route::get('Document/transaction/view', [App\Http\Controllers\Document\TransactionController::class, 'transaction_document']) -> name('transaction-view');
Route::post('Document/transaction/Send', [App\Http\Controllers\Document\TransactionController::class, 'send_email']);

// 인트라넷
Route::post('order/intranet/delivery', [App\Http\Controllers\API\IntranetController::class, 'delivery_return']);
Route::post('order/form-balju/shop', [App\Http\Controllers\API\IntranetController::class, 'receive_shop_data']);

// 통계
Route::GET('/statistics/brand/sales/calendar-data', [App\Http\Controllers\Statistics\BrandSalesController::class, 'sales_calendar_data']);
Route::GET('/statistics/brand/sales/table-data', [App\Http\Controllers\Statistics\BrandSalesController::class, 'dateType_sales_data']);
Route::GET('/statistics/brand/sales/chart-data', [App\Http\Controllers\Statistics\BrandSalesController::class, 'update_chartData']);
Route::GET('/statistics/vendor/sales/calendar-data', [App\Http\Controllers\Statistics\VendorSalesController::class, 'vendor_sales_calender_api']);

// 벤더
Route::post('/vendor/info/check', [App\Http\Controllers\Vendor\FlaBusinessController::class, 'check_rrNumber']);
Route::post('/vendor/file/check', [App\Http\Controllers\Vendor\FlaBusinessController::class, 'check_file']);