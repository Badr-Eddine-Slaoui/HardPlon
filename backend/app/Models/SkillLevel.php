<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SkillLevel extends Model
{
    use HasFactory, SoftDeletes;
    public $incrementing = false;
    protected $primaryKey = null;
    protected $table = "skill_levels";
    protected $fillable = [
        "skill_id",
        "level_id",
        "criteria"
    ];

    public function skill(){
        return $this->belongsTo(SkillLevel::class);
    }

    public function level(){
        return $this->belongsTo(Level::class);
    }
}
