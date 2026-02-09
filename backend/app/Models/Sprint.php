<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sprint extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "sprints";
    protected $fillable = [
        'formation_id',
        'name',
        'description',
        'start_date',
        'end_date',
    ];

    protected $appends = ["is_active"];

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
}
