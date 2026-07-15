<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeacherContract extends Model
{
    use SoftDeletes;
    protected $table = 'teachers_contracts';
    protected $fillable = [
        'teacher_id',
        'contract_number',
        'contract_type',
        'salary',
        'start_date',
        'end_date',
        'status'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status','active');
    }
}