<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeLevel extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "grade_levels";
    protected $fillable = [
        'name',
        'capacity',
        'description',
        'scholar_year_id',
    ];
}
