<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class OvertimeExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $overtimes;

    public function __construct($overtimes)
    {
        $this->overtimes = $overtimes;
    }

    public function collection()
    {
        return $this->overtimes->map(function ($ot) {
            $start = Carbon::parse($ot->start_time);
            $end = Carbon::parse($ot->end_time);
            if ($end->lt($start)) $end->addDay();
            $gross = $start->diffInHours($end);
            $final = min(7, $gross);

            return [
                Carbon::parse($ot->overtime_date)->format('d/m/Y'),
                $ot->employee_name,
                $ot->start_time,
                $ot->end_time,
                $final . ' Jam',
                ucfirst($ot->status),
                $ot->notes ?? '-'
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Operator',
            'Jam Mulai',
            'Jam Selesai',
            'Durasi (Capped 7h)',
            'Status',
            'Keterangan'
        ];
    }

    public function title(): string
    {
        return 'Data Lembur';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '10b981'], // Emerald-500 (Excel Green)
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 12,
            'D' => 12,
            'E' => 20,
            'F' => 15,
            'G' => 30,
        ];
    }
}
