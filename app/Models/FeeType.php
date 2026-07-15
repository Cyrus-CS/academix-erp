<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FeeType extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'academic_year'
    ];

    public function payment() : HasOne{
        return $this->hasOne(Payment::class);
    }
}