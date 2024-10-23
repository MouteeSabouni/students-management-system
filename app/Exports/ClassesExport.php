<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ClassesExport implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;

    public function __construct(public Collection $rows)
    {
        //
    }

    public function collection(): Collection
    {
        return $this->rows;
    }


    public function map($class): array
    {
        return [
            $class->name,
            $class->sections->pluck('name')->implode(', '),
            $class->students->count(),
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Sections',
            'No. of Students',
        ];
    }
}

