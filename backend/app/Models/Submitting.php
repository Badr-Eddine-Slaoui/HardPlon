<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submitting extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'submittings';
    protected $fillable = [
        'brief_id',
        'student_id',
        'squad_id',
        'message',
        'links',
    ];

    protected $casts = [
        'links' => 'array',
    ];

    public function brief(){
        return $this->belongsTo(Brief::class);
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function squad(){
        return $this->belongsTo(Squad::class);
    }
}
