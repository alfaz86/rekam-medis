<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\View;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PatientResource extends Resource
{
    protected static ?string $model = Patient::class;
    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $modelLabel = 'Pasien';
    protected static ?string $pluralModelLabel = 'Data Pasien';
    protected static ?string $navigationLabel = 'Pasien';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Grid::make(12)->schema([
                    Select::make('search')
                        ->label('Cari Pasien')
                        ->placeholder('Cari berdasarkan nama atau no identitas')
                        ->searchable()
                        ->getSearchResultsUsing(function (string $search) {
                            return Patient::query()
                                ->where('name', 'like', '%' . $search . '%')
                                ->orWhere('number_identity', 'like', '%' . $search . '%')
                                ->limit(10)
                                ->get()
                                ->mapWithKeys(fn($patient) => [
                                    $patient->id => "{$patient->name} - {$patient->number_identity}",
                                ])
                                ->toArray();
                        })
                        ->getOptionLabelUsing(fn($value) => optional(Patient::find($value))->name ?? null)
                        ->reactive()
                        ->afterStateUpdated(function (callable $set, $state) {
                            if ($state) {
                                $patient = Patient::find($state);
                                if ($patient) {
                                    $set('name', $patient->name);
                                    $set('number_identity', $patient->number_identity);
                                    $set('husband_name', $patient->husband_name);
                                    $set('birth_date', $patient->birth_date);
                                    $set('birth_place', $patient->birth_place);
                                    $set('phone_number', $patient->phone_number);
                                    $set('address', $patient->address);
                                }
                            } else {
                                $set('name', null);
                                $set('number_identity', self::generateNumberIdentity());
                                $set('husband_name', null);
                                $set('birth_date', null);
                                $set('birth_place', null);
                                $set('phone_number', null);
                                $set('address', null);
                            }
                        })
                        ->hidden(fn($livewire) => $livewire instanceof Pages\EditPatient)
                        ->columnSpan(6),
                ]),

                Grid::make(12)->schema([
                    View::make('components.loading-patient')
                        ->visible(fn() => true)
                        ->hidden(fn($livewire) => $livewire instanceof Pages\EditPatient)
                        ->columnSpan(6),
                ]),

                TextInput::make('number_identity')
                    ->label('No Regis')
                    ->default(self::generateNumberIdentity())
                    ->readOnly()
                    ->required(),

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
                ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->color('primary'),
                    Tables\Actions\DeleteAction::make(),
                    Action::make('print')
                        ->label('Print')
                        ->icon('heroicon-o-printer')
                        ->color('primary')
                        ->url(fn($record) => route('print.patient', [
                            'id' => $record->id,
                        ]), true),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('print')
                        ->label('Print yang dipilih')
                        ->icon('heroicon-o-printer')
                        ->color('primary')
                        ->action(function (Collection $records) {
                            $ids = implode(',', $records->pluck('id')->toArray());
                            $encodedIds = base64_encode($ids);

                            return redirect()->route('print.patients', [
                                'ids' => $encodedIds,
                            ]);
                        })
                        ->openUrlInNewTab(),
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
        $data['birth_place'] = $data['address'];
        $data['age'] = Carbon::parse($data['birth_date'])->age;

        return $data;
    }

    private static function generateNumberIdentity(): string
    {
        $timestamp = time();
        $randomDigits = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT); // 6 digit
        return $randomDigits . $timestamp;
    }

    public function printPatient($id)
    {
        $patients = Patient::whereIn('id', [$id])->get();
        $letterhead = self::getLetterhead();

        return view('patient.print', compact('patients', 'letterhead'));
    }

    public function printPatients(Request $request)
    {
        $encodedIds = $request->get('ids', '');
        $ids = explode(',', base64_decode($encodedIds));
        $patients = Patient::whereIn('id', $ids)->get();
        $letterhead = self::getLetterhead();

        return view('patient.print', compact('patients', 'letterhead'));
    }

    public function getLetterhead(): array
    {
        $title = env('LETTERHEAD_TITLE', 'title');
        $name = env('LETTERHEAD_NAME', 'name');
        $address = self::getLetterheadAddressLines();

        return [
            'title' => $title,
            'name' => $name,
            'address' => $address,
        ];
    }

    public function getLetterheadAddressLines(): array
    {
        $addressString = env('LETTERHEAD_ADDRESS', 'address');
        $addressParts = preg_split('/\s*~nl\s*/', $addressString, -1, PREG_SPLIT_NO_EMPTY);
        $addressLines = [];
        foreach ($addressParts as $index => $part) {
            $addressLines['line' . ($index + 1)] = trim($part);
        }

        return $addressLines;
    }
}
