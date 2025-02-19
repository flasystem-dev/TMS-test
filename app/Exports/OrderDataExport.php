<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\RemembersRowNumber;

use App\Models\Order\OrderData;
use App\Models\Order\OrderExcelDownload;

class OrderDataExport implements FromQuery, WithHeadings, WithMapping, WithChunkReading, ShouldAutoSize, WithColumnFormatting, WithDefaultStyles, WithStyles
{
    protected array $order_idx;
    private int $rowNumber = 0;
    private int $orderCount;


    public function __construct($order_idx)
    {
        $this->order_idx = $order_idx;
        $this->orderCount = count($order_idx);
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return $defaultStyle->getFont()->setName('맑은 고딕');
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER
        ];
    }

    public function query()
    {
        return OrderData::query()
            ->with('delivery', 'item', 'payments', 'vendor', 'pass')
            ->whereIn('order_idx', $this->order_idx);
    }

    public function headings(): array
    {
        return [
            [ $this->orderCount . " 건"],
            [
            '번호',
            '주문번호',
            '주문일시',
            '주문자ID',
            '회원구분',
            '사업자명',
            '주문자연락처1',
            '주문자연락처2',
            '배송상태',
            '상품명',
            '수량',
            '단가',
            '사용적립금',
            '적립적립금',
            '결제방법',
            '결제금액',
            '결제상태',
            '승인번호',
            '입금은행',
            '입금일자',
            '입금자명',
            '주문자명',
            '희망배송일',
            '받는사람',
            '받는사람연락처1',
            '받는사람연락처2',
            '받는사람주소',
            '메세지타입',
            '메세지',
            '요구사항',
            '관리자메모',
            '증빙서류(발행여부)',
            '수기주문',
            '발주여부',
            '사업자발주',
            '사업자옵션',
            '화원사발주',
            '화원사옵션',
            '경조사어',
            '보내는분',
                ]
        ];
    }

    public function map($order): array
    {
        $this->rowNumber++;

        $right = $order->delivery->delivery_ribbon_right ?? "";
        $left = !empty($order->delivery->delivery_ribbon_left) ? "(" . $order->delivery->delivery_ribbon_left . ")" : "";

        return [
            // 번호
            $this->rowNumber,
            // 주문번호
            $order->od_id,
            // 주문일시
            $order->order_time,
            // 주문자ID
            $order->orderer_mall_id ?? '',
            // 회원구분
            !empty($order->orderer_mall_id) ? "일반회원" : "비회원",
            // 사업자명
            $order->vendor->rep_name ?? '',
            // 주문자연락처1
            $order->orderer_phone,
            // 주문자연락처2
            $order->orderer_tel,
            // 배송상태
            CommonCodeName($order->delivery->delivery_state_code),
            // 상품명
            $order->delivery->goods_name,
            // 수량
            1,
            // 단가
            $order->item->item_total_amount,
            // 사용적립금
            0,
            // 적립적립금
            0,
            // 결제방법
            CommonCodeName($order->payment_type_code),
            // 결제금액
            $order->total_amount,
            // 결제상태
            CommonCodeName($order->payment_state_code),
            // 승인번호
            '',
            // 입금은행
            '',
            // 입금일자
            $order->payment_time,
            // 입금자명
            $order->payments->first()?->deposit_name ?? '',
            // 주문자명
            $order->orderer_name,
            // 희망배송일
            $order->delivery->delivery_date,
            // 받는사람
            $order->delivery->receiver_name,
            // 받는사람연락처1
            $order->delivery->receiver_phone,
            // 받는사람연락처2
            $order->delivery->receiver_tel,
            // 받는사람주소
            $order->delivery->delivery_address,
            // 메세지타입
            empty($order->delivery->delivery_card) ? "리본" : "카드",
            // 메세지
            empty($order->delivery->delivery_card) ? $right . $left : $order->delivery->delivery_card,
            // 요구사항
            $order->delivery_message,
            // 관리자메모
            $order->admin_memo,
            // 증빙서류(발행여부)
            '',
            // 수기주문
            $order->handler,
            // 발주여부
            $order->delivery->is_balju === 1 ? "발주" : "미발주",
            // 사업자발주
            $order->vendor_amount,
            // 사업자옵션
            $order->item->vendor_options_amount,
            // 화원사발주
            $order->balju_amount,
            // 화원사옵션
            $order->item->balju_options_amount,
            // 경조사어
            $order->delivery->delivery_ribbon_right ?? "",
            // 보내는분
            $order->delivery->delivery_ribbon_left ?? "",
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow(); // 마지막 데이터가 있는 행 번호
        $highestColumn = $sheet->getHighestColumn(); // 마지막 데이터가 있는 열 문자 (A, B, C, ...)

        $sheet->getStyle("A1:A1")->applyFromArray([
            'font' => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // 텍스트 중앙 정렬
                'vertical'  => Alignment::VERTICAL_CENTER,
            ]
        ]);

        $sheet->getStyle("A2:{$highestColumn}2")->applyFromArray([
            // 첫 번째 행(A1:Z1)에 스타일 적용
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'd1d1d1'], // 배경색을 설정
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // 텍스트 중앙 정렬
                'vertical'  => Alignment::VERTICAL_CENTER,
            ],
        ]);

        $sheet->getStyle("A2:{$highestColumn}{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, // 얇은 테두리
                    'color' => ['argb' => '000000'], // 검은색 테두리
                ],
            ],
        ]);
    }

    public function chunkSize(): int
    {
        return 2000;
    }
}
