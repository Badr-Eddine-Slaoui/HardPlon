<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

class Student extends User
{
    protected $table = "users";
    protected static function booted()
    {
        static::addGlobalScope('student', function (Builder $builder) {
            $builder->where('users.role', 'STUDENT');
        });
    }

    public function class_group(){
        return $this->belongsTo(ClassGroup::class,'id_class','id');
    }

    public function squad_members(){
        return $this->hasMany(SquadMember::class);
    }

    public function submissions(){
        return $this->hasMany(Submission::class);
    }

    public function corrections(){
        return $this->hasMany(Correction::class);
    }
}
