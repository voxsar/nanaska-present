<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresentationEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'presentation_id',
        'evaluated_by',
        'prompt',
        'feedback',
        'score',
        'criteria_scores',
    ];

    protected $casts = [
        'criteria_scores' => 'array',
    ];

    /**
     * Get the presentation that owns the evaluation.
     */
    public function presentation(): BelongsTo
    {
        return $this->belongsTo(Presentation::class);
    }

    /**
     * Get the admin who evaluated the presentation.
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }
}
