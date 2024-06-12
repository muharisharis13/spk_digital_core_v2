<?php

namespace App\Exports;

use App\Models\Unit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UnitExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Unit::with(["motor", "color"])
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Motor',
            'Color',
            'Engine',
            'Frame',
            "Status"
        ];
    }

    /**
     * @param mixed $unit
     *
     * @return array
     */
    public function map($unit): array
    {
        return [
            $unit->motor->motor_name, // Atur sesuai dengan atribut motor yang relevan
            $unit->color->color_name, // Atur sesuai dengan atribut color yang relevan
            $unit->unit_engine,
            $unit->unit_frame,
            $unit->unit_status,
        ];
    }
}
