<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "levels";
    protected $fillable = [
        'name',
        'description',
        'order',
    ];

    protected $appends = ["is_active"];

    protected static function booted()
    {
        static::deleted(function ($level) {
            $level->skill_levels()->delete();
        });

        static::restored(function ($level) {
            $level->skill_levels()->restore();
        });

    }

    public function getIsActiveAttribute()
    {
        return !$this->trashed();
    }

    public function skill_levels(){
        return $this->hasMany(SkillLevel::class);
    }
}
