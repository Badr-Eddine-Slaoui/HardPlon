<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Language extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'docker_image',
        'compile_command',
        'run_command',
    ];

    protected static function booted()
    {
        static::deleted(function ($language) {
            $language->problems()->delete();
        });

        static::restored(function ($language) {
            $language->problems()->restore();
        });

    }

    public function problems()
    {
        return $this->hasMany(Problem::class);
    }
}
