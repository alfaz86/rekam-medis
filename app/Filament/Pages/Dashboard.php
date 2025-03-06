<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\WelcomeWidget;
use App\Filament\Widgets\StatsWidget;
use App\Filament\Widgets\PatientChart;

class Dashboard extends BaseDashboard
{
    public function getWidgets(): array
    {
        return [
            WelcomeWidget::class,
            StatsWidget::class,
            PatientChart::class,
        ];
    }
}
