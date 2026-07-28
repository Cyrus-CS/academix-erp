<?php

namespace App\Models;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ParentUser extends Model
{
    protected $table = 'parents_users';

    /**
     * Les élèves associés à ce parent.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(
            Student::class,
            'parents_users',   // table pivot
            'user_id',         // FK vers ce modèle
            'student_id'       // FK vers Student
        );
    }
}