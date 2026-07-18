<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
        'academic_year_id',
    ];

    protected function casts() : array {
        return [
            'start_time' => 'datetime',
            'end_time' => 'datetime',
        ];
    }

    /**
     * Correspondance jour (nom) → jour (entier stocké en base).
     */
    public const DAYS = [
        'Lundi'     => 0,
        'Mardi'     => 1,
        'Mercredi'  => 2,
        'Jeudi'     => 3,
        'Vendredi'  => 4,
        'Samedi'    => 5,
    ];

    /**
     * Nom du jour lisible à partir de l'entier stocké.
     */
    public function getDayNameAttribute(): ?string
    {
        return array_search($this->day_of_week, self::DAYS) ?: null;
    }
    
    public function classe() : BelongsTo{
        return $this->belongsTo(Classe::class);
    }
    public function subject() : BelongsTo{
        return $this->belongsTo(Subject::class);
    }

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

    public function academicYear(){
        return $this->belongsTo(AcademicYear::class);
    }
}