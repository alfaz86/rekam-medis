<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalRecordReportResource\Pages\ListMedicalRecordReport;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\Midwife;
use App\Models\Room;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Actions\Action as FormAction;
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
                TextColumn::make('patient.name')
                    ->label('Pasien')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('medical_history')
                    ->label('Riwayat Kesehatan')
                    ->searchable()
                    ->limit(30)
                    ->sortable(),
                TextColumn::make('complaint')
                    ->label('Keluhan')
                    ->searchable()
                    ->limit(30)
                    ->sortable(),
                TextColumn::make('examination_results')
                    ->label('Hasil Pemeriksaan')
                    ->searchable()
                    ->limit(30)
                    ->sortable(),
                TextColumn::make('diagnosis')
                    ->label('Diagnosis')
                    ->searchable()
                    ->limit(30)
                    ->sortable(),
                TextColumn::make('medical_treatment')
                    ->label('Tindakan Medis')
                    ->searchable()
                    ->limit(30)
                    ->sortable(),
                TextColumn::make('date')
                    ->label('Tanggal')
                    ->dateTime('d-m-Y')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                // SelectFilter::make('handled_by_id')
                //     ->label('Bidan')
                //     ->options(
                //         fn() => self::getPatientCareTakerLabel()
                //     )
                //     ->query(function (array $data, Builder $query) {
                //         if (!$data['value'])
                //             return;

                //         if (!str_contains($data['value'], ':'))
                //             return;

                //         [$type, $id] = explode(':', $data['value']);

                //         $query->where('handled_by_type', $type)
                //             ->where('handled_by_id', $id);
                //     }),
                // SelectFilter::make('room_id')->label('Ruangan')->options(
                //     Room::pluck('name', 'id')->toArray()
                // ),
                Filter::make('date')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Dari Tanggal')
                            ->native(false)
                            ->displayFormat('d-m-Y')
                            ->placeholder('dd-mm-yyyy')
                            ->suffixAction(
                                FormAction::make('clearDateFrom')
                                    ->icon('heroicon-o-x-circle')
                                    ->action(fn($state, callable $set) => $set('date_from', null))
                                    ->visible(fn($state) => filled($state))
                            ),
                        DatePicker::make('date_until')
                            ->label('Sampai Tanggal')
                            ->native(false)
                            ->displayFormat('d-m-Y')
                            ->placeholder('dd-mm-yyyy')
                            ->suffixAction(
                                FormAction::make('clearDateUntil')
                                    ->icon('heroicon-o-x-circle')
                                    ->action(fn($state, callable $set) => $set('date_until', null))
                                    ->visible(fn($state) => filled($state))
                            ),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['date_from'], fn(Builder $query, $date) => $query->whereDate('date', '>=', $date))
                            ->when($data['date_until'], fn(Builder $query, $date) => $query->whereDate('date', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['date_from'] ?? false) {
                            $indicators[] = 'Dari tanggal ' . Carbon::parse($data['date_from'])->format('d M Y');
                        }

                        if ($data['date_until'] ?? false) {
                            $indicators[] = 'Sampai tanggal ' . Carbon::parse($data['date_until'])->format('d M Y');
                        }

                        return $indicators;
                    }),
            ])
            ->headerActions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn($livewire) => route('print.medical-record-reports', [
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
