<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'number_identity',
        'name',
        'husband_name',
        'age',
        'birth_date',
        'birth_place',
        'phone_number',
        'address',
        'user_id'
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($patient) {
            if ($patient->user) {
                $patient->user->delete();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
