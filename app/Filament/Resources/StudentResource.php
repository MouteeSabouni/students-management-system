<?php

namespace App\Filament\Resources;

use App\Exports\StudentsExport;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->autofocus()->required(),
                TextInput::make('email')->required()->unique(),
                Select::make('class_id')->relationship('class', 'name')->live()->required(),
                Select::make('section_id')
                    ->options(function (Forms\Get $get) {
                        $classId = $get('class_id');

                        if($classId) return Section::where('class_id', $classId)->pluck('name', 'id');
                    })
                    ->required(),
                TextInput::make('password')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable()->sortable(),
                TextColumn::make('class.name')->badge()->sortable(),
                TextColumn::make('section.name')->badge(),
                TextColumn::make('created_at')->label('Join Date')->sortable(),
            ])
            ->persistSortInSession()
            ->filters([
                Filter::make('students-filter')
                ->form([
                    Select::make('class_id')
                        ->label('Filter by Class')
                        ->placeholder('Select a class')
                        ->options(Classes::pluck('name', 'id'))
                        ->afterStateUpdated(fn (callable $set) => $set('section_id', null)),
                    Select::make('section_id')
                        ->label('Filter by Section')
                        ->placeholder('Select a section')
                        ->options(function (Forms\Get $get) {
                            $classId = $get('class_id');

                            return Section::where('class_id', $classId)->pluck('name', 'id');
                        })
                ])
                ->query(function (Builder $query, array $data): Builder {
                    $query = $data['class_id'] ? $query->where('class_id', $data['class_id']) : $query;

                    return $data['section_id'] ? $query->where('section_id', $data['section_id']) : $query;
                })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('export')
                        ->label('Export to Excel')
                        ->icon('heroicon-o-arrow-up-on-square-stack')
                        ->action(function (Collection $rows) {
                            return Excel::download(new StudentsExport($rows), 'students.xlsx');
                    })
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
