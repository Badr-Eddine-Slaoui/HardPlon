<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Problem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'brief_id',
        'brief_skill_level_id',
        'language_id',
        'title',
        'description',
        'hidden_header',
        'hidden_footer',
        'skeleton_code',
        'order_index',
    ];

    public function brief()
    {
        return $this->belongsTo(Brief::class);
    }

    public function test_cases()
    {
        return $this->hasMany(ProblemTestCase::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    public function submissions()
    {
        return $this->hasMany(ProblemSubmission::class);
    }
}
