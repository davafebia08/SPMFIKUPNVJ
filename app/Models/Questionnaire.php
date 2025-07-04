<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'type',
        'academic_period_id',
        'start_date',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean'
    ];

    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriod::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'questionnaire_user')
            ->withPivot('submitted_at')
            ->withTimestamps();
    }

    public function responses()
    {
        return $this->hasMany(Response::class);
    }

    public function permissions()
    {
        return $this->hasMany(QuestionnairePermission::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function suggestions()
    {
        return $this->hasMany(Suggestion::class);
    }
}
