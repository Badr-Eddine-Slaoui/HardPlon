<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassGroup extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "class_groups";
    protected $fillable = [
        'formation_id',
        'name',
        'image_url',
        'capacity',
        'description',
    ];

    protected $appends = [
        'students_count',
        'is_active'
    ];

    public function getStudentsCountAttribute(){
        return $this->students()->count();
    }

    public function getIsActiveAttribute(){
        return !$this->trashed();
    }

    public function formation(){
        return $this->belongsTo(Formation::class);
    }

    public function students(){
        return $this->hasMany(Student::class,'id_class','id');
    }

    public function main_teacher(){
        return $this->hasOne(ClassTeacher::class)->where('role', 'MAIN');
    }

    public function sub_teacher(){
        return $this->hasOne(ClassTeacher::class)->where('role', 'SUB');
    }
}
