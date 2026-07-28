<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSubjectTeacher extends Model
{
    protected $table = "class_subject_teacher";
    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'academic_year_id',
        'hours_per_week'
    ];

    public function teacher() : BelongsTo{
        return $this->belongsTo(Teacher::class);
    }

    public function subject() : BelongsTo{
        return $this->belongsTo(Subject::class);
    }

    public function classe() : BelongsTo{
        return $this->belongsTo(Classe::class, 'class_id');
    }

    public function academicYear() : BelongsTo{
        return $this->belongsTo(AcademicYear::class);
    }
}