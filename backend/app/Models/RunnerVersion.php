<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RunnerVersion extends Model
{
    protected $fillable = [
        'runner_id',
        'version',
        'docker_image',
        'default_config',
        'status',
        'is_default'
    ];

    protected $casts = [
        'default_config' => 'array',
    ];

    public function runner()
    {
        return $this->belongsTo(Runner::class);
    }

    public function stack_runners()
    {
        return $this->hasMany(StackRunner::class);
    }
}
