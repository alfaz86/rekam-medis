<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MedicalRecordResource\Pages;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Medicine;
use App\Models\Midwife;
use App\Models\Patient;
use App\Models\Room;
use App\Traits\HasLetterheadPrint;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MedicalRecordResource extends Resource
{
    use HasLetterheadPrint;

    protected static ?string $model = MedicalRecord::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
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
                        fn(string $search, $livewire) =>
                        Patient::where('name', config('database.like_operator'), "%{$search}%")
                            ->when($livewire instanceof ListRecords, fn($q) => $q->withTrashed())
                            ->limit(10)
                            ->pluck('name', 'id')
                    )
                    ->getOptionLabelUsing(fn($value, $livewire) => Patient::when(
                        $livewire instanceof ListRecords,
                        fn($q) => $q->withTrashed()
                    )->find($value)?->name)
                    ->required(),
                Textarea::make('medical_history')
                    ->label('Riwayat Kesehatan')
                    ->required(),
                Textarea::make('complaint')
                    ->label('Keluhan')
                    ->required(),
                Textarea::make('examination_results')
                    ->label('Hasil Pemeriksaan')
                    ->required(),
                TextInput::make('diagnosis')
                    ->label('Diagnosis')
                    ->required(),
                Select::make('handled_by_id')
                    ->label('Bidan')
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $search, $livewire) => self::searchPatientCareTakers($search, $livewire))
                    ->getOptionLabelUsing(
                        fn($record, $livewire) =>
                        self::getPatientCareTakerLabel($record->handled_by_type, $record->handled_by_id, $livewire)
                    )
                    ->required(),
                Textarea::make('medical_treatment')
                    ->label('Tindakan Medis')
                    ->required(),
                Group::make([
                    Repeater::make('medicineUsages')
                        ->label('Obat')
                        ->relationship('medicineUsages')
                        ->schema([
                            Select::make('medicine_id')
                                ->label('Obat')
                                ->relationship('medicine', 'name')
                                ->searchable()
                                ->options(
                                    fn($livewire) => Medicine::when($livewire instanceof ListRecords, fn($q) => $q->withTrashed())
                                        ->latest()
                                        ->limit(10)
                                        ->pluck('name', 'id')
                                )
                                ->getSearchResultsUsing(function (string $search, $livewire) {
                                    return Medicine::where('name', config('database.like_operator'), "%{$search}%")
                                        ->when($livewire instanceof ListRecords, fn($q) => $q->withTrashed())
                                        ->limit(10)
                                        ->pluck('name', 'id');
                                })
                                ->getOptionLabelUsing(fn($value, $livewire) => Medicine::when($livewire instanceof ListRecords, fn($q) => $q->withTrashed())->find($value)?->name)
                                ->required(),

                            TextInput::make('usage')
                                ->label('Aturan Pakai')
                                ->placeholder(fn($livewire) => $livewire instanceof ListRecords ? '-' : 'misal: 1 tablet 3x sehari')
                                ->required(),
                        ])
                        ->defaultItems(0)
                        ->addActionLabel('Tambah Obat')
                        ->columns(2)
                        ->nullable(),

                    Placeholder::make('')
                        ->content('Tidak ada obat yang ditambahkan.')
                        ->visible(
                            fn(Get $get, $livewire) =>
                            $livewire instanceof ListRecords && count($get('medicineUsages') ?? []) === 0
                        ),
                ])->columns(1),
                Select::make('room_id')
                    ->label('Ruangan')
                    ->options(Room::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required()
                    ->hidden(),
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->hidden(),
                Textarea::make('consultation_and_follow_up')
                    ->label('Konsultasi dan Tindak Lanjut')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(MedicalRecord::query()
                ->with([
                    'patient' => fn($query) => $query->withTrashed(),
                ])
                ->latest())
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
                //
            ])
            ->actions([
                ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->record(function ($record) {
                            return $record->loadMissing([
                                'handledBy' => fn($query) => $query->withTrashed(),
                                'patient' => fn($query) => $query->withTrashed(),
                                'room',
                                'medicineUsages.medicine' => fn($query) => $query->withTrashed(),
                            ]);
                        })
                        ->extraModalFooterActions([
                            Tables\Actions\DeleteAction::make()
                                ->label('Delete')
                                ->color('danger')
                                ->after(function ($record, $livewire) {
                                    return redirect(MedicalRecordResource::getUrl());
                                }),
                            Tables\Actions\Action::make('print')
                                ->label('Print')
                                ->icon('heroicon-o-printer')
                                ->color('primary')
                                ->url(fn(Tables\Actions\Action $action, $record) => route('print.medical-record', [
                                    'id' => $record->id,
                                ]), true),
                        ])
                        ->modalFooterActionsAlignment('end'),
                    Tables\Actions\Action::make('print')
                        ->label('Print')
                        ->icon('heroicon-o-printer')
                        ->color('primary')
                        ->url(fn(Tables\Actions\Action $action, $record) => route('print.medical-record', [
                            'id' => $record->id,
                        ]), true),
                    Tables\Actions\DeleteAction::make(),
                ])
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
        ];
    }

    public static function searchPatientCareTakers(string $search, $livewire): array
    {
        $doctors = collect();
        $midwives = collect();
        $PCP = explode(',', env('PCP', 'doctors'));

        if (in_array('doctors', $PCP)) {
            $doctors = Doctor::where('name', config('database.like_operator'), "%{$search}%")
                ->when($livewire instanceof ListRecords, fn($q) => $q->withTrashed())
                ->limit(10)
                ->get()
                ->mapWithKeys(fn($doctor) => [
                    Doctor::class . ':' . $doctor->id => $doctor->name
                ]);
        }

        if (in_array('midwives', $PCP)) {
            $midwives = Midwife::where('name', config('database.like_operator'), "%{$search}%")
                ->when($livewire instanceof ListRecords, fn($q) => $q->withTrashed())
                ->limit(10)
                ->get()
                ->mapWithKeys(fn($midwife) => [
                    Midwife::class . ':' . $midwife->id => $midwife->name
                ]);
        }

        return $doctors->merge($midwives)->toArray();
    }

    public static function getPatientCareTakerLabel(string $model, int $id, $livewire): ?string
    {
        return match ($model) {
            Doctor::class => Doctor::when($livewire instanceof ListRecords, fn($q) => $q->withTrashed())->find($id)?->name,
            Midwife::class => Midwife::when($livewire instanceof ListRecords, fn($q) => $q->withTrashed())->find($id)?->name,
            default => null
        };
    }

    public function printMedicalRecord($id)
    {
        $medicalRecords = MedicalRecord::with([
            'patient' => fn($query) => $query->withTrashed(),
            'handledBy' => fn($query) => $query->withTrashed(),
            'room',
            'medicineUsages.medicine' => fn($query) => $query->withTrashed(),
        ])->whereIn('id', [$id])->get();
        $letterhead = $this->getLetterhead();

        return view('medical-record.print', [
            'medicalRecords' => $medicalRecords,
            'letterhead' => $letterhead,
        ]);
    }
}
