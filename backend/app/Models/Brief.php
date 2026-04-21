<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brief extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'briefs';
    protected $fillable = [
        "sprint_id",
        "class_group_id",
        "teacher_id",
        "stack_id",
        "title",
        "description",
        "content",
        "is_collective",
        "start_date",
        "end_date",
    ];

    protected $appends = ["is_active"];

    public function getIsActiveAttribute()
    {
        return !$this->trashed();
    }

    public function sprint(){
        return $this->belongsTo(Sprint::class);
    }

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

    public function class_group(){
        return $this->belongsTo(ClassGroup::class);
    }

    public function brief_skill_levels(){
        return $this->hasMany(BriefSkillLevel::class);
    }

    public function submittions(){
        return $this->hasMany(Submission::class);
    }

    public function corrections(){
        return $this->hasMany(Correction::class);
    }

    public function stack(){
        return $this->belongsTo(Stack::class);
    }

    public function brief_versions(){
        return $this->hasMany(BriefVersion::class);
    }

    public function problems(){
        return $this->hasMany(Problem::class);
    }
}
