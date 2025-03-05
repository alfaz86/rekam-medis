<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Models\Doctor;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Dokter';
    protected static ?string $breadcrumb = 'Dokter';

    public static function shouldRegisterNavigation(): bool
    {
        $model = new Doctor();
        $tableName = $model->getTable();
        $APP_USER_TABLE = env('APP_USER_TABLE', 'users');
        $APP_USER_TABLE = explode(',', $APP_USER_TABLE);

        if (Schema::hasTable($tableName) && in_array($tableName, $APP_USER_TABLE)) {
            return true;
        }

        return false;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Nama')->required(),
                TextInput::make('specialist')->label('Spesialis')->required(),
                TextInput::make('phone_number')->label('No HP')->nullable(),
                Textarea::make('address')->label('Alamat')->nullable(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->sortable()->searchable(),
                TextColumn::make('specialist')->label('Spesialis')->sortable()->searchable(),
                TextColumn::make('phone_number')->label('No HP')->sortable(),
                TextColumn::make('user.email')->label('Email Akun')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }

    public static function beforeCreate($data)
    {
        $email = 'doctor_' . date('YmdHis') . '@example.com';

        $firstLetter = strtolower(explode(' ', $data['name'])[0]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make('password'),
            'username' => $firstLetter . time(),
            'role' => 'doctor',
        ]);

        $data['user_id'] = $user->id;
        return $data;
    }
}
