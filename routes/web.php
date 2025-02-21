<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::middleware(['auth','auth-check'])->group(function () {

    Route::get('/', [App\Http\Controllers\HomeController::class, 'root']);

//orderController action
    Route::get('order/ecommerce_orders', [App\Http\Controllers\Order\OrderController::class, 'ecommerce_orders'])->name('index');

// Top 메뉴
    Route::post('/session/brand-session', [App\Http\Controllers\AjaxController::class, 'brandSession']);
    Route::get('/ajax/app-item', [App\Http\Controllers\AjaxController::class, 'appItemSelect']);
    Route::post('/fla/send-data', [App\Http\Controllers\FlaAppController::class, 'send-data']);
    Route::get('vendor/search', [App\Http\Controllers\Vendor\VendorController::class, 'find_vendor']);
    Route::get('vendor/search/orders', [App\Http\Controllers\Vendor\VendorController::class, 'recent_order_from_vendor']);

// 주문 폼 관련
    Route::get('order/{brand}/form/', [App\Http\Controllers\Order\OrderFormController::class, 'order_form'])->name('form-order');
    Route::post('order/form', [App\Http\Controllers\Order\OrderFormController::class, 'insert_order'])->name('form-order-insert');
    Route::get('order/form/msg-templates/{ctgy}', [App\Http\Controllers\Order\OrderFormController::class, 'get_CategoryList'])->name('ctgy-list');
    Route::get('order/form/products', [App\Http\Controllers\Order\OrderFormController::class, 'get_productList']);
    Route::post('order/form/product', [App\Http\Controllers\Order\OrderFormController::class, 'select_product']);
    Route::get('order/form/memo/{brand}', [App\Http\Controllers\Order\OrderFormController::class, 'get_memo'])->name('admin-memo');
    Route::post('order/form/memo', [App\Http\Controllers\Order\OrderFormController::class, 'insert_memo'])->name('memo-insert');
    Route::delete('order/form/memo', [App\Http\Controllers\Order\OrderFormController::class, 'delete_memo'])->name('memo-delete');
    Route::put('order/form/memo', [App\Http\Controllers\Order\OrderFormController::class, 'update_memo'])->name('memo-update');
    Route::get('order/form/ribbon', [App\Http\Controllers\Order\OrderFormController::class, 'previous_ribbon']);
    Route::post('order/pay/after', [App\Http\Controllers\Order\OrderFormController::class, 'order_pay_progress']);
    Route::get('order/form/users', [App\Http\Controllers\Order\OrderFormController::class, 'get_shop_user']);
    Route::get('order/form/location', [App\Http\Controllers\Order\OrderFormController::class, 'location_price']);
    Route::post('order/form/location', [App\Http\Controllers\Order\OrderFormController::class, 'add_locationOption']);

// 인트라넷
    Route::get('order/intranet/balju/{idx}', [App\Http\Controllers\Order\OrderBaljuController::class, 'balju_form']);
    Route::post('order/intranet/balju', [App\Http\Controllers\Order\OrderBaljuController::class, 'order_balju'])->name('order-intranet');

// 주문 리스트 - index
    Route::post('order/cancel', [App\Http\Controllers\Order\OrderController::class, 'cancel_progress'])->name('cancel-progress');
    Route::post('order/cancel-memo', [App\Http\Controllers\Order\OrderController::class, 'cancel_refuse'])->name('cancel-refuse');
    Route::post('Order/Cancel/Complete', [App\Http\Controllers\Order\OrderController::class, 'cancel_complete']);
    Route::get('order/Log/{od_id}', [App\Http\Controllers\Order\OrderController::class, 'log_view']);
    Route::get('order/Log', [App\Http\Controllers\Order\OrderController::class, 'order_log_check']);
    Route::post('order/Option/Add', [App\Http\Controllers\Order\OrderController::class, 'add_price_progress']);
    Route::delete('order/Option/Add/{idx}', [App\Http\Controllers\Order\OrderController::class, 'remove_addPay']);
    Route::patch('order/Option/memo', [App\Http\Controllers\Order\OrderController::class, 'change_pay_memo']);
    Route::post('order/operate/view', [App\Http\Controllers\Order\OrderController::class, 'remove_orders']);
    Route::get('order/set-perPage/', [App\Http\Controllers\Order\OrderController::class, 'set_perPage']);
    Route::get('order/delivery/state', [App\Http\Controllers\Order\OrderController::class, 'update_deli_state']);
    Route::post('order/operate/highlight/{state}', [App\Http\Controllers\Order\OrderController::class, 'highlight_orders']);
    Route::post('order/operate/deposit-complete', [App\Http\Controllers\Order\OrderController::class, 'deposit_completed']);
    Route::post('order/operate/batch-input', [App\Http\Controllers\Order\OrderController::class, 'batch_input']);

// 주문 상세 검색
    Route::get('order/detail-list/', [App\Http\Controllers\Order\OrderController::class, 'order_list_detail']);
    Route::post('order/detail-list/select-orders', [App\Http\Controllers\Order\OrderController::class, 'select_orders_view']);


// 주문 엑셀 다운로드
    Route::post('/order/excel/download/individual', [App\Http\Controllers\Excel\OrderExcelController::class, 'download_order_excel']);
    Route::view('/order/excel/download','order.popup.excel-batchDownload');
    Route::post('/order/excel/download/batch', [App\Http\Controllers\Excel\OrderExcelController::class, 'download_batch_orderExcel']);
    Route::get('/order/excel/files', [App\Http\Controllers\Excel\OrderExcelController::class, 'index']);
    Route::get('/order/excel/file/download/{id}', [App\Http\Controllers\Excel\OrderExcelController::class, 'download_file']);

// 주문 상세
    Route::get('order/order-detail/{order_idx}', [App\Http\Controllers\Order\OrderDetailController::class, 'order_detail']);
    Route::delete('/order/detail/state/{order_idx}', [App\Http\Controllers\Order\OrderDetailController::class, 'delete_order']);
    Route::post('order/order-update', [App\Http\Controllers\Order\OrderDetailController::class, 'order_update']);
    Route::post('order/detail/product', [App\Http\Controllers\Order\OrderDetailController::class, 'change_order_product']);
    Route::get('order/detail/vendor', [App\Http\Controllers\Order\OrderDetailController::class, 'get_vendors']);
    Route::post('order/detail/vendor', [App\Http\Controllers\Order\OrderDetailController::class, 'change_vendor']);
    Route::get('order/detail/state', [App\Http\Controllers\Order\OrderDetailController::class, 'change_payment_state']);
    Route::post('order/detail/payment', [App\Http\Controllers\Order\OrderDetailController::class, 'add_payment']);
    Route::post('order/detail/payment-type', [App\Http\Controllers\Order\OrderDetailController::class, 'change_payment_type']);
    Route::post('order/detail/payment-state', [App\Http\Controllers\Order\OrderDetailController::class, 'change_payment_state_code']);
    Route::post('order/detail/payment/data', [App\Http\Controllers\Order\OrderDetailController::class, 'update_payments']);
    Route::delete('order/detail/payment', [App\Http\Controllers\Order\OrderDetailController::class, 'delete_payment']);
    Route::get('order/detail/alim-talk', [App\Http\Controllers\Order\OrderDetailController::class, 'get_template_data']);
    Route::get('order/cancel/table', [App\Http\Controllers\Order\OrderDetailController::class, 'refund_table']);
    Route::post('order/vendor/balju', [App\Http\Controllers\Order\OrderDetailController::class, 'update_baljuAmount']);

// 미수현황
    Route::get('order/transaction/outstanding', [App\Http\Controllers\Order\OutstandingTransactionController::class, 'index']);

// 증빙서류 관련
    Route::get('Document/document-orders', [App\Http\Controllers\Document\DocumentController::class, 'get_orders']);
    Route::get('Document/cancel/bank', [App\Http\Controllers\Document\DocumentController::class, 'bank_code']);
    Route::get('Document/cancel/table', [App\Http\Controllers\Document\DocumentController::class, 'refund_table']);
    Route::get('Document/transaction-orders', [App\Http\Controllers\Document\TransactionController::class, 'transaction']);
    Route::post('Document/transaction/order/{idx}', [App\Http\Controllers\Document\TransactionController::class, 'update_order_data']);
    Route::get('Document/transaction/view', [App\Http\Controllers\Document\TransactionController::class, 'transaction_document']);
    Route::get('Document/transaction/Send', [App\Http\Controllers\Document\TransactionController::class, 'send_email']);

// 거래처
    Route::get('document/client/index', [App\Http\Controllers\Document\ClientController::class, 'index']);
    Route::get('document/client/client-form/{id}', [App\Http\Controllers\Document\ClientController::class, 'clientForm']);
    Route::post('document/client/client-form', [App\Http\Controllers\Document\ClientController::class, 'clientUpsert']);
    // 거래처 담당자
    Route::get('document/client/manager-form/{id}', [App\Http\Controllers\Document\ClientController::class, 'managerForm']);
    Route::post('document/client/manager-form', [App\Http\Controllers\Document\ClientController::class, 'managerUpsert']);
    Route::delete('document/client/manager/{id}', [App\Http\Controllers\Document\ClientController::class, 'deleteManager']);
    // 거래처 - 회원
    Route::get('document/client/user-form/{brand}', [App\Http\Controllers\Document\ClientController::class, 'userOption']);
    Route::post('document/client/user-form', [App\Http\Controllers\Document\ClientController::class, 'userRegister']);
    Route::patch('document/client/user/manager', [App\Http\Controllers\Document\ClientController::class, 'user_manager']);


// 토스페이먼츠
    Route::get('Tosspayment/Widget/{od_id}', [App\Http\Controllers\Payment\TosspaymentsController::class, 'get_widget']);
    Route::get('Tosspayment/Add/Widget/{idx}', [App\Http\Controllers\Payment\TosspaymentsController::class, 'add_pay_widget']);
    Route::post('Payment/Complain/toss', [App\Http\Controllers\Payment\TosspaymentsController::class, 'complain']);

// 나이스페이
    Route::any('Payment/Nice/Pay/{method}', [App\Http\Controllers\Payment\NicePayController::class, 'RouteHandelerFunc']);
    Route::post('Payment/Nice/refund', [App\Http\Controllers\Payment\NicePayController::class, 'payRefund']);

// 카카오톡 Route Mapping
    Route::view('/KakaoTalk', 'KakaoTalk.index');
    Route::any('/KakaoTalk/{APIName}', [App\Http\Controllers\Message\KakaoTalkController::class, 'RouteHandelerFunc']);

// 카카오톡 페이지
    Route::get('/KakaoTalk/Page/Templates', [App\Http\Controllers\Message\KaKaoTalkPageController::class, 'Send_Talk_Page']);
    Route::get('/KakaoTalk/Page/GetTemplateNameList', [App\Http\Controllers\Message\KaKaoTalkPageController::class, 'GetTemplateNameList']);
    Route::get('/KakaoTalk/Page/GetTemplate', [App\Http\Controllers\Message\KaKaoTalkPageController::class, 'GetTemplate']);
    Route::get('/KakaoTalk/Page/GetUsedTemplate', [App\Http\Controllers\Message\KaKaoTalkPageController::class, 'GetUsedTemplate']);
    Route::post('/KakaoTalk/Page/SetValues', [App\Http\Controllers\Message\KaKaoTalkPageController::class, 'SetValues']);
    Route::get('/KakaoTalk/Page/getColumnName', [App\Http\Controllers\Message\KaKaoTalkPageController::class, 'getColumnName']);
    Route::get('/KakaoTalk/Page/GetTemplateNameList ', [App\Http\Controllers\Message\KaKaoTalkPageController::class, 'GetTemplateNameList ']);
    Route::get('/KakaoTalk/Page/alimLog', [App\Http\Controllers\Message\KaKaoTalkPageController::class, 'AlimLog']);
    Route::get('/KakaoTalk/Page/roadAlimTalkTemplate', [App\Http\Controllers\Message\KaKaoTalkPageController::class, 'roadAlimTalkTemplate']);

// SMS
    Route::get('/SMS/index', [App\Http\Controllers\Message\SMSPageController::class, 'index']);
    Route::get('/SMS/form', [App\Http\Controllers\Message\SMSPageController::class, 'sms_form']);
    Route::post('SMS/custom/send', [App\Http\Controllers\Message\SMSController::class, 'sendSMS']);
    Route::get('SMS/log/message', [App\Http\Controllers\Message\SMSPageController::class, 'sms_message']);
    Route::get('SMS/memo/manage', [App\Http\Controllers\Message\SMSPageController::class, 'sms_memo']);
    Route::get('SMS/memo/manage/note', [App\Http\Controllers\Message\SMSPageController::class, 'get_sms_note']);
    Route::delete('SMS/memo/manage/note', [App\Http\Controllers\Message\SMSPageController::class, 'delete_sms_note']);
    Route::post('SMS/memo/manage/note', [App\Http\Controllers\Message\SMSPageController::class, 'upsert_sms_note']);
    Route::get('SMS/memo/form/note', [App\Http\Controllers\Message\SMSPageController::class, 'sms_form_memoList']);

// etc
    Route::get('util/kakaoMap', function () {
        return view('util.kakaoMap');
    })->name('KakaoMap');

// 플레이오토 2.0
//Route::get('/PlayAuto2/Shop/Code', [App\Http\Controllers\API\PlayAuto2APIController::class, 'get_shopCode']);
    Route::get('/PlayAuto2/Order/Auto', [App\Http\Controllers\API\PlayAuto2APIController::class, 'get_order']);
    Route::get('/PlayAuto2/Order/Auto2', [App\Http\Controllers\API\PlayAuto2APIController::class, 'send_data_TMS']);
    Route::get('/PlayAuto2/Order/Auto3', [App\Http\Controllers\API\PlayAuto2APIController::class, 'test_update']);
    Route::get('/PlayAuto2/Order/Auto4', [App\Http\Controllers\API\PlayAuto2APIController::class, 'get_orderToTMS']);
//Route::get('/PlayAuto2/Order/Auto', [App\Http\Controllers\API\PlayAuto2APIController::class, 'Synchronize_order']);
    Route::get('/PlayAuto2/Order/NR', [App\Http\Controllers\API\PlayAuto2APIController::class, 'setDeliveryState']);
    Route::get('order/playauto/delivery', [App\Http\Controllers\API\PlayAuto2APIController::class, 'resend_delivery']);
    Route::get('order/playauto/connect', [App\Http\Controllers\API\PlayAuto2APIController::class, 'reconnect_order']);

// 쇼핑몰
    Route::get('/shop/account', [App\Http\Controllers\Shop\OpenMarketController::class, 'Account_info']);
    Route::get('/shop/deliveryPrice', [App\Http\Controllers\Shop\ProductController::class, 'deliveryPrice']);
    Route::get('/shop/updateLocAddPrice', [App\Http\Controllers\Shop\ProductController::class, 'updateLocAddPrice']);

// 팝업, 배너
    Route::get('/shop/banners', [App\Http\Controllers\Shop\BannerPopupController::class, 'bannerList']);
    Route::get('/shop/bannerForm', [App\Http\Controllers\Shop\BannerPopupController::class, 'bannerForm']);
    Route::post('/shop/bannerSave', [App\Http\Controllers\Shop\BannerPopupController::class, 'bannerSave']);
    Route::get('/shop/popups', [App\Http\Controllers\Shop\BannerPopupController::class, 'popupList']);
    Route::get('/shop/popupForm', [App\Http\Controllers\Shop\BannerPopupController::class, 'popupForm']);
    Route::post('/shop/popupSave', [App\Http\Controllers\Shop\BannerPopupController::class, 'popupSave']);
    Route::get('/shop/vendors/{brand}', [App\Http\Controllers\Shop\BannerPopupController::class, 'get_vendors']);
    Route::patch('/shop/{type}/orderBy', [App\Http\Controllers\Shop\BannerPopupController::class, 'update_orderBy'])->where('type', 'banner|popup');
    Route::delete('/shop/{type}/{id}', [App\Http\Controllers\Shop\BannerPopupController::class, 'delete_model'])->where('type', 'banner|popup');
    Route::patch('/shop/{type}/use', [App\Http\Controllers\Shop\BannerPopupController::class, 'update_use'])->where('type', 'banner|popup');

// 상품
    // index
    Route::get('/shop/products', [App\Http\Controllers\Shop\ProductController::class, 'products_index']);
    Route::patch('/shop/products/isUsed/{idx}', [App\Http\Controllers\Shop\ProductController::class, 'change_isUsed']);
    Route::patch('/shop/product/state/{column}/{id}', [App\Http\Controllers\Shop\ProductController::class, 'change_state']);

    // form
    Route::get('/shop/product/{id}', [App\Http\Controllers\Shop\ProductController::class, 'product_form']);
    Route::post('/shop/product', [App\Http\Controllers\Shop\ProductController::class, 'upsert_product']);
    Route::get('/shop/product/check/duplicate-code', [App\Http\Controllers\Shop\ProductController::class, 'check_duplicate_code']);
    Route::post('/shop/product/upload/img', [App\Http\Controllers\Shop\ProductController::class, 'upload_file']);
    Route::post('/shop/product/function/search-word', [App\Http\Controllers\Shop\ProductController::class, 'insert_search_word']);
    Route::patch('/shop/product/function/search-word', [App\Http\Controllers\Shop\ProductController::class, 'edit_search_word']);
    Route::delete('/shop/product/function/search-word', [App\Http\Controllers\Shop\ProductController::class, 'delete_search_word']);
    Route::delete('/shop/product/{id}', [App\Http\Controllers\Shop\ProductController::class, 'remove_product']);

// Test
    Route::get('etc/test', [App\Http\Controllers\Test\TestController::class, 'test'])->name('test');
    Route::get('etc/test2', [App\Http\Controllers\Test\TestController::class, 'test2']);
    Route::get('etc/test/samga', [App\Http\Controllers\Test\TestController::class, 'samga_api_test']);
    Route::get('etc/test/test', [App\Http\Controllers\Test\TestController::class, 'bms_api_test']);
    Route::get('playauto/order', [App\Http\Controllers\Test\TestPlayAuto2APIController::class, 'get_orderToTMS']);
    Route::get('playauto/delivery', [App\Http\Controllers\Test\TestPlayAuto2APIController::class, 'setDeliveryState']);
    Route::get('playauto/delivery2', [App\Http\Controllers\Test\TestPlayAuto2APIController::class, 'set_delivery']);
    Route::get('playauto/auto', [App\Http\Controllers\Test\TestPlayAuto2APIController::class, 'get_order']);
    Route::get('order/test', [App\Http\Controllers\Test\TestOrderController::class, 'insert_order_test']);
    Route::post('ETC/Test', [App\Http\Controllers\Test\TestController::class, 'get_file']);
    Route::get('playauto/test', [App\Http\Controllers\API\PlayAuto2APIController::class, 'resend_delivery']);
    Route::get('etc/test/bmsv2-retrieve', [App\Http\Controllers\Test\TestController::class, 'BMSv2_retrieve_api']);
    Route::get('etc/test/bmsv2', [App\Http\Controllers\Test\TestController::class, 'bms2_api_test']);


// 사업자
    Route::get('vendor/fla-business-register', [App\Http\Controllers\Vendor\FlaBusinessController::class, 'flaBusinessRegister']);
    Route::get('vendor/fla-business-list', [App\Http\Controllers\Vendor\FlaBusinessController::class, 'flaBusinessList'])->name('vendor-list');
    Route::post('vendor/fla-business', [App\Http\Controllers\Vendor\FlaBusinessController::class, 'flaBusinessSave'])->name('save-vendor');
    Route::get('vendor/fla-business/view/{idx}', [App\Http\Controllers\Vendor\FlaBusinessController::class, 'flaBusinessView']);
    Route::get('vendor/check_id_dup', [App\Http\Controllers\Vendor\FlaBusinessController::class, 'checkIdDup'])->name('check_id_dup');
    Route::get('vendor/check_domain_dup', [App\Http\Controllers\Vendor\FlaBusinessController::class, 'checkDomainDup'])->name('check_domain_dup');
    Route::get('vendor/form/recommend', [App\Http\Controllers\Vendor\FlaBusinessController::class, 'get_vendor_list']);
    
// 사업자 정산
    Route::get('vendor/fla-cal-list', [App\Http\Controllers\Vendor\FlaCalculateController::class, 'flaCalList'])->name('cal-list');
    Route::get('vendor/fla-cal-list-test', [App\Http\Controllers\Vendor\FlaCalculateController::class, 'test_flaCalList']);

    Route::get('vendor/vendor-excel-example', [App\Http\Controllers\Vendor\FlaCalculateController::class, 'vendorExcelDownload'])->name('vendor-excel-example');
    Route::get('vendor/vendor-excel-alim', [App\Http\Controllers\Vendor\FlaCalculateController::class, 'vendorAlimExcelDownload'])->name('vendor-excel-alim');
    Route::post('vendor/monthly-etc-upload', [App\Http\Controllers\Vendor\FlaCalculateController::class, 'monthlyEtcPriceUpload'])->name('monthly-etc-upload');
    Route::post('vendor/monthly-specification', [App\Http\Controllers\Vendor\FlaCalculateController::class, 'monthlySpecification'])->name('monthly-specification');
    Route::post('vendor/vendor-excel-calcAmount', [App\Http\Controllers\Vendor\FlaCalculateController::class, 'calcAmount_ExcelDownload']);
    Route::post('vendor/calculate/card', [App\Http\Controllers\Vendor\FlaCalculateController::class, 'calc_card_amount']);
    Route::post('vendor/calculate/card-individual', [App\Http\Controllers\Vendor\FlaCalculateController::class, 'calc_cardAmount_individual']);
    Route::get('vendor/statistics/index', [App\Http\Controllers\Statistics\VendorSalesController::class, 'index']);

// 명세서
    Route::get('vendor/specification-form/{sp_id}', [App\Http\Controllers\Vendor\SpecificationController::class, 'specificationForm'])->name('specification-form');
    Route::get('vendor/specification-form/edit/{sp_id}', [App\Http\Controllers\Vendor\SpecificationController::class, 'specificationForm_edit']);
    Route::get('vendor/specification/send', [App\Http\Controllers\Vendor\SpecificationController::class, 'specification_send_list']);
    Route::post('vendor/specification/send/email', [App\Http\Controllers\Vendor\SpecificationController::class, 'send_email']);
    Route::get('vendor/specification/send/talk/excel', [App\Http\Controllers\Vendor\SpecificationController::class, 'specification_send_talkExcel']);
    Route::delete('vendor/specification/id', [App\Http\Controllers\Vendor\SpecificationController::class, 'delete_specification']);
    Route::post('vendor/specification-form/edit', [App\Http\Controllers\Vendor\SpecificationController::class, 'update_specification']);

// PASS 사업자
    Route::get('pass/index', [App\Http\Controllers\Pass\VendorPassController::class, 'index']);
    Route::get('pass/pass-form/{id}', [App\Http\Controllers\Pass\VendorPassController::class, 'passForm']);
    Route::post('pass/pass-form', [App\Http\Controllers\Pass\VendorPassController::class, 'passUpsert']);
    Route::post('pass/simple/status', [App\Http\Controllers\Pass\VendorPassController::class, 'simple_update']);
    Route::get('pass/check/domain', [App\Http\Controllers\Pass\VendorPassController::class, 'checkDomain']);

// 게시판
    Route::get('Board/Notice', [App\Http\Controllers\Board\NotificationController::class, 'notice']);
    Route::get('Board/Notification', [App\Http\Controllers\Board\NotificationController::class, 'notification'])->name('notification');
    Route::get('Board/Notification/{noti_id}', [App\Http\Controllers\Board\NotificationController::class, 'checkIt_notification']);

    Route::get('Board/faq-list', [App\Http\Controllers\Board\faqController::class, 'faqList']);
    Route::get('Board/faq-form', [App\Http\Controllers\Board\faqController::class, 'faqForm']);
    Route::post('Board/faq-save', [App\Http\Controllers\Board\faqController::class, 'faqSave']);

    Route::get('Board/board/{board_type}', [App\Http\Controllers\Board\BoardController::class, 'boardList']);
    Route::get('Board/boardForm/{board_type}/{id}', [App\Http\Controllers\Board\BoardController::class, 'boardForm'])->name('boardForm');
    Route::post('Board/board-save', [App\Http\Controllers\Board\BoardController::class, 'boardSave']);

// 유저
    Route::get('user/index', [App\Http\Controllers\User\UserController::class, 'index'])->name('user-list');
    Route::get('user/user-form/{id}', [App\Http\Controllers\User\UserController::class, 'userForm']);
    Route::post('user/user-form', [App\Http\Controllers\User\UserController::class, 'userSaveOrUpdate']);
    Route::post('user/simple/status', [App\Http\Controllers\User\UserController::class, 'simple_update_user_data']);

// 통계
    Route::get('statistics/brand/index', [App\Http\Controllers\Statistics\BrandSalesController::class, 'index']);
    Route::get('statistics/vendor/index', [App\Http\Controllers\Statistics\VendorSalesController::class, 'index']);
    Route::get('statistics/vendor/specifications/{idx}', [App\Http\Controllers\Statistics\VendorSalesController::class, 'vendor_specification_list']);
    Route::get('statistics/vendor/recommend', [App\Http\Controllers\Statistics\VendorSalesController::class, 'recommendPerson_list']);
    Route::get('statistics/vendor/calendar/{idx}', [App\Http\Controllers\Statistics\VendorSalesController::class, 'vendor_salesCalendar']);

// 개발용
    Route::get('dev/index', [App\Http\Controllers\Dev\DevController::class, 'index']);
    Route::get('dev/update', [App\Http\Controllers\Dev\DevController::class, 'user_require_reload']) -> middleware('restrict-ip');
    Route::post('dev/orderPayment', [App\Http\Controllers\Dev\DevController::class, 'orderPayment']);
    Route::post('dev/user', [App\Http\Controllers\Dev\DevController::class, 'user']);
    Route::post('dev/vendor', [App\Http\Controllers\Dev\DevController::class, 'vendor']);
    Route::get('dev/statistics/url', [App\Http\Controllers\Dev\DevController::class, 'statistics_url']);
});

Route::view('/error/403', 'pages-403')->name('403');
Route::view('/error/404', 'errors.404')->name('404');

// 게시판 다운로드
Route::get('Board/downloadFile', [App\Http\Controllers\Board\BoardController::class, 'downloadFile']);

// 배송사진 확인
Route::get('delivery/photo/{id}', [App\Http\Controllers\Order\DeliveryController::class, 'delivery_photo']);
Route::get('/order/delivery/photo/{id}', [App\Http\Controllers\Order\DeliveryController::class, 'delivery_photo_url']);