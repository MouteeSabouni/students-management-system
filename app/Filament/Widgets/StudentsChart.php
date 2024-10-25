<?php

namespace App\Filament\Widgets;

use App\Models\Student;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class StudentsChart extends ChartWidget
{
    protected static ?string $heading = 'Students Chart';

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $data = Trend::model(Student::class)
            ->between(
                start: now()->subDays(10),
                end: now(),
            )
            ->perDay()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Students',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
