<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'specialist',
        'phone_number',
        'address',
        'user_id',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function ($doctor) {
            if ($doctor->user) {
                $doctor->user->delete();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
