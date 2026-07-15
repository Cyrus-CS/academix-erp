<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'coefficient',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    /**
     * Une matiere apparait dans plusieurs creneaux
     * @return HasMany<Schedule, Subject>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function teacherAssignments() : HasMany{
        return $this->hasMany(ClassSubjectTeacher::class);
    }

}