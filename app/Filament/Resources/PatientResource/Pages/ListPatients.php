<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPatients extends ListRecords
{
    protected static string $resource = PatientResource::class;
    protected static ?string $title = 'Pasien';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Pasien Baru'),
        ];
    }
}
