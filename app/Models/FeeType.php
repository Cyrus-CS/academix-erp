<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeType extends Model
{
    protected $fillable = [
        'name',
        'amount',
        'academic_year_id',
        'frequency',
        'is_active',
        'description',
    ];

    public function payments() : HasMany{
        return $this->HasMany(Payment::class);
    }
}