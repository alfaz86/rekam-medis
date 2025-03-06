<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MidwifeResource\Pages;
use App\Models\Midwife;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;

class MidwifeResource extends Resource
{
    protected static ?string $model = Midwife::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $modelLabel = 'Bidan';
    protected static ?string $pluralModelLabel = 'Data Bidan';
    protected static ?string $navigationLabel = 'Bidan';

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
                TextInput::make('phone_number')->label('No HP')->nullable(),
                TextInput::make('password')->password()->label('Password')->required(
                    fn($record) => is_null(optional($record?->user)->id)
                ),
                Textarea::make('address')->label('Alamat')->nullable(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('user.username')->label('Username')->sortable(),
                Tables\Columns\TextColumn::make('phone_number')->label('No HP')->sortable(),
                Tables\Columns\TextColumn::make('address')->label('Alamat')->limit(50),
            ])
            ->filters([
                //
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
}