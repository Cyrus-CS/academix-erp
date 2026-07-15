<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
// use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'phone',
        'is_active',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function childrens()
    {
        return $this->belongsToMany(
            Student::class,
            'parent_users',
            'user_id',
            'student_id'
        );
    }

    public function teacher() : HasOne{
        return $this->hasOne(Teacher::class);
    }

    public function parent()
    {
        return $this->hasOne(ParentUser::class);
    }

    public function parentStudents(): BelongsToMany{
        return $this->belongsToMany(Student::class, 'parents_users', 'user_id', 'student_id')->withTimestamps();
    }

    public function paymentsCreated(){
        return $this->hasMany(Payment::class, 'created_by');
    }

    //  --------------------------- SCOPES -------------------
    public function scopeActive(Builder $query) {
        $query->where('is_active', true);
    }

    
    /**
     * Verifie si l'utilisateur est un Administrateur
     * @return bool
     */
    public function isAdministrator() : bool{
        return $this->hasRole('Admin');
    }

    /**
     * Verifie si l'utilisateur est un Etudiant
     * @return bool
     */
    public function isStudent() : bool{
        return $this->hasRole('Student');
    }

    /**
     * Verifie si l'utilisateur est un enseignant
     * @return bool
     */
    public function isTeacher() : bool{
        return $this->hasRole('Teacher');
    }

    /**
     * Verifie si l'utilisateur est un parent
     * @return bool
     */
    public function isParent() : bool{
        return $this->hasRole('Parent');
    }
}