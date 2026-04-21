<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProblemTestCase extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'problem_id',
        'input',
        'expected_output',
        'is_hidden'
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
        'input' => 'array',
        'expected_output' => 'array'
    ];

    public function problem()
    {
        return $this->belongsTo(Problem::class);
    }
}
