<?php

namespace App\Filament\Resources;

use App\Exports\StudentsExport;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Maatwebsite\Excel\Facades\Excel;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Academics';

    public static function getNavigationBadge(): ?string
    {
        return static::$model::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->autofocus()->required(),
                TextInput::make('email')->required()->email()->unique(),
                Select::make('class_id')
                    ->relationship('class', 'name')
                    ->live()
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')->label('Class')->required(),
                    ]),
                Select::make('section_id')
                    ->relationship('section', 'name')
                    ->options(function (Forms\Get $get) {
                        $classId = $get('class_id');

                        return $classId
                            ? Section::where('class_id', $classId)->pluck('name', 'id')
                            : [];
                    })
                    ->required()
                    ->createOptionForm([
                        TextInput::make('name')->label('Section name')->required(),
                        Select::make('class_id')
                            ->label('Class')
                            ->relationship('class', 'name')
                            ->required()
                            ->createOptionForm([
                                TextInput::make('name')->label('Class')->required(),
                            ]),
                    ]),
                TextInput::make('password')->required()->hiddenOn('edit'),
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
                TextColumn::make('created_at')->label('Join Date')->date()->sortable(),
            ])
            ->persistSortInSession()
            ->filters([
                Filter::make('students-filter')
                ->form([
                    Select::make('class_id')
                        ->label('Filter by Class')
                        ->placeholder('Select a class')
                        ->options(Classes::pluck('name', 'id'))
                        ->afterStateUpdated(function (Forms\Set $set) {
                            return $set('section_id', null);
                        }),
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
                Tables\Actions\DeleteBulkAction::make()->color(Color::Slate),
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('export-to-excel')
                        ->label('Excel')
                        ->icon('heroicon-o-arrow-up-on-square-stack')
                        ->color(Color::Green)
                        ->action(function (Collection $records) {
                            return Excel::download(new StudentsExport($records), 'students.xlsx');
                    }),
                    Tables\Actions\BulkAction::make('export-to-pdf')
                        ->label('PDF')
                        ->icon('heroicon-o-arrow-up-on-square-stack')
                        ->color(Color::Red)
                        ->action(function (Collection $records) {
                            return response()->streamDownload(function () use ($records) {
                                echo Pdf::loadHtml(
                                    Blade::render('pdf.students', ['students' => $records])
                                )->stream();
                            }, 'students.pdf');
                    }),
                ])->label('Export selected')
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
