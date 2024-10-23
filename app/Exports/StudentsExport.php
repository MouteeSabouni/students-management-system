<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StudentsExport implements FromCollection, WithMapping, WithHeadings
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


    public function map($student): array
    {
        return [
            $student->name,
            $student->email,
            $student->class->name,
            $student->section->name,
            $student->created_at,
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Class',
            'Section',
            'Join Date',
        ];
    }
}
