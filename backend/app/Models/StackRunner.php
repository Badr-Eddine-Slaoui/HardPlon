<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StackRunner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'stack_id',
        'runner_version_id',
        'priority',
    ];

    public function stack()
    {
        return $this->belongsTo(Stack::class);
    }

    public function runner_version()
    {
        return $this->belongsTo(RunnerVersion::class);
    }
}
