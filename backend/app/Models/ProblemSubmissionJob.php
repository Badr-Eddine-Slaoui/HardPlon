<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProblemSubmissionJob extends Model
{

    protected $fillable = [
        'submission_id',
        'status',
        'attempts',
        'last_error',
        'locked_at',
        'locked_by',
        'result',
        'started_at',
        'finished_at'
    ];

    protected $casts = [
        'result' => 'array'
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }
}
