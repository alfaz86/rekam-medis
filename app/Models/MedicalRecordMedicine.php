<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecordMedicine extends Model
{
    protected $fillable = [
        'medical_record_id',
        'medicine_id',
        'usage',
    ];

    public function medicalRecord()
    {
        return $this->belongsTo(MedicalRecord::class, 'medical_record_id');
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class, 'medicine_id');
    }
}
