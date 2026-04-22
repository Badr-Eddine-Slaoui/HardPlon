<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Correction extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "corrections";
    protected $fillable = [
        'brief_id',
        'student_id',
        'teacher_id',
        'message',
        'result'
    ];

    protected static function booted()
    {
        static::deleted(function ($correction) {
            $correction->correction_details()->delete();
        });

        static::restored(function ($correction) {
            $correction->correction_details()->restore();
        });

    }

    public function brief(){
        return $this->belongsTo(Brief::class);
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

    public function correction_details(){
        return $this->hasMany(CorrectionDetail::class);
    }
}
