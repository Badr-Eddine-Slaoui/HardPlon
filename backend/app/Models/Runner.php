<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Runner extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
        'status',
    ];

    public function versions()
    {
        return $this->hasMany(RunnerVersion::class);
    }
}
