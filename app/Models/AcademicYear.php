<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current'  
    ];

    protected function casts() : array {
        return [
          'start_date' => 'datetime',
          'end_date' => 'datetime',
          'is_current' => 'boolean',
        ];
    }
    
    // -------------------- RELATIONS -------------------
    public function terms() : HasMany{
        return $this->hasMany(Term::class);
    }

    // ----------------------------------- SCOPE ----------------------------------
    public function scopeActive(Builder $builder) : Builder{
        return $builder->where('is_current', true);
    }
}