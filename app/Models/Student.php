<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Student extends Model
{
    protected $fillable = [
       'user_id', 'class_id', 'matricule',
        'birth_date', 'gender', 'photo_path',
        'guardian_name', 'guardian_phone', 'academic_year_id',
        'address'
    ];
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    // ------------------------ Boot : génération automatique du matricule ───────────
    protected static function booted(): void
    {
        static::creating(function (Student $student) {
            if (empty($student->matricule)) {
                $student->matricule = static::generateMatricule(
                    $student->academic_year
                );
            }
        });
    }

    public static function generateMatricule(int $academicYearId): string
    {
        $year = AcademicYear::findOrFail($academicYearId)->start_date->format('Y');

        $prefix = "ETU-{$year}-";

        $last = static::where('academic_year_id', $academicYearId)
            ->lockForUpdate()
            ->orderByDesc('id')
            ->value('matricule');

        $number = $last
            ? ((int) substr($last, -4)) + 1
            : 1;

        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Moyenne générale de l'élève
     * @return float|int
     */
    public function average(): float
    {
        $grades = $this->grades;

        if ($grades->isEmpty()) {
            return 0;
        }

        $total = 0;
        $coefficients = 0;

        foreach ($grades as $grade) {
            $coefficient = $grade->subject->coefficient;

            $total += $grade->score * $coefficient;
            $coefficients += $coefficient;
        }

        return $coefficients > 0 ? round($total / $coefficients, 2) : 0;
    }

    /**
     * Moyenne par trimestre
     * @param Term $term
     * @return float|int
     */
    public function averageByTerm(Term $term): float
    {
        $grades = $this->grades()
            ->with('subject')
            ->where('term_id', $term->id)
            ->get();

        if ($grades->isEmpty()) {
            return 0;
        }

        $total = 0;
        $coefficients = 0;

        foreach ($grades as $grade) {

            $coef = $grade->subject->coefficient;

            $total += $grade->score * $coef;
            $coefficients += $coef;
        }

        return round($total / $coefficients, 2);
    }
    
    /**
     * Classement de l'élève
     * @param Term $term
     * @return int
     */
    public function rank(Term $term): int
    {
        $students = self::where('class_id', $this->class_id)
            ->with('grades.subject')
            ->get()
            ->sortByDesc(fn ($student) => $student->averageByTerm($term->id))
            ->values();

        return $students
            ->search(fn ($student) => $student->id === $this->id) + 1;
    }

    /**
     * Taux de présence
     * @return float|int
     */
    public function attendanceRate(): float
    {
        $total = $this->attendances()->count();

        if ($total === 0) {
            return 0;
        }

        $present = $this->attendances()
            ->where('status', 'present')
            ->count();

        return round(($present / $total) * 100, 2);
    }

    /**
     * Nombre d'absences
     * @return int
     */
    public function absencesCount(): int
    {
        return $this->attendances()
            ->where('status', 'absent')
            ->count();
    }

    /**
     * Nombre de retards
     * @return int
     */
    public function lateCount(): int
    {
        return $this->attendances()
            ->where('status', 'late')
            ->count();
    }

    /**
     * moyenne générale annuelle
     * @return float|int
     */
    public function yearlyAverage(): float
    {
        $grades = $this->grades()
            ->with('subject')
            ->get();

        if ($grades->isEmpty()) {
            return 0;
        }

        $total = 0;
        $coefficients = 0;

        foreach ($grades as $grade) {

            $coef = $grade->subject->coefficient;

            $total += $grade->score * $coef;
            $coefficients += $coef;
        }

        return round($total / $coefficients, 2);
    }

    public static function newStudentsInMonth() : int{
        return static::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count();
    }

    public function updatePhoto(?UploadedFile $photo): void
    {
        if (!$photo) {
            return;
        }

        if ($this->photo) {
            Storage::disk('public')->delete($this->photo);
        }

        $this->update([
            'photo' => $photo->store('students', 'public'),
        ]);
    }

    public function deletePhoto(): void
    {
        if ($this->photo) {
            Storage::disk('public')->delete($this->photo);

            $this->update([
                'photo' => null,
            ]);
        }
    }

     // ----------------------- REALTIONS ------------------
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function classe(): BelongsTo{
        return $this->belongsTo(Classe::class, 'class_id');
    }

    public function grades() : HasMany{
        return $this->hasMany(Grade::class);
    }

    public function attendances() : HasMany{
        return $this->hasMany(Attendance::class);
    }

    public function payments() : HasMany{
        return $this->hasMany(Payment::class);
    }

    public function academicYear(){
        return $this->belongsTo(AcademicYear::class);
    }

    public function reportCards(): HasMany{
        return $this->hasMany(ReportCard::class);
    }
    public function parents() : BelongsToMany{
        return $this->belongsToMany(User::class, 'parents_users', 'student_id', 'user_id')->withTimestamps();
    }

    // ------------------------ SCOPES --------------------------------------

    public function scopeSearch($query, ?string $search)
    {
        if (!$search) {
            return;
        }

        $query->where(function ($query) use ($search) {

            $query
                ->where('matricule', 'like', "%{$search}%")
                ->orWhereHas('user', function ($query) use ($search) {

                    $query
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");

                });
            });
    }

    public function scopeGender($query, ?string $gender)
    {
        if ($gender) {
            $query->where('gender', $gender);
        }
    }

    public function scopeGuardian($query, ?string $guardian)
    {
        if ($guardian) {

            $query->where(
                'guardian_name',
                'like',
                "%{$guardian}%"
            );
        }
    }

    public function scopeBirthDate($query, ?string $date)
    {
        if ($date) {

            $query->whereDate(
                'birth_date',
                $date
            );

        }
    }
    
    public function scopeForYear(Builder $query, int $year)
    {
        return $query->where('academic_year_id', $year);
    }

    public function scopeInClass(Builder $query, int $classId)
    {
        return $query->where('class_id', $classId);
    }

    // ------------------------------- ACCESSORS ----------------------------------- 
    public function getFullNameAttribute(): string
    {
        return $this->user->name;
    }

    protected function age(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->birth_date
                ? now()->diffInYears($this->birth_date)
                : null
        );
    }

}