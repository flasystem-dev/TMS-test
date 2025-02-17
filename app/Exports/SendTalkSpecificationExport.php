<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\Vendor;

class SendTalkSpecificationExport implements FromView, ShouldAutoSize, WithDefaultStyles, WithColumnFormatting, WithStyles
{
    protected $vendors;

    public function __construct($vendors)
    {
        $this->vendors = $vendors;
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return $defaultStyle->getFont()->setName('맑은 고딕')->setSize(10);
    }

    public function styles(Worksheet $sheet)
    {
        $highestRow = $sheet->getHighestRow(); // 마지막 데이터가 있는 행 번호
        $highestColumn = $sheet->getHighestColumn(); // 마지막 데이터가 있는 열 문자 (A, B, C, ...)

        $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
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

        $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN, // 얇은 테두리
                    'color' => ['argb' => '000000'], // 검은색 테두리
                ],
            ],
        ]);
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function view(): View
    {
        $data['vendors'] = $this->vendors;

        return view('excel.send-talk-specification', $data);
    }
}
