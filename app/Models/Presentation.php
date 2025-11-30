<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Presentation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_path',
        'video_path',
        'audio_path',
        'status',
    ];

    /**
     * Get the user that owns the presentation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the evaluation for the presentation.
     */
    public function evaluation(): HasOne
    {
        return $this->hasOne(PresentationEvaluation::class);
    }
}
