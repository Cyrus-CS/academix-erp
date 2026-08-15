<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'status',
        'description',
        'contract_pdf_path',
    ];

    protected function casts() : array{
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function teacher() : BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
    
    // ------------------------- SCOPES -----------------------
    public function scopeActive(Builder $query)
    {
        return $query->where('status','active');
    }

    protected function computedStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->end_date && $this->end_date->isPast() && $this->status === 'active'
                ? 'expired'
                : $this->status,
        );
    }
}