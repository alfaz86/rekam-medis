<?php

namespace App\Filament\Resources\MidwifeResource\Pages;

use App\Filament\Resources\MidwifeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMidwives extends ListRecords
{
    protected static string $resource = MidwifeResource::class;
    protected static ?string $title = 'Bidan';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
