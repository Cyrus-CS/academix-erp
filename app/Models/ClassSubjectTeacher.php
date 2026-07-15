<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSubjectTeacher extends Model
{
    protected $table = "class_subject_teacher";
    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'academic_year_id',
    ];
}