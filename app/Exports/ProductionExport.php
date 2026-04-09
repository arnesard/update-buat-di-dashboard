<?php

namespace App\Exports;

use App\Models\Reception;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProductionExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $receptions;

    public function __construct($receptions)
    {
        $this->receptions = $receptions;
    }

    public function collection()
    {
        return $this->receptions->map(function ($reception) {
            return [
                $reception->date->format('d/m/Y'),
                $reception->created_at->format('H:i'),
                optional($reception->employee)->plant ?? '-',
                optional($reception->employee)->name ?? 'Unknown',
                optional($reception->employee)->group ?? '-',
                'Shift ' . $reception->shift,
                $reception->job_today ?? '-',
                optional($reception->employee)->position ?? '-',
                $reception->ritase_result ?? '-',
                $reception->production_count,
                $reception->notes ?? '-'
            ];
        });
    }

    public function title(): string
    {
        return 'Data Produksi';
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Waktu Check In',
            'Plant',
            'Operator',
            'Group',
            'Shift',
            'Jenis Pekerjaan',
            'Status',
            'Ritase',
            'Jumlah Produksi',
            'Catatan'
        ];
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
                    'startColor' => ['rgb' => '3b82f6'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,
            'B' => 15,
            'C' => 8,
            'D' => 20,
            'E' => 8,
            'F' => 10,
            'G' => 15,
            'H' => 15,
            'I' => 10,
            'J' => 15,
            'K' => 30,
        ];
    }
}
