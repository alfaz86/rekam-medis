<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    protected $fillable = [
        'patient_id',
        'room_id',
        'handled_by_type',
        'handled_by_id',
        'complaint',
        'diagnosis',
        'date',
        'medical_history',
        'examination_results',
        'medical_treatment',
        'consultation_and_follow_up'
    ];

    public function handledBy()
    {
        return $this->morphTo();
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function medicines()
    {
        return $this->belongsToMany(Medicine::class, 'medical_record_medicines')
            ->withTimestamps();
    }

    public function medicineUsages()
    {
        return $this->hasMany(MedicalRecordMedicine::class, 'medical_record_id');
    }
}
