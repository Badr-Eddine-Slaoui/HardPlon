<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BriefSkillLevel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "brief_skill_levels";
    protected $fillable = [
        "brief_id",
        "skill_id",
        "level_id",
    ];

    public function brief(){
        return $this->belongsTo(Brief::class);
    }

    public function skill(){
        return $this->belongsTo(Skill::class);
    }

    public function level(){
        return $this->belongsTo(Level::class);
    }

    public function correction_details(){
        return $this->hasMany(CorrectionDetail::class);
    }
}
