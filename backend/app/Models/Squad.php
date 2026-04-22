<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Squad extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'squads';
    protected $fillable = [
        'sprint_id',
        'name',
        'member_count',
    ];

    protected static function booted()
    {
        static::deleted(function ($squad) {
            $squad->squad_members()->delete();
        });

        static::restored(function ($squad) {
            $squad->squad_members()->restore();
        });

    }

    public function sprint(){
        return $this->belongsTo(Sprint::class);
    }

    public function squad_members(){
        return $this->hasMany(SquadMember::class);
    }
}
