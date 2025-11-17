<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GradingRuleItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'grading_rule_id',
        'exam_id',
        'weight',
    ];

    /**
     * Get the casts for the model.
     */
    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
        ];
    }

    /**
     * Get the grading rule that owns the item.
     */
    public function gradingRule(): BelongsTo
    {
        return $this->belongsTo(GradingRule::class);
    }

    /**
     * Get the exam that owns the item.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }
}
