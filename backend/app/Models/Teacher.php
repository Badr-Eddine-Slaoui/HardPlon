<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Teacher extends User
{
    protected $table = "users";
    protected static function booted()
    {
        static::addGlobalScope('teacher', function (Builder $builder) {
            $builder->where('users.role', 'TEACHER');
        });

        static::deleted(function ($teacher) {
            $teacher->class_teachers()->delete();
            $teacher->briefs()->delete();
            $teacher->corrections()->delete();
        });

        static::restored(function ($teacher) {
            $teacher->class_teachers()->restore();
            $teacher->briefs()->restore();
            $teacher->corrections()->restore();
        });
    }

    public function class_teachers(){
        return $this->hasMany(ClassTeacher::class);
    }

    public function briefs(){
        return $this->hasMany(Brief::class);
    }

    public function corrections(){
        return $this->hasMany(Correction::class);
    }
}
