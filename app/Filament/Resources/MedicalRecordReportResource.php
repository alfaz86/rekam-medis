<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalRecordReportResource\Pages\ListMedicalRecordReport;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Midwife;
use App\Models\Room;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MedicalRecordReportResource extends Resource
{
    protected static ?string $model = MedicalRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $modelLabel = 'Laporan Rekam Medis';
    protected static ?string $pluralModelLabel = 'Laporan Rekam Medis';
    protected static ?string $navigationLabel = 'Laporan Rekam Medis';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int $navigationSort = 8;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('patient.name')->label('Pasien')->searchable()->sortable(),
                TextColumn::make('complaint')->label('Keluhan')->searchable()->sortable(),
                TextColumn::make('handled_by_id')
                    ->label('Bidan')
                    ->formatStateUsing(fn($record) => $record->handledBy?->name)
                    ->searchable()->sortable(),
                TextColumn::make('diagnosis')->label('Diagnosis')->searchable()->sortable(),
                TextColumn::make('room.name')->label('Ruangan')->searchable()->sortable(),
                TextColumn::make('date')->label('Tanggal')->sortable(),
            ])
            ->filters([
                SelectFilter::make('patient_id')->label('Nama Pasien')->options(
                    Patient::pluck('name', 'id')->toArray()
                ),
                SelectFilter::make('handled_by_id')
                    ->label('Bidan')
                    ->options(
                        fn() => self::getPatientCareTakerLabel()
                    )
                    ->query(function (array $data, Builder $query) {
                        if (!$data['value'])
                            return;

                        if (!str_contains($data['value'], ':'))
                            return;

                        [$type, $id] = explode(':', $data['value']);

                        $query->where('handled_by_type', $type)
                            ->where('handled_by_id', $id);
                    }),
                SelectFilter::make('room_id')->label('Ruangan')->options(
                    Room::pluck('name', 'id')->toArray()
                ),
                Filter::make('date')
                    ->form([
                        DatePicker::make('date_from'),
                        DatePicker::make('date_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['date_from'], fn(Builder $query, $date) => $query->whereDate('date', '>=', $date))
                            ->when($data['date_until'], fn(Builder $query, $date) => $query->whereDate('date', '<=', $date));
                    }),
            ])
            ->headerActions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn($livewire) => route('print.records', [
                        'filters' => $livewire->tableFilters,
                    ]), true),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMedicalRecordReport::route('/'),
        ];
    }

    public static function getPatientCareTakerLabel(): array
    {
        $doctors = collect();
        $midwives = collect();
        $PCP = explode(',', env('PCP', 'doctors'));

        if (in_array('doctors', $PCP)) {
            $doctors = Doctor::get()
                ->mapWithKeys(fn($doctor) => [
                    Doctor::class . ':' . $doctor->id => $doctor->name
                ]);
        }

        if (in_array('midwives', $PCP)) {
            $midwives = Midwife::get()
                ->mapWithKeys(fn($midwife) => [
                    Midwife::class . ':' . $midwife->id => $midwife->name
                ]);
        }

        return $doctors->merge($midwives)->toArray();
    }
}
