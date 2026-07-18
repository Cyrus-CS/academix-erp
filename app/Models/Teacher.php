<?php

namespace App\Models;

use App\Models\ClassSubjectTeacher;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{

    protected $table = 'teachers';
    protected $fillable = [
        'user_id',
        'employee_number',
        'specialty',
        'qualification',
        'nationality',
        'status',
        'bio',
        'gender',
        'date_of_birth',
        'position',
        'address',
        'photo'
    ];

    protected function casts() : array{
        return [
            'date_of_birth' => 'datetime'   
        ];
    }

    /**
     * Methode pour gerer les contrats actifs
     * @return mixed|\stdClass|null
     */
    public function currentContract(){
        return $this->contracts()->where('status', 'active')
                        ->latest()->first();
    }

    /**
     * Methode pour gerer le Salaire actuel
     */
    public function currentSalary() : float|int{
        return $this->currentContract()?->salary ?? 0;
    }
    
    /**
     * Nombre de classes pour chaque enseignant
     * @return int
     */
    public function classesCount() : int{
        return $this->assignments()->distinct('class_id')->count('class_id');
    }

    
    /**
     * Nombre d'élèves enseignés
     * @return int
     */
    public function studentsCount() : int{
        $classIds = $this->assignments()->pluck('class_id');
        return Student::whereIn('class_id', $classIds)->count();
    }

    public static function newTeachersInMonth() : int{
        return static::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
    }

    // ----------------- RELATIONS -------------------
    public function user() : BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function assignments() : HasMany{
        return $this->hasMany(ClassSubjectTeacher::class);
    }
    
    public function contracts() : HasMany{
        return $this->hasMany(TeacherContract::class);
    }

    public function classes() : BelongsToMany{
        return $this->belongsToMany(Classe::class)->withPivot('subject_id', 'academic_year');
    }

    public function subjects() : BelongsToMany{
        return $this->belongsToMany(Subject::class, 'class_subject_teacher','teacher_id', 'subject_id')->withPivot('class_id', 'academic_year');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Un enseignant dispense plusieurs cours dans son emploi du temps
     * @return HasMany<Schedule, Teacher>
     */
    public function schedules() : HasMany{
        return $this->hasMany(Schedule::class);
    }

    public function attendances() : HasMany{
        return $this->hasMany(Attendance::class);
    }

    // ----------------- ACCESSEURS ------------------

    public function getFullNameAttribute() : string{
        return $this->user->name;
    }
}