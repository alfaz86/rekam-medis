<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Models\Patient;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationLabel = 'Pasien';
    protected static ?string $breadcrumb = 'Pasien';

    public static function shouldRegisterNavigation(): bool
    {
        $model = new Patient();
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
                TextInput::make('number_identity')->label('No Identitas')->required(),
                TextInput::make('name')->label('Nama')->required(),
                TextInput::make('husband_name')->label('Nama Suami')->nullable(),
                TextInput::make('age')->label('Usia')->numeric()->required(),
                DatePicker::make('birth_date')->label('Tanggal Lahir')->required(),
                TextInput::make('birth_place')->label('Tempat Lahir')->required(),
                TextInput::make('phone_number')->label('No HP')->nullable(),
                Textarea::make('address')->label('Alamat')->required(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('number_identity')->label('No Identitas')->sortable()->searchable(),
                TextColumn::make('name')->label('Nama')->sortable()->searchable(),
                TextColumn::make('age')->label('Usia')->sortable(),
                TextColumn::make('birth_date')->label('Tanggal Lahir')->sortable(),
                TextColumn::make('phone_number')->label('No HP')->sortable(),
                TextColumn::make('user.email')->label('Email Akun')->sortable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }

    public static function beforeCreate($data)
    {
        $email = 'patient_' . time() . '@example.com';

        $firstLetter = strtolower(explode(' ', $data['name'])[0]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make('password'),
            'username' => $firstLetter . time(),
            'role' => 'patient',
        ]);

        $data['user_id'] = $user->id;

        return $data;
    }
}
