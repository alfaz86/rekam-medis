<?php

namespace App\Models;

use App\Traits\HasSoftDeleteSuffix;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory, SoftDeletes, HasSoftDeleteSuffix;

    protected $fillable = [
        'number_identity',
        'name',
        'husband_name',
        'age',
        'birth_date',
        'birth_place',
        'phone_number',
        'address',
        'user_id',
        'created_at',
    ];

    public static function boot()
    {
        parent::boot();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
