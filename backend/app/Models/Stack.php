<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stack extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    public function briefs()
    {
        return $this->hasMany(Brief::class);
    }

    public function stack_runners()
    {
        return $this->hasMany(StackRunner::class);
    }
}
