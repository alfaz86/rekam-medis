<?php

namespace App\Filament\Resources\MidwifeResource\Pages;

use App\Filament\Resources\MidwifeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMidwife extends CreateRecord
{
    protected static string $resource = MidwifeResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return MidwifeResource::beforeCreate($data);
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
