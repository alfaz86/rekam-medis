<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
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
    protected static ?string $modelLabel = 'Pasien';
    protected static ?string $pluralModelLabel = 'Data Pasien';
    protected static ?string $navigationLabel = 'Daftar Pasien';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('number_identity')
                    ->label('No Identitas')
                    ->required()
                    ->hidden(),

                TextInput::make('name')
                    ->label('Nama')
                    ->required(),

                TextInput::make('husband_name')
                    ->label('Nama Suami')
                    ->nullable(),

                TextInput::make('age')
                    ->label('Usia')
                    ->numeric()
                    ->required()
                    ->hidden(),

                DatePicker::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->native(false)
                    ->displayFormat('d-m-Y')
                    ->format('Y-m-d')
                    ->required(),

                TextInput::make('birth_place')
                    ->label('Tempat Lahir')
                    ->default('-')
                    ->hidden(),

                TextInput::make('phone_number')
                    ->label('No HP')
                    ->nullable(),

                Textarea::make('address')
                    ->label('Alamat')
                    ->required(),

                DatePicker::make('created_at')
                    ->label('Tanggal Daftar')
                    ->required()
                    ->native(false)
                    ->displayFormat('d-m-Y')
                    ->format('Y-m-d')
                    ->default(now()),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('birth_date')
                    ->label('Tanggal Lahir')
                    ->sortable()
                    ->dateTime('d-m-Y')
                    ->searchable(),
                TextColumn::make('husband_name')
                    ->label('Nama Suami')
                    ->default('-')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(50),
                TextColumn::make('phone_number')
                    ->label('No HP')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Tanggal Daftar')
                    ->sortable()
                    ->dateTime('d-m-Y')
                    ->searchable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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

        if ($data['phone_number'] === null) {
            $data['phone_number'] = '-';
        }
        $data['user_id'] = $user->id;
        $data['number_identity'] = self::generateNumberIdentity();
        $data['birth_place'] = $data['address'];
        $data['age'] = Carbon::parse($data['birth_date'])->age;

        return $data;
    }

    private static function generateNumberIdentity(): string
    {
        $timestamp = time();
        $randomDigits = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT); // 6 digit
        return $timestamp . $randomDigits;
    }
}
