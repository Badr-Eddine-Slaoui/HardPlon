<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Submission extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'submissions';
    protected $fillable = [
        'brief_id',
        'student_id',
        'squad_id',
        'message',
        'link',
    ];

    protected static function booted()
    {
        static::deleted(function ($submission) {
            $submission->evaluationJob()->delete();
            $submission->problemSubmissions()->delete();
            $submission->problemSubmissionJob()->delete();
        });

        static::restored(function ($submission) {
            $submission->evaluationJob()->restore();
            $submission->problemSubmissions()->restore();
            $submission->problemSubmissionJob()->restore();
        });

    }

    public function brief(){
        return $this->belongsTo(Brief::class);
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function squad(){
        return $this->belongsTo(Squad::class);
    }

    public function evaluationJob()
    {
        return $this->hasOne(EvaluationJob::class);
    }

    public function problemSubmissions()
    {
        return $this->hasMany(ProblemSubmission::class);
    }

    public function problemSubmissionJob()
    {
        return $this->hasOne(ProblemSubmissionJob::class);
    }

    public function correction()
    {
        return $this->hasOne(Correction::class);
    }
}
