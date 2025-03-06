<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\DoctorResource;
use App\Filament\Resources\MedicineResource;
use App\Filament\Resources\MidwifeResource;
use App\Filament\Resources\PatientResource;
use App\Models\Doctor;
use App\Models\Midwife;
use App\Models\Patient;
use App\Models\Medicine;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $PCP = env('PCP', 'doctors');
        $PCP = explode(',', $PCP);

        $doctorStats = Stat::make('Total Dokter', Doctor::count())
            ->description('Klik untuk melihat')
            ->url(DoctorResource::getUrl())
            ->icon('heroicon-o-user');

        $midwifeStats = Stat::make('Total Bidan', Midwife::count())
            ->description('Klik untuk melihat')
            ->url(MidwifeResource::getUrl())
            ->icon('heroicon-o-user');

        $patientStats = Stat::make('Total Pasien', Patient::count())
            ->description('Klik untuk melihat')
            ->url(PatientResource::getUrl())
            ->icon('heroicon-o-user');

        $medicineStats = Stat::make('Total Obat', Medicine::count())
            ->description('Klik untuk melihat')
            ->url(MedicineResource::getUrl())
            ->icon('heroicon-o-beaker');

        $stats = [
            $patientStats,
            $medicineStats,
        ];

        if (in_array('doctors', $PCP)) {
            array_push($stats, $doctorStats);
        }

        if (in_array('midwives', $PCP)) {
            array_push($stats, $midwifeStats);
        }

        return $stats;
    }
}
