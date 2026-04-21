<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationJob extends Model
{
    protected $table = 'evaluation_jobs';
    protected $fillable = [
        'submission_id',
        'status',
        'attempts',
        'last_error',
        'locked_at',
        'locked_by',
        'result',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'result' => 'array',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
