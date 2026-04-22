<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Runner extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'type',
        'description',
        'status',
    ];

    protected static function booted()
    {
        static::deleted(function ($runner) {
            $runner->versions()->delete();
        });

        static::restored(function ($runner) {
            $runner->versions()->restore();
        });

    }

    public function versions()
    {
        return $this->hasMany(RunnerVersion::class);
    }
}
