<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\API\PlayAuto2APIController;

class PlayAutoOrders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 180;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $playauto2 = new PlayAuto2APIController;

        $playauto2->get_order();            // 쇼핑몰 -> 플레이오토 주문 가져오기
        sleep(1);
        $playauto2->Synchronize_order();    // 쇼핑몰 -> 플레이오토 주문 동기화
        sleep(1);
        $playauto2->send_data_TMS();        // 플레이오토 -> TMS 신규, 취소 주문 정보 보내기
        sleep(1);
        $playauto2->send_cancelData_TMS();  // 플레이오토 -> TMS 취소완료 주문 정보 보내기
        sleep(1);
        $playauto2->get_CS();               // 쇼핑몰 -> 플레이오토 문의 가져오기
    }
}
