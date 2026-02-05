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
}
