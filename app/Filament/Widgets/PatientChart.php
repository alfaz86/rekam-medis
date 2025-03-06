<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Patient;
use Carbon\Carbon;

class PatientChart extends ChartWidget
{
    protected static ?string $heading = 'Total Pasien Per Bulan';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $months = collect(range(1, 12))->map(function ($month) {
            return Carbon::create(null, $month, 1)->translatedFormat('F');
        });

        $patientsPerMonth = collect(range(1, 12))->map(function ($month) {
            return Patient::whereMonth('created_at', $month)
                ->whereYear('created_at', date('Y'))
                ->count();
        });

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pasien',
                    'data' => $patientsPerMonth->toArray(),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ],
            ],
            'labels' => $months->toArray(),
        ];
    }

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    protected function getOptions(): array
    {
        $maxData = collect(range(1, 12))->map(function ($month) {
            return Patient::whereMonth('created_at', $month)
                ->whereYear('created_at', date('Y'))
                ->count();
        })->max();

        $maxY = $maxData > 0 ? pow(10, ceil(log10($maxData))) : 10;

        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => $maxY,
                    'ticks' => [
                        'stepSize' => $maxY / 10,
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
