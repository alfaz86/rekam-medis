<?php

namespace App\Filament\Pages;

use App\Models\Patient;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PatientReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $model = Patient::class;

    protected static ?string $title = 'Laporan Pasien';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.patient-report';

    protected static ?string $navigationGroup = 'Laporan';

    protected static ?string $navigationLabel = 'Laporan Pasien';

    protected static ?string $slug = 'patient-reports';

    public function getBreadcrumbs(): array
    {
        return [
            route(static::getRouteName()) => 'Laporan Pasien',
            'List',
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Patient::with(['user']))
            ->recordUrl(null)
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->sortable()
                    ->dateTime('d-m-Y')
                    ->searchable(),
                TextColumn::make('husband_name')
                    ->label('Nama Suami')
                    ->default('-')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(50),
                TextColumn::make('phone_number')
                    ->label('No HP')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->sortable()
                    ->dateTime('d-m-Y')
                    ->searchable(),
            ])
            ->filters([
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
                            ->when($data['date_from'], fn(Builder $query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['date_until'], fn(Builder $query, $date) => $query->whereDate('created_at', '<=', $date));
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
                    ->url(fn($livewire) => route('print.patient-reports', [
                        'filters' => $livewire->tableFilters,
                    ]), true),
            ]);
    }
}
