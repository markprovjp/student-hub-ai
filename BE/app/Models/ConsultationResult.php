<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationResult extends Model
{
    protected $fillable = [
        'user_id',
        'input_data',
        'ai_result',
        'recommended_majors',
        'study_suggestions',
        'confidence_score',
        'session_id'
    ];

    protected $casts = [
        'input_data' => 'array',
        'recommended_majors' => 'array',
        'study_suggestions' => 'array',
        'confidence_score' => 'float'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
