<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_period_id',
        'questionnaire_id',
        'summary_data',
        'notes',
        'analysis_content',
        'conclusion_content',
        'followup_content',
        'generated_by'
    ];

    protected $casts = [
        'summary_data' => 'json',
        'generated_at' => 'datetime'
    ];

    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }

    public function generator()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
