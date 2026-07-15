<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Term extends Model
{
    protected $fillable = [
        'name', 
        'academic_year_id',
        'start_date',
        'end_date'
    ];

    protected function casts(){
        return [
          'start_date' => 'datetime',
          'end_date' => 'datetime'  
        ];
    }

    // ----------------------------- RELATIONS ---------------------

    public function academicYear() : BelongsTo{
        return $this->belongsTo(AcademicYear::class);
    }

    public function grades() : HasMany{
        return $this->hasMany(Grade::class);
    }

    public function reportCards(){
        return $this->hasMany(ReportCard::class);
    }
}