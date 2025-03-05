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
    protected static ?string $navigationLabel = 'Rekam Medis';
    protected static ?string $breadcrumb = 'Rekam Medis';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('patient_id')
                    ->label('Pasien')
                    ->options(Patient::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Textarea::make('complaint')
                    ->label('Keluhan')
                    ->required(),
                Select::make('handled_by_id')
                    ->label('Bidan')
                    ->live()
                    ->options(self::getPatientCareTakers())
                    ->afterStateUpdated(function (Set $set, ?string $state) {
                        if ($state) {
                            [$model, $id] = explode(':', $state);
                            $set('handled_by_type', $model);
                            $set('handled_by_id', $state);
                        }
                    })
                    ->required(),
                TextInput::make('diagnosis')
                    ->label('Diagnosis')
                    ->required(),
                Select::make('medicine_ids')
                    ->label('Obat')
                    ->multiple()
                    ->options(Medicine::all()->pluck('name', 'id'))
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

    public static function getPatientCareTakers(): array
    {
        $doctors = collect();
        $midwifes = collect();
        $APP_USER_TABLE = env('APP_USER_TABLE', 'users');
        $APP_USER_TABLE = explode(',', $APP_USER_TABLE);

        if (in_array('doctors', $APP_USER_TABLE)) {
            $doctors = Doctor::all()->mapWithKeys(fn($doctor) => [
                Doctor::class . ':' . $doctor->id => $doctor->name
            ]);
        }

        if (in_array('midwives', $APP_USER_TABLE)) {
            $midwifes = Midwife::all()->mapWithKeys(fn($midwife) => [
                Midwife::class . ':' . $midwife->id => $midwife->name
            ]);
        }

        return $doctors->merge($midwifes)->toArray();
    }
}
