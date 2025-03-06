<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Resources\DoctorResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $email = 'doctor_' . time() . '@example.com';
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
        $user->role = 'doctor';
        $user->save();

        $data['user_id'] = $user->id;

        $record = static::getModel()::create($data);

        return $record;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
