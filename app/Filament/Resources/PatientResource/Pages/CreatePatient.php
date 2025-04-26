<?php

namespace App\Filament\Resources\PatientResource\Pages;

use App\Filament\Resources\PatientResource;
use App\Models\Patient;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePatient extends CreateRecord
{
    protected static string $resource = PatientResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        if (!empty($data['search'])) {
            $patient = Patient::where('number_identity', $data['number_identity'])
                ->first();

            if ($patient) {
                $patient->update([
                    'name' => $data['name'],
                    'husband_name' => $data['husband_name'],
                    'birth_date' => $data['birth_date'],
                    'birth_place' => $data['address'],
                    'age' => Carbon::parse($data['birth_date'])->age,
                    'phone_number' => $data['phone_number'] ?? '-',
                    'address' => $data['address'],
                    'created_at' => $data['created_at'],
                ]);

                return $patient;
            }
        }

        $data = PatientResource::beforeCreate($data);
        return Patient::create($data);
    }

    protected function getCreatedNotificationMessage(): ?string
    {
        $search = $this->form->getState()['search'] ?? null;

        if ($search) {
            return 'Data pasien berhasil diperbarui';
        }

        return null;
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
