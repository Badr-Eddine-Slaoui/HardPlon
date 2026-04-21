<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProblemSubmission extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'submission_id',
        'problem_id',
        'code'
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }
}
