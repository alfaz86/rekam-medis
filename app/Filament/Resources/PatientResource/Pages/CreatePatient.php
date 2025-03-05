<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return PatientResource::beforeCreate($data);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
