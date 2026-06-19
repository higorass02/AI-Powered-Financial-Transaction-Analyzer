<?php

namespace App\Models;

use App\Enums\AnomalyLevel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AIAnalysis extends Model
{
    use HasFactory;

    protected $table = 'ai_analyses';

    protected $fillable = [
        'transaction_id',
        'category_suggested',
        'confidence',
        'is_anomaly',
        'anomaly_level',
        'anomaly_reason',
        'reasoning',
        'recommendations',
        'needs_review',
        'user_feedback',
        'prompt_tokens',
        'completion_tokens',
        'model_used',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'float',
            'is_anomaly' => 'boolean',
            'anomaly_level' => AnomalyLevel::class,
            'recommendations' => 'array',
            'needs_review' => 'boolean',
            'prompt_tokens' => 'integer',
            'completion_tokens' => 'integer',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function isHighConfidence(): bool
    {
        return $this->confidence >= 0.7;
    }
}
