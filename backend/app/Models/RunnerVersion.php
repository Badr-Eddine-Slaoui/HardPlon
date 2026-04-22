<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RunnerVersion extends Model
{
    use SoftDeletes;
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

    protected static function booted()
    {
        static::deleted(function ($runnerVersion) {
            $runnerVersion->stack_runners()->delete();
        });

        static::restored(function ($runnerVersion) {
            $runnerVersion->stack_runners()->restore();
        });

    }

    public function runner()
    {
        return $this->belongsTo(Runner::class);
    }

    public function stack_runners()
    {
        return $this->hasMany(StackRunner::class);
    }
}
