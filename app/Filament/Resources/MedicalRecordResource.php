<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalRecordResource\Pages;
use App\Filament\Resources\MedicalRecordResource\RelationManagers;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Midwife;
use App\Models\Patient;
use App\Models\Room;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MedicalRecordResource extends Resource
{
    protected static ?string $model = MedicalRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $modelLabel = 'Rekam Medis';
    protected static ?string $pluralModelLabel = 'Rekam Medis';
    protected static ?string $navigationLabel = 'Rekam Medis';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('patient_id')
                    ->label('Pasien')
                    ->searchable()
                    ->getSearchResultsUsing(
                        fn(string $search) =>
                        Patient::where('name', 'like', "%{$search}%")
                            ->limit(10)
                            ->pluck('name', 'id')
                    )
                    ->getOptionLabelUsing(fn($value) => Patient::find($value)?->name)
                    ->required(),
                Textarea::make('complaint')
                    ->label('Keluhan')
                    ->required(),
                Select::make('handled_by_id')
                    ->label('Bidan')
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search) => self::searchPatientCareTakers($search))
                    ->getOptionLabelUsing(fn($value) => self::getPatientCareTakerLabel($value))
                    ->required(),
                TextInput::make('diagnosis')
                    ->label('Diagnosis')
                    ->required(),
                Select::make('medicine_ids')
                    ->label('Obat')
                    ->multiple()
                    ->searchable()
                    ->getSearchResultsUsing(
                        fn(string $search) =>
                        Medicine::where('name', 'like', "%{$search}%")
                            ->limit(10)
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->getOptionLabelsUsing(
                        fn(array $values) =>
                        Medicine::whereIn('id', $values)
                            ->pluck('name', 'id')
                            ->toArray()
                    )
                    ->relationship('medicines', 'name')
                    ->default(fn($record) => $record?->medicines->pluck('id')->toArray())
                    ->required(),
                Select::make('room_id')
                    ->label('Ruangan')
                    ->options(Room::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->default(now()),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.name')
                    ->label('Pasien')
                    ->searchable(),
                TextColumn::make('complaint')
                    ->label('Keluhan')
                    ->searchable(),
                TextColumn::make('handled_by_id')
                    ->label('Bidan')
                    ->formatStateUsing(fn($record) => $record->handledBy?->name)
                    ->searchable(),
                TextColumn::make('diagnosis')
                    ->label('Diagnosis')
                    ->searchable(),
                TextColumn::make('room.name')
                    ->label('Ruangan')
                    ->searchable(),
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListMedicalRecords::route('/'),
            'create' => Pages\CreateMedicalRecord::route('/create'),
            'edit' => Pages\EditMedicalRecord::route('/{record}/edit'),
        ];
    }

    public static function searchPatientCareTakers(string $search): array
    {
        $doctors = collect();
        $midwives = collect();
        $PCP = explode(',', env('PCP', 'doctors'));

        if (in_array('doctors', $PCP)) {
            $doctors = Doctor::where('name', 'like', "%{$search}%")
                ->limit(10)
                ->get()
                ->mapWithKeys(fn($doctor) => [
                    Doctor::class . ':' . $doctor->id => $doctor->name
                ]);
        }

        if (in_array('midwives', $PCP)) {
            $midwives = Midwife::where('name', 'like', "%{$search}%")
                ->limit(10)
                ->get()
                ->mapWithKeys(fn($midwife) => [
                    Midwife::class . ':' . $midwife->id => $midwife->name
                ]);
        }

        return $doctors->merge($midwives)->toArray();
    }

    public static function getPatientCareTakerLabel($value): ?string
    {
        $parts = explode(':', $value);

        if (count($parts) !== 2) {
            return null;
        }

        [$model, $id] = $parts;

        return match ($model) {
            Doctor::class => Doctor::find($id)?->name,
            Midwife::class => Midwife::find($id)?->name,
            default => null
        };
    }
}
