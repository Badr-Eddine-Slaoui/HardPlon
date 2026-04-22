<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "skills";
    protected $fillable = [
        'formation_id',
        'code',
        'title',
        'description',
    ];

    protected $appends = ["is_active"];

    protected static function booted()
    {
        static::deleted(function ($skill) {
            $skill->sprint_skills()->delete();
            $skill->skill_levels()->delete();
        });

        static::restored(function ($skill) {
            $skill->sprint_skills()->restore();
            $skill->skill_levels()->restore();
        });

    }

    public function getIsActiveAttribute()
    {
        return !$this->trashed();
    }

    public function formation(){
        return $this->belongsTo(Formation::class);
    }

    public function sprint_skills(){
        return $this->hasMany(SprintSkill::class);
    }

    public function skill_levels(){
        return $this->hasMany(SkillLevel::class);
    }
}
