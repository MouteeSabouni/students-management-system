<?php

namespace App\Filament\Widgets;

use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClassesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $classes = Classes::with('students', 'sections')->get();

        foreach ($classes as $class) {
            $stats[] = Stat::make('Students: ' . $class->students()->count() . ' — ' . 'Sections: ' . $class->sections()->count(), $class->name);
        }

        return $stats;
    }
}
