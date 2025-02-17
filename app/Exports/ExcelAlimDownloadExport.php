<?php

namespace App\Exports;

use App\Models\Vendor;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelDownloadExport implements FromCollection, WithHeadings, WithMapping,WithStyles, WithColumnWidths{
    /**
    * @return \Illuminate\Support\Collection
    */

    protected $brand;

    public function __construct($brand,$year,$month)
    {
        $this->brand = $brand;
        $this->year = $year;
        $this->month = $month;
    }
    public function collection()
    {
        $today = date("Y-m-d",time());

        return Vendor::join('specification', 'vendor.idx', '=', 'specification.mall_code')->get();
        
    }

    public function headings(): array
    {
        return [
            '수신자 번호', '#{연도}', '#{월}', '#{입금일}'
        ]; //컬럼 이름을 원하는 대로 변경하십시오.
    }
    public function map($vendor): array
    {
        return [
            $vendor->rep_tel,
            $vendor->year,
            $vendor->month,
            $vendor->deposit_date
        ];
    }
    public function styles(Worksheet $sheet)
    {
        return [
            // 헤더 스타일 설정 (A1부터 H1까지)
            1    => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ]
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 10,
            'C' => 10,
            'D' => 10,
        ];
    }
}
