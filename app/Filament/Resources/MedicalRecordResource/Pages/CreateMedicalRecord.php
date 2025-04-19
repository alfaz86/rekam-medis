<?php

namespace App\Filament\Resources\MedicalRecordResource\Pages;

use App\Filament\Resources\MedicalRecordResource;
use App\Models\MedicalRecord;
use App\Models\Room;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicalRecord extends CreateRecord
{
    protected static string $resource = MedicalRecordResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function handleRecordCreation(array $data): MedicalRecord
    {
        if (strpos($data['handled_by_id'], ':') !== false) {
            [$handledByType, $handledById] = explode(':', $data['handled_by_id']);

            $data['handled_by_type'] = $handledByType;
            $data['handled_by_id'] = $handledById;
        }

        if (!isset($data['room_id'])) {
            $room = Room::first();
            $data['room_id'] = $room->id;
        }

        $data['date'] = now();

        // Simpan data rekam medis
        $medicalRecord = MedicalRecord::create($data);

        // Simpan relasi ke tabel medical_record_medicines
        $medicalRecordMedicines = $data['medicineUsages'] ?? [];
        foreach ($medicalRecordMedicines as $medicine) {
            if (!empty($medicine['medicine_id']) && !empty($medicine['usage'])) {
                $medicalRecord->medical_record_medicines()->attach($medicine['medicine_id'], [
                    'usage' => $medicine['usage'],
                ]);
            }
        }

        return $medicalRecord;
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
