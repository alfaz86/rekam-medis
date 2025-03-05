<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MidwifeResource\Pages;
use App\Models\Midwife;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class MidwifeResource extends Resource
{
    protected static ?string $model = Midwife::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Bidan';
    protected static ?string $breadcrumb = 'Bidan';

    public static function shouldRegisterNavigation(): bool
    {
        $model = new Midwife();
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
                TextInput::make('phone_number')->label('No HP')->nullable(),
                Textarea::make('address')->label('Alamat')->nullable(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nama')->sortable()->searchable(),
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMidwives::route('/'),
            'create' => Pages\CreateMidwife::route('/create'),
            'edit' => Pages\EditMidwife::route('/{record}/edit'),
        ];
    }

    public static function beforeCreate($data)
    {
        $email = 'midwife_' . date('YmdHis') . '@example.com';

        $firstLetter = strtolower(explode(' ', $data['name'])[0]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make('password'),
            'username' => $firstLetter . time(),
            'role' => 'midwife',
        ]);

        $data['user_id'] = $user->id;
        return $data;
    }
}
