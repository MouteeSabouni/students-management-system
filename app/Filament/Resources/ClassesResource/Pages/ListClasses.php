<?php

namespace App\Filament\Resources\ClassesResource\Pages;

use App\Filament\Resources\ClassesResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListClasses extends ListRecords
{
    protected static string $resource = ClassesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All classes'),
            'active' => Tab::make('Classes with no sections')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDoesntHave('sections')),
            'inactive' => Tab::make('Classes with no students')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDoesntHave('students')),
        ];
    }

}
