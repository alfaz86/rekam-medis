<?php

namespace App\Filament\Resources\MidwifeResource\Pages;

use App\Filament\Resources\MidwifeResource;
use App\Models\User;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CreateMidwife extends CreateRecord
{
    protected static string $resource = MidwifeResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $email = 'midwife_' . time() . '@example.com';
        $firstLetter = strtolower(explode(' ', $data['name'])[0]);
        $username = $data['username'] ? $data['username'] : $firstLetter . time();
        $password = $data['password']
            ? Hash::make($data['password'])
            : Hash::make('password');

        $user = new User();
        $user->name = $data['name'];
        $user->username = $username;
        $user->password = $password;
        $user->email = $email;
        $user->role = 'midwife';
        $user->save();

        $data['user_id'] = $user->id;

        $record = static::getModel()::create($data);

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()
            ->label('Simpan');
    }

    protected function getCancelFormAction(): Action
    {
        return parent::getCancelFormAction()
            ->label('Batal');
    }
}
