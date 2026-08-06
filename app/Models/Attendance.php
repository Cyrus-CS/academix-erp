<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'student_id',
        'teacher_id',
        'class_id',
        'subject_id',
        'date',
        'status',
        'reason'
    ];

    protected function casts() : array{
        return [
            'date' => 'datetime'
        ];
    }

    // ------------------------------- RELATIONS --------------------------------
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class,'class_id');
    }

    public function subject() : BelongsTo{
        return $this->belongsTo(Subject::class);
    }

    // -------------------------------------------------- PERSONNAL FUNCTION -------------------------
    public static function present(): int
    {
        return static::whereDate('date', today())
            ->where('status', 'present')
            ->count();
    }

    public static function absent(): int
    {
        return static::whereDate('date', today())
            ->where('status', 'absent')
            ->count();
    }

    public static function late(): int
    {
        return static::whereDate('date', today())
            ->where('status', 'late')
            ->count();
    }
    
}