<?php

namespace App\Models;

use App\Traits\HasSoftDeleteSuffix;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Midwife extends Model
{
    use HasFactory, SoftDeletes, HasSoftDeleteSuffix;

    protected $fillable = [
        'name',
        'phone_number',
        'address',
        'user_id',
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
