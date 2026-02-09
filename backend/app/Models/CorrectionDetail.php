<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorrectionDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "correction_details";
    protected $fillable = [
        'correction_id',
        'brief_skill_level_id',
        'grade',
    ];

    public function correction(){
        return $this->belongsTo(Correction::class);
    }

    public function brief_skill_level(){
        return $this->belongsTo(BriefSkillLevel::class);
    }
}
