<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportCard extends Model
{

protected $table = 'report_cards';
    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id',
        'average',
        'rank',
        'appreciation',
        'report_card_pdf_path',
    ];

    protected $casts = [
        'period' => 'integer',
    ];

   /**
    * Calcul automatique de moyenne
    * @return float|int
    */
   public function calculateAverage() : float|int{
        return $this->student->averageByTerm($this->term);
   }

   /**
    * Génération d'appréciation
    * @return string
    */
   public function generateAppreciation() : string{
        $average = $this->average;
        return match(true){
            $average >= 16 => "Excellent travail",
            $average >= 14 => "Très bon travail",
            $average >= 12 => "Bon travail",
            $average >= 10 => "Assez bien",
            default => "Doit redoubler d'effort"
        };
   }

   // ------------------ RELATIONS ------------------
   public function student() : BelongsTo{
        return $this->belongsTo(Student::class);
   }

    public function academicYear(){
        return $this->belongsTo(AcademicYear::class);
   }
}