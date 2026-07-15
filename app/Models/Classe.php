<?php
// app/Models/SchoolClass.php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    protected $table = 'classes';

    protected $fillable = ['name', 'level', 'capacity', 'academic_year_id'];

    /**
     * Liste des Meilleurs élèves
     * @param Term $term
     * @param int $limit
     */
    public function topStudents(Term $term, int $limit = 5){
        return $this->students->sortByDesc(fn($student) 
                        =>$student->averageByTerm($term->id)
                    )->take($limit);
    }

    /**
     * Compte l'effectif réel d'une classe
     * @return int
     */
    public function studentsCount() : int{
        return $this->students()->count();
    }

    /**
     * Calcul le taux d'occupation
     * @return float|int
     */
    public function occupancyRate(): float
    {
        if ($this->capacity === 0) {
            return 0;
        }

        return round(
            ($this->studentsCount() / $this->capacity) * 100,
            2
        );
    }

    // ---------------------- RELATIONS ---------------------
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            Teacher::class,
            'class_subject_teacher',
            'class_id',
            'teacher_id'
        )->withPivot('subject_id', 'academic_year');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(
            Subject::class,
            'class_subject_teacher',
            'class_id',
            'subject_id'
        )->withPivot('teacher_id', 'academic_year');
    }

    /**
     * Une classe possede plusieurs creneaux
     * @return HasMany<Schedule, Classe>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'class_id');
    }

    public function reportCards(): HasMany
    {
        return $this->hasMany(ReportCard::class, 'class_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    // ------------------------------- SCOPES ---------------------------------    
    public function scopeForYear(Builder $query, int $year)
    {
        return $query->where('academic_year', $year);
    }

    // -------------------------------- helpers --------------------------------------
    public function hasCapacity(): bool
    {
        return $this->students()->count() < $this->capacity;
    }

    public function getRemainingCapacityAttribute(): int
    {
        return $this->capacity - $this->students()->count();
    }
}