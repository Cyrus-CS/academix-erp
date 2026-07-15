<?php
// app/Models/Grade.php
namespace App\Models;

use App\Models\Classe;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    protected $fillable = [
        'student_id', 'subject_id', 'class_id',
        'score', 'max_score', 'type', 'period',
        'academic_year', 'comment',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
    ];

    protected function percentage(): Attribute
    {
        return Attribute::make(
            get: fn() => round(
                ($this->score / $this->max_score) * 100,
                2
            )
        );
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class, 'class_id');
    }

    // Score normalisé sur 20
    public function getScoreOn20Attribute(): float
    {
        if ($this->max_score == 0) return 0;
        return round(($this->score * 20) / $this->max_score, 2);
    }

    // -------------------- SCOPES ----------------------------------
    public function scopePeriod(Builder $query, int $period)
    {
        return $query->where('period', $period);
    }

    public function scopeForYear(Builder $query, int $year)
    {
        return $query->where('academic_year', $year);
    }
}