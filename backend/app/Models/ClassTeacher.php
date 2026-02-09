<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassTeacher extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "class_teachers";
    protected $fillable = [
        "class_group_id",
        "teacher_id",
        "role",
        "assigned_at",
    ];

    public function class_group(){
        return $this->belongsTo(ClassGroup::class);
    }

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
}
