<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScholarYear extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'scholar_years';
    protected $fillable = [
        'start_year',
        'end_year',
        'capacity',
    ];
}
