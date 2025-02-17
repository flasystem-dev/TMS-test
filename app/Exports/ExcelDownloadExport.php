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

    public function __construct($brand)
    {
        $this->brand = $brand;
    }
    public function collection()
    {
        $today = date("Y-m-d",time());

        return Vendor::select('idx', 'vendor_id', 'rep_name')->where('brand_type',$this->brand)->where('is_valid','Y')->get();
        
    }

    public function headings(): array
    {
        return [
            '몰번호', '아이디', '대표자명','기타(1)','기타(2)','기타(3)'
        ]; //컬럼 이름을 원하는 대로 변경하십시오.
    }
    public function map($vendor): array
    {
        return [
            $vendor->idx,
            $vendor->vendor_id,
            $vendor->rep_name,
//            '0', // 카드수수료에 대한 값, 예시로 빈 값
//            '0', // 당월주문미입금액에 대한 값, 예시로 빈 값
            '0', // 기타(1)에 대한 값, 예시로 빈 값
            '0', // 기타(2)에 대한 값, 예시로 빈 값
            '0', // 기타(3)에 대한 값, 예시로 빈 값
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
            'A' => 15, // 몰번호
            'B' => 20, // 상점명
            'C' => 20, // 대표자명
//            'D' => 20, // 카드수수료
//            'E' => 25, // 당월주문미입금액
            'F' => 15, // 기타(1)
            'G' => 15, // 기타(2)
            'H' => 15, // 기타(3)
        ];
    }
}
