<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionnairePermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'questionnaire_id',
        'role',
        'can_fill',
        'can_view_results'
    ];

    protected $casts = [
        'can_fill' => 'boolean',
        'can_view_results' => 'boolean'
    ];

    public function questionnaire()
    {
        return $this->belongsTo(Questionnaire::class);
    }
}
