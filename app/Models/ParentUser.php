<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentUser extends Model
{
    protected $table = 'parents_users';
    public $incrementing = true; // si tu as un id auto-increment
     protected $fillable = [
        'user_id',
        'student_id',
    ];
}