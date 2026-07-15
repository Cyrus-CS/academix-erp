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
        'end_time'
    ];
    
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