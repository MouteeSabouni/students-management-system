<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LatestStudentsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [];
        $students = Student::where(['user_id' => auth()->id()])
            ->with(['class', 'section'])
            ->latest()
            ->limit(4)
            ->get();

        foreach ($students as $student) {
            $stats[] = (new Stat(
                "#{$student->id} — {$student->class->name} — {$student->section->name}",
                $student->name
            ))->description('Added ' . $student->created_at->diffForHumans())
            ->extraAttributes(['Class' => $student->class]);
        }
        return $stats;
    }
}
