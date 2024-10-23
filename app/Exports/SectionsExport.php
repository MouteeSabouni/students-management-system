<?php

namespace App\Exports;

use App\Models\Section;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SectionsExport implements FromCollection, WithMapping, WithHeadings
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


    public function map($section): array
    {
        return [
            $section->name,
            $section->class->name,
            $section->students->count(),
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Class',
            'No. of Students',
        ];
    }
}
