<?php

namespace App\Filament\Resources\MedicalRecordResource\Pages;

use App\Filament\Resources\MedicalRecordResource;
use App\Models\MedicalRecord;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMedicalRecord extends CreateRecord
{
    protected static string $resource = MedicalRecordResource::class;

    protected function handleRecordCreation(array $data): MedicalRecord
    {
        if (strpos($data['handled_by_id'], ':') !== false) {
            [$handledByType, $handledById] = explode(':', $data['handled_by_id']);

            $data['handled_by_type'] = $handledByType;
            $data['handled_by_id'] = $handledById;
        }

        // Simpan data rekam medis
        $medicalRecord = MedicalRecord::create($data);

        // Simpan relasi ke tabel pivot medical_record_medicine
        if (!empty($data['medicine_ids']) && is_array($data['medicine_ids'])) {
            $medicalRecord->medicines()->sync($data['medicine_ids']);
        }

        return $medicalRecord;
    }
}
