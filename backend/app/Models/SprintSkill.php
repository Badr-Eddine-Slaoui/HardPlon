<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SprintSkill extends Model
{
    use HasFactory, SoftDeletes;
    public $incrementing = false;
    protected $primaryKey = null;
    protected $table = "sprint_skills";
    protected $fillable = [
        'sprint_id',
        'skill_id',
    ];

    public function sprint(){
        return $this->belongsTo(Sprint::class);
    }

    public function skill(){
        return $this->belongsTo(Skill::class);
    }
}
