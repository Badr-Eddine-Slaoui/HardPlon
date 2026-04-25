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

    protected $appends = ["is_active"];
    
    public function getIsActiveAttribute()
    {
        return !$this->trashed();
    }

    protected static function booted()
    {
        static::deleted(function ($stack) {
            $stack->stack_runners()->delete();
        });

        static::restored(function ($stack) {
            $stack->stack_runners()->restore();
        });

    }

    public function briefs()
    {
        return $this->hasMany(Brief::class);
    }

    public function stack_runners()
    {
        return $this->hasMany(StackRunner::class);
    }
}
