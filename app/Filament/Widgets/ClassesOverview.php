<?php

namespace App\Filament\Widgets;

use App\Models\Classes;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClassesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [];
        $classes = Classes::with('students', 'sections')->latest()->limit(4)->get();

        foreach ($classes as $class) {
            $studentsCount = $class->students->count();
            $sectionsCount = $class->sections->count();

            $stats[] = (new Stat(
                'Students: ' . $studentsCount . ' — ' . 'Sections: ' . $sectionsCount,
                $class->name
            ))->description('Added ' . $class->created_at->diffForHumans());
        }
            return $stats;
    }
}
