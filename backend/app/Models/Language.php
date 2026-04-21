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

    public function problems()
    {
        return $this->hasMany(Problem::class);
    }
}
