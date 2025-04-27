<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
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
        $PCP = env('PCP', 'doctors');
        $PCP = explode(',', $PCP);

        if (Schema::hasTable($tableName) && in_array($tableName, $PCP)) {
            return true;
        }

        return false;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                TextInput::make('name')->label('Nama')->required(),
                TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->unique('users', 'username', ignorable: fn($record) => optional($record?->user)->id ? $record->user : null)
                    ->formatStateUsing(fn($state, $record) => optional($record?->user)->username ?? $state)
                    ->rules([
                        'regex:/^[a-zA-Z0-9_]+$/',
                        'min:3',
                    ])
                    ->live()
                    ->afterStateUpdated(
                        fn($state, callable $set) =>
                        $set('username', preg_replace('/[^a-zA-Z0-9_]/', '', $state))
                    ),
                TextInput::make('specialist')->label('Spesialis')->required(),
                TextInput::make('password')->password()->label('Password')->required(
                    fn($record) => is_null(optional($record?->user)->id)
                ),
                TextInput::make('phone_number')->label('No HP')->nullable(),
                Textarea::make('address')->label('Alamat')->nullable(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('name')->label('Nama')->sortable()->searchable(),
                TextColumn::make('user.username')->label('Username')->sortable(),
                TextColumn::make('specialist')->label('Spesialis')->sortable()->searchable(),
                TextColumn::make('phone_number')->label('No HP')->sortable(),
                TextColumn::make('address')->label('Alamat')->limit(50),
            ])
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
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
}
