<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brief extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'briefs';
    protected $fillable = [
        "sprint_id",
        "class_group_id",
        "teacher_id",
        "title",
        "description",
        "content",
        "is_collective",
        "start_date",
        "end_date",
    ];
}
